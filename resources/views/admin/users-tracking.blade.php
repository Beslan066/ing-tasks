@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Статистика -->
            <div class="row g-6 mb-6">
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="me-1">
                                    <p class="text-heading mb-1">Всего пользователей</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-2">{{ $totalUsers }}</h4>
                                        <p class="text-success mb-1">(+{{ $newUsersCount }})</p>
                                    </div>
                                    <small class="mb-0">За последнюю неделю</small>
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
                                    <p class="text-heading mb-1">Активные сессии</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $activeSessions }}</h4>
                                        <p class="text-success mb-1">({{ $activePercentage }}%)</p>
                                    </div>
                                    <small class="mb-0">Пользователей онлайн</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-danger rounded-3">
                                        <i class="ri-user-add-line ri-26px"></i>
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
                                    <p class="text-heading mb-1">Уникальных устройств</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $uniqueDevices }}</h4>
                                        <p class="text-info mb-1">{{ $devicesCount }}</p>
                                    </div>
                                    <small class="mb-0">Всего зарегистрировано</small>
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
                                    <p class="text-heading mb-1">Стран</p>
                                    <div class="d-flex align-items-center">
                                        <h4 class="mb-1 me-1">{{ $countriesCount }}</h4>
                                        <p class="text-success mb-1">геолокаций</p>
                                    </div>
                                    <small class="mb-0">По всему миру</small>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded-3">
                                        <i class="ri-global-line ri-26px"></i>
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
                                <option value="active">Активен</option>
                                <option value="inactive">Неактивен</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filterDate" placeholder="Дата активности">
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.map') }}" class="btn btn-primary w-100">
                                <i class="ri-map-pin-line me-1"></i>Карта
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="datatables-users table table-hover" id="users-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Пользователь</th>
                            <th>Email</th>
                            <th>Устройство</th>
                            <th>IP адрес</th>
                            <th>Геолокация</th>
                            <th>Последняя активность</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            @php
                                $lastSession = $user->sessions->first();
                            @endphp
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <div class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            <div class="small text-muted">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $user->email }}</span>
                                    <div class="small text-muted">
                                        @if($user->email_verified_at)
                                            <i class="ri-checkbox-circle-line text-success"></i> Подтвержден
                                        @else
                                            <i class="ri-close-circle-line text-danger"></i> Не подтвержден
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($lastSession)
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center">
                                                @if($lastSession->device_type == 'mobile')
                                                    <i class="ri-smartphone-line me-1 text-primary"></i>
                                                @elseif($lastSession->device_type == 'tablet')
                                                    <i class="ri-tablet-line me-1 text-primary"></i>
                                                @else
                                                    <i class="ri-computer-line me-1 text-primary"></i>
                                                @endif
                                                <span>{{ ucfirst($lastSession->device_type) }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="ri-browser-line"></i> {{ $lastSession->browser }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="ri-windows-line"></i> {{ $lastSession->os }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Нет данных</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lastSession)
                                        <div class="d-flex flex-column">
                                            <code>{{ $lastSession->ip_address }}</code>
                                            <div class="small text-muted">
                                                <i class="ri-shield-check-line"></i>
                                                @if($lastSession->device_fingerprint)
                                                    Отпечаток: {{ substr($lastSession->device_fingerprint, 0, 8) }}...
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lastSession && $lastSession->latitude && $lastSession->longitude)
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center">
                                                <i class="ri-map-pin-line text-danger me-1"></i>
                                                <span>{{ $lastSession->city ?: 'Не определен' }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                {{ $lastSession->country ?: 'Страна не определена' }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="ri-global-line"></i>
                                                <a href="https://www.google.com/maps?q={{ $lastSession->latitude }},{{ $lastSession->longitude }}"
                                                   target="_blank">
                                                    {{ number_format($lastSession->latitude, 4) }}, {{ number_format($lastSession->longitude, 4) }}
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="ri-map-pin-line"></i> Не определено
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($lastSession)
                                        <div class="d-flex flex-column">
                                            <div class="small">
                                                {{ $lastSession->last_activity->diffForHumans() }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $lastSession->last_activity->format('d.m.Y H:i:s') }}
                                            </div>
                                            @if($lastSession->is_current)
                                                <span class="badge bg-success mt-1">Сейчас онлайн</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($lastSession && $lastSession->is_current)
                                            <span class="badge bg-success me-2">Активен</span>
                                        @else
                                            <span class="badge bg-secondary me-2">Офлайн</span>
                                        @endif
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-2-line"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                        <i class="ri-eye-line me-1"></i> Детали
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#sessionModal-{{ $user->id }}">
                                                        <i class="ri-history-line me-1"></i> Все сессии ({{ $user->sessions->count() }})
                                                    </a>
                                                </li>
                                                @if($lastSession && $lastSession->latitude && $lastSession->longitude)
                                                    <li>
                                                        <a class="dropdown-item" href="https://www.google.com/maps?q={{ $lastSession->latitude }},{{ $lastSession->longitude }}" target="_blank">
                                                            <i class="ri-map-pin-line me-1"></i> Открыть на карте
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Модальные окна для каждой сессии -->
        @foreach($users as $user)
            <div class="modal fade" id="sessionModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Сессии пользователя: {{ $user->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>Устройство</th>
                                        <th>ОС / Браузер</th>
                                        <th>Геолокация</th>
                                        <th>Последняя активность</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user->sessions as $session)
                                        <tr @if($session->is_current) class="table-success" @endif>
                                            <td><code>{{ $session->ip_address }}</code></td>
                                            <td>
                                                @if($session->device_type == 'mobile')
                                                    <i class="ri-smartphone-line"></i>
                                                @elseif($session->device_type == 'tablet')
                                                    <i class="ri-tablet-line"></i>
                                                @else
                                                    <i class="ri-computer-line"></i>
                                                @endif
                                                {{ ucfirst($session->device_type) }}
                                            </td>
                                            <td>
                                                <small>{{ $session->os }}</small><br>
                                                <small>{{ $session->browser }}</small>
                                            </td>
                                            <td>
                                                @if($session->latitude && $session->longitude)
                                                    {{ $session->city }}, {{ $session->country }}
                                                    <br>
                                                    <small>
                                                        <a href="https://www.google.com/maps?q={{ $session->latitude }},{{ $session->longitude }}" target="_blank">
                                                            {{ number_format($session->latitude, 4) }}, {{ number_format($session->longitude, 4) }}
                                                        </a>
                                                    </small>
                                                @else
                                                    Не определено
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $session->last_activity->diffForHumans() }}</small>
                                                <br>
                                                <small class="text-muted">{{ $session->last_activity->format('d.m.Y H:i') }}</small>
                                                @if($session->is_current)
                                                    <span class="badge bg-success d-block mt-1">Текущая</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Фильтрация таблицы
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
                            const deviceCell = row.cells[3];
                            if (deviceCell && !deviceCell.innerText.toLowerCase().includes(deviceValue)) {
                                showRow = false;
                            }
                        }

                        // Фильтр по статусу
                        if (statusValue && showRow) {
                            const statusCell = row.cells[7];
                            if (statusCell && !statusCell.innerText.toLowerCase().includes(statusValue)) {
                                showRow = false;
                            }
                        }

                        row.style.display = showRow ? '' : 'none';
                    });
                }

                filterDevice.addEventListener('change', filterTable);
                filterStatus.addEventListener('change', filterTable);

                // Инициализация DataTable (если используете)
                if ($.fn.DataTable) {
                    $('#users-table').DataTable({
                        order: [[6, 'desc']],
                        pageLength: 10,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json'
                        }
                    });
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .table td {
                vertical-align: middle;
            }
            .avatar-sm {
                width: 2rem;
                height: 2rem;
            }
            code {
                font-size: 0.75rem;
            }
            .bg-label-primary {
                background-color: rgba(105, 108, 255, 0.16);
                color: #696cff;
            }
            .bg-label-success {
                background-color: rgba(40, 199, 111, 0.16);
                color: #28c76f;
            }
            .bg-label-danger {
                background-color: rgba(255, 82, 82, 0.16);
                color: #ff5252;
            }
            .bg-label-warning {
                background-color: rgba(255, 168, 0, 0.16);
                color: #ffa800;
            }
            .bg-label-info {
                background-color: rgba(0, 207, 232, 0.16);
                color: #00cfe8;
            }
        </style>
    @endpush

@endsection
