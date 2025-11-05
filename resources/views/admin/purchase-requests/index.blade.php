@extends('adminlte::page')

@section('title', 'Purchase Requests')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Purchase Requests</h1>
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
            <form method="GET" action="{{ route('admin.purchase-requests.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client_id">Filter by Client</label>
                            <select name="client_id" id="client_id" class="form-control">
                                <option value="">All Clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->business_name ?? $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Filter by Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.purchase-requests.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Purchase Requests List</h3>
                <a href="{{ route('admin.purchase-requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create Purchase Request
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Client</th>
                                <th>Vendor</th>
                                <th>Amount (BDT)</th>
                                <th>Amount (INR)</th>
                                <th>GST</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        @forelse($purchaseRequests as $request)
                            <tr>
                                <td><strong>{{ $request->po_number ?? $request->request_number }}</strong></td>
                                <td>
                                    <strong>{{ $request->user->business_name ?? $request->user->name }}</strong><br>
                                    <small class="text-muted">{{ $request->user->email }}</small>
                                </td>
                                <td>{{ $request->vendor->vendor_name }}</td>
                                <td><strong>{{ number_format($request->amount_bdt ?? $request->amount, 2) }}</strong></td>
                                <td>₹{{ number_format($request->amount_inr ?? 0, 2) }}</td>
                                    <td>
                                        @if($request->is_gst_payment)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-warning">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($request->status === 'completed')
                                            <span class="badge badge-primary">Completed</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->payment_status === 'paid')
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.purchase-requests.show', $request->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status === 'pending')
                                        <a href="{{ route('admin.purchase-requests.edit', $request->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No purchase requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $purchaseRequests->appends(request()->query())->links() }}
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
            min-width: 900px;
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

