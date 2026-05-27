@extends('layouts.admin')

@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <div class="card-header flex-column flex-md-row border-bottom">
                            <div class="head-label text-center"><h5 class="card-title mb-0">Список новостей</h5></div>
                            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                                <div>
                                    <div>
                                        <button
                                            class="btn btn-secondary buttons-collection dropdown-toggle btn-label-primary waves-effect waves-light"
                                            tabindex="0" aria-controls="DataTables_Table_0" type="button"
                                            aria-haspopup="dialog" aria-expanded="false"><span><i
                                                    class="ri-external-link-line me-sm-1"></i> <span
                                                    class="d-none d-sm-inline-block">Export</span></span></button>
                                        <button type="button"
                                                class="btn btn-secondary dropdown-toggle waves-effect waves-light"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Фильтр
                                        </button>
                                        <a href="{{route('admin.news.create')}}"
                                           class="btn btn-secondary create-new btn-primary waves-effect waves-light"
                                           tabindex="0" aria-controls="DataTables_Table_0"><span><i
                                                    class="ri-add-line ri-16px me-sm-2"></i> <span
                                                    class="d-none d-sm-inline-block">Добавить</span></span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mt-5 mt-md-0">

                            </div>
                            <div class="col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                                <div id="DataTables_Table_0_filter" class="dataTables_filter">

                                    <label>
                                        <input
                                            type="search" class="form-control form-control-sm" placeholder="Поиск:"
                                            aria-controls="DataTables_Table_0">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Заголовок</th>
                                <th>Создан</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            @foreach($news as $item)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{$item->id}}</span>
                                    </td>
                                    <td>{{$item->title }}</td>

                                    <td><span
                                            class="badge rounded-pill bg-label-primary me-1">{{$item->created_at}}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-edit-alt"></i> Редактировать
                                        </a>
                                        <form action="{{ route('admin.news.update', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить новость?')">
                                                <i class="bx bx-trash"></i> Удалить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{$news->links()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- / Content -->

    </div>
    <!-- Content wrapper -->

@endsection
