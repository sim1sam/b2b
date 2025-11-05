@extends('adminlte::page')

@section('title', 'My Disputes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-exclamation-triangle"></i> My Disputes</h1>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            @if($disputes->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No disputes found.</p>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Dispute Status</th>
                                <th>Dispute Note</th>
                                <th>Opened At</th>
                                <th>Time Remaining</th>
                                <th>Admin Response</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disputes as $invoice)
                                @php
                                    $hoursRemaining = 0;
                                    $minutesRemaining = 0;
                                    if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
                                        $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
                                        $totalMinutesRemaining = (48 * 60) - $minutesPassed;
                                        if ($totalMinutesRemaining > 0) {
                                            $hoursRemaining = floor($totalMinutesRemaining / 60);
                                            $minutesRemaining = $totalMinutesRemaining % 60;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>
                                        @if($invoice->dispute_status === 'open')
                                            <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> Open</span>
                                        @elseif($invoice->dispute_status === 'resolved')
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Resolved</span>
                                        @else
                                            <span class="badge badge-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->dispute_note)
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $invoice->dispute_note }}">
                                                {{ \Illuminate\Support\Str::limit($invoice->dispute_note, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->dispute_opened_at)
                                            {{ $invoice->dispute_opened_at->format('d M Y, h:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->dispute_status === 'open' && ($hoursRemaining > 0 || $minutesRemaining > 0))
                                            <span class="badge badge-warning">{{ str_pad($hoursRemaining, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutesRemaining, 2, '0', STR_PAD_LEFT) }}</span>
                                        @elseif($invoice->dispute_status === 'open')
                                            <span class="badge badge-secondary">Expired</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->admin_response)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.disputes.show', $invoice->id) }}" class="btn btn-sm btn-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @foreach($disputes as $invoice)
                        @php
                            $hoursRemaining = 0;
                            $minutesRemaining = 0;
                            if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
                                $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
                                $totalMinutesRemaining = (48 * 60) - $minutesPassed;
                                if ($totalMinutesRemaining > 0) {
                                    $hoursRemaining = floor($totalMinutesRemaining / 60);
                                    $minutesRemaining = $totalMinutesRemaining % 60;
                                }
                            }
                        @endphp
                        <div class="card mb-3 border-left-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1 font-weight-bold">
                                            {{ $invoice->invoice_number }}
                                        </h5>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-calendar"></i> 
                                            @if($invoice->dispute_opened_at)
                                                {{ $invoice->dispute_opened_at->format('d M Y, h:i A') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        @if($invoice->dispute_status === 'open')
                                            <span class="badge badge-danger mb-1 d-block">
                                                <i class="fas fa-exclamation-circle"></i> Open
                                            </span>
                                            @if($hoursRemaining > 0 || $minutesRemaining > 0)
                                                <small class="text-muted d-block">{{ str_pad($hoursRemaining, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutesRemaining, 2, '0', STR_PAD_LEFT) }}</small>
                                            @else
                                                <small class="text-muted d-block">Expired</small>
                                            @endif
                                        @elseif($invoice->dispute_status === 'resolved')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> Resolved
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Closed</span>
                                        @endif
                                    </div>
                                </div>

                                @if($invoice->dispute_note)
                                <div class="mb-2">
                                    <small class="text-muted d-block">Dispute Note</small>
                                    <p class="mb-0" style="word-break: break-word;">{{ $invoice->dispute_note }}</p>
                                </div>
                                @endif

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Time Remaining</small>
                                        @if($invoice->dispute_status === 'open' && ($hoursRemaining > 0 || $minutesRemaining > 0))
                                            <span class="badge badge-warning">{{ str_pad($hoursRemaining, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutesRemaining, 2, '0', STR_PAD_LEFT) }}</span>
                                        @elseif($invoice->dispute_status === 'open')
                                            <span class="badge badge-secondary">Expired</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Admin Response</small>
                                        @if($invoice->admin_response)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('customer.disputes.show', $invoice->id) }}" class="btn btn-sm btn-primary flex-fill">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    {{ $disputes->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    /* Desktop table styles */
    @media (min-width: 768px) {
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            padding: 8px 6px;
            font-size: 0.875rem;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody td {
            padding: 8px 6px;
            font-size: 0.875rem;
        }
    }
    /* Mobile styles */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 0.75rem;
        }
        .card.mb-3 {
            margin-bottom: 0.75rem !important;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }
    @media (max-width: 575.98px) {
        .flex-fill {
            flex: 1 1 auto;
        }
        .d-flex.gap-2 {
            flex-wrap: wrap;
        }
    }
</style>
@stop
