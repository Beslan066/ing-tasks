@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp
        <!-- !!!class="w-[calc(100% - 250px)]" -->
    <div>
        <!-- Заголовок -->
        <div
            class="flex justify-between items-center mb-6 max-[800px]:flex-col max-[800px]:items-baseline max-[800px]:space-y-4">
            <div>
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Лента событий</h2>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Лента событий</h2>
                @endif
            </div>

        </div>
        <!-- Лента событий -->
        @if($backgroundImage && $backgroundEnabled)
            <div class="space-y-3">
                @forelse($activities as $activity)
                    <div class="bg-transparent/10 rounded-xl shadow-sm border-none p-4 hover:shadow-md transition-all duration-200 mb-2">
                        <div class="flex items-start space-x-4">
                            <!-- Иконка -->
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xl {{ $activity->getColorClass() }}">
                                    {{ $activity->getIcon() }}
                                </div>
                            </div>

                            <!-- Контент -->
                            <div class="flex-1 min-w-0">
                                <div class="text-white text-sm md:text-base">
                                    {!! $activity->description !!}
                                </div>

                                <!-- Дополнительная информация для разных типов событий -->
                                @if($activity->action === 'task_assigned' && isset($activity->properties['assigned_to_name']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Исполнитель: {{ $activity->properties['assigned_to_name'] }}
                                    </div>
                                @endif

                                @if($activity->action === 'task_rejected' && isset($activity->properties['reason']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Причина: {{ $activity->properties['reason'] }}
                                    </div>
                                @endif

                                @if($activity->action === 'file_uploaded' && isset($activity->properties['file_name']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Файл: {{ $activity->properties['file_name'] }}
                                    </div>
                                @endif

                                <!-- Мета информация -->
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $activity->getFormattedDate() }}
                            </span>

                                    @if($activity->user)
                                        <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $activity->user->name }}
                                </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-lg">Пока нет событий</p>
                        <p class="text-sm mt-1">Новые события будут появляться здесь по мере активности в системе</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="space-y-3">
                @forelse($activities as $activity)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start space-x-4">
                            <!-- Иконка -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl {{ $activity->getColorClass() }}">
                                    {{ $activity->getIcon() }}
                                </div>
                            </div>

                            <!-- Контент -->
                            <div class="flex-1 min-w-0">
                                <div class="text-gray-800 text-sm md:text-base">
                                    {!! $activity->description !!}
                                </div>

                                <!-- Дополнительная информация для разных типов событий -->
                                @if($activity->action === 'task_assigned' && isset($activity->properties['assigned_to_name']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Исполнитель: {{ $activity->properties['assigned_to_name'] }}
                                    </div>
                                @endif

                                @if($activity->action === 'task_rejected' && isset($activity->properties['reason']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Причина: {{ $activity->properties['reason'] }}
                                    </div>
                                @endif

                                @if($activity->action === 'file_uploaded' && isset($activity->properties['file_name']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Файл: {{ $activity->properties['file_name'] }}
                                    </div>
                                @endif

                                <!-- Мета информация -->
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $activity->getFormattedDate() }}
                            </span>

                                    @if($activity->user)
                                        <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $activity->user->name }}
                                </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-lg">Пока нет событий</p>
                        <p class="text-sm mt-1">Новые события будут появляться здесь по мере активности в системе</p>
                    </div>
                @endforelse
            </div>
        @endif

        <!-- Пагинация -->
        @if($activities->hasPages())
            <div class="mt-8">
                {{ $activities->withQueryString()->links() }}
            </div>
        @endif
    </div>


@endsection




@push('scripts')
    <script>
        function updateFilters() {
            const action = document.getElementById('action-filter').value;
            const userId = document.getElementById('user-filter').value;

            let url = '{{ route("activity.index") }}';
            const params = new URLSearchParams();

            if (action && action !== 'all') params.append('action', action);
            if (userId) params.append('user_id', userId);

            if (params.toString()) {
                url += '?' + params.toString();
            }

            window.location.href = url;
        }

        function resetFilters() {
            window.location.href = '{{ route("activity.index") }}';
        }

        document.getElementById('action-filter').addEventListener('change', updateFilters);
        document.getElementById('user-filter').addEventListener('change', updateFilters);
    </script>
@endpush
