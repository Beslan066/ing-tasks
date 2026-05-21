@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Карта пользователей</h3>
                <a href="{{ route('admin.users.tracking') }}" class="btn btn-secondary">Назад</a>
            </div>
            <div class="card-body">
                <div id="map" style="height: 600px; width: 100%;"></div>
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
            // Инициализация карты
            var map = L.map('map').setView([55.751244, 37.618423], 5);

            // Добавляем слой карты
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Данные пользователей
            var sessions = @json($sessions);

            // Создаем красный маркер
            var redIcon = L.icon({
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
            sessions.forEach(function(session) {
                if (session.latitude && session.longitude) {
                    // Попап с информацией о пользователе
                    var popupContent = `
                    <div style="min-width: 200px;">
                        <strong>${session.user ? session.user.name : 'Пользователь не найден'}</strong><br>
                        Email: ${session.user ? session.user.email : 'Не указан'}<br>
                        <hr>
                        <strong>Информация о сессии:</strong><br>
                        IP: ${session.ip_address}<br>
                        Устройство: ${session.device_type || 'Не определено'}<br>
                        ОС: ${session.os || 'Не определена'}<br>
                        Браузер: ${session.browser || 'Не определен'}<br>
                        <hr>
                        <strong>Геолокация:</strong><br>
                        Адрес: ${session.address || 'Не определен'}<br>
                        Город: ${session.city || 'Не определен'}<br>
                        Страна: ${session.country || 'Не определена'}<br>
                        <hr>
                        Последняя активность: ${new Date(session.last_activity).toLocaleString()}<br>
                        ${session.is_current ? '<span style="color: green;">● Текущая сессия</span>' : ''}
                        <br>
                        <a href="/admin/users/${session.user_id}" target="_blank" style="margin-top: 10px; display: inline-block;">
                            Подробнее о пользователе
                        </a>
                    </div>
                `;

                    // Создаем маркер
                    var marker = L.marker([session.latitude, session.longitude], {icon: redIcon})
                        .bindPopup(popupContent);

                    markers.push(marker);
                }
            });

            // Добавляем все маркеры на карту
            if (markers.length > 0) {
                // Группируем маркеры для лучшей производительности
                var markerClusterGroup = L.markerClusterGroup();
                markers.forEach(function(marker) {
                    markerClusterGroup.addLayer(marker);
                });
                map.addLayer(markerClusterGroup);

                // Автоматически центрируем карту на первом маркере, если он есть
                if (markers[0]) {
                    var firstMarker = markers[0].getLatLng();
                    map.setView([firstMarker.lat, firstMarker.lng], 5);
                }
            } else {
                // Если нет маркеров, показываем сообщение
                L.popup()
                    .setLatLng([55.751244, 37.618423])
                    .setContent('Нет пользователей с геолокацией для отображения на карте')
                    .openOn(map);
            }

            // Логируем количество маркеров
            console.log('Добавлено маркеров: ' + markers.length);
        });
    </script>

    <style>
        .leaflet-popup-content {
            min-width: 250px;
            font-size: 12px;
        }
        .leaflet-popup-content strong {
            color: #333;
        }
        .leaflet-popup-content hr {
            margin: 8px 0;
        }
        .leaflet-popup-content a {
            color: #007bff;
            text-decoration: none;
        }
        .leaflet-popup-content a:hover {
            text-decoration: underline;
        }
    </style>

@endsection
