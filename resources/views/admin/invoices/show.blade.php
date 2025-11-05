@extends('adminlte::page')

@section('title', 'Original Invoice')

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Original Invoice</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-0">Original Invoice - {{ $invoice->invoice_number }}</h3>
                        <div class="mt-2 mt-md-0">
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Invoices
                            </a>
                            <button onclick="window.print()" class="btn btn-primary btn-sm">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background: white;">
                    <!-- Invoice Header -->
                    <div class="row mb-4" style="min-height: 120px;">
                        <div class="col-md-6" style="display: flex; flex-direction: column; justify-content: center;">
                            <div style="flex: 1;"></div>
                            <div style="flex: 1; display: flex; align-items: center;">
                                @if($admin && $admin->admin_logo)
                                    <img src="{{ Storage::url($admin->admin_logo) }}" alt="Logo" style="max-height: 114px; max-width: 286px;">
                                @endif
                            </div>
                            <div style="flex: 1;"></div>
                        </div>
                        <div class="col-md-6 text-right" style="display: flex; flex-direction: column; justify-content: flex-start;">
                            <h3 class="mb-2"><strong>Original Invoice</strong></h3>
                            <p class="mb-1"><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                            <p class="mb-1"><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                            <p class="mb-0"><strong>Shipping Mark:</strong> {{ $invoice->shipping_mark }}</p>
                        </div>
                    </div>

                    <hr style="border-top: 2px solid #000; margin: 20px 0;">

                    <!-- Billed By / Billed To Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2" style="text-decoration: underline;"><strong>Billed by:</strong></p>
                            @if($admin)
                                <p class="mb-1"><strong>{{ $admin->company_name ?? 'N/A' }}</strong></p>
                                @if($admin->address_line1)
                                    <p class="mb-1">{{ $admin->address_line1 }}</p>
                                @endif
                                @if($admin->address_line2)
                                    <p class="mb-1">{{ $admin->address_line2 }}</p>
                                @endif
                                @php
                                    $locationParts = [];
                                    if ($admin->district) $locationParts[] = $admin->district;
                                    if ($admin->country) $locationParts[] = $admin->country;
                                    $location = implode(', ', $locationParts);
                                @endphp
                                @if($location)
                                    <p class="mb-1">{{ $location }}</p>
                                @endif
                                @if($admin->email)
                                    <p class="mb-1">Email: {{ $admin->email }}</p>
                                @endif
                                @if($admin->mobile_number)
                                    <p class="mb-0">Mobile: {{ $admin->mobile_number }}</p>
                                @endif
                            @else
                                <p class="text-muted">Admin profile not configured</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2" style="text-decoration: underline;"><strong>Billed to:</strong></p>
                            <p class="mb-1"><strong>{{ $invoice->user->business_name ?? $invoice->user->name }}</strong></p>
                            @php
                                $clientAddress = $invoice->user->address;
                                $addressLines = $clientAddress ? explode("\n", $clientAddress) : [];
                            @endphp
                            @if(count($addressLines) > 0 && trim($addressLines[0]))
                                <p class="mb-1">{{ trim($addressLines[0]) }}</p>
                            @endif
                            @if(count($addressLines) > 1 && trim($addressLines[1]))
                                <p class="mb-1">{{ trim($addressLines[1]) }}</p>
                            @endif
                            @if($invoice->user->email)
                                <p class="mb-1">Email: {{ $invoice->user->email }}</p>
                            @endif
                            @if($invoice->user->mobile_number)
                                <p class="mb-0">Mobile: {{ $invoice->user->mobile_number }}</p>
                            @endif
                        </div>
                    </div>

                    <hr style="border-top: 1px solid #ccc; margin: 20px 0;">

                    <!-- Purchase Orders List -->
                    <div class="mb-3">
                        <h5><strong>Purchase Orders Included:</strong></h5>
                        <p>
                            @foreach($invoice->purchaseRequests as $index => $pr)
                                {{ $pr->po_number }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>

                    <hr style="border-top: 1px solid #ccc; margin: 20px 0;">

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>PO Number</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Weight (Kg)</th>
                                    <th>Rate per Unit</th>
                                    <th>Item Cost</th>
                                    <th>Shipping Cost</th>
                                    <th>Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $itemIndex = 1; @endphp
                                @foreach($invoice->purchaseRequests as $pr)
                                    @foreach($pr->items as $item)
                                        @php
                                            $shippingCharge = $item->shippingCharge;
                                            $isQtyBased = $shippingCharge && $shippingCharge->unit_type === 'qty';
                                        @endphp
                                        <tr>
                                            <td>{{ $itemIndex++ }}</td>
                                            <td>{{ $pr->po_number }}</td>
                                            <td><strong>{{ $item->item_name }}</strong></td>
                                            <td>{{ number_format($item->quantity, 2) }}</td>
                                            <td>{{ number_format($item->weight, 2) }}</td>
                                            <td>৳{{ number_format($item->rate_per_unit, 2) }}</td>
                                            <td>৳{{ number_format($item->item_cost, 2) }}</td>
                                            <td>
                                                ৳{{ number_format($item->shipping_cost, 2) }}
                                                @if($isQtyBased)
                                                    <br><small class="text-muted">(Includes item cost: ৳{{ number_format($item->item_cost, 2) }})</small>
                                                @endif
                                            </td>
                                            <td><strong>৳{{ number_format($item->total_cost, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="text-right"><strong>Items Subtotal:</strong></td>
                                    <td><strong>৳{{ number_format($itemsTotalCost, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Net Weight:</strong></td>
                                    <td colspan="4"><strong>{{ number_format($totalNetWeight, 2) }} Kg</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Packaging Weight:</strong></td>
                                    <td colspan="4">
                                        <strong>{{ number_format($packagingWeight, 2) }} Kg</strong>
                                        <small class="text-muted">
                                            ({{ $totalNetWeight < 20 ? '10%' : '8%' }} of net weight)
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Packaging Cost:</strong></td>
                                    <td colspan="4">
                                        <strong>৳{{ number_format($packagingCost, 2) }}</strong>
                                        @if($packagingWeight > 0 && $lowestShippingCharge > 0)
                                            <small class="text-muted">
                                                ({{ number_format($packagingWeight, 2) }} Kg × ৳{{ number_format($lowestShippingCharge, 2) }}/Kg)
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                @if($totalTransportationCharge > 0)
                                <tr>
                                    <td colspan="8" class="text-right"><strong>Transportation / Delivery Charge:</strong></td>
                                    <td><strong>৳{{ number_format($totalTransportationCharge, 2) }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="8" class="text-right"><strong>Round Off:</strong></td>
                                    <td>
                                        <strong>৳{{ number_format($invoice->total_amount - $invoice->rounded_total, 2) }}</strong>
                                        <small class="text-muted">(Round to nearest lower 50)</small>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="8" class="text-right"><strong>Total Amount:</strong></td>
                                    <td><strong class="text-primary" style="font-size: 1.2em;">৳{{ number_format($invoice->rounded_total, 2) }}</strong></td>
                                </tr>
                                <tr class="text-muted">
                                    <td colspan="8" class="text-right"><small>Original Amount:</small></td>
                                    <td><small>৳{{ number_format($invoice->total_amount, 2) }}</small></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Footer -->
                    <div class="mt-5 pt-4" style="border-top: 1px solid #ccc;">
                        <p class="text-center text-muted mb-0" style="font-style: italic;">
                            This is a system generated invoice and doesn't require any signature.
                        </p>
                    </div>

                    <!-- Admin Actions -->
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cog"></i> Admin Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Payment Status:</strong> 
                                            @if($invoice->payment_status === 'paid')
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </p>
                                        <p><strong>Delivery Request:</strong> 
                                            @if($invoice->delivery_request_status === 'requested')
                                                <span class="badge badge-info">Requested</span>
                                            @else
                                                <span class="text-muted">Not Requested</span>
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
                                        @if($invoice->dispute_status === 'open')
                                            <p><strong>Dispute Status:</strong> 
                                                <span class="badge badge-danger">Open</span>
                                                @if($invoice->dispute_opened_at)
                                                    <br><small class="text-muted">
                                                        Opened: {{ $invoice->dispute_opened_at->format('d M Y, h:i A') }}
                                                @php
                                                    $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
                                                    $totalMinutesRemaining = (48 * 60) - $minutesPassed;
                                                    if ($totalMinutesRemaining > 0) {
                                                        $hoursRemaining = floor($totalMinutesRemaining / 60);
                                                        $minutesRemaining = $totalMinutesRemaining % 60;
                                                    } else {
                                                        $hoursRemaining = 0;
                                                        $minutesRemaining = 0;
                                                    }
                                                @endphp
                                                @if($hoursRemaining > 0 || $minutesRemaining > 0)
                                                    ({{ str_pad($hoursRemaining, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutesRemaining, 2, '0', STR_PAD_LEFT) }} remaining)
                                                @else
                                                    (Window closed)
                                                @endif
                                                    </small>
                                                @endif
                                            </p>
                                            @if($invoice->dispute_note)
                                                <p><strong>Dispute Note:</strong> {{ $invoice->dispute_note }}</p>
                                            @endif
                                        @elseif($invoice->dispute_status === 'resolved')
                                            <p><strong>Dispute Status:</strong> <span class="badge badge-success">Resolved</span></p>
                                        @elseif($invoice->dispute_status === 'closed')
                                            <p><strong>Dispute Status:</strong> <span class="badge badge-secondary">Closed</span></p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($invoice->delivery_request_status === 'requested' && $invoice->delivery_status !== 'delivered')
                                            <form action="{{ route('admin.invoices.mark-delivered', $invoice->id) }}" method="POST" class="mb-3">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-lg btn-block" onclick="return confirm('Mark this invoice as delivered? This will open a 48-hour dispute window for the client.');">
                                                    <i class="fas fa-truck"></i> Mark as Delivered
                                                </button>
                                            </form>
                                        @endif

                                        @if($invoice->delivery_status === 'delivered' && $invoice->order_status !== 'completed' && $invoice->dispute_status !== 'open')
                                            <form action="{{ route('admin.invoices.complete-order', $invoice->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-lg btn-block" onclick="return confirm('Mark this order as completed?');">
                                                    <i class="fas fa-check-double"></i> Complete Order
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

    @section('css')
    <style>
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .card-header .btn {
                margin-top: 5px;
                width: 100%;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
        }
        
        @media print {
            /* Hide navigation and buttons */
            .btn, .card-header, .content-header, .main-header, .main-sidebar, .main-footer, .navbar {
                display: none !important;
            }
            
            /* Remove card styling */
            .card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
            }
            
            .card-body {
                padding: 20px !important;
                background: white !important;
            }
            
            /* Ensure logo displays */
            img {
                max-width: 286px !important;
                max-height: 114px !important;
                height: auto !important;
            }
            
            /* Fix flexbox in print */
            .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }
            
            .col-md-6 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
            
            /* Ensure colors print properly */
            * {
                color: #000 !important;
                background: white !important;
            }
            
            .text-primary, .text-danger, .text-muted, .text-info {
                color: #000 !important;
            }
            
            .bg-light {
                background-color: #f8f9fa !important;
            }
            
            /* Table borders */
            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }
            
            table td, table th {
                border: 1px solid #000 !important;
                padding: 8px !important;
            }
            
            /* Ensure hr lines print */
            hr {
                border-top: 2px solid #000 !important;
                margin: 20px 0 !important;
            }
            
            /* Page setup */
            @page {
                margin: 1cm;
                size: A4;
            }
            
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Fix spacing */
            .mb-0, .mb-1, .mb-2, .mb-3, .mb-4 {
                margin-bottom: 0.5rem !important;
            }
            
            .mt-4 {
                margin-top: 1rem !important;
            }
        }
    </style>
    @stop
@stop
