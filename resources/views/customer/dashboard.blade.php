@extends('adminlte::page')

@push('js')
    @include('components.footer')
@endpush

@section('title', 'Customer Dashboard')

@section('content_header')
    <h1><i class="fas fa-user"></i> Customer Dashboard</h1>
@stop

@section('content')
    <!-- Fund Statistics -->
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
                <a href="{{ route('customer.funds.index') }}" class="small-box-footer">
                    View Funds <i class="fas fa-arrow-circle-right"></i>
                </a>
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
                <a href="{{ route('customer.funds.index') }}" class="small-box-footer">
                    View Transactions <i class="fas fa-arrow-circle-right"></i>
                </a>
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
                <a href="{{ route('customer.purchase-requests.index') }}" class="small-box-footer">
                    View Purchases <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
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
        
        <div class="col-lg-3 col-6">
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Recent Purchase Requests</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.purchase-requests.index') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentPurchaseRequests->count() > 0)
                        <table class="table table-bordered table-sm">
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
                    @else
                        <p class="text-muted">No purchase requests yet. <a href="{{ route('customer.purchase-requests.create') }}">Create one now</a></p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Recent Transactions</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.funds.index') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <table class="table table-bordered table-sm">
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
                    @else
                        <p class="text-muted">No transactions yet. <a href="{{ route('customer.funds.create') }}">Add funds now</a></p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Recent Invoices</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.invoices.index') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentInvoices->count() > 0)
                        <table class="table table-bordered table-sm">
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
                    @else
                        <p class="text-muted">No invoices yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-circle"></i> Account Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                            <p><strong>Role:</strong> <span class="badge badge-primary">{{ ucfirst(Auth::user()->role) }}</span></p>
                            <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Vendors:</strong> {{ $totalVendors }}</p>
                            <p><strong>Active Vendors:</strong> {{ $activeVendors }}</p>
                            <p><strong>Total Purchase Requests:</strong> {{ $totalPurchaseRequests }}</p>
                            <p><strong>Available Balance:</strong> <span class="text-success"><strong>৳{{ number_format($availableBalance, 2) }}</strong></span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Quick Actions</h5>
                            <a href="{{ route('customer.funds.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Funds
                            </a>
                            <a href="{{ route('customer.purchase-requests.create') }}" class="btn btn-success">
                                <i class="fas fa-shopping-cart"></i> Create Purchase Request
                            </a>
                            <a href="{{ route('customer.vendors.create') }}" class="btn btn-info">
                                <i class="fas fa-store"></i> Add Vendor
                            </a>
                            <a href="{{ route('customer.funds.index') }}" class="btn btn-warning">
                                <i class="fas fa-wallet"></i> View Funds
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('css')
    <style>
        .small-box {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@stop
