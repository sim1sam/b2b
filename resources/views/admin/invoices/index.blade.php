@extends('adminlte::page')

@section('title', 'Invoices')

@section('content_header')
    <h1><i class="fas fa-file-invoice-dollar"></i> Invoices</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if($invoices->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No invoices found.
                </div>
            @else
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Client</th>
                            <th>Shipping Mark</th>
                            <th>Purchase Orders</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Delivery Request</th>
                            <th>Delivery Status</th>
                            <th>Order Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->user->business_name ?? $invoice->user->name }}</td>
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
                                    @if($invoice->delivery_request_status === 'requested')
                                        <span class="badge badge-info"><i class="fas fa-truck"></i> Requested</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->delivery_status === 'delivered')
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Delivered</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->order_status === 'completed')
                                        <span class="badge badge-primary"><i class="fas fa-check-double"></i> Completed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
