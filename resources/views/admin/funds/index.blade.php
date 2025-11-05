@extends('adminlte::page')

@section('title', 'Funds')

@section('content_header')
    <h1><i class="fas fa-wallet"></i> Funds</h1>
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

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalPending }}</h3>
                    <p>Pending Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalApproved }}</h3>
                    <p>Approved Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalRejected }}</h3>
                    <p>Rejected Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>৳{{ number_format($pendingDeposits, 2) }}</h3>
                    <p>Pending Deposits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.funds.index') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search Customer</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Email or Name">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Fund Transactions</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Screenshot</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>
                                <strong>{{ $transaction->user->name }}</strong><br>
                                <small class="text-muted">{{ $transaction->user->email }}</small>
                            </td>
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
                            <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.funds.show', $transaction->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($transaction->type === 'deposit' && $transaction->status === 'pending')
                                    <a href="{{ route('admin.funds.edit', $transaction->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
@stop

