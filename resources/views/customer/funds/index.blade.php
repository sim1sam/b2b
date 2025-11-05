@extends('adminlte::page')

@section('title', 'Funds')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-wallet"></i> Funds</h1>
        <a href="{{ route('customer.funds.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add
        </a>
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

    <!-- Balance Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>৳{{ number_format($availableBalance, 2) }}</h3>
                    <p>Available Balance</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>৳{{ number_format($totalDeposits, 2) }}</h3>
                    <p>Total Deposits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>৳{{ number_format($totalSpent, 2) }}</h3>
                    <p>Total Spent</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>৳{{ number_format($pendingDeposits, 2) }}</h3>
                    <p>Pending Deposits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Transaction History</h3>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="d-none d-md-block">
                <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Screenshot</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($transaction->type === 'deposit')
                                        <span class="badge badge-success">Deposit</span>
                                    @elseif($transaction->type === 'purchase')
                                        <span class="badge badge-primary">Purchase</span>
                                    @else
                                        <span class="badge badge-warning">Withdrawal</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->type === 'deposit')
                                        <strong class="text-success">+৳{{ number_format($transaction->amount, 2) }}</strong>
                                    @else
                                        <strong class="text-danger">-৳{{ number_format($transaction->amount, 2) }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($transaction->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->payment_screenshot)
                                        <a href="{{ Storage::url($transaction->payment_screenshot) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-image"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->notes ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('customer.funds.show', $transaction->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No transactions found. <a href="{{ route('customer.funds.create') }}">Add funds now</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($transactions as $transaction)
                    <div class="card mb-3 border-left-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1 font-weight-bold">
                                        Transaction #{{ $transaction->id }}
                                    </h5>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-calendar"></i> {{ $transaction->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($transaction->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($transaction->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Type</small>
                                    @if($transaction->type === 'deposit')
                                        <span class="badge badge-success">Deposit</span>
                                    @elseif($transaction->type === 'purchase')
                                        <span class="badge badge-primary">Purchase</span>
                                    @else
                                        <span class="badge badge-warning">Withdrawal</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Amount</small>
                                    @if($transaction->type === 'deposit')
                                        <strong class="text-success">+৳{{ number_format($transaction->amount, 2) }}</strong>
                                    @else
                                        <strong class="text-danger">-৳{{ number_format($transaction->amount, 2) }}</strong>
                                    @endif
                                </div>
                            </div>

                            @if($transaction->payment_screenshot)
                            <div class="mb-2">
                                <small class="text-muted d-block">Payment Screenshot</small>
                                <a href="{{ Storage::url($transaction->payment_screenshot) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-image"></i> View Screenshot
                                </a>
                            </div>
                            @endif

                            @if($transaction->notes)
                            <div class="mb-2">
                                <small class="text-muted d-block">Notes</small>
                                <span>{{ $transaction->notes }}</span>
                            </div>
                            @endif

                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('customer.funds.show', $transaction->id) }}" class="btn btn-sm btn-info flex-fill">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No transactions found.</p>
                        <a href="{{ route('customer.funds.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add funds now
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
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
    /* Remove scrollbars */
    .card-body {
        overflow-x: hidden;
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
        .small-box {
            margin-bottom: 1rem;
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

