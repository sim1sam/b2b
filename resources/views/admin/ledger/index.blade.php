@extends('adminlte::page')

@section('title', 'Customer Ledger Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-book"></i> Customer Ledger Report</h1>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Select Customer</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ledger.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $selectedCustomerId == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->business_name ?? $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedCustomer && $ledgerData)
        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6 mb-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $ledgerData['totalPurchases'] }}</h3>
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
                        <h3>{{ number_format((float)$ledgerData['totalPurchaseAmount'], 2, '.', '') }}</h3>
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
                        <h3>{{ $ledgerData['totalInvoices'] }}</h3>
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
                        <h3>{{ number_format((float)$ledgerData['totalInvoiceAmount'], 2, '.', '') }}</h3>
                        <p>Invoice Amount</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6 mb-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format((float)$ledgerData['totalDeposits'], 2, '.', '') }}</h3>
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
                        <h3>{{ number_format((float)($ledgerData['totalWithdrawals'] + $ledgerData['totalPurchasePayments'] + $ledgerData['totalInvoicePayments']), 2, '.', '') }}</h3>
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
                        <h3>{{ $ledgerData['totalTransactions'] }}</h3>
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
                        <h3>{{ number_format((float)$ledgerData['currentBalance'], 2, '.', '') }}</h3>
                        <p>Current Balance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.ledger.view', ['customer' => $selectedCustomer->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
<style>
    .gap-2 {
        gap: 0.5rem;
    }
    .small-box {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
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
    @media (max-width: 767.98px) {
        .small-box .inner h3 {
            font-size: 1.5rem;
        }
    }
</style>
@stop

