<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\News\StoreRequest;
use App\Http\Requests\Frontend\News\UpdateRequest;
use App\Models\News;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{

    // Индексная страница новостей
    public function index()
    {
        $news = News::paginate(10);

        return view('admin.news.index', [
            'news' => $news
        ]);
    }

    public function create() {

        return view('admin.news.create');
    }

    public function store(StoreRequest $request) {
        $data = $request->validated();

        // Обработка контента из редактора
        if ($request->has('content')) {
            $data['content'] = $request->input('content');
        }

        // Обработка изображения
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image'] = $path;
        }

        News::create($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'Новость успешно создана');
    }

    public function edit(News $news) {
        return view('admin.news.edit', compact('news'));
    }

    public function update(UpdateRequest $request, News $news) {
        // Логирование для отладки
        Log::info('Update request received', $request->all());

        $data = $request->validated();

        // Обязательно берем content из запроса
        $data['content'] = $request->input('content');

        // Обработка изображения
        if ($request->hasFile('image')) {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('images', 'public');
            $data['image'] = $path;
        } else {
            unset($data['image']);
        }

        // Обновление новости
        $news->update($data);

        Log::info('News updated successfully', ['id' => $news->id, 'content_length' => strlen($news->content)]);

        return redirect()->route('admin.news.index')
            ->with('success', 'Новость успешно обновлена');
    }


    public function destroy(News $news) {

        $news->delete();

        return redirect()->route('admin.news.index');
    }
}
