@extends('layouts.admin')

@section('title', 'Статистика обращений')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500">Всего обращений</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['new'] }}</div>
            <div class="text-sm text-yellow-700">Новых</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['in_progress'] }}</div>
            <div class="text-sm text-blue-700">В работе</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-600">{{ $stats['answered'] }}</div>
            <div class="text-sm text-green-700">Отвечено</div>
        </div>
        <div class="bg-gray-50 rounded-lg shadow p-4 border-l-4 border-gray-500">
            <div class="text-2xl font-bold text-gray-600">{{ $stats['closed'] }}</div>
            <div class="text-sm text-gray-700">Закрыто</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4">📈 Динамика обращений</h3>
        <canvas id="ticketsChart" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Количество обращений',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    tension: 0.3
                }]
            }
        });
    </script>
@endsection
