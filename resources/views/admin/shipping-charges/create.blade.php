@extends('adminlte::page')

@section('title', 'Create Shipping Charge')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Create Shipping Charge</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Shipping Charge</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shipping-charges.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="user_id">Client <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->business_name ?? $client->name }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="item_name">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" id="item_name" 
                                   class="form-control @error('item_name') is-invalid @enderror" 
                                   value="{{ old('item_name') }}" required>
                            @error('item_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rate_per_unit">Rate per Unit (BDT) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_per_unit" id="rate_per_unit" 
                                   class="form-control @error('rate_per_unit') is-invalid @enderror" 
                                   value="{{ old('rate_per_unit') }}" step="0.01" min="0" required>
                            @error('rate_per_unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Rate per quantity or weight unit</small>
                        </div>

                        <div class="form-group">
                            <label for="unit_type">Unit Type <span class="text-danger">*</span></label>
                            <select name="unit_type" id="unit_type" class="form-control @error('unit_type') is-invalid @enderror" required>
                                <option value="qty" {{ old('unit_type') == 'qty' ? 'selected' : '' }}>Quantity</option>
                                <option value="weight" {{ old('unit_type') == 'weight' ? 'selected' : '' }}>Weight</option>
                            </select>
                            @error('unit_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_charge_per_kg">Shipping Charge per Kg (BDT) <span class="text-danger">*</span></label>
                            <input type="number" name="shipping_charge_per_kg" id="shipping_charge_per_kg" 
                                   class="form-control @error('shipping_charge_per_kg') is-invalid @enderror" 
                                   value="{{ old('shipping_charge_per_kg', 0) }}" step="0.01" min="0" required>
                            @error('shipping_charge_per_kg')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Shipping Charge
                            </button>
                            <a href="{{ route('admin.shipping-charges.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

