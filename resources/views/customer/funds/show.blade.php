@extends('adminlte::page')

@section('title', 'Transaction Details')

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Transaction Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction #{{ $fundTransaction->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.funds.index') }}" class="btn btn-sm btn-secondary">
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
                                    <strong class="text-success">+৳{{ number_format($fundTransaction->amount, 2) }}</strong>
                                @else
                                    <strong class="text-danger">-৳{{ number_format($fundTransaction->amount, 2) }}</strong>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($fundTransaction->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($fundTransaction->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending Review</span>
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
                                @if($fundTransaction->status === 'rejected')
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Rejection Reason:</strong><br>
                                        {{ $fundTransaction->admin_note }}
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> {{ $fundTransaction->admin_note }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if($fundTransaction->purchaseRequest)
                        <tr>
                            <th>Purchase Request</th>
                            <td>
                                <a href="{{ route('customer.purchase-requests.show', $fundTransaction->purchaseRequest) }}">
                                    {{ $fundTransaction->purchaseRequest->request_number }}
                                </a>
                            </td>
                        </tr>
                        @endif
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
        </div>
    </div>
@stop

