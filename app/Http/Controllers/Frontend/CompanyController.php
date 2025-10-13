<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Company\StoreRequest;
use App\Models\Company;
use Illuminate\Http\Request;

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
}
