@extends('adminlte::page')

@section('title', 'Fund Transaction Details')

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Fund Transaction Details</h1>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction #{{ $fundTransaction->id }}</h3>
                    <div class="card-tools">
                        @if($fundTransaction->type === 'deposit' && $fundTransaction->status === 'pending')
                            <a href="{{ route('admin.funds.edit', $fundTransaction->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit/Approve/Reject
                            </a>
                        @endif
                        <a href="{{ route('admin.funds.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Transaction ID</th>
                            <td>#{{ $fundTransaction->id }}</td>
                        </tr>
                        <tr>
                            <th>Customer</th>
                            <td>
                                <strong>{{ $fundTransaction->user->name }}</strong><br>
                                <small class="text-muted">{{ $fundTransaction->user->email }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $fundTransaction->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                @if($fundTransaction->type === 'deposit')
                                    <span class="badge badge-success">Deposit</span>
                                @elseif($fundTransaction->type === 'purchase')
                                    <span class="badge badge-primary">Purchase</span>
                                @else
                                    <span class="badge badge-warning">Withdrawal</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>
                                @if($fundTransaction->type === 'deposit')
                                    <strong class="text-success" style="font-size: 1.2em;">+৳{{ number_format($fundTransaction->amount, 2) }}</strong>
                                @else
                                    <strong class="text-danger" style="font-size: 1.2em;">-৳{{ number_format($fundTransaction->amount, 2) }}</strong>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($fundTransaction->status === 'approved')
                                    <span class="badge badge-success" style="font-size: 1em;">Approved</span>
                                @elseif($fundTransaction->status === 'rejected')
                                    <span class="badge badge-danger" style="font-size: 1em;">Rejected</span>
                                @else
                                    <span class="badge badge-warning" style="font-size: 1em;">Pending Review</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Customer Notes</th>
                            <td>{{ $fundTransaction->notes ?? 'No notes provided' }}</td>
                        </tr>
                        @if($fundTransaction->admin_note)
                        <tr>
                            <th>Admin Note</th>
                            <td>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> {{ $fundTransaction->admin_note }}
                                </div>
                            </td>
                        </tr>
                        @endif
                        @if($fundTransaction->purchaseRequest)
                        <tr>
                            <th>Purchase Request</th>
                            <td>
                                <a href="{{ route('admin.purchase-requests.show', $fundTransaction->purchaseRequest) }}" target="_blank">
                                    {{ $fundTransaction->purchaseRequest->request_number }}
                                </a>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $fundTransaction->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($fundTransaction->payment_screenshot)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Screenshot</h3>
                </div>
                <div class="card-body text-center">
                    <img src="{{ Storage::url($fundTransaction->payment_screenshot) }}" alt="Payment Screenshot" 
                         class="img-fluid img-thumbnail" style="max-width: 100%;">
                    <p class="mt-2">
                        <a href="{{ Storage::url($fundTransaction->payment_screenshot) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </p>
                </div>
            </div>
            @endif

            @if($fundTransaction->type === 'deposit' && $fundTransaction->status === 'pending')
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.funds.approve', $fundTransaction->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this fund transaction?');">
                            <i class="fas fa-check"></i> Approve Transaction
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times"></i> Reject Transaction
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    @if($fundTransaction->type === 'deposit' && $fundTransaction->status === 'pending')
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.funds.reject', $fundTransaction->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Fund Transaction</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="admin_note">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="admin_note" id="admin_note" rows="4" 
                                      class="form-control @error('admin_note') is-invalid @enderror" 
                                      required placeholder="Enter the reason for rejection...">{{ old('admin_note') }}</textarea>
                            @error('admin_note')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">This note will be visible to the customer.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@stop

