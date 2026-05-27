@extends('layouts.admin')

@section('content')


    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Ошибка!</strong> Пожалуйста, исправьте следующие ошибки:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Basic Layout -->

            <!-- Multi Column with Form Separator -->
            <div class="card mb-6">
                <h5 class="card-header">Новости - создание</h5>
                <form class="card-body" action="{{route('admin.news.store')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="row g-6">

                        <div class="col-md-6 mb-6 mt-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="formtabs-first-name" class="form-control" placeholder="Заголовок новости" name="title">
                                <label for="selectpickerBasic">Название</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-6 mt-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="formtabs-first-lead" class="form-control" placeholder="Короткое описание" name="lead">
                                <label for="">Лид</label>
                            </div>
                        </div>


                        <!-- Snow Theme -->
                        <div class="col-12">
                            <div class="card mb-6">
                                <h5 class="card-header">Контент</h5>
                                <div class="card-body">
                                    <div id="snow-toolbar">
                        <span class="ql-formats">
                          <select class="ql-font"></select>
                          <select class="ql-size"></select>
                        </span>
                                        <span class="ql-formats">
                          <button class="ql-bold"></button>
                          <button class="ql-italic"></button>
                          <button class="ql-underline"></button>
                          <button class="ql-strike"></button>
                        </span>
                                        <span class="ql-formats">
                          <select class="ql-color"></select>
                          <select class="ql-background"></select>
                        </span>
                                        <span class="ql-formats">
                          <button class="ql-script" value="sub"></button>
                          <button class="ql-script" value="super"></button>
                        </span>
                                        <span class="ql-formats">
                          <button class="ql-header" value="1"></button>
                          <button class="ql-header" value="2"></button>
                          <button class="ql-blockquote"></button>
                          <button class="ql-code-block"></button>
                        </span>
                                    </div>
                                    <div id="snow-editor">
                                        <h6>Quill Rich Text Editor</h6>
                                        <p>
                                            Cupcake ipsum dolor sit amet. Halvah cheesecake chocolate bar gummi bears cupcake. Pie
                                            macaroon bear claw. Soufflé I love candy canes I love cotton candy I love.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="content" id="news_content">
                        </div>

                        <div class="mb-4">
                            <label for="formFile" class="form-label">Изображение новости</label>
                            <input class="form-control" type="file" id="formFile" name="image">
                        </div>



                        <div class="">
                            <button type="reset" class="btn btn-outline-secondary waves-effect">Отмена</button>
                            <button type="submit" class="btn btn-primary me-4 waves-effect waves-light">Создать
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
@endsection


@section('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Инициализация Quill редактора
        var quill = new Quill('#snow-editor', {
            theme: 'snow',
            modules: {
                toolbar: '#snow-toolbar'
            }
        });

        // Получаем форму
        var form = document.querySelector('form[action="{{route('admin.news.store')}}"]');

        // При отправке формы
        form.onsubmit = function() {
            // Получаем HTML содержимое редактора
            var content = quill.root.innerHTML;
            // Записываем его в скрытое поле
            document.getElementById('news_content').value = content;
            return true;
        };
    </script>
@endsection
