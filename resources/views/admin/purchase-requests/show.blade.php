@extends('adminlte::page')

@section('title', 'Purchase Request Details')

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Purchase Request Details</h1>
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
                    <h3 class="card-title">{{ $purchaseRequest->po_number ?? $purchaseRequest->request_number }}</h3>
                    <div class="card-tools">
                        @if($purchaseRequest->status === 'pending')
                            <a href="{{ route('admin.purchase-requests.edit', $purchaseRequest->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        @if($purchaseRequest->status === 'approved' || $purchaseRequest->payment_status === 'paid')
                            <a href="{{ route('admin.purchase-requests.received-items', $purchaseRequest->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-check-double"></i> Cross-Check Received Items
                            </a>
                        @endif
                        @if($purchaseRequest->items->isNotEmpty())
                            <a href="{{ route('admin.purchase-requests.provisional-invoice', $purchaseRequest->id) }}" class="btn btn-sm btn-success" target="_blank">
                                <i class="fas fa-file-invoice"></i> View Provisional Invoice
                            </a>
                        @endif
                        <a href="{{ route('admin.purchase-requests.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">PO Number</th>
                            <td><strong>{{ $purchaseRequest->po_number ?? $purchaseRequest->request_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Request Number</th>
                            <td><strong>{{ $purchaseRequest->request_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Client</th>
                            <td>
                                <strong>{{ $purchaseRequest->user->business_name ?? $purchaseRequest->user->name }}</strong><br>
                                <small class="text-muted">{{ $purchaseRequest->user->email }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Vendor</th>
                            <td>
                                <strong>{{ $purchaseRequest->vendor->vendor_name }}</strong>
                                @if($purchaseRequest->vendor->gstin)
                                    <br><small class="text-muted">GSTIN: {{ $purchaseRequest->vendor->gstin }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Amount (INR)</th>
                            <td>
                                <strong>₹{{ number_format($purchaseRequest->amount_inr ?? 0, 2) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>GST Payment</th>
                            <td>
                                @if($purchaseRequest->is_gst_payment)
                                    <span class="badge badge-success">Yes</span>
                                    <small class="text-muted">(Amount × Exchange Rate)</small>
                                @else
                                    <span class="badge badge-warning">No</span>
                                    <small class="text-muted">([Amount + (Amount × 5%) + 200] × Exchange Rate)</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Amount (BDT)</th>
                            <td>
                                <strong class="text-primary" style="font-size: 1.2em;">{{ number_format($purchaseRequest->amount_bdt ?? $purchaseRequest->amount, 2) }}</strong>
                            </td>
                        </tr>
                        @if($purchaseRequest->user->exchange_rate)
                        <tr>
                            <th>Exchange Rate</th>
                            <td>
                                <span class="badge badge-success" style="font-size: 1em;">
                                    1 BDT = ₹{{ number_format($purchaseRequest->user->exchange_rate, 4) }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Description</th>
                            <td>{{ $purchaseRequest->description ?? 'No description' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($purchaseRequest->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($purchaseRequest->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($purchaseRequest->status === 'completed')
                                    <span class="badge badge-primary">Completed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Payment Status</th>
                            <td>
                                @if($purchaseRequest->payment_status === 'paid')
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Shipping Mark</th>
                            <td>
                                <form action="{{ route('admin.purchase-requests.update-shipping-mark', $purchaseRequest->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group">
                                        <input type="text" 
                                               name="shipping_mark" 
                                               value="{{ old('shipping_mark', $purchaseRequest->shipping_mark) }}" 
                                               class="form-control form-control-sm" 
                                               placeholder="Enter shipping mark">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                @if($purchaseRequest->shipping_mark)
                                    <small class="text-muted d-block mt-1">Current: <strong>{{ $purchaseRequest->shipping_mark }}</strong></small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $purchaseRequest->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $purchaseRequest->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vendor Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $purchaseRequest->vendor->vendor_name }}</p>
                    <p><strong>Contact:</strong> {{ $purchaseRequest->vendor->contact_number }}</p>
                    @if($purchaseRequest->vendor->gstin)
                        <p><strong>GSTIN:</strong> {{ $purchaseRequest->vendor->gstin }}</p>
                    @endif
                    @if($purchaseRequest->vendor->payment_number)
                        <p><strong>GPay/PhonePe:</strong> {{ $purchaseRequest->vendor->payment_number }}</p>
                    @endif
                </div>
            </div>

            @if($purchaseRequest->fundTransaction)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Fund Transaction</h3>
                </div>
                <div class="card-body">
                    <p><strong>Transaction ID:</strong> #{{ $purchaseRequest->fundTransaction->id }}</p>
                    <p><strong>Amount:</strong> {{ number_format(abs($purchaseRequest->fundTransaction->amount), 2) }}</p>
                    <p><strong>Status:</strong> 
                        @if($purchaseRequest->fundTransaction->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($purchaseRequest->fundTransaction->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Payment Status</h3>
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> 
                        @if($purchaseRequest->payment_status === 'paid')
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Paid</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                        @endif
                    </p>
                    
                    @if($purchaseRequest->payment_status === 'paid' && $purchaseRequest->payment_screenshot)
                        <div class="mt-3">
                            <p><strong>Payment Screenshot:</strong></p>
                            <a href="{{ Storage::url($purchaseRequest->payment_screenshot) }}" target="_blank">
                                <img src="{{ Storage::url($purchaseRequest->payment_screenshot) }}" 
                                     alt="Payment Screenshot" 
                                     class="img-fluid img-thumbnail" 
                                     style="max-width: 100%; cursor: pointer;">
                            </a>
                            <small class="text-muted d-block mt-2">Click to view full size</small>
                        </div>
                    @endif

                    @if($purchaseRequest->payment_status !== 'paid')
                        <div class="mt-3">
                            <form action="{{ route('admin.purchase-requests.mark-as-paid', $purchaseRequest->id) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="payment_screenshot">Upload Payment Screenshot <span class="text-danger">*</span></label>
                                    <input type="file" 
                                           name="payment_screenshot" 
                                           id="payment_screenshot" 
                                           class="form-control @error('payment_screenshot') is-invalid @enderror" 
                                           accept="image/*" 
                                           required>
                                    @error('payment_screenshot')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF, SVG (Max: 2MB)</small>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check-circle"></i> Mark as Paid
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Invoice & Tracking ID</h3>
                </div>
                <div class="card-body">
                    <!-- Invoice Section -->
                    <div class="mb-4">
                        <p><strong>Invoice:</strong></p>
                        @if($purchaseRequest->invoice)
                            <div class="mb-2">
                                @if(strtolower(pathinfo($purchaseRequest->invoice, PATHINFO_EXTENSION)) === 'pdf')
                                    <a href="{{ Storage::url($purchaseRequest->invoice) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i> View Invoice (PDF)
                                    </a>
                                @else
                                    <a href="{{ Storage::url($purchaseRequest->invoice) }}" target="_blank">
                                        <img src="{{ Storage::url($purchaseRequest->invoice) }}" 
                                             alt="Invoice" 
                                             class="img-fluid img-thumbnail" 
                                             style="max-width: 200px; cursor: pointer;">
                                    </a>
                                    <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Click to view full size</small>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No invoice uploaded yet by the client.
                            </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Tracking ID Section -->
                    <div>
                        <p><strong>Tracking ID:</strong></p>
                        @if($purchaseRequest->tracking_id_file)
                            <div class="mb-2">
                                @if(strtolower(pathinfo($purchaseRequest->tracking_id_file, PATHINFO_EXTENSION)) === 'pdf')
                                    <a href="{{ Storage::url($purchaseRequest->tracking_id_file) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i> View Tracking ID (PDF)
                                    </a>
                                @else
                                    <a href="{{ Storage::url($purchaseRequest->tracking_id_file) }}" target="_blank">
                                        <img src="{{ Storage::url($purchaseRequest->tracking_id_file) }}" 
                                             alt="Tracking ID" 
                                             class="img-fluid img-thumbnail" 
                                             style="max-width: 200px; cursor: pointer;">
                                    </a>
                                    <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Click to view full size</small>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No tracking ID file uploaded yet by the client.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($purchaseRequest->items->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Received Items Summary</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Weight (Kg)</th>
                                    <th>Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseRequest->items as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ number_format($item->quantity, 2) }}</td>
                                        <td>{{ number_format($item->weight, 2) }}</td>
                                        <td><strong>{{ number_format($item->total_cost, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $itemsTotal = $purchaseRequest->items->sum('total_cost');
                                    // Calculate net weight (only from weight-based items, exclude quantity-based items)
                                    $netWeight = $purchaseRequest->items()
                                        ->whereHas('shippingCharge', function($query) {
                                            $query->where('unit_type', 'weight');
                                        })
                                        ->sum('weight');
                                    $packagingWeight = $netWeight < 20 ? $netWeight * 0.10 : $netWeight * 0.08;
                                    
                                    // Get lowest shipping charge per kg from client configuration
                                    $lowestShippingCharge = $purchaseRequest->user->lowest_shipping_charge_per_kg ?? 0;
                                    
                                    $packagingCost = $packagingWeight * $lowestShippingCharge;
                                    $transportationCharge = $purchaseRequest->transportation_charge ?? 0;
                                    $totalCost = $itemsTotal + $packagingCost + $transportationCharge;
                                    
                                    // Calculate round-off to previous 50
                                    $roundedTotal = floor($totalCost / 50) * 50;
                                    $roundOffAmount = $totalCost - $roundedTotal;
                                @endphp
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Items Subtotal:</strong></td>
                                    <td><strong>{{ number_format($itemsTotal, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Net Weight:</strong></td>
                                    <td><strong>{{ number_format($netWeight, 2) }} Kg</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Packaging Weight:</strong></td>
                                    <td>
                                        <strong>{{ number_format($packagingWeight, 2) }} Kg</strong>
                                        <small class="text-muted">({{ $netWeight < 20 ? '10%' : '8%' }})</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Packaging Cost:</strong></td>
                                    <td>
                                        <strong>{{ number_format($packagingCost, 2) }}</strong>
                                        <small class="text-muted">(Lowest: {{ number_format($lowestShippingCharge, 2) }}/Kg)</small>
                                    </td>
                                </tr>
                                @if($transportationCharge > 0)
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Transportation Charge:</strong></td>
                                    <td><strong>{{ number_format($transportationCharge, 2) }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Round Off:</strong></td>
                                    <td>
                                        <strong>{{ number_format($roundOffAmount, 2) }}</strong>
                                        <small class="text-muted">(Round to nearest lower 50)</small>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                    <td><strong class="text-primary">{{ number_format($roundedTotal, 2) }}</strong></td>
                                </tr>
                                <tr class="text-muted">
                                    <td colspan="3" class="text-right"><small>Original Amount:</small></td>
                                    <td><small>{{ number_format($totalCost, 2) }}</small></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.purchase-requests.provisional-invoice', $purchaseRequest->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-file-invoice"></i> View Full Provisional Invoice
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

