<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['company', 'ownedCompanies']);


        $departments = Department::all();
        $categories = Category::all();
        $companies = Company::where('id', $user->company_id)->get();

        return view('welcome', [
            'companies' => $companies,
            'categories' => $categories,
            'departments' => $departments,
            'user' => $user,
            'ownedCompanies' => $user->ownedCompanies,
        ]);
    }

    public function noCompanies() {


        return view('frontend.no-companies');
    }


}
