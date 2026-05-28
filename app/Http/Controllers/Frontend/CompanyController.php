<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Company\StoreRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index() {
        return view('frontend.company.index');
    }

    public function create()
    {
        return view('frontend.company.create');
    }

    public function store(StoreRequest $request) {

        $data = $request->validated();

        $company = Company::firstOrCreate($data);

        $company->save();


        return to_route('companies.index');


    }

    /**
     * Обновление лицензии компании (улучшение до премиум)
     */
    public function upgradeLicense(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->back()->with('error', 'Компания не найдена');
        }

        $newLicenseType = $request->input('new_license_type');

        // Проверяем, что тип лицензии допустим
        if (!in_array($newLicenseType, ['premium', 'basic'])) {
            return redirect()->back()->with('error', 'Неверный тип лицензии');
        }

        // Если уже премиум, не нужно обновлять
        if ($company->license_type === 'premium') {
            return redirect()->back()->with('info', 'У вас уже активен Премиум тариф');
        }

        // Вместо прямого обновления, лучше перенаправить на страницу оплаты
        // Так как улучшение до премиум должно быть платным
        return redirect()->route('licence.index')->with('info', 'Для перехода на Премиум тариф необходимо оплатить подписку');
    }
}
