@extends('adminlte::page')

@section('title', 'Dispute Details')

@section('content_header')
    <h1><i class="fas fa-exclamation-triangle"></i> Dispute Details</h1>
    <div class="float-right">
        <a href="{{ route('admin.disputes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Disputes
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

    <div class="row">
        <div class="col-md-8">
            <!-- Invoice Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Invoice Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                            <p><strong>Client:</strong> {{ $invoice->user->business_name ?? $invoice->user->name }}</p>
                            <p><strong>Shipping Mark:</strong> {{ $invoice->shipping_mark }}</p>
                            <p><strong>Total Amount:</strong> {{ number_format($invoice->rounded_total, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                            <p><strong>Payment Status:</strong> 
                                @if($invoice->payment_status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </p>
                            <p><strong>Delivery Status:</strong> 
                                @if($invoice->delivery_status === 'delivered')
                                    <span class="badge badge-success">Delivered</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </p>
                            <p><strong>Order Status:</strong> 
                                @if($invoice->order_status === 'completed')
                                    <span class="badge badge-primary">Completed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-eye"></i> View Full Invoice
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dispute Details -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Dispute Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Dispute Status:</strong> 
                                @if($invoice->dispute_status === 'open')
                                    <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> Open</span>
                                @elseif($invoice->dispute_status === 'resolved')
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Resolved</span>
                                @else
                                    <span class="badge badge-secondary">Closed</span>
                                @endif
                            </p>
                            <p><strong>Opened At:</strong> 
                                @if($invoice->dispute_opened_at)
                                    {{ $invoice->dispute_opened_at->format('d M Y, h:i A') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                            @if($invoice->dispute_resolved_at)
                                <p><strong>Resolved At:</strong> {{ $invoice->dispute_resolved_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($invoice->dispute_status === 'open' && $invoice->dispute_opened_at)
                                <p><strong>Time Remaining:</strong> 
                                    @if($hoursRemaining > 0 || $minutesRemaining > 0)
                                        <span class="badge badge-warning">{{ str_pad($hoursRemaining, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutesRemaining, 2, '0', STR_PAD_LEFT) }}</span>
                                    @else
                                        <span class="badge badge-secondary">Expired</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <p><strong>Client's Dispute Note:</strong></p>
                        <div class="alert alert-danger">
                            {{ $invoice->dispute_note ?? 'No dispute note provided.' }}
                        </div>
                    </div>

                    @if($invoice->admin_response)
                        <div class="mt-3">
                            <p><strong>Admin Response:</strong></p>
                            <div class="alert alert-info">
                                {{ $invoice->admin_response }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Admin Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Admin Actions</h5>
                </div>
                <div class="card-body">
                    @if($invoice->dispute_status === 'open' || $invoice->dispute_status === 'resolved')
                        <!-- Add Response Form -->
                        <div class="mb-4">
                            <h6><strong>Add Response</strong></h6>
                            <form action="{{ route('admin.disputes.add-response', $invoice->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="admin_response">Your Response:</label>
                                    <textarea name="admin_response" id="admin_response" rows="5" class="form-control @error('admin_response') is-invalid @enderror" required placeholder="Write your response to the client's dispute...">{{ old('admin_response') }}</textarea>
                                    @error('admin_response')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-comment"></i> Add Response
                                </button>
                            </form>
                            <small class="text-muted">Adding a response will mark the dispute as "Resolved".</small>
                        </div>

                        <hr>

                        <!-- Mark as Solved -->
                        <div>
                            <h6><strong>Mark as Solved</strong></h6>
                            <p class="text-muted">Close this dispute if it has been fully resolved.</p>
                            <form action="{{ route('admin.disputes.mark-solved', $invoice->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this dispute as solved? This will close the dispute.');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check-circle"></i> Mark as Solved
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This dispute is closed.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
