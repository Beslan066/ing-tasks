@extends('layouts.app')

@section('content')
    <!-- Страница досок -->
    <div id="boards">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold" style="color: #16a34a;">Мои отделы</h1>
                <p class="text-gray-500">Управляйте рабочими пространствами и проектами</p>
            </div>
            <button class="bg-primary text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-secondary transition" onclick="openDepartmentModal()">
                <i class="fas fa-plus"></i>
                <span>Новый отдел</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if(isset($departments))
                @foreach($departments as $department)
                    <div class="bg-white rounded-lg shadow-md p-6 card-hover cursor-pointer">
                        <div class="flex items-center space-x-3 mb-4">
                            <div>
                                <h3 class="font-bold text-lg">{{$department->name}}</h3>
                                <div class="flex align-items-center">
                                    <p class="text-gray-500 mr-2">{{$department->tasks()->count()}} активных задач</p>
                                    <p class="text-gray-500">{{$department->users()->count()}} сотрудников</p>
                                </div>
                            </div>


                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Обновлено 2 часа назад</span>
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">ИИ</div>
                                <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">АП</div>
                                <div class="w-8 h-8 rounded-full bg-purple-500 border-2 border-white flex items-center justify-center text-white text-xs">МК</div>
                            </div>
                        </div>
                    </div>

                @endforeach
            @endif
        </div>
    </div>
@endsection
