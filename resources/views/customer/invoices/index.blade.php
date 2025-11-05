@extends('adminlte::page')

@section('title', 'My Invoices')

@section('content_header')
    <h1><i class="fas fa-file-invoice-dollar"></i> My Invoices</h1>
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
        <div class="card-body">
            @if($invoices->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No invoices found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
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
                                    <td>
                                        <a href="{{ route('customer.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
