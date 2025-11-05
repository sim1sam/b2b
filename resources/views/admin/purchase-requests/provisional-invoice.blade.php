@extends('adminlte::page')

@section('title', 'Provisional Invoice')

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Provisional Invoice</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-0">Provisional Invoice - {{ $purchaseRequest->po_number ?? 'N/A' }}</h3>
                        <div class="mt-2 mt-md-0">
                            <a href="{{ route('admin.purchase-requests.show', $purchaseRequest->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Purchase Order
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
                            <h3 class="mb-2"><strong>Provisional Invoice</strong></h3>
                            <p class="mb-1"><strong>PO Number:</strong> {{ $purchaseRequest->po_number ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>PO Date:</strong> {{ $purchaseRequest->created_at->format('d M Y') }}</p>
                            <p class="mb-0"><strong>Update on:</strong> {{ now()->format('d M Y, h:i A') }}</p>
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
                            <p class="mb-1"><strong>{{ $purchaseRequest->user->business_name ?? $purchaseRequest->user->name }}</strong></p>
                            @php
                                $clientAddress = $purchaseRequest->user->address;
                                $addressLines = $clientAddress ? explode("\n", $clientAddress) : [];
                            @endphp
                            @if(count($addressLines) > 0 && trim($addressLines[0]))
                                <p class="mb-1">{{ trim($addressLines[0]) }}</p>
                            @endif
                            @if(count($addressLines) > 1 && trim($addressLines[1]))
                                <p class="mb-1">{{ trim($addressLines[1]) }}</p>
                            @endif
                            @if($purchaseRequest->user->email)
                                <p class="mb-1">Email: {{ $purchaseRequest->user->email }}</p>
                            @endif
                            @if($purchaseRequest->user->mobile_number)
                                <p class="mb-0">Mobile: {{ $purchaseRequest->user->mobile_number }}</p>
                            @endif
                        </div>
                    </div>

                    <hr style="border-top: 1px solid #ccc; margin: 20px 0;">

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
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
                                @foreach($purchaseRequest->items as $index => $item)
                                    @php
                                        $shippingCharge = $item->shippingCharge;
                                        $isQtyBased = $shippingCharge && $shippingCharge->unit_type === 'qty';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->item_name }}</strong></td>
                                        <td>{{ number_format($item->quantity, 2) }}</td>
                                        <td>{{ number_format($item->weight, 2) }}</td>
                                        <td>{{ number_format($item->rate_per_unit, 2) }}</td>
                                        <td>{{ number_format($item->item_cost, 2) }}</td>
                                        <td>
{{ number_format($item->shipping_cost, 2) }}
                                            @if($isQtyBased)
                                                <br><small class="text-muted">(Includes item cost: {{ number_format($item->item_cost, 2) }})</small>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($item->total_cost, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-right"><strong>Items Subtotal:</strong></td>
                                    <td><strong>{{ number_format($itemsTotalCost, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Net Weight:</strong></td>
                                    <td colspan="4"><strong>{{ number_format($netWeight, 2) }} Kg</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Packaging Weight:</strong></td>
                                    <td colspan="4">
                                        <strong>{{ number_format($packagingWeight, 2) }} Kg</strong>
                                        <small class="text-muted">
                                            ({{ $netWeight < 20 ? '10%' : '8%' }} of net weight)
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Packaging Cost:</strong></td>
                                    <td colspan="4">
                                        <strong>{{ number_format($packagingCost, 2) }}</strong>
                                        @if($packagingWeight > 0 && $lowestShippingCharge > 0)
                                            <small class="text-muted">
                                                ({{ number_format($packagingWeight, 2) }} Kg × {{ number_format($lowestShippingCharge, 2) }}/Kg)
                                            </small>
                                        @elseif($packagingWeight > 0 && $lowestShippingCharge == 0)
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Lowest shipping charge not configured for this client. Please update client settings.
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                (Packaging weight: {{ number_format($packagingWeight, 2) }} Kg)
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                @if($transportationCharge > 0)
                                <tr>
                                    <td colspan="7" class="text-right"><strong>Transportation / Delivery Charge:</strong></td>
                                    <td><strong>{{ number_format($transportationCharge, 2) }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="7" class="text-right"><strong>Round Off:</strong></td>
                                    <td>
                                        <strong>{{ number_format($roundOffAmount, 2) }}</strong>
                                        <small class="text-muted">(Round to nearest lower 50)</small>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="7" class="text-right"><strong>Total Amount:</strong></td>
                                    <td><strong class="text-primary" style="font-size: 1.2em;">{{ number_format($roundedTotal, 2) }}</strong></td>
                                </tr>
                                <tr class="text-muted">
                                    <td colspan="7" class="text-right"><small>Original Amount:</small></td>
                                    <td><small>{{ number_format($totalCost, 2) }}</small></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Note:</strong> This is a provisional invoice. Final charges may vary based on actual received items.
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
            
            /* Ensure alert boxes print */
            .alert {
                border: 1px solid #000 !important;
                background: white !important;
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
