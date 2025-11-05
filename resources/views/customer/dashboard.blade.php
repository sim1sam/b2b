@extends('adminlte::page')

@push('js')
    @include('components.footer')
    @include('components.logo-link')
@endpush

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    </div>
@stop

@section('content')
    <!-- First Row: Available Funds, Purchase (INR), Purchase (BDT), Invoices (BDT) -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format((float)$availableBalance, 2, '.', '') }}</h3>
                    <p>Available Funds</p>
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
                    <h3>₹{{ number_format((float)$totalPurchaseINR, 2, '.', '') }}</h3>
                    <p>Purchase (INR)</p>
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
                    <h3>{{ number_format((float)$totalPurchaseBDT, 2, '.', '') }}</h3>
                    <p>Purchase (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <a href="{{ route('customer.purchase-requests.index') }}" class="small-box-footer">
                    View Purchases <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format((float)$totalInvoicesBDT, 2, '.', '') }}</h3>
                    <p>Invoices (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('customer.invoices.index') }}" class="small-box-footer">
                    View Invoices <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Second Row: Pending Requests, Approved Requests, Completed Requests, Purchase Requests -->
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
                <a href="{{ route('customer.purchase-requests.index', ['status' => 'pending']) }}" class="small-box-footer">
                    View Pending <i class="fas fa-arrow-circle-right"></i>
                </a>
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
                <a href="{{ route('customer.purchase-requests.index', ['status' => 'approved']) }}" class="small-box-footer">
                    View Approved <i class="fas fa-arrow-circle-right"></i>
                </a>
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
                <a href="{{ route('customer.purchase-requests.index', ['status' => 'completed']) }}" class="small-box-footer">
                    View Completed <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
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

    <!-- Third Row: Due Invoices, Provisional Invoices, Delivery Pending, Shipment Completed -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format((float)$dueInvoicesBDT, 2, '.', '') }}</h3>
                    <p>Due Invoices (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('customer.invoices.index') }}?payment_status=pending" class="small-box-footer">
                    View Due <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format((float)$provisionalInvoicesBDT, 2, '.', '') }}</h3>
                    <p>Provisional Invoices (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <a href="{{ route('customer.purchase-requests.index') }}" class="small-box-footer">
                    View Provisional <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $deliveryPending }}</h3>
                    <p>Delivery Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                <a href="{{ route('customer.invoices.index') }}?payment_status=paid&delivery_status=pending" class="small-box-footer">
                    View Pending <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $shipmentCompleted }}</h3>
                    <p>Shipment Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <a href="{{ route('customer.invoices.index') }}?order_status=completed" class="small-box-footer">
                    View Completed <i class="fas fa-arrow-circle-right"></i>
                </a>
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
    @media (max-width: 575.98px) {
        .small-box .inner h3 {
            font-size: 1.25rem;
        }
    }
</style>
@stop
