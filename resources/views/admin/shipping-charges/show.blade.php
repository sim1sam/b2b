@extends('adminlte::page')

@section('title', 'Shipping Charge Details')

@section('content_header')
    <h1><i class="fas fa-shipping-fast"></i> Shipping Charge Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $shippingCharge->item_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.shipping-charges.edit', $shippingCharge->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.shipping-charges.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Client</th>
                            <td>
                                <strong>{{ $shippingCharge->user->business_name ?? $shippingCharge->user->name }}</strong><br>
                                <small class="text-muted">{{ $shippingCharge->user->email }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Item Name</th>
                            <td><strong>{{ $shippingCharge->item_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Rate per Unit</th>
                            <td><strong>{{ number_format($shippingCharge->rate_per_unit, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Unit Type</th>
                            <td>
                                @if($shippingCharge->unit_type === 'qty')
                                    <span class="badge badge-info">Quantity</span>
                                @else
                                    <span class="badge badge-warning">Weight</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Shipping Charge per Kg</th>
                            <td><strong>{{ number_format($shippingCharge->shipping_charge_per_kg, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($shippingCharge->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $shippingCharge->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $shippingCharge->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

