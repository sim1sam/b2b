@extends('adminlte::page')

@section('title', 'Create Purchase Request')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Create Purchase Request (On Behalf of Client)</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Error:</strong>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Purchase Request(s)</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.purchase-requests.create') }}" class="mb-4">
                        <div class="form-group">
                            <label for="client_id">Select Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ ($selectedClientId ?? '') == $client->id ? 'selected' : '' }}>
                                        {{ $client->business_name ?? $client->name }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if($selectedClientId)
                        @php
                            $client = $clients->firstWhere('id', $selectedClientId);
                        @endphp
                        
                        <div class="alert alert-info">
                            <i class="fas fa-user"></i> 
                            <strong>Client:</strong> {{ $client->business_name ?? $client->name }}<br>
                            <i class="fas fa-wallet"></i> 
                            <strong>Total Available Funds:</strong> {{ number_format($availableBalance, 2) }}
                            @if($exchangeRate && $exchangeRate > 0)
                                <br><i class="fas fa-exchange-alt"></i> 
                                <strong>Exchange Rate:</strong> 
                                <span class="badge badge-success" style="font-size: 1em;">1 BDT = ₹{{ number_format($exchangeRate, 4) }}</span>
                            @else
                                <br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Exchange rate not set for this client. Please set it in client profile.</span>
                            @endif
                        </div>

                        @if($vendors->isEmpty())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> No active vendors found for this client. 
                                <a href="{{ route('admin.vendors.create') }}" target="_blank">Create a vendor first</a>
                            </div>
                        @else
                            <form action="{{ route('admin.purchase-requests.store') }}" method="POST" id="purchaseRequestForm">
                                @csrf
                                <input type="hidden" name="client_id" value="{{ $selectedClientId }}">

                                <div id="requests-container">
                                    <!-- Request 1 -->
                                    <div class="request-item card mb-3" data-index="0">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fas fa-shopping-cart"></i> Purchase Request #1
                                                <button type="button" class="btn btn-sm btn-danger float-right remove-request" style="display: none;">
                                                    <i class="fas fa-times"></i> Remove
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 col-12 mb-3">
                                                    <label for="vendor_id_0">Select Vendor <span class="text-danger">*</span></label>
                                                    <select name="requests[0][vendor_id]" id="vendor_id_0" 
                                                            class="form-control @error('requests.0.vendor_id') is-invalid @enderror" required>
                                                        <option value="">-- Select Vendor --</option>
                                                        @foreach($vendors as $vendor)
                                                            <option value="{{ $vendor->id }}" {{ old('requests.0.vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                                {{ $vendor->vendor_name }} 
                                                                @if($vendor->gstin)
                                                                    (GSTIN: {{ $vendor->gstin }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('requests.0.vendor_id')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 col-12 mb-3">
                                                    <label for="amount_inr_0">Amount (INR) <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₹</span>
                                                        </div>
                                                        <input type="number" name="requests[0][amount_inr]" id="amount_inr_0" 
                                                               class="form-control amount-inr @error('requests.0.amount_inr') is-invalid @enderror" 
                                                               value="{{ old('requests.0.amount_inr') }}" 
                                                               step="0.01" min="0.01" required 
                                                               placeholder="Enter amount in INR" data-index="0">
                                                        @error('requests.0.amount_inr')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input gst-checkbox" type="checkbox" 
                                                               name="requests[0][is_gst_payment]" id="is_gst_0" value="1" 
                                                               data-index="0" {{ old('requests.0.is_gst_payment') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_gst_0">
                                                            <strong>GST Payment</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        If unchecked: [Amount + (Amount × 5%) + 200] × Exchange Rate<br>
                                                        If checked: Amount × Exchange Rate
                                                    </small>
                                                </div>

                                                <div class="col-md-6 col-12 mb-3">
                                                    <label>Calculated Amount (BDT)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"></span>
                                                        </div>
                                                        <input type="text" class="form-control calculated-bdt" 
                                                               id="calculated_bdt_0" readonly 
                                                               value="0.00" style="background-color: #f8f9fa; font-weight: bold;">
                                                    </div>
                                                </div>


                                                <div class="col-12 mb-3">
                                                    <label for="description_0">Description</label>
                                                    <textarea name="requests[0][description]" id="description_0" rows="3" 
                                                              class="form-control @error('requests.0.description') is-invalid @enderror" 
                                                              placeholder="Describe the purchase request, items needed, etc.">{{ old('requests.0.description') }}</textarea>
                                                    @error('requests.0.description')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-info" id="add-request-btn">
                                        <i class="fas fa-plus"></i> Add Another Purchase Request
                                    </button>
                                </div>

                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Total Amount (BDT):</strong> 
                                    <span id="total-amount-bdt" class="font-weight-bold" style="font-size: 1.2em;">0.00</span>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="fund_transaction_id">Deduct from Fund <span class="text-danger">*</span></label>
                                    <select name="fund_transaction_id" id="fund_transaction_id" 
                                            class="form-control fund-select @error('fund_transaction_id') is-invalid @enderror" required>
                                        <option value="total" data-amount="{{ $availableBalance }}" selected>
                                            Total Available Funds: {{ number_format($availableBalance, 2) }}
                                        </option>
                                    </select>
                                    @error('fund_transaction_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="fund-error-message" id="fund-error" style="display: none; color: #dc3545; margin-top: 5px;">
                                        <i class="fas fa-exclamation-triangle"></i> Low Fund! Please Add Fund or contact admin for a manual payment.
                                    </div>
                                    <small class="form-text text-muted">
                                        <strong>Total Available Funds:</strong> {{ number_format($availableBalance, 2) }}
                                    </small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                        <i class="fas fa-save"></i> Submit Purchase Request(s)
                                    </button>
                                    <a href="{{ route('admin.purchase-requests.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please select a client to create purchase requests.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($selectedClientId)
    @section('css')
    <style>
        .request-item {
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
        .request-item .card-header {
            border-bottom: 2px solid #dee2e6;
        }
        .calculated-bdt {
            color: #28a745;
        }
        @media (max-width: 768px) {
            .request-item {
                margin-bottom: 1rem !important;
            }
            .btn-lg {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
    @stop

    @section('js')
    <script>
        let requestIndex = 1;
        const exchangeRate = {{ $exchangeRate ?? 1.0 }};
        const availableBalance = {{ $availableBalance }};

        // Add new request
        document.getElementById('add-request-btn').addEventListener('click', function() {
            const container = document.getElementById('requests-container');
            const firstRequest = container.querySelector('.request-item');
            const newRequest = firstRequest.cloneNode(true);
            
            newRequest.setAttribute('data-index', requestIndex);
            newRequest.querySelector('.card-header h5').innerHTML = 
                '<i class="fas fa-shopping-cart"></i> Purchase Request #' + (requestIndex + 1) + 
                '<button type="button" class="btn btn-sm btn-danger float-right remove-request"><i class="fas fa-times"></i> Remove</button>';
            
            // Update all inputs in the new request
            newRequest.querySelectorAll('select, input, textarea').forEach(function(input) {
                if (input.name) {
                    input.name = input.name.replace(/\[0\]/, '[' + requestIndex + ']');
                }
                if (input.id) {
                    input.id = input.id.replace('_0', '_' + requestIndex);
                }
                if (input.getAttribute('for')) {
                    input.setAttribute('for', input.getAttribute('for').replace('_0', '_' + requestIndex));
                }
                if (input.classList.contains('amount-inr') || input.classList.contains('gst-checkbox') || input.classList.contains('fund-select')) {
                    input.setAttribute('data-index', requestIndex);
                }
                if (input.classList.contains('calculated-bdt')) {
                    input.id = 'calculated_bdt_' + requestIndex;
                }
                // Clear values
                if (input.type !== 'checkbox') {
                    input.value = '';
                } else {
                    input.checked = false;
                }
                // Reset select to first option
                if (input.tagName === 'SELECT' && !input.classList.contains('fund-select')) {
                    input.selectedIndex = 0;
                }
            });
            
            // Remove fund dropdown from cloned request (it's now at the bottom)
            const fundDropdown = newRequest.querySelector('.fund-select');
            if (fundDropdown) {
                fundDropdown.closest('.col-md-6')?.remove();
            }
            
            // Update labels
            newRequest.querySelectorAll('label').forEach(function(label) {
                if (label.getAttribute('for')) {
                    label.setAttribute('for', label.getAttribute('for').replace('_0', '_' + requestIndex));
                }
            });
            
            // Show remove button
            newRequest.querySelector('.remove-request').style.display = 'block';
            
            container.appendChild(newRequest);
            requestIndex++;
            
            // Attach event listeners to new request
            attachEventListeners(newRequest);
        });

        // Remove request
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-request')) {
                const requestItem = e.target.closest('.request-item');
                if (document.querySelectorAll('.request-item').length > 1) {
                    requestItem.remove();
                    updateRequestNumbers();
                    calculateTotal();
                } else {
                    alert('At least one purchase request is required.');
                }
            }
        });

        // Update request numbers
        function updateRequestNumbers() {
            document.querySelectorAll('.request-item').forEach(function(item, index) {
                item.querySelector('.card-header h5').innerHTML = 
                    '<i class="fas fa-shopping-cart"></i> Purchase Request #' + (index + 1) + 
                    '<button type="button" class="btn btn-sm btn-danger float-right remove-request"' + 
                    (index === 0 ? ' style="display: none;"' : '') + '><i class="fas fa-times"></i> Remove</button>';
            });
        }

        // Calculate BDT amount for a request
        function calculateBDT(index) {
            const amountINR = parseFloat(document.getElementById('amount_inr_' + index).value) || 0;
            const isGstPayment = document.getElementById('is_gst_' + index).checked;
            
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
            
            document.getElementById('calculated_bdt_' + index).value = amountBDT.toFixed(2);
            calculateTotal();
        }

        // Calculate total BDT
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.calculated-bdt').forEach(function(input) {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('total-amount-bdt').textContent = '' + total.toFixed(2);
            validateTotalFund();
        }

        // Attach event listeners to a request item
        function attachEventListeners(requestItem) {
            const index = requestItem.getAttribute('data-index');
            const amountInput = requestItem.querySelector('.amount-inr');
            const gstCheckbox = requestItem.querySelector('.gst-checkbox');
            
            if (amountInput) {
                amountInput.addEventListener('input', function() {
                    calculateBDT(index);
                    validateTotalFund();
                });
            }
            
            if (gstCheckbox) {
                gstCheckbox.addEventListener('change', function() {
                    calculateBDT(index);
                    validateTotalFund();
                });
            }
        }
        
        // Validate total fund amount against total calculated BDT
        function validateTotalFund() {
            const fundSelect = document.getElementById('fund_transaction_id');
            const totalAmount = parseFloat(document.getElementById('total-amount-bdt').textContent.replace('', '').replace(',', '')) || 0;
            const fundError = document.getElementById('fund-error');
            
            if (!fundSelect || totalAmount === 0) {
                fundError.style.display = 'none';
                fundSelect.classList.remove('is-invalid');
                return true;
            }
            
            const selectedOption = fundSelect.options[fundSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                fundError.style.display = 'none';
                fundSelect.classList.remove('is-invalid');
                return true;
            }
            
            // Use total available balance for validation against total amount
            if (availableBalance < totalAmount) {
                fundError.style.display = 'block';
                fundSelect.classList.add('is-invalid');
                return false;
            } else {
                fundError.style.display = 'none';
                fundSelect.classList.remove('is-invalid');
                return true;
            }
        }
        
        // Validate fund when dropdown changes
        document.addEventListener('DOMContentLoaded', function() {
            const fundSelect = document.getElementById('fund_transaction_id');
            if (fundSelect) {
                fundSelect.addEventListener('change', function() {
                    validateTotalFund();
                });
            }
        });

        // Initialize event listeners for first request
        document.querySelectorAll('.request-item').forEach(function(item) {
            attachEventListeners(item);
        });

        // Restore form data from old input if validation failed
        @if(old('requests'))
            const oldRequests = @json(old('requests'));
            if (oldRequests && oldRequests.length > 1) {
                // Add additional request items if they existed
                for (let i = 1; i < oldRequests.length; i++) {
                    document.getElementById('add-request-btn').click();
                    
                    // Set values after a small delay to ensure DOM is ready
                    setTimeout(function() {
                        const index = i;
                        if (oldRequests[index]) {
                            const req = oldRequests[index];
                            
                            // Set vendor
                            const vendorSelect = document.getElementById('vendor_id_' + index);
                            if (vendorSelect && req.vendor_id) {
                                vendorSelect.value = req.vendor_id;
                            }
                            
                            // Set amount INR
                            const amountInput = document.getElementById('amount_inr_' + index);
                            if (amountInput && req.amount_inr) {
                                amountInput.value = req.amount_inr;
                                calculateBDT(index);
                            }
                            
                            // Set GST checkbox
                            const gstCheckbox = document.getElementById('is_gst_' + index);
                            if (gstCheckbox) {
                                gstCheckbox.checked = req.is_gst_payment == 1 || req.is_gst_payment == true;
                                calculateBDT(index);
                            }
                            
                            // Set description
                            const descriptionTextarea = document.getElementById('description_' + index);
                            if (descriptionTextarea && req.description) {
                                descriptionTextarea.value = req.description;
                            }
                        }
                    }, 100 * i);
                }
            }
            
            // Set values for first request
            if (oldRequests[0]) {
                const req = oldRequests[0];
                
                // Set vendor
                const vendorSelect = document.getElementById('vendor_id_0');
                if (vendorSelect && req.vendor_id) {
                    vendorSelect.value = req.vendor_id;
                }
                
                // Set amount INR
                const amountInput = document.getElementById('amount_inr_0');
                if (amountInput && req.amount_inr) {
                    amountInput.value = req.amount_inr;
                    calculateBDT(0);
                }
                
                // Set GST checkbox
                const gstCheckbox = document.getElementById('is_gst_0');
                if (gstCheckbox) {
                    gstCheckbox.checked = req.is_gst_payment == 1 || req.is_gst_payment == true;
                    calculateBDT(0);
                }
                
                // Set description
                const descriptionTextarea = document.getElementById('description_0');
                if (descriptionTextarea && req.description) {
                    descriptionTextarea.value = req.description;
                }
            }
        @endif

        // Form validation - prevent submission if insufficient funds
        document.getElementById('purchaseRequestForm').addEventListener('submit', function(e) {
            const totalAmount = parseFloat(document.getElementById('total-amount-bdt').textContent.replace('', '').replace(',', '')) || 0;
            
            // Validate total fund
            if (!validateTotalFund()) {
                e.preventDefault();
                // Show error message in alert box
                const fundError = document.getElementById('fund-error');
                if (fundError) {
                    fundError.style.display = 'block';
                }
                const fundSelect = document.getElementById('fund_transaction_id');
                if (fundSelect) {
                    fundSelect.classList.add('is-invalid');
                    fundSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            if (totalAmount > availableBalance) {
                e.preventDefault();
                // Show error message
                const fundError = document.getElementById('fund-error');
                if (fundError) {
                    fundError.style.display = 'block';
                }
                const fundSelect = document.getElementById('fund_transaction_id');
                if (fundSelect) {
                    fundSelect.classList.add('is-invalid');
                    fundSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        });
    </script>
    @stop
    @endif
@stop

