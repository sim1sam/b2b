@extends('adminlte::page')

@section('title', 'Ledger Report')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <h1 class="mb-2 mb-md-0"><i class="fas fa-book"></i> Ledger Report</h1>
        <div>
            <a href="{{ route('customer.ledger.view', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> View
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Date Filter -->
    <div class="card mb-3 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('customer.ledger.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('customer.ledger.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-3 no-print">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPurchases }}</h3>
                    <p>Total Purchases</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format((float)$totalPurchaseAmount, 2, '.', '') }}</h3>
                    <p>Purchase Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalInvoices }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format((float)$totalInvoiceAmount, 2, '.', '') }}</h3>
                    <p>Invoice Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-3 no-print">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format((float)$totalDeposits, 2, '.', '') }}</h3>
                    <p>Total Deposits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format((float)($totalWithdrawals + $totalPurchasePayments + $totalInvoicePayments), 2, '.', '') }}</h3>
                    <p>Total Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalTransactions }}</h3>
                    <p>Total Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format((float)$currentBalance, 2, '.', '') }}</h3>
                    <p>Current Balance</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fas fa-exchange-alt"></i> Transaction History</h3>
        </div>
        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
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
                                            <span class="text-success font-weight-bold">+{{ number_format($transaction->amount, 2) }}</span>
                                        @else
                                            <span class="text-danger font-weight-bold">-{{ number_format(abs($transaction->amount), 2) }}</span>
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
                                    <td>{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @foreach($transactions as $transaction)
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
                                            <span class="text-success font-weight-bold">+{{ number_format($transaction->amount, 2) }}</span>
                                        @else
                                            <span class="text-danger font-weight-bold">-{{ number_format(abs($transaction->amount), 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">{{ $transaction->created_at->format('d M Y, h:i A') }}</small>
                                    @if($transaction->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($transaction->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </div>
                                @if($transaction->notes)
                                    <p class="mb-0 small"><strong>Notes:</strong> {{ $transaction->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-3 text-center">
                    <p class="text-muted mb-0">No transactions found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fas fa-shopping-cart"></i> Purchase History</h3>
        </div>
        <div class="card-body p-0">
            @if($purchases->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Request #</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('customer.purchase-requests.show', $purchase->id) }}">
                                            {{ $purchase->request_number }}
                                        </a>
                                    </td>
                                    <td>{{ $purchase->vendor->vendor_name ?? '-' }}</td>
                                    <td>{{ number_format($purchase->amount, 2) }}</td>
                                    <td>
                                        @if($purchase->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($purchase->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($purchase->status === 'completed')
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
                    @foreach($purchases as $purchase)
                        <div class="card mb-2 border-left-primary mx-2 mt-2">
                            <div class="card-body">
                                <h6 class="mb-1 font-weight-bold">
                                    <a href="{{ route('customer.purchase-requests.show', $purchase->id) }}">
                                        {{ $purchase->request_number }}
                                    </a>
                                </h6>
                                <p class="mb-1 small"><strong>Vendor:</strong> {{ $purchase->vendor->vendor_name ?? '-' }}</p>
                                <p class="mb-1 small"><strong>Amount:</strong> {{ number_format($purchase->amount, 2) }}</p>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">{{ $purchase->created_at->format('d M Y') }}</small>
                                    @if($purchase->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($purchase->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @elseif($purchase->status === 'completed')
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
                <div class="p-3 text-center">
                    <p class="text-muted mb-0">No purchases found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fas fa-file-invoice-dollar"></i> Invoice History</h3>
        </div>
        <div class="card-body p-0">
            @if($invoices->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('customer.invoices.show', $invoice->id) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($invoice->rounded_total, 2) }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @foreach($invoices as $invoice)
                        <div class="card mb-2 border-left-primary mx-2 mt-2">
                            <div class="card-body">
                                <h6 class="mb-1 font-weight-bold">
                                    <a href="{{ route('customer.invoices.show', $invoice->id) }}">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </h6>
                                <p class="mb-1 small"><strong>Amount:</strong> {{ number_format($invoice->rounded_total, 2) }}</p>
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
                <div class="p-3 text-center">
                    <p class="text-muted mb-0">No invoices found.</p>
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
        .form-label {
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
    }
    /* Print Styles */
    @media print {
        .btn, .main-header, .main-sidebar, .main-footer, .navbar, .d-md-none {
            display: none !important;
        }
        /* Hide summary cards and date filter */
        .no-print, .no-print * {
            display: none !important;
        }
        /* Show content header for print */
        .content-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            page-break-after: avoid;
        }
        .content-header h1 {
            margin: 0;
            font-size: 20px;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            page-break-inside: avoid;
        }
        .card-body {
            padding: 10px !important;
            background: white !important;
        }
        * {
            color: #000 !important;
            background: white !important;
        }
        .text-primary, .text-danger, .text-muted, .text-info, .text-success {
            color: #000 !important;
        }
        .bg-info, .bg-success, .bg-primary, .bg-warning, .bg-danger {
            background-color: #f8f9fa !important;
            border: 1px solid #000 !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
            page-break-inside: auto;
        }
        table thead {
            display: table-header-group;
        }
        table tbody tr {
            page-break-inside: avoid;
        }
        table td, table th {
            border: 1px solid #000 !important;
            padding: 6px !important;
            font-size: 0.75rem !important;
        }
        .badge {
            border: 1px solid #000 !important;
            padding: 2px 6px !important;
            background: white !important;
            color: #000 !important;
        }
        hr {
            border-top: 2px solid #000 !important;
            margin: 15px 0 !important;
        }
        @page {
            margin: 1cm;
            size: A4;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12px !important;
        }
        .mb-0, .mb-1, .mb-2, .mb-3, .mb-4 {
            margin-bottom: 0.5rem !important;
        }
        .mt-4 {
            margin-top: 1rem !important;
        }
        h1, h2, h3, h4, h5, h6 {
            page-break-after: avoid;
        }
        .row {
            page-break-inside: avoid;
        }
    }
</style>
@stop

