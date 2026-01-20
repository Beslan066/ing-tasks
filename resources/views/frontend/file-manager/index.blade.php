@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">
        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `Хранилище`}">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Хранилище</h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                               href="{{ route('dashboard') }}">
                                Home
                                <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                                          stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">File Manager</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <!-- Информация о хранилище -->
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-2">
                        Лимиты хранилища
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Тариф:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($company->license_type == 'basic') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($company->license_type == 'optimal') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @else bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                                @endif">
                                {{ $company->getLicenseTypeName() }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Макс. размер файла:
                            @php
                                $maxFileSize = match($company->license_type) {
                                    'basic' => '100 MB',
                                    'optimal' => '500 MB',
                                    'premium' => '1 GB',
                                    default => '100 MB'
                                };
                            @endphp
                            <span class="font-medium">{{ $maxFileSize }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800 dark:text-white/90">
                            {{ $storageUsage->getFormattedUsedStorage() }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            из {{ $storageUsage->getFormattedTotalStorage() }}
                        </div>
                    </div>
                    <div class="relative w-32">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="h-2.5 rounded-full
                                @if($storageUsage->getUsagePercentage() > 90) bg-red-600
                                @elseif($storageUsage->getUsagePercentage() > 70) bg-yellow-500
                                @else bg-green-600
                                @endif"
                                 style="width: {{ min($storageUsage->getUsagePercentage(), 100) }}%">
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">
                            {{ round($storageUsage->getUsagePercentage(), 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <!-- Media Card -->
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-4 py-4 sm:pl-6 sm:pr-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Статистика по типам файлов
                            </h3>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <!-- Search Input -->
                                <div class="relative">
                                    <button
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"
                                                  fill=""></path>
                                        </svg>
                                    </button>

                                    <input type="text" placeholder="Поиск файлов..."
                                           class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-3.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-green-300 focus:outline-hidden focus:ring-3 focus:ring-green-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-green-800 xl:w-[300px]">
                                </div>

                                <!-- Upload Button with Modal -->
                                <button onclick="openUploadModal()"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 sm:w-auto">
                                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M9.2502 4.99951C9.2502 4.5853 9.58599 4.24951 10.0002 4.24951C10.4144 4.24951 10.7502 4.5853 10.7502 4.99951V9.24971H15.0006C15.4148 9.24971 15.7506 9.5855 15.7506 9.99971C15.7506 10.4139 15.4148 10.7497 15.0006 10.7497H10.7502V15.0001C10.7502 15.4143 10.4144 15.7501 10.0002 15.7501C9.58599 15.7501 9.2502 15.4143 9.2502 15.0001V10.7497H5C4.58579 10.7497 4.25 10.4139 4.25 9.99971C4.25 9.5855 4.58579 9.24971 5 9.24971H9.2502V4.99951Z"
                                              fill=""></path>
                                    </svg>
                                    Загрузить файл
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 xl:grid-cols-3">
                            <!-- Image Statistics -->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-green-600/[0.08] text-green-500">
                                        <svg class="fill-current" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.05 3.9L8.45 4.35L9.05 3.9ZM2.25 2.25H6.5V0.75H2.25V2.25ZM1.5 15V3H0V15H1.5ZM17.75 15.75H2.25V17.25H17.75V15.75ZM18.5 6V15H20V6H18.5ZM17.75 3.75H10.25V5.25H17.75V3.75ZM9.65 3.45L8.3 1.65L7.1 2.55L8.45 4.35L9.65 3.45ZM10.25 3.75C10.0139 3.75 9.79164 3.63885 9.65 3.45L8.45 4.35C8.87492 4.91656 9.5418 5.25 10.25 5.25V3.75ZM20 6C20 4.75736 18.9926 3.75 17.75 3.75V5.25C18.1642 5.25 18.5 5.58579 18.5 6H20ZM17.75 17.25C18.9926 17.25 20 16.2426 20 15H18.5C18.5 15.4142 18.1642 15.75 17.75 15.75V17.25ZM0 15C0 16.2426 1.00736 17.25 2.25 17.25V15.75C1.83579 15.75 1.5 15.4142 1.5 15H0ZM6.5 2.25C6.73607 2.25 6.95836 2.36115 7.1 2.55L8.3 1.65C7.87508 1.08344 7.2082 0.75 6.5 0.75V2.25ZM2.25 0.75C1.00736 0.75 0 1.75736 0 3H1.5C1.5 2.58579 1.83579 2.25 2.25 2.25V0.75Z"
                                                fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Изображения
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            {{ $fileStats['images']['count'] }} файлов
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $fileStats['images']['count'] }}
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        @php
                                            $size = $fileStats['images']['size'];
                                            $units = ['B', 'KB', 'MB', 'GB'];
                                            $pow = floor(($size ? log($size) : 0) / log(1024));
                                            $pow = min($pow, count($units) - 1);
                                            $size = round($size / pow(1024, $pow), 2);
                                        @endphp
                                        {{ $size }} {{ $units[$pow] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Video Statistics -->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-green-600/[0.08] text-green-600">
                                        <svg class="stroke-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.70825 5.93126L6.70825 18.0687C6.70825 19.2416 7.9937 19.9607 8.99315 19.347L18.8765 13.2783C19.83 12.6928 19.83 11.3072 18.8765 10.7217L8.99315 4.65301C7.9937 4.03931 6.70825 4.75844 6.70825 5.93126Z"
                                                stroke="" stroke-width="1.5" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Видео
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            {{ $fileStats['videos']['count'] }} файлов
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $fileStats['videos']['count'] }}
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        @php
                                            $size = $fileStats['videos']['size'];
                                            $pow = floor(($size ? log($size) : 0) / log(1024));
                                            $pow = min($pow, count($units) - 1);
                                            $size = round($size / pow(1024, $pow), 2);
                                        @endphp
                                        {{ $size }} {{ $units[$pow] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Documents Statistics -->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-warning-500/[0.08] text-warning-500">
                                        <svg class="fill-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M19.8335 19.75C19.8335 20.9926 18.8261 22 17.5835 22H7.0835C5.84086 22 4.8335 20.9926 4.8335 19.75V9.62105C4.8335 9.02455 5.07036 8.45247 5.49201 8.03055L10.8597 2.65951C11.2817 2.23725 11.8542 2 12.4512 2H17.5835C18.8261 2 19.8335 3.00736 19.8335 4.25V19.75ZM17.5835 20.5C17.9977 20.5 18.3335 20.1642 18.3335 19.75V4.25C18.3335 3.83579 17.9977 3.5 17.5835 3.5H12.5815L12.5844 7.49913C12.5853 8.7424 11.5776 9.75073 10.3344 9.75073H6.3335V19.75C6.3335 20.1642 6.66928 20.5 7.0835 20.5H17.5835ZM7.39262 8.25073L11.0823 4.55876L11.0844 7.5002C11.0847 7.91462 10.7488 8.25073 10.3344 8.25073H7.39262ZM8.5835 14.5C8.5835 14.0858 8.91928 13.75 9.3335 13.75H15.3335C15.7477 13.75 16.0835 14.0858 16.0835 14.5C16.0835 14.9142 15.7477 15.25 15.3335 15.25H9.3335C8.91928 15.25 8.5835 14.9142 8.5835 14.5ZM8.5835 17.5C8.5835 17.0858 8.91928 16.75 9.3335 16.75H12.3335C12.7477 16.75 13.0835 17.0858 13.0835 17.5C13.0835 17.9142 12.7477 18.25 12.3335 18.25H9.3335C8.91928 18.25 8.5835 17.9142 8.5835 17.5Z"
                                                  fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Документы
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            {{ $fileStats['documents']['count'] }} файлов
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $fileStats['documents']['count'] }}
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        @php
                                            $size = $fileStats['documents']['size'];
                                            $pow = floor(($size ? log($size) : 0) / log(1024));
                                            $pow = min($pow, count($units) - 1);
                                            $size = round($size / pow(1024, $pow), 2);
                                        @endphp
                                        {{ $size }} {{ $units[$pow] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Media Card -->
            </div>

            <!-- Последние файлы -->
            <div class="col-span-12">
                <!-- ====== Table Seven Start -->
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-4 flex items-center justify-between px-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Последние файлы
                            </h3>
                        </div>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <div class="min-w-[1026px]">
                            <!-- table header start -->
                            <div class="grid grid-cols-11 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Имя файла
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Тип
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Размер
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Дата загрузки
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p
                                        class="w-full text-center text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Действия
                                    </p>
                                </div>
                            </div>
                            <!-- table header end -->

                            <!-- table body start -->
                            @foreach($files as $file)
                                <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                    <div class="col-span-3 flex items-center">
                                        <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                            <div>
                                                @if(str_contains($file->mime_type, 'image'))
                                                    <img src="{{ Storage::url($file->path) }}" alt="icon" class="dark:hidden rounded w-[40px]">
                                                    <img src="{{ asset('images/icons/file-image-dark.svg') }}" alt="icon" class="hidden dark:block">
                                                @elseif(str_contains($file->mime_type, 'video'))
                                                    <img src="{{ asset('images/icons/file-video.svg') }}" alt="icon" class="dark:hidden">
                                                    <img src="{{ asset('images/icons/file-video-dark.svg') }}" alt="icon" class="hidden dark:block">
                                                @elseif(str_contains($file->mime_type, 'pdf') || $file->extension == 'pdf')
                                                    <div class="w-8 h-8 flex items-center justify-center bg-red-100 dark:bg-gray-800 rounded">
                                                        <span class="text-xs font-medium text-red-500">{{ strtoupper(substr($file->extension, 0, 3)) }}</span>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded">
                                                        <span class="text-xs font-medium">{{ strtoupper(substr($file->extension, 0, 3)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            {{ $file->name }}
                                        </div>
                                    </div>
                                    <div class="col-span-2 flex items-center">
                                        <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                            @if(str_contains($file->mime_type, 'image')) Изображение
                                            @elseif(str_contains($file->mime_type, 'video')) Видео
                                            @elseif(str_contains($file->mime_type, 'audio')) Аудио
                                            @elseif(str_contains($file->mime_type, 'pdf')) PDF
                                            @elseif(in_array($file->extension, ['doc', 'docx', 'txt'])) Документ
                                            @else Файл
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-span-2 flex items-center">
                                        <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                            @php
                                                $size = $file->size;
                                                $units = ['B', 'KB', 'MB', 'GB'];
                                                $pow = floor(($size ? log($size) : 0) / log(1024));
                                                $pow = min($pow, count($units) - 1);
                                                $size = round($size / pow(1024, $pow), 2);
                                            @endphp
                                            {{ $size }} {{ $units[$pow] }}
                                        </p>
                                    </div>
                                    <div class="col-span-2 flex items-center">
                                        <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                            {{ $file->created_at->format('d.m.Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="col-span-2 flex items-center">
                                        <div class="flex w-full items-center justify-center gap-2">
                                            <a href="{{ route('files.download', $file) }}"
                                               class="text-gray-500 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400"
                                               title="Скачать">
                                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13 8V4H7V8H4L10 14L16 8H13ZM4 16H16V18H4V16Z" fill="currentColor"/>
                                                </svg>
                                            </a>

                                            @if(Auth::user()->role->name === 'Руководитель' ||
                                               (Auth::user()->role->name === 'Менеджер' && $file->department_id == Auth::user()->department_id) ||
                                               $file->uploaded_by == Auth::user()->id)
                                                <form action="{{ route('files.destroy', $file) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Вы уверены, что хотите удалить этот файл?')"
                                                            class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400"
                                                            title="Удалить">
                                                        <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                  d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                                  fill=""></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($files->isEmpty())
                                <div class="border-t border-gray-100 px-6 py-12 text-center dark:border-gray-800">
                                    <p class="text-gray-500 dark:text-gray-400">Файлы не найдены</p>
                                </div>
                            @endif
                            <!-- table body end -->
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($files->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4 dark:border-gray-800">
                            {{ $files->links() }}
                        </div>
                    @endif
                </div>
                <!-- ====== Table Seven End -->
            </div>
        </div>
    </div>

    <!-- Modal for File Upload -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Загрузка файла
                            </h3>
                            <div class="mt-4">
                                <form id="uploadForm" action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Выберите файл
                                        </label>
                                        <input type="file" name="file" id="fileInput"
                                               class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-blue-50 file:text-blue-700
                                                hover:file:bg-blue-100
                                                dark:file:bg-blue-900 dark:file:text-blue-300"
                                               required>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Максимальный размер:
                                            @if($company->license_type == 'basic') 100MB
                                            @elseif($company->license_type == 'optimal') 500MB
                                            @else 1GB
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Папка (необязательно)
                                        </label>
                                        <input type="text" name="folder"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                               placeholder="Например: documents">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" form="uploadForm"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Загрузить
                    </button>
                    <button type="button" onclick="closeUploadModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
            document.getElementById('uploadModal').classList.add('block');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.remove('block');
            document.getElementById('uploadModal').classList.add('hidden');
        }

        // Закрытие модального окна при клике вне его
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });

        // Обработка формы загрузки
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                e.preventDefault();
                alert('Пожалуйста, выберите файл');
                return;
            }

            // Показываем индикатор загрузки
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Загрузка...</span>';
            submitBtn.disabled = true;
        });
    </script>
@endsection
