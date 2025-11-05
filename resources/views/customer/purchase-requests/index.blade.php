@extends('adminlte::page')

@section('title', 'Purchase Requests')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Purchase Requests</h1>
    <a href="{{ route('customer.purchase-requests.create') }}" class="btn btn-primary float-right">
        <i class="fas fa-plus"></i> Create Purchase Request
    </a>
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
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
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

            {{ $purchaseRequests->links() }}
        </div>
    </div>
@stop
