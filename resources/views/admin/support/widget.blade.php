@php
    $stats = \App\Models\SupportTicket::selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    $total = array_sum($stats);
    $new = $stats['new'] ?? 0;
    $inProgress = $stats['in_progress'] ?? 0;
    $answered = $stats['answered'] ?? 0;
    $closed = $stats['closed'] ?? 0;
@endphp

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Всего</h6>
                        <h2 class="mb-0">{{ $total }}</h2>
                    </div>
                    <i class="bi bi-envelope fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Новые</h6>
                        <h2 class="mb-0">{{ $new }}</h2>
                    </div>
                    <i class="bi bi-star fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">В работе</h6>
                        <h2 class="mb-0">{{ $inProgress }}</h2>
                    </div>
                    <i class="bi bi-gear fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Отвечено</h6>
                        <h2 class="mb-0">{{ $answered }}</h2>
                    </div>
                    <i class="bi bi-check-circle fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>
