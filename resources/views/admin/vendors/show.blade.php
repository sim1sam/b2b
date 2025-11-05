@extends('adminlte::page')

@section('title', 'Vendor Details')

@section('content_header')
    <h1><i class="fas fa-store"></i> Vendor Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $vendor->vendor_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Vendor Name</th>
                            <td>{{ $vendor->vendor_name }}</td>
                        </tr>
                        <tr>
                            <th>GSTIN</th>
                            <td>{{ $vendor->gstin ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td>{{ $vendor->contact_number }}</td>
                        </tr>
                        <tr>
                            <th>Account Details</th>
                            <td>{{ $vendor->account_details ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>GPay/PhonePe Number</th>
                            <td>{{ $vendor->payment_number ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($vendor->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $vendor->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $vendor->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">QR Code</h3>
                </div>
                <div class="card-body text-center">
                    @if($vendor->qr_code)
                        <img src="{{ Storage::url($vendor->qr_code) }}" alt="QR Code" 
                             class="img-fluid img-thumbnail" style="max-width: 300px;">
                        <p class="mt-2">
                            <a href="{{ Storage::url($vendor->qr_code) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </p>
                    @else
                        <p class="text-muted">No QR code uploaded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

