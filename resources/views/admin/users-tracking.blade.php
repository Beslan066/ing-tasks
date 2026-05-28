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
                            <li class="breadcrumb-item active" aria-current="page">
                                Пользователи
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Статистика -->
            <div class="row g-6 mb-6">
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="me-1">
                                    <p class="text-heading mb-1">Всего пользователей</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-2">{{ $totalUsers ?? 0 }}</h4>
                                        <p class="text-success mb-1">(+{{ $newUsersCount ?? 0 }})</p>
                                    </div>
                                    <small class="mb-0">Зарегистрировано</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded-3">
                                        <i class="ri-group-line ri-26px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="me-1">
                                    <p class="text-heading mb-1">Онлайн сейчас</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $onlineUsersCount ?? 0 }}</h4>
                                        <p class="text-success mb-1">({{ $activePercentage ?? 0 }}%)</p>
                                    </div>
                                    <small class="mb-0">Активных в последние 5 мин</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded-3">
                                        <i class="ri-user-follow-line ri-26px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="me-1">
                                    <p class="text-heading mb-1">Активных сессий</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $activeSessions ?? 0 }}</h4>
                                    </div>
                                    <small class="mb-0">Всего открытых сессий</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded-3">
                                        <i class="ri-device-line ri-26px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="me-1">
                                    <p class="text-heading mb-1">Устройств сегодня</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $uniqueDevices ?? 0 }}</h4>
                                    </div>
                                    <small class="mb-0">Уникальных устройств</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded-3">
                                        <i class="ri-smartphone-line ri-26px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица пользователей -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Фильтры</h5>
                    <div class="d-flex justify-content-between align-items-center row gx-5 pt-4 gap-5 gap-md-0">
                        <div class="col-md-3">
                            <select class="form-select" id="filterDevice">
                                <option value="">Все устройства</option>
                                <option value="desktop">Десктоп</option>
                                <option value="mobile">Мобильный</option>
                                <option value="tablet">Планшет</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Все статусы</option>
                                <option value="online">Онлайн</option>
                                <option value="offline">Офлайн</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.map') }}" class="btn btn-primary w-100">
                                <i class="ri-map-pin-line me-1"></i> Показать карту
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table table-hover" id="users-table">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Пользователь</th>
                            <th>Email</th>
                            <th>Статус</th>
                            <th>Устройство</th>
                            <th>IP адрес</th>
                            <th>Геолокация</th>
                            <th>Последняя активность</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            @php
                                // Получаем последнюю сессию
                                $sessions = $user->onlineSessions ?? collect();
                                $lastSession = $sessions->isNotEmpty() ? $sessions->first() : null;

                                // Проверяем онлайн статус
                                $isOnline = $lastSession && !$lastSession->logout_at &&
                                           $lastSession->last_activity_at &&
                                           $lastSession->last_activity_at->diffInMinutes(now()) < 5;

                                // Определяем тип устройства
                                $deviceIcon = 'ri-computer-line';
                                $deviceType = 'Десктоп';
                                if ($lastSession && $lastSession->user_agent) {
                                    $ua = $lastSession->user_agent;
                                    if (strpos($ua, 'Mobile') !== false) {
                                        $deviceIcon = 'ri-smartphone-line';
                                        $deviceType = 'Мобильный';
                                    } elseif (strpos($ua, 'iPad') !== false || strpos($ua, 'Tablet') !== false) {
                                        $deviceIcon = 'ri-tablet-line';
                                        $deviceType = 'Планшет';
                                    }
                                }

                                // Определяем браузер
                                $browser = 'Unknown';
                                if ($lastSession && $lastSession->user_agent) {
                                    $ua = $lastSession->user_agent;
                                    if (strpos($ua, 'Chrome') !== false) $browser = 'Chrome';
                                    elseif (strpos($ua, 'Firefox') !== false) $browser = 'Firefox';
                                    elseif (strpos($ua, 'Safari') !== false) $browser = 'Safari';
                                    elseif (strpos($ua, 'Edge') !== false) $browser = 'Edge';
                                }

                                // Геолокация
                                $hasLocation = $lastSession && $lastSession->latitude && $lastSession->longitude;

                                // Последняя активность
                                $lastActivity = $lastSession ? $lastSession->last_activity_at : null;
                            @endphp

                            <tr @if($isOnline) class="table-success" @endif>
                                <!-- ID -->
                                <td>
                                    <span class="fw-medium">#{{ $user->id }}</span>
                                </td>

                                <!-- Пользователь -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="avatar-initial rounded-circle bg-label-primary" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    {{ $user->getInitials() }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            <div class="small text-muted">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td>
                                    <div>
                                        <span class="text-muted">{{ $user->email }}</span>
                                        @if($user->email_verified_at)
                                            <div class="small text-success">
                                                <i class="ri-checkbox-circle-line"></i> Подтвержден
                                            </div>
                                        @else
                                            <div class="small text-warning">
                                                <i class="ri-error-warning-line"></i> Не подтвержден
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Статус -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($isOnline)
                                            <span class="badge bg-success me-2">
                                                <i class="ri-circle-fill me-1" style="font-size: 8px;"></i> Онлайн
                                            </span>
                                            @if($lastActivity)
                                                <div class="small text-muted">
                                                    {{ $lastActivity->diffForHumans() }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary me-2">Офлайн</span>
                                            @if($lastActivity)
                                                <div class="small text-muted">
                                                    Был(а) {{ $lastActivity->diffForHumans() }}
                                                </div>
                                            @else
                                                <div class="small text-muted">Нет данных</div>
                                            @endif
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
                                        @if($browser != 'Unknown')
                                            <div class="small text-muted mt-1">
                                                <i class="ri-browser-line me-1"></i> {{ $browser }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- IP адрес -->
                                <td>
                                    @if($lastSession && $lastSession->ip_address)
                                        <code class="small">{{ $lastSession->ip_address }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Геолокация -->
                                <td>
                                    @if($hasLocation)
                                        <div class="d-flex align-items-center">
                                            <i class="ri-map-pin-line text-danger me-1"></i>
                                            <span>{{ $lastSession->city ?? 'По IP' }}</span>
                                        </div>
                                        <div class="small text-muted">
                                            {{ $lastSession->country ?? '' }}
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
                                        <div class="small">
                                            {{ $lastActivity->diffForHumans() }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $lastActivity->format('d.m.Y H:i:s') }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Действия -->
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-line"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                    <i class="ri-eye-line me-1"></i> Детали
                                                </a>
                                            </li>
                                            @if($lastSession && $lastSession->ip_address && !in_array($lastSession->ip_address, ['127.0.0.1', '::1', 'localhost']))
                                                <li>
                                                    <a class="dropdown-item" href="https://whatismyipaddress.com/ip/{{ $lastSession->ip_address }}" target="_blank">
                                                        <i class="ri-shield-cross-line me-1"></i> Информация об IP
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="confirmForceLogout({{ $user->id }})">
                                                    <i class="ri-logout-circle-r-line me-1"></i> Принудительный выход
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
    <script>
        // Фильтрация таблицы
        document.addEventListener('DOMContentLoaded', function() {
            const filterDevice = document.getElementById('filterDevice');
            const filterStatus = document.getElementById('filterStatus');
            const tableRows = document.querySelectorAll('#users-table tbody tr');

            function filterTable() {
                const deviceValue = filterDevice.value;
                const statusValue = filterStatus.value;

                tableRows.forEach(row => {
                    let showRow = true;

                    // Фильтр по устройству
                    if (deviceValue) {
                        const deviceCell = row.cells[4];
                        if (deviceCell && !deviceCell.innerText.toLowerCase().includes(deviceValue)) {
                            showRow = false;
                        }
                    }

                    // Фильтр по статусу
                    if (statusValue && showRow) {
                        const statusCell = row.cells[3];
                        if (statusCell && !statusCell.innerText.toLowerCase().includes(statusValue)) {
                            showRow = false;
                        }
                    }

                    row.style.display = showRow ? '' : 'none';
                });
            }

            if (filterDevice) filterDevice.addEventListener('change', filterTable);
            if (filterStatus) filterStatus.addEventListener('change', filterTable);

            // Инициализация DataTable
            if (typeof $.fn.DataTable !== 'undefined') {
                $('#users-table').DataTable({
                    order: [[7, 'desc']],
                    pageLength: 10,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json'
                    }
                });
            }
        });

        // Принудительный выход
        function confirmForceLogout(userId) {
            if (confirm('Вы уверены, что хотите принудительно завершить все сессии пользователя?')) {
                fetch(`/admin/users/${userId}/force-logout`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Пользователь отключен');
                        } else {
                            alert('Пользователь отключен');
                        }
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Ошибка при отключении');
                        } else {
                            alert('Ошибка при отключении');
                        }
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
        .avatar-sm {
            width: 32px !important;
            height: 32px !important;
        }
        code {
            font-size: 0.75rem;
            background: #f5f5f5;
            padding: 2px 4px;
            border-radius: 4px;
        }
    </style>
@endpush
