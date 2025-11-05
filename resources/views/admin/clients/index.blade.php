@extends('adminlte::page')

@section('title', 'Clients')

@section('content_header')
    <h1><i class="fas fa-users"></i> Clients</h1>
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

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Clients List</h3>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Client
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Client Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Exchange Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>
                                @if($client->logo)
                                    <img src="{{ Storage::url($client->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: cover;" class="img-thumbnail">
                                @else
                                    <span class="text-muted">No Logo</span>
                                @endif
                            </td>
                            <td><strong>{{ $client->business_name ?? $client->name }}</strong></td>
                            <td>{{ $client->contact_person_name ?? 'N/A' }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->mobile_number ?? 'N/A' }}</td>
                            <td>
                                @if($client->exchange_rate)
                                    <span class="badge badge-info">1 BDT = ₹{{ number_format($client->exchange_rate, 4) }}</span>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <div class="mt-3">
                {{ $clients->links() }}
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

