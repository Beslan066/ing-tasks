<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\News\StoreRequest;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{

    // Индексная страница новостей
    public function index()
    {
        $news = News::all();

        return view('admin.news.index', [
            'news' => $news
        ]);
    }

    public function create() {

        return view('admin.news.create');
    }

    public function store(StoreRequest $request) {

        $data = $request->validated();

        if (isset($data['image'])) {
            $path = Storage::put('images', $data['image']);
            $data['image'] = $path ?? null;
        }

        return redirect()->route('admin.news.index');
    }

    public function edit(News $news) {
        return view('admin.news.edit', ['news' => $news]);
    }

    public function update(StoreRequest $request, News $news) {
        $data = $request->validated();

        if (isset($data['image'])) {
            // Удаляем старое изображение
            if ($news->image) {
                Storage::delete($news->image);
            }
            $path = Storage::put('images', $data['image']);
            $data['image'] = $path;
        }

        $news->update($data);

        return redirect()->route('admin.news.index');
    }

    public function destroy(News $news) {

        $news->delete();

        return redirect()->route('admin.news.index');
    }
}
