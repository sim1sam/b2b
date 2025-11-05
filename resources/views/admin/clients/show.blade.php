@extends('adminlte::page')

@section('title', 'Client Details')

@section('content_header')
    <h1><i class="fas fa-user"></i> Client Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $client->business_name ?? $client->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Client Name</th>
                            <td><strong>{{ $client->business_name ?? $client->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Contact Person Name</th>
                            <td>{{ $client->contact_person_name ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $client->email }}</td>
                        </tr>
                        <tr>
                            <th>Mobile Number</th>
                            <td>{{ $client->mobile_number ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $client->address ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Exchange Rate (BDT to INR)</th>
                            <td>
                                @if($client->exchange_rate)
                                    <span class="badge badge-success" style="font-size: 1.1em;">
                                        1 BDT = ₹{{ number_format($client->exchange_rate, 4) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Lowest Shipping Charge per Kg (BDT)</th>
                            <td>
                                @if($client->lowest_shipping_charge_per_kg)
                                    <span class="badge badge-info" style="font-size: 1.1em;">
{{ number_format($client->lowest_shipping_charge_per_kg, 2) }}/Kg
                                    </span>
                                    <br><small class="text-muted">Used for packaging cost calculation</small>
                                @else
                                    <span class="text-danger">Not Set</span>
                                    <br><small class="text-muted">Please configure this in client settings</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Registered Name</th>
                            <td>{{ $client->name }}</td>
                        </tr>
                        <tr>
                            <th>Registered At</th>
                            <td>{{ $client->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($client->logo)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Logo</h3>
                </div>
                <div class="card-body text-center">
                    <img src="{{ Storage::url($client->logo) }}" alt="Logo" 
                         class="img-fluid img-thumbnail" style="max-width: 100%;">
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

