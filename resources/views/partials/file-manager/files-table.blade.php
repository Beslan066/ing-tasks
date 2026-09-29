@php
    // ---------- Режим отображения ----------
    $glass = $backgroundEnabled && $backgroundImage;

    // ---------- Хелперы ----------
    $formatSize = function ($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max((int) $bytes, 0);
        $pow   = $bytes > 0 ? min((int) floor(log($bytes, 1024)), count($units) - 1) : 0;

        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    };

    $fileType = function ($file) {
        $mime = (string) $file->mime_type;
        $ext  = strtolower((string) $file->extension);

        return match (true) {
            str_contains($mime, 'image') => 'image',
            str_contains($mime, 'video') => 'video',
            str_contains($mime, 'audio') => 'audio',
            str_contains($mime, 'pdf') || $ext === 'pdf' => 'pdf',
            in_array($ext, ['doc', 'docx', 'txt'], true) => 'document',
            default => 'file',
        };
    };

    $typeLabels = [
        'image'    => 'Изображение',
        'video'    => 'Видео',
        'audio'    => 'Аудио',
        'pdf'      => 'PDF',
        'document' => 'Документ',
        'file'     => 'Файл',
    ];

    $authUser  = Auth::user();
    $canDelete = fn ($file) => $authUser->role->name === 'Руководитель'
        || ($authUser->role->name === 'Менеджер' && $file->department_id == $authUser->department_id)
        || $file->uploaded_by == $authUser->id;

    // ---------- Классы: значения ТОЧНО как в исходном шаблоне ----------
    // Внешние карточки
    $cardCls = $glass
        ? 'border-none backdrop-blur-md bg-transparent/20 text-white'
        : 'border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]';

    // Плитки статистики
    $tileCls = $glass
        ? 'border-none backdrop-blur-md bg-transparent/20 text-white'
        : 'border border-gray-100 bg-white dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5';

    // Заголовок «Статистика по типам файлов»
    $titleCls = $glass ? 'text-white' : 'text-gray-800 dark:text-white/90';

    // Заголовки плиток и «Последние файлы»
    $h4Cls = $glass ? 'text-white dark:text-white/90' : 'text-gray-800 dark:text-white/90';

    // Серый текст (в исходнике одинаковый в обоих режимах)
    $grayCls = 'text-gray-500 dark:text-gray-400';

    // Шапка карточки статистики / блок плиток
    $headerBorderCls = $glass ? 'border-none' : '';
    $statsWrapCls    = $glass
        ? 'border-none p-4 sm:p-6'
        : 'border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6';

    // Поиск
    $searchIconCls = $glass ? 'text-white' : 'text-gray-500 dark:text-gray-400';
    $inputCls      = $glass
        ? 'w-full rounded-lg bg-transparent py-2.5 pl-[42px] pr-3.5 text-sm placeholder:text-white outline-none text-white bg-transparent/20  border-none border-[1px]'
        : 'h-11 w-full rounded-lg border-none bg-transparent py-2.5 pl-[42px] pr-3.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400  focus:outline-hidden focus:ring-3 focus:ring-green-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-green-800 xl:w-[300px] outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100';

    // Таблица
    $headRowCls = $glass
        ? 'border-t border-gray-200 border-none'
        : 'border-t border-gray-200 dark:border-gray-800';
    $headFirstCls = $glass ? 'text-gray-500' : 'text-gray-500 dark:text-gray-400';
    $rowCls     = $glass
        ? 'bg-transparent/20 border-none text-white'
        : 'border-t border-gray-100 dark:border-gray-800';
    $nameCls    = $glass ? '' : 'text-gray-700 dark:text-gray-400';
    $cellCls    = $glass ? '' : 'text-gray-700 dark:text-gray-400';
    $genericIconCls = $glass ? 'bg-gray-500 dark:bg-gray-800' : 'bg-gray-100 dark:bg-gray-800';
    $dlCls      = $glass ? '' : 'text-gray-500';
    $delCls     = $glass
        ? 'hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400'
        : 'text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400';
    $pagCls     = $glass ? '' : 'border-t border-gray-100';

    // ---------- Плитки статистики ----------
    $statTiles = [
        ['key' => 'images',    'label' => 'Изображения', 'box' => 'bg-green-600/[0.08] text-green-500'],
        ['key' => 'videos',    'label' => 'Видео',       'box' => 'bg-green-600/[0.08] text-green-600'],
        ['key' => 'documents', 'label' => 'Документы',   'box' => $glass ? 'bg-green-600/[0.08] text-green-600' : 'bg-warning-500/[0.08] text-warning-500'],
    ];
@endphp

<div class="grid grid-cols-12 gap-6">

    {{-- ===================== Статистика ===================== --}}
    <div class="col-span-12">
        <div class="rounded-2xl {{ $cardCls }}">
            <div class="px-4 py-4 sm:pl-6 sm:pr-4 {{ $headerBorderCls }}">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold {{ $titleCls }}">Статистика по типам файлов</h3>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        {{-- Поиск --}}
                        <div class="relative">
                            <button type="button"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 {{ $searchIconCls }}">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"
                                          fill=""></path>
                                </svg>
                            </button>

                            <input type="text" placeholder="Поиск файлов..." class="{{ $inputCls }}">
                        </div>

                        {{-- Загрузка --}}
                        <button type="button" onclick="openUploadModal()"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 px-4 py-3 text-sm font-medium text-white sm:w-auto transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M9.2502 4.99951C9.2502 4.5853 9.58599 4.24951 10.0002 4.24951C10.4144 4.24951 10.7502 4.5853 10.7502 4.99951V9.24971H15.0006C15.4148 9.24971 15.7506 9.5855 15.7506 9.99971C15.7506 10.4139 15.4148 10.7497 15.0006 10.7497H10.7502V15.0001C10.7502 15.4143 10.4144 15.7501 10.0002 15.7501C9.58599 15.7501 9.2502 15.4143 9.2502 15.0001V10.7497H5C4.58579 10.7497 4.25 10.4139 4.25 9.99971C4.25 9.5855 4.58579 9.24971 5 9.24971H9.2502V4.99951Z"
                                      fill=""></path>
                            </svg>
                            <span>Загрузить файл</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="{{ $statsWrapCls }}">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 xl:grid-cols-3">
                    @foreach($statTiles as $tile)
                        @php $stat = $fileStats[$tile['key']]; @endphp

                        <div class="flex items-center justify-between rounded-2xl py-4 pl-4 pr-4 {{ $tileCls }}">
                            <div class="flex items-center gap-4">
                                <div class="flex h-[52px] w-[52px] items-center justify-center rounded-xl {{ $tile['box'] }}">
                                    @if($tile['key'] === 'images')
                                        <svg class="fill-current" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.05 3.9L8.45 4.35L9.05 3.9ZM2.25 2.25H6.5V0.75H2.25V2.25ZM1.5 15V3H0V15H1.5ZM17.75 15.75H2.25V17.25H17.75V15.75ZM18.5 6V15H20V6H18.5ZM17.75 3.75H10.25V5.25H17.75V3.75ZM9.65 3.45L8.3 1.65L7.1 2.55L8.45 4.35L9.65 3.45ZM10.25 3.75C10.0139 3.75 9.79164 3.63885 9.65 3.45L8.45 4.35C8.87492 4.91656 9.5418 5.25 10.25 5.25V3.75ZM20 6C20 4.75736 18.9926 3.75 17.75 3.75V5.25C18.1642 5.25 18.5 5.58579 18.5 6H20ZM17.75 17.25C18.9926 17.25 20 16.2426 20 15H18.5C18.5 15.4142 18.1642 15.75 17.75 15.75V17.25ZM0 15C0 16.2426 1.00736 17.25 2.25 17.25V15.75C1.83579 15.75 1.5 15.4142 1.5 15H0ZM6.5 2.25C6.73607 2.25 6.95836 2.36115 7.1 2.55L8.3 1.65C7.87508 1.08344 7.2082 0.75 6.5 0.75V2.25ZM2.25 0.75C1.00736 0.75 0 1.75736 0 3H1.5C1.5 2.58579 1.83579 2.25 2.25 2.25V0.75Z"
                                                  fill=""></path>
                                        </svg>
                                    @elseif($tile['key'] === 'videos')
                                        <svg class="stroke-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.70825 5.93126L6.70825 18.0687C6.70825 19.2416 7.9937 19.9607 8.99315 19.347L18.8765 13.2783C19.83 12.6928 19.83 11.3072 18.8765 10.7217L8.99315 4.65301C7.9937 4.03931 6.70825 4.75844 6.70825 5.93126Z"
                                                  stroke="" stroke-width="1.5" stroke-linejoin="round"></path>
                                        </svg>
                                    @else
                                        <svg class="fill-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M19.8335 19.75C19.8335 20.9926 18.8261 22 17.5835 22H7.0835C5.84086 22 4.8335 20.9926 4.8335 19.75V9.62105C4.8335 9.02455 5.07036 8.45247 5.49201 8.03055L10.8597 2.65951C11.2817 2.23725 11.8542 2 12.4512 2H17.5835C18.8261 2 19.8335 3.00736 19.8335 4.25V19.75ZM17.5835 20.5C17.9977 20.5 18.3335 20.1642 18.3335 19.75V4.25C18.3335 3.83579 17.9977 3.5 17.5835 3.5H12.5815L12.5844 7.49913C12.5853 8.7424 11.5776 9.75073 10.3344 9.75073H6.3335V19.75C6.3335 20.1642 6.66928 20.5 7.0835 20.5H17.5835ZM7.39262 8.25073L11.0823 4.55876L11.0844 7.5002C11.0847 7.91462 10.7488 8.25073 10.3344 8.25073H7.39262ZM8.5835 14.5C8.5835 14.0858 8.91928 13.75 9.3335 13.75H15.3335C15.7477 13.75 16.0835 14.0858 16.0835 14.5C16.0835 14.9142 15.7477 15.25 15.3335 15.25H9.3335C8.91928 15.25 8.5835 14.9142 8.5835 14.5ZM8.5835 17.5C8.5835 17.0858 8.91928 16.75 9.3335 16.75H12.3335C12.7477 16.75 13.0835 17.0858 13.0835 17.5C13.0835 17.9142 12.7477 18.25 12.3335 18.25H9.3335C8.91928 18.25 8.5835 17.9142 8.5835 17.5Z"
                                                  fill=""></path>
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="mb-1 text-sm font-medium {{ $h4Cls }}">{{ $tile['label'] }}</h4>
                                    <span class="block text-sm {{ $grayCls }}">{{ $stat['count'] }} файлов</span>
                                </div>
                            </div>

                            <div>
                                <span class="mb-1 block text-right text-sm {{ $grayCls }}">{{ $stat['count'] }}</span>
                                <span class="block text-right text-sm {{ $grayCls }}">{{ $formatSize($stat['size']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Последние файлы ===================== --}}
    <div class="col-span-12">
        <div class="overflow-hidden rounded-2xl pt-4 {{ $cardCls }}">
            <div class="mb-4 flex items-center justify-between px-6">
                <div>
                    <h3 class="text-lg font-semibold {{ $h4Cls }}">Последние файлы</h3>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto">
                <div class="min-w-[1026px] max-[500px]:min-w-full">

                    {{-- Шапка таблицы --}}
                    <div class="grid grid-cols-11 px-6 py-3 max-[500px]:grid-cols-2 {{ $headRowCls }}">
                        <div class="col-span-3 flex items-center max-[500px]:col-span-1">
                            <p class="text-theme-sm font-medium {{ $headFirstCls }} max-[500px]:text-left">Имя файла</p>
                        </div>
                        <div class="col-span-2 flex items-center max-[500px]:hidden">
                            <p class="text-theme-sm font-medium {{ $grayCls }}">Тип</p>
                        </div>
                        <div class="col-span-2 flex items-center max-[500px]:hidden">
                            <p class="text-theme-sm font-medium {{ $grayCls }}">Размер</p>
                        </div>
                        <div class="col-span-2 flex items-center max-[500px]:hidden">
                            <p class="text-theme-sm font-medium {{ $grayCls }}">Дата загрузки</p>
                        </div>
                        <div class="col-span-2 flex items-center max-[500px]:col-span-1">
                            <p class="w-full text-center text-theme-sm font-medium {{ $grayCls }} max-[500px]:text-right">Действия</p>
                        </div>
                    </div>

                    {{-- Строки --}}
                    @foreach($files as $file)
                        @php $type = $fileType($file); @endphp

                        <div class="grid grid-cols-11 px-6 py-[18px] max-[500px]:grid-cols-2 cursor-pointer {{ $rowCls }}"
                             @if($type === 'image')
                                 data-preview="true"
                                 data-image-url="{{ Storage::url($file->path) }}"
                                 data-image-name="{{ $file->name }}"
                             @endif>

                            {{-- Имя --}}
                            <div class="col-span-3 flex items-center max-[500px]:col-span-1">
                                <div class="flex w-full items-center gap-2 text-sm {{ $nameCls }} max-[500px]:justify-start">
                                    <div>
                                        @if($type === 'image')
                                            <img src="{{ Storage::url($file->path) }}" alt="icon"
                                                 class="dark:hidden rounded w-[40px]">
                                            <img src="{{ asset('images/icons/file-image-dark.svg') }}" alt="icon"
                                                 class="hidden dark:block">
                                        @elseif($type === 'video')
                                            <img src="{{ asset('images/icons/file-video.svg') }}" alt="icon"
                                                 class="dark:hidden">
                                            <img src="{{ asset('images/icons/file-video-dark.svg') }}" alt="icon"
                                                 class="hidden dark:block">
                                        @elseif($type === 'pdf')
                                            <div class="w-8 h-8 flex items-center justify-center bg-red-100 dark:bg-gray-800 rounded">
                                                <span class="text-xs font-medium text-red-500">{{ strtoupper(substr($file->extension, 0, 3)) }}</span>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 flex items-center justify-center rounded {{ $genericIconCls }}">
                                                <span class="text-xs font-medium">{{ strtoupper(substr($file->extension, 0, 3)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    {{ $file->name }}
                                </div>
                            </div>

                            {{-- Тип --}}
                            <div class="col-span-2 flex items-center max-[500px]:hidden">
                                <p class="text-theme-sm {{ $cellCls }}">{{ $typeLabels[$type] }}</p>
                            </div>

                            {{-- Размер --}}
                            <div class="col-span-2 flex items-center max-[500px]:hidden">
                                <p class="text-theme-sm {{ $cellCls }}">{{ $formatSize($file->size) }}</p>
                            </div>

                            {{-- Дата --}}
                            <div class="col-span-2 flex items-center max-[500px]:hidden">
                                <p class="text-theme-sm {{ $cellCls }}">{{ $file->created_at->format('d.m.Y H:i') }}</p>
                            </div>

                            {{-- Действия --}}
                            <div class="col-span-2 flex items-center max-[500px]:col-span-1">
                                <div class="flex w-full items-center justify-center gap-2 max-[500px]:justify-end">
                                    <a href="{{ route('files.download', $file) }}"
                                       class="{{ $dlCls }} hover:text-green-500"
                                       title="Скачать">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13 8V4H7V8H4L10 14L16 8H13ZM4 16H16V18H4V16Z" fill="currentColor"/>
                                        </svg>
                                    </a>

                                    @if($canDelete($file))
                                        <form action="{{ route('files.destroy', $file) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Вы уверены, что хотите удалить этот файл?')"
                                                    class="{{ $delCls }}"
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
                            <p class="{{ $grayCls }}">Файлы не найдены</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Пагинация --}}
            @if($files->hasPages())
                <div class="px-6 py-4 dark:border-gray-800 {{ $pagCls }}">
                    {{ $files->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
