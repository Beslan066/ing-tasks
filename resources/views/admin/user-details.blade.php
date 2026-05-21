@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Хлебные крошки -->
            <div class="row mb-6">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.users.tracking') }}">Пользователи</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Детали пользователя: {{ $user->name }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Информация о пользователе -->
            <div class="row g-6 mb-6">
                <!-- Профиль пользователя -->
                <div class="col-xl-4 col-lg-5 col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-6">
                                <div class="avatar avatar-xl me-4">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle">
                                    @else
                                        <div class="avatar-initial rounded-circle bg-label-primary" style="font-size: 2rem;">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $user->name }}</h4>
                                    <p class="text-muted mb-0">{{ $user->email }}</p>
                                    <div class="mt-2">
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Email подтвержден</span>
                                        @else
                                            <span class="badge bg-warning">Email не подтвержден</span>
                                        @endif

                                        @if($user->is_admin ?? false)
                                            <span class="badge bg-info">Администратор</span>
                                        @endif

                                        @if($user->isOnline())
                                            <span class="badge bg-success">Онлайн</span>
                                        @else
                                            <span class="badge bg-secondary">Офлайн</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="info-container">
                                <ul class="list-unstyled mb-6">
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">📅 Дата регистрации:</span>
                                        <span>{{ $user->created_at->format('d.m.Y H:i:s') }}</span>
                                        <span class="text-muted">({{ $user->created_at->diffForHumans() }})</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">🕐 Последняя активность:</span>
                                        @if($user->last_activity_at)
                                            <span>{{ \Carbon\Carbon::parse($user->last_activity_at)->format('d.m.Y H:i:s') }}</span>
                                            <span class="text-muted">({{ \Carbon\Carbon::parse($user->last_activity_at)->diffForHumans() }})</span>
                                        @else
                                            <span class="text-muted">Нет данных</span>
                                        @endif
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">💻 Всего сессий:</span>
                                        <span class="badge bg-primary">{{ $sessions->count() }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">🌍 Уникальных устройств:</span>
                                        <span class="badge bg-info">{{ $sessions->unique('device_fingerprint')->count() }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2">📍 Посещенных стран:</span>
                                        <span class="badge bg-success">{{ $sessions->whereNotNull('country')->unique('country')->count() }}</span>
                                    </li>
                                </ul>

                                <div class="d-flex justify-content-center pt-3">
                                    <a href="{{ route('admin.users.tracking') }}" class="btn btn-secondary me-3">
                                        <i class="ri-arrow-left-line me-1"></i> Назад
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmClearSessions({{ $user->id }})">
                                        <i class="ri-delete-bin-line me-1"></i> Очистить сессии
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Карта с последней локацией -->
                <div class="col-xl-8 col-lg-7 col-md-7">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Последняя геолокация пользователя</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $lastSession = $sessions->first();
                            @endphp

                            @if($lastSession && $lastSession->latitude && $lastSession->longitude)
                                <div id="user-map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="ri-map-pin-line me-2"></i>
                                        <strong>Адрес:</strong> {{ $lastSession->address ?: 'Не определен' }}<br>
                                        <strong>Координаты:</strong>
                                        <a href="https://www.google.com/maps?q={{ $lastSession->latitude }},{{ $lastSession->longitude }}" target="_blank">
                                            {{ number_format($lastSession->latitude, 6) }}, {{ number_format($lastSession->longitude, 6) }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ri-map-pin-line" style="font-size: 64px; color: #ccc;"></i>
                                    <p class="mt-3 text-muted">Нет данных о геолокации для этого пользователя</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Все сессии пользователя -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="ri-history-line me-2"></i>История сессий пользователя
                    </h5>
                    <p class="card-text text-muted mt-2">
                        Всего сессий: {{ $sessions->count() }} |
                        Активных: {{ $sessions->where('is_current', true)->count() }}
                    </p>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table table-hover" id="sessions-table">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>IP адрес</th>
                            <th>Устройство</th>
                            <th>Браузер / ОС</th>
                            <th>Геолокация</th>
                            <th>Последняя активность</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sessions as $session)
                            @php
                                // Определяем тип устройства
                                $deviceIcon = 'ri-computer-line';
                                $deviceType = 'Десктоп';
                                if ($session->device_type) {
                                    if ($session->device_type == 'mobile') {
                                        $deviceIcon = 'ri-smartphone-line';
                                        $deviceType = 'Мобильный';
                                    } elseif ($session->device_type == 'tablet') {
                                        $deviceIcon = 'ri-tablet-line';
                                        $deviceType = 'Планшет';
                                    }
                                }

                                // Определяем браузер
                                $browser = $session->browser ?: 'Unknown';
                                $os = $session->os ?: 'Unknown';

                                // Проверяем наличие геолокации
                                $hasLocation = $session->latitude && $session->longitude;
                                $location = $hasLocation ? ($session->city ?? 'По IP') : 'Не определено';

                                // Последняя активность
                                $lastActivity = $session->last_activity ?? $session->last_activity_at ?? null;
                            @endphp

                            <tr @if($session->is_current) class="table-success" @endif>
                                <!-- ID -->
                                <td>
                                    <span class="fw-medium">#{{ $session->id }}</span>
                                </td>

                                <!-- IP адрес -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <code class="small">{{ $session->ip_address ?? 'Unknown' }}</code>
                                        @if($session->id)
                                            <div class="small text-muted mt-1">
                                                <i class="ri-shield-check-line"></i>
                                                Сессия: #{{ $session->id }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Устройство -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $deviceIcon }} me-1 text-primary"></i>
                                            <span>{{ $deviceType }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Браузер / ОС -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="small">
                                            <i class="ri-browser-line me-1"></i> {{ $browser }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="ri-windows-line me-1"></i> {{ $os }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Геолокация -->
                                <td>
                                    @if($hasLocation)
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center">
                                                <i class="ri-map-pin-line text-danger me-1"></i>
                                                <span>{{ $location }}</span>
                                            </div>
                                            @if($session->country)
                                                <div class="small text-muted">
                                                    {{ $session->country }}
                                                </div>
                                            @endif
                                            <div class="small mt-1">
                                                <a href="https://www.google.com/maps?q={{ $session->latitude }},{{ $session->longitude }}" target="_blank">
                                                    {{ number_format($session->latitude, 4) }}, {{ number_format($session->longitude, 4) }}
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="ri-map-pin-line"></i> Не определено
                                        </span>
                                    @endif
                                </td>

                                <!-- Последняя активность -->
                                <td>
                                    @if($lastActivity)
                                        <div class="d-flex flex-column">
                                            <div class="small">
                                                {{ $lastActivity instanceof \Carbon\Carbon ? $lastActivity->diffForHumans() : 'недавно' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $lastActivity instanceof \Carbon\Carbon ? $lastActivity->format('d.m.Y H:i:s') : $lastActivity }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Статус -->
                                <td>
                                    @if($session->is_current)
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line me-1"></i>Активна
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="ri-time-line me-1"></i>Завершена
                                        </span>
                                    @endif
                                </td>

                                <!-- Действия -->
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-line"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($hasLocation)
                                                <li>
                                                    <a class="dropdown-item" href="https://www.google.com/maps?q={{ $session->latitude }},{{ $session->longitude }}" target="_blank">
                                                        <i class="ri-map-pin-line me-1"></i> Открыть на карте
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="copyToClipboard('{{ $session->ip_address }}')">
                                                    <i class="ri-clipboard-line me-1"></i> Скопировать IP
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="confirmDeleteSession({{ $session->id }})">
                                                    <i class="ri-delete-bin-line me-1"></i> Удалить сессию
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Подключаем Leaflet для карты -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Инициализация карты для пользователя
        @if(isset($lastSession) && $lastSession && $lastSession->latitude && $lastSession->longitude)
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('user-map').setView([{{ $lastSession->latitude }}, {{ $lastSession->longitude }}], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var redIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            L.marker([{{ $lastSession->latitude }}, {{ $lastSession->longitude }}], {icon: redIcon})
                .addTo(map)
                .bindPopup(`
                    <strong>{{ $user->name }}</strong><br>
                    Последняя активность: {{ $lastSession->last_activity->format('d.m.Y H:i:s') }}
                `)
                .openPopup();
        });
        @endif

        // Инициализация DataTable
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#sessions-table').DataTable({
                order: [[5, 'desc']],
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json'
                },
                columnDefs: [
                    { orderable: false, targets: 7 }
                ]
            });
        }

        // Функция копирования в буфер обмена
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.success('IP адрес скопирован в буфер обмена');
                } else {
                    alert('IP адрес скопирован: ' + text);
                }
            }, function() {
                alert('Не удалось скопировать');
            });
        }

        // Подтверждение удаления сессии
        function confirmDeleteSession(sessionId) {
            if (confirm('Вы уверены, что хотите удалить эту сессию?')) {
                fetch(`/admin/sessions/${sessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Ошибка при удалении сессии');
                    }
                });
            }
        }

        // Подтверждение очистки всех сессий
        function confirmClearSessions(userId) {
            if (confirm('ВНИМАНИЕ! Вы уверены, что хотите удалить ВСЕ сессии этого пользователя?')) {
                fetch(`/admin/users/${userId}/clear-sessions`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Ошибка при очистке сессий');
                    }
                });
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .table-success {
            background-color: #e6f7e6 !important;
        }
        .avatar-xl {
            width: 100px;
            height: 100px;
        }
        .info-container .list-unstyled li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-container .list-unstyled li:last-child {
            border-bottom: none;
        }
        #user-map {
            z-index: 1;
        }
        .leaflet-container {
            z-index: 1;
        }
    </style>
@endpush
