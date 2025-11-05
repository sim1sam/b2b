@extends('adminlte::page')

@section('title', 'Purchase Requests')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-shopping-cart"></i> Purchase Requests</h1>
        <a href="{{ route('customer.purchase-requests.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create
        </a>
    </div>
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
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="d-none d-md-block">
                <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>PO Number</th>
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
                                <td>{{ $request->vendor->vendor_name }}</td>
                                <td><strong>৳{{ number_format($request->amount_bdt ?? $request->amount, 2) }}</strong></td>
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
                                    <a href="{{ route('customer.purchase-requests.show', $request) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status === 'pending')
                                        <a href="{{ route('customer.purchase-requests.edit', $request) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customer.purchase-requests.destroy', $request) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this purchase request? Amount will be refunded.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No purchase requests found. <a href="{{ route('customer.purchase-requests.create') }}">Create one now</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($purchaseRequests as $request)
                    <div class="card mb-3 border-left-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1 font-weight-bold">
                                        {{ $request->po_number ?? $request->request_number }}
                                    </h5>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-store"></i> {{ $request->vendor->vendor_name }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($request->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @elseif($request->status === 'completed')
                                        <span class="badge badge-primary">Completed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Amount (BDT)</small>
                                    <strong class="text-success">৳{{ number_format($request->amount_bdt ?? $request->amount, 2) }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Amount (INR)</small>
                                    <strong>₹{{ number_format($request->amount_inr ?? 0, 2) }}</strong>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">GST</small>
                                    @if($request->is_gst_payment)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-warning">No</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Payment</small>
                                    @if($request->payment_status === 'paid')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Date</small>
                                <span>{{ $request->created_at->format('d M Y') }}</span>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('customer.purchase-requests.show', $request) }}" class="btn btn-sm btn-info flex-fill">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($request->status === 'pending')
                                    <a href="{{ route('customer.purchase-requests.edit', $request) }}" class="btn btn-sm btn-warning flex-fill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('customer.purchase-requests.destroy', $request) }}" method="POST" class="flex-fill" onsubmit="return confirm('Are you sure you want to cancel this purchase request? Amount will be refunded.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-100">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No purchase requests found.</p>
                        <a href="{{ route('customer.purchase-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create one now
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="card-footer">
                {{ $purchaseRequests->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    /* Remove scrollbars */
    .card-body {
        overflow-x: hidden;
    }
    /* Desktop table styles */
    @media (min-width: 768px) {
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            padding: 8px 6px;
            font-size: 0.875rem;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody td {
            padding: 8px 6px;
            font-size: 0.875rem;
        }
    }
    @media (max-width: 767.98px) {
        .card-body {
            padding: 0.75rem;
        }
        .card.mb-3 {
            margin-bottom: 0.75rem !important;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }
    @media (max-width: 575.98px) {
        .flex-fill {
            flex: 1 1 auto;
        }
        .d-flex.gap-2 {
            flex-wrap: wrap;
        }
    }
</style>
@stop
