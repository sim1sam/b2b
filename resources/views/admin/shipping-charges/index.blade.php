@extends('adminlte::page')

@section('title', 'Shipping Charges')

@section('content_header')
    <h1><i class="fas fa-shipping-fast"></i> Shipping Charges</h1>
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.shipping-charges.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client_id">Filter by Client:</label>
                            <select name="client_id" id="client_id" class="form-control">
                                <option value="">-- All Clients --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->business_name ?? $client->name }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply Filters</button>
                            <a href="{{ route('admin.shipping-charges.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Shipping Charges List</h3>
                <a href="{{ route('admin.shipping-charges.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Shipping Charge
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Item Name</th>
                            <th>Rate per Unit</th>
                            <th>Unit Type</th>
                            <th>Shipping Charge/Kg</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shippingCharges as $charge)
                            <tr>
                                <td>
                                    <strong>{{ $charge->user->business_name ?? $charge->user->name }}</strong><br>
                                    <small class="text-muted">{{ $charge->user->email }}</small>
                                </td>
                                <td><strong>{{ $charge->item_name }}</strong></td>
                                <td>{{ number_format($charge->rate_per_unit, 2) }}</td>
                                <td>
                                    @if($charge->unit_type === 'qty')
                                        <span class="badge badge-info">Quantity</span>
                                    @else
                                        <span class="badge badge-warning">Weight</span>
                                    @endif
                                </td>
                                <td>{{ number_format($charge->shipping_charge_per_kg, 2) }}</td>
                                <td>
                                    @if($charge->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <a href="{{ route('admin.shipping-charges.show', $charge->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.shipping-charges.edit', $charge->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.shipping-charges.destroy', $charge->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this shipping charge?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No shipping charges found. <a href="{{ route('admin.shipping-charges.create') }}">Create one now</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $shippingCharges->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    /* Responsive table wrapper */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Tablet and mobile view - allow horizontal scroll */
    @media (max-width: 991px) {
        .table {
            min-width: 800px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .card-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header .btn {
            margin-top: 10px;
            width: 100%;
        }
    }
    
    /* Mobile view */
    @media (max-width: 576px) {
        .card-header h3 {
            font-size: 1.1rem;
        }
        
        .table td {
            font-size: 0.875rem;
        }
        
        .table .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@stop

