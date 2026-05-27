<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\News\StoreRequest;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{

    // Индексная страница новостей
    public function index()
    {

        $newsList = News::paginate(5);


        return view('frontend.news.index', [
            'newsList' => $newsList
        ]);
    }
}
