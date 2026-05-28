@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="ri-map-pin-line me-2"></i>Карта пользователей
                </h3>
                <a href="{{ route('admin.users.tracking') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line me-1"></i>Назад
                </a>
            </div>
            <div class="card-body">
                <!-- Статистика -->
                <div class="row g-6 mb-6">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar mx-auto mb-3">
                                    <div class="avatar-initial bg-label-primary rounded-3">
                                        <i class="ri-group-line ri-26px"></i>
                                    </div>
                                </div>
                                <h5 class="mb-1" id="totalUsersCount">0</h5>
                                <p class="text-muted mb-0">Всего пользователей</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar mx-auto mb-3">
                                    <div class="avatar-initial bg-label-success rounded-3">
                                        <i class="ri-map-pin-user-line ri-26px"></i>
                                    </div>
                                </div>
                                <h5 class="mb-1" id="markersCount">0</h5>
                                <p class="text-muted mb-0">На карте</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar mx-auto mb-3">
                                    <div class="avatar-initial bg-label-info rounded-3">
                                        <i class="ri-earth-line ri-26px"></i>
                                    </div>
                                </div>
                                <h5 class="mb-1" id="countriesCount">0</h5>
                                <p class="text-muted mb-0">Стран</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Карта -->
                <div id="map" style="height: 600px; width: 100%; border-radius: 8px; z-index: 1;"></div>

                <!-- Легенда -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="ri-information-line me-2 ri-20px"></i>
                            <div>
                                <strong>Информация:</strong> На карте отображаются пользователи с определенной геолокацией.
                                Зеленые маркеры — пользователи онлайн, красные — офлайн.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключаем Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Подключаем Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Подключаем MarkerCluster -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Данные пользователей из PHP
            var sessions = @json($sessions);

            // Фильтруем только сессии с координатами
            var validSessions = sessions.filter(function(session) {
                return session.latitude && session.longitude &&
                    session.latitude !== 0 && session.longitude !== 0;
            });

            // Обновляем статистику
            document.getElementById('totalUsersCount').innerText = validSessions.length;
            document.getElementById('markersCount').innerText = validSessions.length;

            // Подсчет уникальных стран
            var uniqueCountries = new Set();
            validSessions.forEach(function(session) {
                if (session.country && session.country !== 'Локальный') {
                    uniqueCountries.add(session.country);
                }
            });
            document.getElementById('countriesCount').innerText = uniqueCountries.size;

            // Если нет данных, показываем сообщение
            if (validSessions.length === 0) {
                var map = L.map('map').setView([55.751244, 37.618423], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                L.popup()
                    .setLatLng([55.751244, 37.618423])
                    .setContent('<div class="text-center"><i class="ri-map-pin-line ri-3x"></i><br><strong>Нет пользователей с геолокацией</strong><br>Пользователи появятся на карте после того, как войдут в систему</div>')
                    .openOn(map);
                return;
            }

            // Определяем центр карты (среднее значение всех координат)
            var centerLat = validSessions.reduce((sum, s) => sum + parseFloat(s.latitude), 0) / validSessions.length;
            var centerLng = validSessions.reduce((sum, s) => sum + parseFloat(s.longitude), 0) / validSessions.length;

            // Инициализация карты
            var map = L.map('map').setView([centerLat, centerLng], 5);

            // Добавляем слой карты
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Кастомные иконки
            var onlineIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var offlineIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Массив для маркеров
            var markers = [];

            // Добавляем маркеры для каждой сессии
            validSessions.forEach(function(session) {
                // Определяем онлайн статус
                var isOnline = session.is_current === 1 || session.is_current === true;
                var icon = isOnline ? onlineIcon : offlineIcon;

                // Форматируем дату
                var lastActivity = session.last_activity ? new Date(session.last_activity).toLocaleString('ru-RU') : 'Неизвестно';

                // Попап с информацией о пользователе
                var popupContent = `
                    <div style="min-width: 260px; max-width: 320px;">
                        <div class="d-flex align-items-center mb-2">
                            ${session.user && session.user.avatar ?
                    `<img src="/storage/${session.user.avatar}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">` :
                    `<div class="avatar-initial rounded-circle bg-label-primary me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="ri-user-line ri-20px"></i>
                                </div>`
                }
                            <div>
                                <strong style="font-size: 14px;">${session.user ? session.user.name : 'Пользователь не найден'}</strong><br>
                                <span style="font-size: 11px; color: #666;">ID: ${session.user_id}</span>
                            </div>
                        </div>
                        <hr style="margin: 8px 0;">
                        <div style="font-size: 12px;">
                            <div class="mb-1">
                                <i class="ri-mail-line" style="width: 20px; display: inline-block;"></i>
                                <strong>Email:</strong> ${session.user ? session.user.email : 'Не указан'}
                            </div>
                            <div class="mb-1">
                                <i class="ri-map-pin-line" style="width: 20px; display: inline-block;"></i>
                                <strong>Геолокация:</strong><br>
                                <span style="margin-left: 20px;">📍 ${session.city || 'Не определен'}${session.city && session.country ? ', ' : ''}${session.country || ''}</span><br>
                                <span style="margin-left: 20px; font-size: 10px; color: #999;">
                                    📍 ${parseFloat(session.latitude).toFixed(4)}°, ${parseFloat(session.longitude).toFixed(4)}°
                                </span>
                            </div>
                            <div class="mb-1">
                                <i class="ri-global-line" style="width: 20px; display: inline-block;"></i>
                                <strong>IP:</strong> ${session.ip_address || 'Не определен'}
                            </div>
                            <div class="mb-1">
                                <i class="ri-device-line" style="width: 20px; display: inline-block;"></i>
                                <strong>Устройство:</strong>
                                ${session.device_type === 'mobile' ? '📱 Мобильный' :
                    session.device_type === 'tablet' ? '📟 Планшет' : '💻 Десктоп'}
                                ${session.browser ? ` (${session.browser})` : ''}
                            </div>
                            <div class="mb-1">
                                <i class="ri-time-line" style="width: 20px; display: inline-block;"></i>
                                <strong>Последняя активность:</strong><br>
                                <span style="margin-left: 20px;">${lastActivity}</span>
                            </div>
                        </div>
                        <hr style="margin: 8px 0;">
                        <div class="d-flex justify-content-between mt-2">
                            <span style="font-size: 11px;">
                                ${isOnline ? '<span style="color: green;">🟢 Онлайн</span>' : '<span style="color: red;">🔴 Офлайн</span>'}
                            </span>
                            <a href="/admin/users/${session.user_id}" target="_blank" class="btn btn-sm btn-primary" style="font-size: 11px; padding: 4px 8px;">
                                <i class="ri-eye-line"></i> Подробнее
                            </a>
                        </div>
                    </div>
                `;

                // Создаем маркер с соответствующей иконкой
                var marker = L.marker([parseFloat(session.latitude), parseFloat(session.longitude)], {icon: icon})
                    .bindPopup(popupContent, {maxWidth: 350});

                markers.push(marker);
            });

            // Группируем маркеры для лучшей производительности
            var markerClusterGroup = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });

            markers.forEach(function(marker) {
                markerClusterGroup.addLayer(marker);
            });

            map.addLayer(markerClusterGroup);

            // Автоматически подгоняем границы карты под все маркеры
            if (markers.length > 0) {
                var bounds = L.latLngBounds(markers.map(m => m.getLatLng()));
                map.fitBounds(bounds, {padding: [50, 50]});
            }

            // Добавляем масштаб
            L.control.scale({metric: true, imperial: false, position: 'bottomright'}).addTo(map);

            // Логируем количество маркеров
            console.log('✅ Добавлено маркеров: ' + markers.length);
            console.log('🌍 Уникальных стран: ' + uniqueCountries.size);
        });
    </script>

    <style>
        .leaflet-popup-content {
            min-width: 260px;
            margin: 10px;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }
        .leaflet-popup-close-button {
            font-size: 16px !important;
            padding: 4px 8px !important;
        }
        .btn-sm {
            font-size: 11px;
            padding: 4px 8px;
        }
        .ri-mail-line, .ri-map-pin-line, .ri-global-line, .ri-device-line, .ri-time-line {
            color: #666;
        }
        .avatar-initial {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-label-primary {
            background-color: #e9ecef;
        }
    </style>
@endsection
