<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Company;
use App\Models\AdditionalUserPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        // Показываем ТОЛЬКО активные подписки (по одной на компанию)
        $query = Subscription::with(['company', 'additionalUserPurchases'])
            ->where('status', 'active')  // Только активные подписки
            ->where('expires_at', '>', now());  // Только не истекшие

        // Фильтр по типу
        if ($request->type && in_array($request->type, ['premium', 'basic'])) {
            $query->where('type', $request->type);
        }

        // Поиск по названию компании
        if ($request->search) {
            $query->whereHas('company', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->orderBy('id', 'desc')->paginate($request->per_page ?? 20);

        // Статистика (только активные подписки)
        $totalCompanies = Company::count();
        $premiumCount = Subscription::where('type', 'premium')->where('status', 'active')->where('expires_at', '>', now())->count();
        $basicCount = Company::where('license_type', 'basic')->count();
        $totalUsers = User::count();

        return view('admin.subscribe.index', compact(
            'subscriptions',
            'totalCompanies',
            'premiumCount',
            'basicCount',
            'totalUsers'
        ));
    }

    public function companyInfo($id)
    {
        $company = Company::findOrFail($id);
        $subscription = Subscription::where('company_id', $id)->where('status', 'active')->first();

        return response()->json([
            'id' => $company->id,
            'name' => $company->name,
            'phone' => $company->phone,
            'license_type' => $company->license_type,
            'active_users' => $company->getActiveUsersCount(),
            'max_users' => $subscription ? $subscription->getTotalUserSlots() : 5,
            'used_storage' => $company->getFormattedUsedStorage(),
            'total_storage' => $company->getFormattedStorageLimit(),
            'created_at' => $company->created_at->format('d.m.Y')
        ]);
    }

    public function addUsers(Request $request, $subscriptionId)
    {
        $request->validate([
            'user_count' => 'required|integer|min:1|max:100',
            'period' => 'required|in:month,6months,year'
        ]);

        $subscription = Subscription::findOrFail($subscriptionId);

        if ($subscription->type !== 'premium') {
            return response()->json(['error' => 'Дополнительные пользователи доступны только для Премиум тарифа'], 400);
        }

        $months = match($request->period) {
            '6months' => 6,
            'year' => 12,
            default => 1
        };

        $additionalPurchase = AdditionalUserPurchase::create([
            'company_id' => $subscription->company_id,
            'subscription_id' => $subscription->id,
            'user_count' => $request->user_count,
            'period' => $request->period,
            'expires_at' => now()->addMonths($months),
            'is_active' => true
        ]);

        return response()->json(['success' => true, 'purchase' => $additionalPurchase]);
    }

    public function cancel($subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);
        $subscription->update(['status' => 'cancelled', 'auto_renew' => false]);

        return response()->json(['success' => true]);
    }

    public function destroy($subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);
        $subscription->delete();

        return response()->json(['success' => true]);
    }
}
