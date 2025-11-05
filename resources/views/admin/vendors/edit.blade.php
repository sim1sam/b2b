@extends('adminlte::page')

@section('title', 'Edit Vendor')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Edit Vendor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.vendors.update', $vendor) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="user_id">Assign to Client (Optional)</label>
                    <select name="user_id" id="user_id" 
                            class="form-control @error('user_id') is-invalid @enderror">
                        <option value="">-- No Client (General Vendor) --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('user_id', $vendor->user_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->business_name ?? $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Select a client to assign this vendor to, or leave empty for a general vendor available to all clients.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vendor_name">Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" name="vendor_name" id="vendor_name" 
                                   class="form-control @error('vendor_name') is-invalid @enderror" 
                                   value="{{ old('vendor_name', $vendor->vendor_name) }}" required>
                            @error('vendor_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gstin">GSTIN (Optional)</label>
                            <input type="text" name="gstin" id="gstin" 
                                   class="form-control @error('gstin') is-invalid @enderror" 
                                   value="{{ old('gstin', $vendor->gstin) }}" maxlength="15">
                            @error('gstin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="contact_number" id="contact_number" 
                           class="form-control @error('contact_number') is-invalid @enderror" 
                           value="{{ old('contact_number', $vendor->contact_number) }}" required>
                    @error('contact_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_details">Account Details</label>
                    <textarea name="account_details" id="account_details" rows="3" 
                              class="form-control @error('account_details') is-invalid @enderror">{{ old('account_details', $vendor->account_details) }}</textarea>
                    @error('account_details')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_number">GPay/PhonePe Number</label>
                    <input type="text" name="payment_number" id="payment_number" 
                           class="form-control @error('payment_number') is-invalid @enderror" 
                           value="{{ old('payment_number', $vendor->payment_number) }}">
                    @error('payment_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Enter GPay or PhonePe number</small>
                </div>

                <div class="form-group">
                    <label for="qr_code">QR Code Image</label>
                    @if($vendor->qr_code)
                        <div class="mb-2">
                            <p>Current QR Code:</p>
                            <img src="{{ Storage::url($vendor->qr_code) }}" alt="Current QR Code" 
                                 style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" name="qr_code" id="qr_code" 
                               class="custom-file-input @error('qr_code') is-invalid @enderror" 
                               accept="image/*" onchange="previewImage(this)">
                        <label class="custom-file-label" for="qr_code">Choose new file (leave empty to keep current)</label>
                        @error('qr_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Upload new QR code to replace existing one</small>
                    <div id="imagePreview" class="mt-2" style="display: none;">
                        <img id="preview" src="" alt="Preview" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" 
                               class="form-check-input" value="1" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active Vendor
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Vendor
                    </button>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @section('js')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = e.target.files[0]?.name || 'Choose file';
            e.target.nextElementSibling.textContent = fileName;
        });
    </script>
    @stop
@stop

