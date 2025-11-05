@extends('adminlte::page')

@section('title', 'My Invoices')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> My Invoices</h1>
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

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Available Balance</span>
                    <span class="info-box-number">৳{{ number_format($availableBalance, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($invoices->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No invoices found.</p>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Shipping Mark</th>
                                <th>Purchase Orders</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Delivery Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>{{ $invoice->shipping_mark }}</td>
                                    <td>{{ $invoice->purchaseRequests->count() }} order(s)</td>
                                    <td><strong>৳{{ number_format($invoice->rounded_total, 2) }}</strong></td>
                                    <td>
                                        @if($invoice->payment_status === 'paid')
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->delivery_status === 'delivered')
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Package Received</span>
                                        @elseif($invoice->delivery_request_status === 'requested')
                                            <span class="badge badge-info"><i class="fas fa-truck"></i> Requested</span>
                                        @elseif($invoice->payment_status === 'paid')
                                            <span class="badge badge-secondary">Not Requested</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td style="white-space: nowrap;">
                                        <a href="{{ route('customer.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($invoice->dispute_window_open ?? false)
                                            <a href="{{ route('customer.invoices.show', $invoice->id) }}#dispute-section" class="btn btn-sm btn-warning" title="Dispute Window Open - Time Remaining: {{ str_pad($invoice->hours_remaining ?? 0, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($invoice->minutes_remaining ?? 0, 2, '0', STR_PAD_LEFT) }}">
                                                <i class="fas fa-exclamation-triangle"></i> Dispute
                                            </a>
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
                        <div class="card mb-3 border-left-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1 font-weight-bold">
                                            {{ $invoice->invoice_number }}
                                        </h5>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-box"></i> {{ $invoice->purchaseRequests->count() }} order(s)
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        @if($invoice->dispute_window_open ?? false)
                                            <span class="badge badge-warning mb-1 d-block">
                                                <i class="fas fa-exclamation-triangle"></i> Dispute Open
                                            </span>
                                            <small class="text-muted d-block">{{ str_pad($invoice->hours_remaining ?? 0, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($invoice->minutes_remaining ?? 0, 2, '0', STR_PAD_LEFT) }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Shipping Mark</small>
                                        <span>{{ $invoice->shipping_mark }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Total Amount</small>
                                        <strong class="text-success">৳{{ number_format($invoice->rounded_total, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Payment</small>
                                        @if($invoice->payment_status === 'paid')
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Delivery</small>
                                        @if($invoice->delivery_status === 'delivered')
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Received</span>
                                        @elseif($invoice->delivery_request_status === 'requested')
                                            <span class="badge badge-info"><i class="fas fa-truck"></i> Requested</span>
                                        @elseif($invoice->payment_status === 'paid')
                                            <span class="badge badge-secondary">Not Requested</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block">Date</small>
                                    <span>{{ $invoice->invoice_date->format('d M Y') }}</span>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('customer.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary flex-fill">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($invoice->dispute_window_open ?? false)
                                        <a href="{{ route('customer.invoices.show', $invoice->id) }}#dispute-section" class="btn btn-sm btn-warning flex-fill">
                                            <i class="fas fa-exclamation-triangle"></i> Dispute
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    {{ $invoices->links() }}
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
