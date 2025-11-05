@extends('adminlte::page')

@section('title', 'Shipping Marks')

@section('content_header')
    <h1><i class="fas fa-tags"></i> Shipping Marks</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if($shippingMarks->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No shipping marks found. Please add shipping marks to purchase orders.
                </div>
            @else
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Shipping Mark</th>
                            <th>Client</th>
                            <th>Purchase Orders</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shippingMarks as $markData)
                            <tr>
                                <td><strong>{{ $markData['mark'] }}</strong></td>
                                <td>{{ $markData['client']->business_name ?? $markData['client']->name }}</td>
                                <td>{{ $markData['count'] }} order(s)</td>
                                <td>
                                    @if($markData['has_invoice'])
                                        <span class="badge badge-success">Invoice Generated</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.shipping-marks.show', urlencode($markData['mark'])) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop
