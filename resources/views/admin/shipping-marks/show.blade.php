@extends('adminlte::page')

@section('title', 'Shipping Mark Details')

@section('content_header')
    <h1><i class="fas fa-tag"></i> Shipping Mark: {{ $decodedMark }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Client: {{ $client->business_name ?? $client->name }}</h3>
                    <div class="card-tools">
                        @if(!$hasInvoice)
                            <form action="{{ route('admin.invoices.generate', urlencode($decodedMark)) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-invoice"></i> Generate Final Invoice
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.shipping-marks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <h4>Purchase Orders ({{ $purchaseRequests->count() }})</h4>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Vendor</th>
                                <th>Amount (BDT)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseRequests as $pr)
                                <tr>
                                    <td>{{ $pr->po_number }}</td>
                                    <td>{{ $pr->vendor->vendor_name }}</td>
                                    <td>{{ number_format($pr->amount_bdt ?? $pr->amount, 2) }}</td>
                                    <td>
                                        @if($pr->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-warning">{{ ucfirst($pr->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.purchase-requests.show', $pr->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($purchaseRequests->where('items', '!=', null)->where('items', '!=', [])->isNotEmpty())
                        <div class="mt-4">
                            <h4>Summary</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Total Amount:</th>
                                    <td><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Round Off:</th>
                                    <td>{{ number_format($roundOffAmount, 2) }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Final Amount:</th>
                                    <td><strong class="text-primary">{{ number_format($roundedTotal, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
