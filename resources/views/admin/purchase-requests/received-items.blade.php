@extends('adminlte::page')

@section('title', 'Cross-Check Received Items')

@section('content_header')
    <h1><i class="fas fa-check-double"></i> Cross-Check Received Items</h1>
    <a href="{{ route('admin.purchase-requests.show', $purchaseRequest->id) }}" class="btn btn-secondary float-right">
        <i class="fas fa-arrow-left"></i> Back to Purchase Order
    </a>
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
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart"></i> PO Number: {{ $purchaseRequest->po_number ?? $purchaseRequest->request_number }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Client:</strong> {{ $purchaseRequest->user->business_name ?? $purchaseRequest->user->name }}
                        <br><strong>Vendor:</strong> {{ $purchaseRequest->vendor->vendor_name }}
                    </div>

                    @if($shippingCharges->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No shipping charges configured for this client. 
                            <a href="{{ route('admin.shipping-charges.create') }}?user_id={{ $purchaseRequest->user_id }}">Configure shipping charges first</a>
                        </div>
                    @endif

                    <form action="{{ route('admin.purchase-requests.store-received-items', $purchaseRequest->id) }}" method="POST" id="receivedItemsForm">
                        @csrf

                        <div id="items-container">
                            <!-- Item 1 -->
                            <div class="item-row card mb-3" data-index="0">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-box"></i> Item #1
                                        <button type="button" class="btn btn-sm btn-danger float-right remove-item" style="display: none;">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="item_0">Item <span class="text-danger">*</span></label>
                                            <select name="items[0][shipping_charge_id]" id="item_0" 
                                                    class="form-control item-select @error('items.0.shipping_charge_id') is-invalid @enderror" 
                                                    data-index="0" required>
                                                <option value="">-- Select Item --</option>
                                                @foreach($shippingCharges as $charge)
                                                    <option value="{{ $charge->id }}" 
                                                            data-rate="{{ $charge->rate_per_unit }}" 
                                                            data-shipping="{{ $charge->shipping_charge_per_kg }}">
                                                        {{ $charge->item_name }} 
                                                        (Rate: ৳{{ number_format($charge->rate_per_unit, 2) }}/{{ $charge->unit_type === 'qty' ? 'unit' : 'kg' }}, 
                                                        Shipping: ৳{{ number_format($charge->shipping_charge_per_kg, 2) }}/kg)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('items.0.shipping_charge_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="item_name_0">Item Name <span class="text-danger">*</span></label>
                                            <input type="text" name="items[0][item_name]" id="item_name_0" 
                                                   class="form-control @error('items.0.item_name') is-invalid @enderror" 
                                                   value="{{ old('items.0.item_name') }}" required>
                                            @error('items.0.item_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="quantity_0">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="items[0][quantity]" id="quantity_0" 
                                                   class="form-control quantity-input @error('items.0.quantity') is-invalid @enderror" 
                                                   value="{{ old('items.0.quantity') }}" step="0.01" min="0.01" 
                                                   data-index="0" required>
                                            @error('items.0.quantity')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="weight_0">Weight (Kg) <span class="text-danger">*</span></label>
                                            <input type="number" name="items[0][weight]" id="weight_0" 
                                                   class="form-control weight-input @error('items.0.weight') is-invalid @enderror" 
                                                   value="{{ old('items.0.weight', 0) }}" step="0.01" min="0" 
                                                   data-index="0" required>
                                            @error('items.0.weight')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label>Calculated Cost</label>
                                            <input type="text" class="form-control calculated-cost" 
                                                   id="calculated_cost_0" readonly 
                                                   value="৳0.00" style="background-color: #f8f9fa; font-weight: bold;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right mb-3">
                            <button type="button" class="btn btn-info" id="add-item-btn" {{ $shippingCharges->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-plus"></i> Add Another Item
                            </button>
                        </div>

                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-calculator"></i>
                            <strong>Items Total Cost:</strong> 
                            <span id="total-cost" class="font-weight-bold" style="font-size: 1.2em;">৳0.00</span>
                            <br><small class="text-muted">(Packaging cost and transportation charge will be calculated separately)</small>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-truck"></i> Transportation / Delivery Charge (Optional)</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="transportation_charge">Transportation / Delivery Charge (BDT)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">৳</span>
                                        </div>
                                        <input type="number" 
                                               name="transportation_charge" 
                                               id="transportation_charge" 
                                               class="form-control @error('transportation_charge') is-invalid @enderror" 
                                               value="{{ old('transportation_charge', $purchaseRequest->transportation_charge ?? 0) }}" 
                                               step="0.01" min="0">
                                        @error('transportation_charge')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Enter transportation or delivery charge if applicable</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg" {{ $shippingCharges->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i> Submit & Generate Provisional Invoice
                            </button>
                            <a href="{{ route('admin.purchase-requests.show', $purchaseRequest->id) }}" class="btn btn-secondary btn-lg">
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
        let itemIndex = 1;

        // Add new item
        document.getElementById('add-item-btn')?.addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const firstItem = container.querySelector('.item-row');
            const newItem = firstItem.cloneNode(true);
            
            newItem.setAttribute('data-index', itemIndex);
            newItem.querySelector('.card-header h5').innerHTML = 
                '<i class="fas fa-box"></i> Item #' + (itemIndex + 1) + 
                '<button type="button" class="btn btn-sm btn-danger float-right remove-item"><i class="fas fa-times"></i> Remove</button>';
            
            // Update all inputs in the new item
            newItem.querySelectorAll('select, input').forEach(function(input) {
                if (input.name) {
                    input.name = input.name.replace(/\[0\]/, '[' + itemIndex + ']');
                }
                if (input.id) {
                    input.id = input.id.replace('_0', '_' + itemIndex);
                }
                if (input.classList.contains('item-select') || input.classList.contains('quantity-input') || input.classList.contains('weight-input')) {
                    input.setAttribute('data-index', itemIndex);
                }
                if (input.classList.contains('calculated-cost')) {
                    input.id = 'calculated_cost_' + itemIndex;
                }
                // Clear values
                if (input.type !== 'checkbox') {
                    input.value = '';
                }
                // Reset select to first option
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
            
            // Show remove button
            newItem.querySelector('.remove-item').style.display = 'block';
            
            container.appendChild(newItem);
            itemIndex++;
            
            // Attach event listeners to new item
            attachEventListeners(newItem);
        });

        // Remove item
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                const itemRow = e.target.closest('.item-row');
                if (document.querySelectorAll('.item-row').length > 1) {
                    itemRow.remove();
                    updateItemNumbers();
                    calculateTotal();
                } else {
                    alert('At least one item is required.');
                }
            }
        });

        // Update item numbers
        function updateItemNumbers() {
            document.querySelectorAll('.item-row').forEach(function(item, index) {
                item.querySelector('.card-header h5').innerHTML = 
                    '<i class="fas fa-box"></i> Item #' + (index + 1) + 
                    '<button type="button" class="btn btn-sm btn-danger float-right remove-item"' + 
                    (index === 0 ? ' style="display: none;"' : '') + '><i class="fas fa-times"></i> Remove</button>';
            });
        }

        // Calculate cost for an item
        function calculateCost(index) {
            const itemSelect = document.getElementById('item_' + index);
            const quantityInput = document.getElementById('quantity_' + index);
            const weightInput = document.getElementById('weight_' + index);
            const costDisplay = document.getElementById('calculated_cost_' + index);
            
            if (!itemSelect || !quantityInput || !weightInput || !costDisplay) return;
            
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                costDisplay.value = '৳0.00';
                calculateTotal();
                return;
            }
            
            const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
            const shippingRate = parseFloat(selectedOption.getAttribute('data-shipping')) || 0;
            const quantity = parseFloat(quantityInput.value) || 0;
            const weight = parseFloat(weightInput.value) || 0;
            
            const itemCost = quantity * rate;
            const shippingCost = weight * shippingRate;
            const totalCost = itemCost + shippingCost;
            
            costDisplay.value = '৳' + totalCost.toFixed(2);
            calculateTotal();
        }

        // Calculate total cost
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.calculated-cost').forEach(function(input) {
                const value = parseFloat(input.value.replace('৳', '').replace(',', '')) || 0;
                total += value;
            });
            document.getElementById('total-cost').textContent = '৳' + total.toFixed(2);
        }

        // Attach event listeners to an item row
        function attachEventListeners(itemRow) {
            const index = itemRow.getAttribute('data-index');
            const itemSelect = itemRow.querySelector('.item-select');
            const quantityInput = itemRow.querySelector('.quantity-input');
            const weightInput = itemRow.querySelector('.weight-input');
            
            if (itemSelect) {
                itemSelect.addEventListener('change', function() {
                    // Auto-fill item name from selected option
                    const itemNameInput = document.getElementById('item_name_' + index);
                    if (itemNameInput && this.selectedIndex > 0) {
                        const optionText = this.options[this.selectedIndex].text;
                        const itemName = optionText.split(' (Rate:')[0];
                        itemNameInput.value = itemName;
                    }
                    calculateCost(index);
                });
            }
            
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    calculateCost(index);
                });
            }
            
            if (weightInput) {
                weightInput.addEventListener('input', function() {
                    calculateCost(index);
                });
            }
        }

        // Initialize event listeners for first item
        document.querySelectorAll('.item-row').forEach(function(item) {
            attachEventListeners(item);
        });
    </script>
    @stop
@stop

