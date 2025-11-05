@extends('adminlte::page')

@push('js')
    @include('components.footer')
@endpush

@section('title', 'Customer Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-user"></i> Customer Dashboard</h1>
    </div>
@stop

@section('content')
    <!-- Fund Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>৳{{ number_format((float)$availableBalance, 2, '.', '') }}</h3>
                    <p>Available Balance</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <a href="{{ route('customer.funds.index') }}" class="small-box-footer">
                    View Funds <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>৳{{ number_format((float)$totalDeposits, 2, '.', '') }}</h3>
                    <p>Total Deposits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <a href="{{ route('customer.funds.index') }}" class="small-box-footer">
                    View Transactions <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>৳{{ number_format((float)$totalSpent, 2, '.', '') }}</h3>
                    <p>Total Spent</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('customer.purchase-requests.index') }}" class="small-box-footer">
                    View Purchases <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalPurchaseRequests }}</h3>
                    <p>Purchase Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list"></i>
                </div>
                <a href="{{ route('customer.purchase-requests.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Purchase Request Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingPurchaseRequests }}</h3>
                    <p>Pending Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $approvedPurchaseRequests }}</h3>
                    <p>Approved Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $completedPurchaseRequests }}</h3>
                    <p>Completed Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $activeVendors }}</h3>
                    <p>Active Vendors</p>
                </div>
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <a href="{{ route('customer.vendors.index') }}" class="small-box-footer">
                    View Vendors <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalInvoices }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('customer.invoices.index') }}" class="small-box-footer">
                    View Invoices <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $paidInvoices }}</h3>
                    <p>Paid Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('customer.invoices.index') }}" class="small-box-footer">
                    View Invoices <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingInvoices }}</h3>
                    <p>Pending Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $deliveredInvoices }}</h3>
                    <p>Delivered Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $completedOrders }}</h3>
                    <p>Completed Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $openDisputes }}</h3>
                    <p>Open Disputes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                @if($openDisputes > 0)
                    <a href="{{ route('customer.invoices.index') }}" class="small-box-footer">
                        Review Now <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-shopping-cart"></i> Recent Purchase Requests</h3>
                    <a href="{{ route('customer.purchase-requests.index') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentPurchaseRequests->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Vendor</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPurchaseRequests as $request)
                                        <tr>
                                            <td>
                                                <a href="{{ route('customer.purchase-requests.show', $request) }}">
                                                    {{ $request->request_number }}
                                                </a>
                                            </td>
                                            <td>{{ $request->vendor->vendor_name }}</td>
                                            <td>৳{{ number_format($request->amount, 2) }}</td>
                                            <td>
                                                @if($request->status === 'approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($request->status === 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @elseif($request->status === 'completed')
                                                    <span class="badge badge-primary">Completed</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Mobile Card View -->
                        <div class="d-md-none">
                            @foreach($recentPurchaseRequests as $request)
                                <div class="card mb-2 border-left-primary mx-2 mt-2">
                                    <div class="card-body">
                                        <h6 class="mb-1 font-weight-bold">
                                            <a href="{{ route('customer.purchase-requests.show', $request) }}">
                                                {{ $request->request_number }}
                                            </a>
                                        </h6>
                                        <p class="mb-1 small"><strong>Vendor:</strong> {{ $request->vendor->vendor_name }}</p>
                                        <p class="mb-1 small"><strong>Amount:</strong> ৳{{ number_format($request->amount, 2) }}</p>
                                        <div>
                                            @if($request->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($request->status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($request->status === 'completed')
                                                <span class="badge badge-primary">Completed</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3">
                            <p class="text-muted mb-0">No purchase requests yet. <a href="{{ route('customer.purchase-requests.create') }}">Create one now</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-exchange-alt"></i> Recent Transactions</h3>
                    <a href="{{ route('customer.funds.index') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="badge badge-success">Deposit</span>
                                                @elseif($transaction->type === 'purchase')
                                                    <span class="badge badge-primary">Purchase</span>
                                                @elseif($transaction->type === 'invoice_payment')
                                                    <span class="badge badge-info">Invoice Payment</span>
                                                @else
                                                    <span class="badge badge-warning">Withdrawal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="text-success">+৳{{ number_format($transaction->amount, 2) }}</span>
                                                @else
                                                    <span class="text-danger">-৳{{ number_format(abs($transaction->amount), 2) }}</span>
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
                                            <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Mobile Card View -->
                        <div class="d-md-none">
                            @foreach($recentTransactions as $transaction)
                                <div class="card mb-2 border-left-primary mx-2 mt-2">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                @if($transaction->type === 'deposit')
                                                    <span class="badge badge-success">Deposit</span>
                                                @elseif($transaction->type === 'purchase')
                                                    <span class="badge badge-primary">Purchase</span>
                                                @elseif($transaction->type === 'invoice_payment')
                                                    <span class="badge badge-info">Invoice Payment</span>
                                                @else
                                                    <span class="badge badge-warning">Withdrawal</span>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                @if($transaction->type === 'deposit')
                                                    <span class="text-success font-weight-bold">+৳{{ number_format($transaction->amount, 2) }}</span>
                                                @else
                                                    <span class="text-danger font-weight-bold">-৳{{ number_format(abs($transaction->amount), 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $transaction->created_at->format('d M Y') }}</small>
                                            @if($transaction->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($transaction->status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3">
                            <p class="text-muted mb-0">No transactions yet. <a href="{{ route('customer.funds.create') }}">Add funds now</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-file-invoice-dollar"></i> Recent Invoices</h3>
                    <a href="{{ route('customer.invoices.index') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentInvoices->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('customer.invoices.show', $invoice->id) }}">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>৳{{ number_format($invoice->rounded_total, 2) }}</td>
                                            <td>
                                                @if($invoice->payment_status === 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($invoice->order_status === 'completed')
                                                    <span class="badge badge-primary">Completed</span>
                                                @elseif($invoice->dispute_status === 'open')
                                                    <span class="badge badge-danger">Dispute</span>
                                                @elseif($invoice->delivery_status === 'delivered')
                                                    <span class="badge badge-info">Delivered</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Mobile Card View -->
                        <div class="d-md-none">
                            @foreach($recentInvoices as $invoice)
                                <div class="card mb-2 border-left-primary mx-2 mt-2">
                                    <div class="card-body">
                                        <h6 class="mb-1 font-weight-bold">
                                            <a href="{{ route('customer.invoices.show', $invoice->id) }}">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </h6>
                                        <p class="mb-1 small"><strong>Amount:</strong> ৳{{ number_format($invoice->rounded_total, 2) }}</p>
                                        <div class="d-flex gap-2 mb-1">
                                            <div>
                                                <small class="text-muted d-block">Payment</small>
                                                @if($invoice->payment_status === 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Status</small>
                                                @if($invoice->order_status === 'completed')
                                                    <span class="badge badge-primary">Completed</span>
                                                @elseif($invoice->dispute_status === 'open')
                                                    <span class="badge badge-danger">Dispute</span>
                                                @elseif($invoice->delivery_status === 'delivered')
                                                    <span class="badge badge-info">Delivered</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $invoice->invoice_date->format('d M Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3">
                            <p class="text-muted mb-0">No invoices yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-user-circle"></i> Account Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                            <p><strong>Role:</strong> <span class="badge badge-primary">{{ ucfirst(Auth::user()->role) }}</span></p>
                            <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Vendors:</strong> {{ $totalVendors }}</p>
                            <p><strong>Active Vendors:</strong> {{ $activeVendors }}</p>
                            <p><strong>Total Purchase Requests:</strong> {{ $totalPurchaseRequests }}</p>
                            <p><strong>Available Balance:</strong> <span class="text-success"><strong>৳{{ number_format((float)$availableBalance, 2, '.', '') }}</strong></span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5>Quick Actions</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('customer.funds.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Add Funds
                                </a>
                                <a href="{{ route('customer.purchase-requests.create') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-shopping-cart"></i> Create Purchase Request
                                </a>
                                <a href="{{ route('customer.vendors.create') }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-store"></i> Add Vendor
                                </a>
                                <a href="{{ route('customer.funds.index') }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-wallet"></i> View Funds
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
    .small-box {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    /* Balance Cards - Consistent formatting */
    .small-box .inner h3 {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .small-box .inner p {
        margin: 5px 0 0 0;
        font-size: 0.875rem;
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
        .card.mb-2 {
            margin-bottom: 0.5rem !important;
        }
        .small-box {
            margin-bottom: 0.75rem !important;
        }
        .small-box .inner h3 {
            font-size: 1.5rem;
        }
        .row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        .row > [class*="col-"] {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .btn {
            margin-bottom: 0.25rem;
        }
    }
    @media (max-width: 575.98px) {
        .flex-fill {
            flex: 1 1 auto;
        }
        .d-flex.gap-2 {
            flex-wrap: wrap;
        }
        .small-box .inner h3 {
            font-size: 1.25rem;
        }
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .card-header .btn {
            margin-top: 0.5rem;
            width: 100%;
        }
    }
</style>
@stop
