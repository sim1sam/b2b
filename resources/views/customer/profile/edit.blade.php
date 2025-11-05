@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <h1><i class="fas fa-user-edit"></i> Edit Profile</h1>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Profile</h3>
                </div>
                <div class="card-body">
                    @if($user->exchange_rate)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Exchange Rate:</strong> 1 BDT = ₹{{ number_format($user->exchange_rate, 4) }} (INR)
                    </div>
                    @endif

                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="business_name">Business Name (Client Name) <span class="text-danger">*</span></label>
                            <input type="text" name="business_name" id="business_name" 
                                   class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name', $user->business_name) }}" required>
                            @error('business_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="logo">Logo</label>
                            @if($user->logo)
                                <div class="mb-2">
                                    <p>Current Logo:</p>
                                    <img src="{{ Storage::url($user->logo) }}" alt="Current Logo" 
                                         style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="logo" id="logo" 
                                       class="custom-file-input @error('logo') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(this)">
                                <label class="custom-file-label" for="logo">Choose new file (leave empty to keep current)</label>
                                @error('logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Upload your business logo (JPEG, PNG, JPG, GIF, SVG - Max 2MB)</small>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="preview" src="" alt="Preview" style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact_person_name">Contact Person Name</label>
                            <input type="text" name="contact_person_name" id="contact_person_name" 
                                   class="form-control @error('contact_person_name') is-invalid @enderror" 
                                   value="{{ old('contact_person_name', $user->contact_person_name) }}">
                            @error('contact_person_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mobile_number">Mobile Number</label>
                            <input type="text" name="mobile_number" id="mobile_number" 
                                   class="form-control @error('mobile_number') is-invalid @enderror" 
                                   value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="e.g., +880 1234567890">
                            @error('mobile_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($user->exchange_rate)
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Exchange Rate</h3>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary">1 BDT = ₹{{ number_format($user->exchange_rate, 4) }}</h2>
                    <p class="text-muted">This rate will be used for purchase requests</p>
                </div>
            </div>
            @endif
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

