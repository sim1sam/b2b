@extends('adminlte::page')

@section('title', 'Edit Purchase Request')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Edit Purchase Request</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit: {{ $purchaseRequest->po_number ?? $purchaseRequest->request_number }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-user"></i> 
                        <strong>Client:</strong> {{ $purchaseRequest->user->business_name ?? $purchaseRequest->user->name }}<br>
                        <i class="fas fa-wallet"></i> 
                        <strong>Available Balance:</strong> {{ number_format($availableBalance, 2) }}
                        @if($exchangeRate && $exchangeRate > 0)
                            <br><i class="fas fa-exchange-alt"></i> 
                            <strong>Exchange Rate:</strong> 
                            <span class="badge badge-success" style="font-size: 1em;">1 BDT = ₹{{ number_format($exchangeRate, 4) }}</span>
                        @else
                            <br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Exchange rate not set. Please contact admin.</span>
                        @endif
                    </div>

                    <form action="{{ route('admin.purchase-requests.update', $purchaseRequest->id) }}" method="POST" id="editPurchaseRequestForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="vendor_id">Select Vendor <span class="text-danger">*</span></label>
                            <select name="vendor_id" id="vendor_id" 
                                    class="form-control @error('vendor_id') is-invalid @enderror" required>
                                <option value="">-- Select Vendor --</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $purchaseRequest->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->vendor_name }} 
                                        @if($vendor->gstin)
                                            (GSTIN: {{ $vendor->gstin }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount_inr">Amount (INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₹</span>
                                </div>
                                <input type="number" name="amount_inr" id="amount_inr" 
                                       class="form-control @error('amount_inr') is-invalid @enderror" 
                                       value="{{ old('amount_inr', $purchaseRequest->amount_inr ?? 0) }}" 
                                       step="0.01" min="0.01" required>
                                @error('amount_inr')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="is_gst_payment" id="is_gst" value="1" 
                                       {{ old('is_gst_payment', $purchaseRequest->is_gst_payment) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_gst">
                                    <strong>GST Payment</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                If unchecked: [Amount + (Amount × 5%) + 200] × Exchange Rate<br>
                                If checked: Amount × Exchange Rate
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Calculated Amount (BDT)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"></span>
                                </div>
                                <input type="text" class="form-control" 
                                       id="calculated_bdt" readonly 
                                       value="{{ number_format($purchaseRequest->amount_bdt ?? $purchaseRequest->amount, 2) }}" 
                                       style="background-color: #f8f9fa; font-weight: bold; color: #28a745;">
                            </div>
                            <small class="form-text text-muted">
                                Current Amount: {{ number_format($purchaseRequest->amount_bdt ?? $purchaseRequest->amount, 2) }} | 
                                Available: {{ number_format($availableBalance, 2) }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $purchaseRequest->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>PO Number:</strong> {{ $purchaseRequest->po_number ?? $purchaseRequest->request_number }}<br>
                            <strong>Request Number:</strong> {{ $purchaseRequest->request_number }}
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Purchase Request
                            </button>
                            <a href="{{ route('admin.purchase-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('js')
    <script>
        const exchangeRate = {{ $exchangeRate ?? 1.0 }};
        
        function calculateBDT() {
            const amountINR = parseFloat(document.getElementById('amount_inr').value) || 0;
            const isGstPayment = document.getElementById('is_gst').checked;
            
            let amountBDT = 0;
            if (exchangeRate > 0 && amountINR > 0) {
                if (isGstPayment) {
                    amountBDT = amountINR * exchangeRate;
                } else {
                    // [Input Amount + (Input Amount × 5%) + 200] × Exchange Rate
                    const amountWithGst = amountINR + (amountINR * 0.05) + 200;
                    amountBDT = amountWithGst * exchangeRate;
                }
            }
            
            document.getElementById('calculated_bdt').value = amountBDT.toFixed(2);
        }
        
        document.getElementById('amount_inr').addEventListener('input', calculateBDT);
        document.getElementById('is_gst').addEventListener('change', calculateBDT);
    </script>
    @stop
@stop

