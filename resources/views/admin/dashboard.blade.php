@extends('adminlte::page')

@push('js')
    @include('components.footer')
    @include('components.logo-link')
@endpush

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
@stop

@section('content')
    <!-- First Row: Available Funds, PO Fund, PO Value, GST Earning -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format((float)$availableFundsBDT, 2, '.', '') }}</h3>
                    <p>Available Funds (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <a href="{{ route('admin.funds.index') }}" class="small-box-footer">
                    View Funds <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format((float)$poFundBDT, 2, '.', '') }}</h3>
                    <p>PO Fund (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('admin.purchase-requests.index') }}?payment_status=pending" class="small-box-footer">
                    View POs <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>₹{{ number_format((float)$poValueINR, 2, '.', '') }}</h3>
                    <p>PO Value (INR)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.purchase-requests.index') }}?payment_status=pending" class="small-box-footer">
                    View POs <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format((float)$gstEarningBDT, 2, '.', '') }}</h3>
                    <p>GST Earning (BDT)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('admin.purchase-requests.index') }}?is_gst_payment=0&payment_status=pending" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Second Row: Deposit Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format((float)$pendingDeposits, 2, '.', '') }}</h3>
                    <p>Pending Deposits</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.funds.index') }}?type=deposit&status=pending" class="small-box-footer">
                    View Pending <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format((float)$todayDeposits, 2, '.', '') }}</h3>
                    <p>Today's Deposit</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="{{ route('admin.funds.index') }}?type=deposit&status=approved" class="small-box-footer">
                    View Transactions <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format((float)$thisMonthDeposits, 2, '.', '') }}</h3>
                    <p>This Month's Deposit</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('admin.funds.index') }}?type=deposit&status=approved" class="small-box-footer">
                    View Transactions <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format((float)$lastMonthDeposits, 2, '.', '') }}</h3>
                    <p>Last Month's Deposit</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('admin.funds.index') }}?type=deposit&status=approved" class="small-box-footer">
                    View Transactions <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Third Row: Purchase Request Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalPurchaseRequests }}</h3>
                    <p>Purchase Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list"></i>
                </div>
                <a href="{{ route('admin.purchase-requests.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingPurchaseRequests }}</h3>
                    <p>Pending Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.purchase-requests.index') }}?status=pending" class="small-box-footer">
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
                <a href="{{ route('admin.purchase-requests.index') }}?status=approved" class="small-box-footer">
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
                <a href="{{ route('admin.purchase-requests.index') }}?status=completed" class="small-box-footer">
                    View Completed <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Fourth Row: Invoice Metrics -->
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
                <a href="{{ route('admin.invoices.index') }}?payment_status=pending" class="small-box-footer">
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
                <a href="{{ route('admin.shipping-marks.index') }}" class="small-box-footer">
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
                <a href="{{ route('admin.invoices.index') }}?payment_status=paid&delivery_status=pending" class="small-box-footer">
                    View Pending <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $completedDeliveries }}</h3>
                    <p>Shipment Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <a href="{{ route('admin.invoices.index') }}?order_status=completed" class="small-box-footer">
                    View Completed <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Last 30 Days Graph -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Last 30 Days Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="last30DaysChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Clients with Order Trend -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users"></i> Top Clients</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Total Orders</th>
                                    <th>Total Amount (BDT)</th>
                                    <th>Recent Orders (30 Days)</th>
                                    <th>Recent Amount (BDT)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topClients as $client)
                                    <tr>
                                        <td>
                                            <strong>{{ $client->business_name ?? $client->name }}</strong><br>
                                            <small class="text-muted">{{ $client->email }}</small>
                                        </td>
                                        <td>{{ $client->total_orders }}</td>
                                        <td>{{ number_format((float)$client->total_amount, 2, '.', '') }}</td>
                                        <td>{{ $client->recent_orders }}</td>
                                        <td>{{ number_format((float)$client->recent_amount, 2, '.', '') }}</td>
                                        <td>
                                            <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.ledger.index', ['customer_id' => $client->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-book"></i> Ledger
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No clients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('last30DaysChart').getContext('2d');
    
    var chartData = @json($last30Days);
    
    var dates = chartData.map(item => item.date);
    var deposits = chartData.map(item => parseFloat(item.deposits));
    var purchaseOrders = chartData.map(item => parseFloat(item.purchase_orders));
    var invoiceValues = chartData.map(item => parseFloat(item.invoice_value));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Fund Deposits (BDT)',
                    data: deposits,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                },
                {
                    label: 'Purchase Orders (BDT)',
                    data: purchaseOrders,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                },
                {
                    label: 'Invoice Value (BDT)',
                    data: invoiceValues,
                    borderColor: 'rgb(255, 206, 86)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

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
