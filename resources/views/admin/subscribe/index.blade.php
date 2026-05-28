@extends('layouts.admin')

@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Статистика -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Всего компаний</span>
                                    <h3 class="card-title mb-2">{{ $totalCompanies }}</h3>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-label-primary p-2">
                                        <i class="ri-building-line ri-22px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Премиум подписки</span>
                                    <h3 class="card-title mb-2 text-primary">{{ $premiumCount }}</h3>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-label-primary p-2">
                                        <i class="ri-crown-line ri-22px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Базовые подписки</span>
                                    <h3 class="card-title mb-2 text-secondary">{{ $basicCount }}</h3>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-label-secondary p-2">
                                        <i class="ri-user-line ri-22px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Всего пользователей</span>
                                    <h3 class="card-title mb-2 text-success">{{ $totalUsers }}</h3>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-label-success p-2">
                                        <i class="ri-group-line ri-22px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица подписок -->
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <div class="card-header flex-column flex-md-row border-bottom">
                            <div class="head-label text-center">
                                <h5 class="card-title mb-0">Управление подписками</h5>
                            </div>
                            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                                <div>
                                    <button type="button"
                                            class="btn btn-secondary dropdown-toggle waves-effect waves-light"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-filter-line me-1"></i> Фильтр
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index') }}">Все</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['type' => 'premium']) }}">Премиум</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['type' => 'basic']) }}">Базовые</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}">Активные</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}">Истекшие</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Поиск -->
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length">
                                    <label>
                                        Показывать
                                        <select id="perPage" class="form-select form-select-sm d-inline-block w-auto">
                                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                        записей
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                                <div class="dataTables_filter">
                                    <form method="GET" action="{{ route('admin.subscriptions.index') }}">
                                        <label>
                                            <input type="search" name="search" class="form-control form-control-sm"
                                                   placeholder="Поиск по компании..."
                                                   value="{{ request('search') }}"
                                                   aria-controls="DataTables_Table_0">
                                        </label>
                                        <button type="submit" class="btn btn-sm btn-primary ms-1">
                                            <i class="ri-search-line"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-secondary ms-1">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Компания</th>
                                <th>Тариф</th>
                                <th>Статус</th>
                                <th>Пользователи</th>
                                <th>Доп. пользователи</th>
                                <th>Всего слотов</th>
                                <th>Хранилище</th>
                                <th>Дата начала</th>
                                <th>Дата окончания</th>
                                <th>Дней осталось</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($subscriptions as $subscription)
                                @php
                                    $company = $subscription->company;
                                    $usedUsers = $company ? $company->getActiveUsersCount() : 0;
                                    $totalSlots = $subscription->getTotalUserSlots();
                                    $usedPercentage = $totalSlots > 0 ? round(($usedUsers / $totalSlots) * 100) : 0;
                                    $daysRemaining = $subscription->expires_at->diffInDays(now(), false);
                                    $isExpired = $daysRemaining < 0;
                                    $statusColor = $subscription->status === 'active' ? 'success' : ($isExpired ? 'danger' : 'secondary');
                                    $statusText = $subscription->status === 'active' ? 'Активна' : ($isExpired ? 'Истекла' : 'Отменена');
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $subscription->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $company->name ?? '—' }}</strong>
                                            <small class="text-muted">ID: {{ $company->id ?? '—' }}</small>
                                            @if($company && $company->phone)
                                                <small class="text-muted">{{ $company->phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($subscription->type === 'premium')
                                            <span class="badge bg-label-primary">
                                                <i class="ri-crown-line me-1"></i> Премиум
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary">
                                                <i class="ri-user-line me-1"></i> Базовый
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $statusColor }}">
                                            {{ $statusText }}
                                        </span>
                                        @if($subscription->status === 'active' && !$isExpired)
                                            <small class="d-block text-success">
                                                <i class="ri-checkbox-circle-line"></i> Действует
                                            </small>
                                        @elseif($isExpired)
                                            <small class="d-block text-danger">
                                                <i class="ri-error-warning-line"></i> Просрочена
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $usedUsers }} / {{ $totalSlots }}</span>
                                                <span class="text-muted small">{{ $usedPercentage }}%</span>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-{{ $usedPercentage > 80 ? 'danger' : ($usedPercentage > 60 ? 'warning' : 'success') }}"
                                                     style="width: {{ $usedPercentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $additionalPurchases = $subscription->additionalUserPurchases()
                                                ->where('is_active', true)
                                                ->where('expires_at', '>', now())
                                                ->get();
                                            $totalAdditional = $additionalPurchases->sum('user_count');
                                        @endphp
                                        @if($totalAdditional > 0)
                                            <span class="badge bg-label-info">
                                                +{{ $totalAdditional }} ({{ $additionalPurchases->count() }} покупок)
                                            </span>
                                            <small class="d-block text-muted mt-1">
                                                @foreach($additionalPurchases as $purchase)
                                                    {{ $purchase->user_count }} шт. до {{ $purchase->expires_at->format('d.m.Y') }}
                                                    @if(!$loop->last)<br>@endif
                                                @endforeach
                                            </small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $totalSlots }}</span>
                                        <small class="d-block text-muted">базовых: {{ $subscription->base_user_slots }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $usedStorage = $company ? $company->getStorageStats()['formatted_used'] ?? '0 B' : '0 B';
                                            $totalStorage = $company ? $company->getFormattedStorageLimit() : '0 B';
                                            $storagePercent = $company && $company->storageUsage ? $company->storageUsage->getUsagePercentage() : 0;

                                            // Если хранилище показывает неправильно, получаем напрямую
                                            if ($company && $company->storageUsage && $company->storageUsage->used_storage > 0) {
                                                $usedBytes = $company->storageUsage->used_storage;
                                                if ($usedBytes > 1073741824) { // больше 1GB
                                                    $usedStorage = round($usedBytes / 1073741824, 2) . ' GB';
                                                } else {
                                                    $usedStorage = round($usedBytes / 1048576, 2) . ' MB';
                                                }
                                            }
                                        @endphp
                                        <div class="d-flex flex-column">
                                            <span>{{ $usedStorage }} / {{ $totalStorage }}</span>
                                            <div class="progress" style="width: 80px; height: 3px;">
                                                <div class="progress-bar bg-info" style="width: {{ min($storagePercent, 100) }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $subscription->starts_at->format('d.m.Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small>{{ $subscription->expires_at->format('d.m.Y') }}</small>
                                            @if($subscription->status === 'active' && $daysRemaining >= 0)
                                                <small class="text-{{ $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 30 ? 'warning' : 'success') }}">
                                                    {{ $daysRemaining }} дн.
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($subscription->status === 'active' && $daysRemaining >= 0)
                                            <span class="fw-semibold text-{{ $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 30 ? 'warning' : 'success') }}">
                                                {{ $daysRemaining }}
                                            </span>
                                            <small class="d-block text-muted">дней</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                   data-bs-target="#viewCompanyModal"
                                                   data-company-id="{{ $company->id ?? 0 }}"
                                                   data-company-name="{{ $company->name ?? '' }}">
                                                    <i class="ri-eye-line me-1"></i> Просмотр компании
                                                </a>
                                                @if($subscription->status === 'active')
                                                    <a class="dropdown-item text-warning" href="#"
                                                       onclick="cancelSubscription({{ $subscription->id }})">
                                                        <i class="ri-stop-circle-line me-1"></i> Отменить подписку
                                                    </a>
                                                @endif
                                                <a class="dropdown-item text-info" href="#"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#addUsersModal"
                                                   data-subscription-id="{{ $subscription->id }}"
                                                   data-company-name="{{ $company->name ?? '' }}">
                                                    <i class="ri-user-add-line me-1"></i> Добавить пользователей
                                                </a>
                                                <a class="dropdown-item text-danger" href="#"
                                                   onclick="deleteSubscription({{ $subscription->id }})">
                                                    <i class="ri-delete-bin-line me-1"></i> Удалить запись
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-5">
                                        <i class="ri-inbox-line ri-3x text-muted"></i>
                                        <p class="text-muted mt-2">Подписки не найдены</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        <!-- Пагинация -->
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_info">
                                    Показано с {{ $subscriptions->firstItem() ?? 0 }} по {{ $subscriptions->lastItem() ?? 0 }}
                                    из {{ $subscriptions->total() }} записей
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $subscriptions->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно просмотра компании -->
    <div class="modal fade" id="viewCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Информация о компании</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body" id="companyModalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления пользователей -->
    <div class="modal fade" id="addUsersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addUsersForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление дополнительных пользователей</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="subscription_id" id="modalSubscriptionId">
                        <div class="mb-3">
                            <label class="form-label">Компания</label>
                            <input type="text" class="form-control" id="modalCompanyName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Количество пользователей</label>
                            <input type="number" name="user_count" class="form-control" min="1" max="100" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Период</label>
                            <select name="period" class="form-select" required>
                                <option value="month">1 месяц</option>
                                <option value="6months">6 месяцев (скидка 10%)</option>
                                <option value="year">12 месяцев (скидка 15%)</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small>Цена: 400 ₽ за пользователя в месяц. Скидки применяются при покупке на срок.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Просмотр компании
        document.querySelectorAll('[data-bs-target="#viewCompanyModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const companyId = this.dataset.companyId;
                const companyName = this.dataset.companyName;

                fetch(`/admin/companies/${companyId}/info`)
                    .then(response => response.json())
                    .then(data => {
                        const content = `
                            <h6>${data.name}</h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> ${data.id}</p>
                                    <p><strong>Телефон:</strong> ${data.phone || '—'}</p>
                                    <p><strong>Тариф:</strong> ${data.license_type === 'premium' ? 'Премиум' : 'Базовый'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Пользователей:</strong> ${data.active_users} / ${data.max_users}</p>
                                    <p><strong>Хранилище:</strong> ${data.used_storage} / ${data.total_storage}</p>
                                    <p><strong>Дата регистрации:</strong> ${data.created_at}</p>
                                </div>
                            </div>
                        `;
                        document.getElementById('companyModalContent').innerHTML = content;
                    })
                    .catch(() => {
                        document.getElementById('companyModalContent').innerHTML = '<div class="alert alert-danger">Ошибка загрузки данных</div>';
                    });
            });
        });

        // Добавление пользователей
        document.querySelectorAll('[data-bs-target="#addUsersModal"]').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalSubscriptionId').value = this.dataset.subscriptionId;
                document.getElementById('modalCompanyName').value = this.dataset.companyName;
                document.getElementById('addUsersForm').action = `/admin/subscriptions/${this.dataset.subscriptionId}/add-users`;
            });
        });

        // Отмена подписки
        function cancelSubscription(subscriptionId) {
            if (confirm('Вы уверены, что хотите отменить эту подписку?')) {
                fetch(`/admin/subscriptions/${subscriptionId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Ошибка: ' + data.error);
                        }
                    });
            }
        }

        // Удаление записи о подписке
        function deleteSubscription(subscriptionId) {
            if (confirm('Вы уверены? Это действие нельзя отменить.')) {
                fetch(`/admin/subscriptions/${subscriptionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Ошибка: ' + data.error);
                        }
                    });
            }
        }

        // Изменение количества записей на странице
        document.getElementById('perPage')?.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
    </script>
@endsection
