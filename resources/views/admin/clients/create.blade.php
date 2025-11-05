@extends('adminlte::page')

@section('title', 'Create New Client')

@section('content_header')
    <h1><i class="fas fa-user-plus"></i> Create New Client</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Client</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Registered name for the client account</small>
                        </div>

                        <div class="form-group">
                            <label for="business_name">Business Name (Client Name)</label>
                            <input type="text" name="business_name" id="business_name" 
                                   class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name') }}">
                            @error('business_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <div class="custom-file">
                                <input type="file" name="logo" id="logo" 
                                       class="custom-file-input @error('logo') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(this)">
                                <label class="custom-file-label" for="logo">Choose file</label>
                                @error('logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Upload client logo (JPEG, PNG, JPG, GIF, SVG - Max 2MB)</small>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="preview" src="" alt="Preview" style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact_person_name">Contact Person Name</label>
                            <input type="text" name="contact_person_name" id="contact_person_name" 
                                   class="form-control @error('contact_person_name') is-invalid @enderror" 
                                   value="{{ old('contact_person_name') }}">
                            @error('contact_person_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mobile_number">Mobile Number</label>
                            <input type="text" name="mobile_number" id="mobile_number" 
                                   class="form-control @error('mobile_number') is-invalid @enderror" 
                                   value="{{ old('mobile_number') }}" placeholder="e.g., +880 1234567890">
                            @error('mobile_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-control" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="exchange_rate">Exchange Rate (BDT to INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">1 BDT = ₹</span>
                                </div>
                                <input type="number" name="exchange_rate" id="exchange_rate" 
                                       class="form-control @error('exchange_rate') is-invalid @enderror" 
                                       value="{{ old('exchange_rate', 1.0) }}" 
                                       step="0.0001" min="0.0001" required>
                                @error('exchange_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Set the conversion rate for purchase requests. This will be shown to the client.</small>
                        </div>

                        <div class="form-group">
                            <label for="lowest_shipping_charge_per_kg">Lowest Shipping Charge per Kg (BDT) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">৳</span>
                                </div>
                                <input type="number" name="lowest_shipping_charge_per_kg" id="lowest_shipping_charge_per_kg" 
                                       class="form-control @error('lowest_shipping_charge_per_kg') is-invalid @enderror" 
                                       value="{{ old('lowest_shipping_charge_per_kg', 0) }}" 
                                       step="0.01" min="0" required>
                                @error('lowest_shipping_charge_per_kg')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> This charge will be used for calculating packaging cost in provisional invoices.
                                <br><strong>Formula:</strong> Packaging Cost = Packaging Weight × Lowest Shipping Charge per Kg
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Client
                            </button>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
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
