@extends('adminlte::page')

@section('title', 'Funds')

@section('content_header')
    <h1><i class="fas fa-wallet"></i> Funds</h1>
    <a href="{{ route('customer.funds.create') }}" class="btn btn-primary float-right">
        <i class="fas fa-plus"></i> Add Funds
    </a>
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
        <div class="card-body">
            <table class="table table-bordered table-striped">
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

            {{ $transactions->links() }}
        </div>
    </div>
@stop

