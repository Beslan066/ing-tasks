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
                                    <p class="text-heading mb-1">Стран сегодня</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $countriesCount ?? 0 }}</h4>
                                    </div>
                                    <small class="mb-0">География посещений</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded-3">
                                        <i class="ri-earth-line ri-26px"></i>
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
                            <select class="form-select" id="filterCountry">
                                <option value="">Все страны</option>
                                @foreach($uniqueCountries ?? [] as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
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
                        @forelse($users ?? [] as $user)
                            @php
                                // Получаем данные из UserSession (геолокация, устройство)
                                $lastSession = $user->sessions->first();

                                // Получаем онлайн статус из UserOnlineSession
                                $onlineSession = $user->onlineSessions->first();
                                $isOnline = $onlineSession && !$onlineSession->logout_at &&
                                           $onlineSession->last_activity_at &&
                                           $onlineSession->last_activity_at->diffInMinutes(now()) < 5;

                                // Геолокация из UserSession
                                $hasLocation = $lastSession && $lastSession->latitude && $lastSession->longitude;
                                $city = $lastSession ? $lastSession->city : null;
                                $country = $lastSession ? $lastSession->country : null;
                                $latitude = $lastSession ? $lastSession->latitude : null;
                                $longitude = $lastSession ? $lastSession->longitude : null;
                                $ipAddress = $lastSession ? $lastSession->ip_address : ($onlineSession ? $onlineSession->ip_address : null);

                                // Тип устройства
                                $deviceType = $lastSession ? ($lastSession->device_type ?? 'desktop') : 'desktop';
                                $browser = $lastSession ? ($lastSession->browser ?? 'Unknown') : 'Unknown';
                                $os = $lastSession ? ($lastSession->os ?? 'Unknown') : 'Unknown';

                                // Иконка устройства
                                $deviceIcon = 'ri-computer-line';
                                $deviceTypeText = 'Десктоп';
                                if ($deviceType === 'mobile') {
                                    $deviceIcon = 'ri-smartphone-line';
                                    $deviceTypeText = 'Мобильный';
                                } elseif ($deviceType === 'tablet') {
                                    $deviceIcon = 'ri-tablet-line';
                                    $deviceTypeText = 'Планшет';
                                }

                                // Последняя активность
                                $lastActivity = $lastSession ? $lastSession->last_activity : ($onlineSession ? $onlineSession->last_activity_at : null);

                                // Инициалы для аватара
                                $initials = $user->name ? implode('', array_map(function($word) {
                                    return mb_substr($word, 0, 1);
                                }, explode(' ', trim($user->name)))) : strtoupper(mb_substr($user->email, 0, 1));
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
                                                <div class="avatar-initial rounded-circle bg-label-primary" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $user->name ?? 'Без имени' }}</span>
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
                                        @else
                                            <span class="badge bg-secondary me-2">Офлайн</span>
                                        @endif
                                        @if($lastActivity)
                                            <div class="small text-muted">
                                                {{ is_string($lastActivity) ? $lastActivity : $lastActivity->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Устройство -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $deviceIcon }} me-1 text-primary"></i>
                                            <span>{{ $deviceTypeText }}</span>
                                        </div>
                                        @if($browser && $browser != 'Unknown')
                                            <div class="small text-muted mt-1">
                                                <i class="ri-browser-line me-1"></i> {{ $browser }}
                                            </div>
                                        @endif
                                        @if($os && $os != 'Unknown')
                                            <div class="small text-muted">
                                                <i class="ri-computer-line me-1"></i> {{ $os }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- IP адрес -->
                                <td>
                                    @if($ipAddress)
                                        <code class="small">{{ $ipAddress }}</code>
                                        @if(!in_array($ipAddress, ['127.0.0.1', '::1', 'localhost']))
                                            <div class="small text-muted mt-1">
                                                <a href="https://whatismyipaddress.com/ip/{{ $ipAddress }}" target="_blank" class="text-info">
                                                    <i class="ri-shield-cross-line"></i> Инфо об IP
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Геолокация -->
                                <td>
                                    @if($hasLocation && $city)
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-map-pin-line text-danger me-1"></i>
                                                <strong>{{ $city }}</strong>
                                            </div>
                                            <div class="small text-muted">
                                                {{ $country ?? 'Россия' }}
                                            </div>
                                            @if($latitude && $longitude)
                                                <div class="small text-info mt-1">
                                                    <i class="ri-crosshair-line"></i>
                                                    {{ number_format($latitude, 4) }}, {{ number_format($longitude, 4) }}
                                                </div>
                                                <div class="small mt-1">
                                                    <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank" class="text-primary">
                                                        <i class="ri-map-2-line"></i> Открыть на карте
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($country)
                                        <div class="d-flex align-items-center">
                                            <i class="ri-map-pin-line text-danger me-1"></i>
                                            <div>
                                                {{ $country }}
                                                <div class="small text-muted">По стране</div>
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
                                        <div class="small">
                                            {{ is_string($lastActivity) ? $lastActivity : $lastActivity->diffForHumans() }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ is_string($lastActivity) ? $lastActivity : $lastActivity->format('d.m.Y H:i:s') }}
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
                                            @if($hasLocation && $latitude && $longitude)
                                                <li>
                                                    <a class="dropdown-item" href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank">
                                                        <i class="ri-map-2-line me-1"></i> Открыть на карте
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
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="ri-user-unfollow-line ri-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Пользователи не найдены</p>
                                </td>
                            </tr>
                        @endforelse
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
            const filterCountry = document.getElementById('filterCountry');
            const tableRows = document.querySelectorAll('#users-table tbody tr');

            function filterTable() {
                const deviceValue = filterDevice ? filterDevice.value : '';
                const statusValue = filterStatus ? filterStatus.value : '';
                const countryValue = filterCountry ? filterCountry.value : '';

                tableRows.forEach(row => {
                    let showRow = true;

                    // Фильтр по устройству (5-я колонка)
                    if (deviceValue && showRow) {
                        const deviceCell = row.cells[4];
                        if (deviceCell && !deviceCell.innerText.toLowerCase().includes(deviceValue)) {
                            showRow = false;
                        }
                    }

                    // Фильтр по статусу (4-я колонка)
                    if (statusValue && showRow) {
                        const statusCell = row.cells[3];
                        if (statusCell && !statusCell.innerText.toLowerCase().includes(statusValue)) {
                            showRow = false;
                        }
                    }

                    // Фильтр по стране (7-я колонка)
                    if (countryValue && showRow) {
                        const geoCell = row.cells[6];
                        if (geoCell && !geoCell.innerText.toLowerCase().includes(countryValue.toLowerCase())) {
                            showRow = false;
                        }
                    }

                    row.style.display = showRow ? '' : 'none';
                });
            }

            if (filterDevice) filterDevice.addEventListener('change', filterTable);
            if (filterStatus) filterStatus.addEventListener('change', filterTable);
            if (filterCountry) filterCountry.addEventListener('change', filterTable);

            // Инициализация DataTable (если подключен)
            if (typeof $.fn !== 'undefined' && $.fn.DataTable) {
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
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Сессии пользователя завершены');
                            } else {
                                alert('Сессии пользователя завершены');
                            }
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            const errorMsg = data.error || 'Ошибка при отключении';
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMsg);
                            } else {
                                alert(errorMsg);
                            }
                        }
                    }).catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при выполнении запроса');
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
        .badge {
            font-weight: 500;
        }
        .ri-map-pin-line, .ri-crosshair-line {
            font-size: 12px;
        }
        .dropdown-menu .dropdown-item i {
            font-size: 14px;
        }
    </style>
@endpush
