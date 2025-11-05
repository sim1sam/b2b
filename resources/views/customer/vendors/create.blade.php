@extends('adminlte::page')

@section('title', 'Add New Vendor')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Add New Vendor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customer.vendors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vendor_name">Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" name="vendor_name" id="vendor_name" 
                                   class="form-control @error('vendor_name') is-invalid @enderror" 
                                   value="{{ old('vendor_name') }}" required>
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
                                   value="{{ old('gstin') }}" maxlength="15" placeholder="e.g., 29ABCDE1234F1Z5">
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
                           value="{{ old('contact_number') }}" required placeholder="+91 9876543210">
                    @error('contact_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_details">Account Details</label>
                    <textarea name="account_details" id="account_details" rows="3" 
                              class="form-control @error('account_details') is-invalid @enderror" 
                              placeholder="Bank account number, IFSC code, etc.">{{ old('account_details') }}</textarea>
                    @error('account_details')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_number">GPay/PhonePe Number</label>
                    <input type="text" name="payment_number" id="payment_number" 
                           class="form-control @error('payment_number') is-invalid @enderror" 
                           value="{{ old('payment_number') }}" placeholder="e.g., 9876543210">
                    @error('payment_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Enter GPay or PhonePe number</small>
                </div>

                <div class="form-group">
                    <label for="qr_code">QR Code Image</label>
                    <div class="custom-file">
                        <input type="file" name="qr_code" id="qr_code" 
                               class="custom-file-input @error('qr_code') is-invalid @enderror" 
                               accept="image/*" onchange="previewImage(this)">
                        <label class="custom-file-label" for="qr_code">Choose file (JPEG, PNG, JPG, GIF, SVG - Max 2MB)</label>
                        @error('qr_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Upload QR code for payment (optional)</small>
                    <div id="imagePreview" class="mt-2" style="display: none;">
                        <img id="preview" src="" alt="Preview" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" 
                               class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active Vendor
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Vendor
                    </button>
                    <a href="{{ route('customer.vendors.index') }}" class="btn btn-secondary">
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

