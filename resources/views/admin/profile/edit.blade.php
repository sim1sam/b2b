@extends('adminlte::page')

@push('meta')
    @include('components.favicon')
@endpush

@push('js')
    @include('components.footer')
@endpush

@section('title', 'Admin Profile')

@section('content_header')
    <h1><i class="fas fa-user"></i> Admin Profile</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Admin Profile</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" 
                                   class="form-control @error('company_name') is-invalid @enderror" 
                                   value="{{ old('company_name', $admin->company_name) }}">
                            @error('company_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="admin_logo">Admin Logo (For Invoices)</label>
                            @if($admin->admin_logo)
                                <div class="mb-2">
                                    <p>Current Logo:</p>
                                    <img src="{{ Storage::url($admin->admin_logo) }}" alt="Current Logo" 
                                         style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="admin_logo" id="admin_logo" 
                                       class="custom-file-input @error('admin_logo') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(this, 'previewAdminLogo')">
                                <label class="custom-file-label" for="admin_logo">Choose new file (leave empty to keep current)</label>
                                @error('admin_logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Upload admin logo for invoices (JPEG, PNG, JPG, GIF, SVG - Max 2MB)</small>
                            <div id="previewAdminLogo" class="mt-2" style="display: none;">
                                <img id="previewAdminLogoImg" src="" alt="Preview" style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="system_logo">System Logo (AdminLTE Logo)</label>
                            @if($admin->system_logo)
                                <div class="mb-2">
                                    <p>Current Logo:</p>
                                    <img src="{{ Storage::url($admin->system_logo) }}" alt="Current System Logo" 
                                         style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="system_logo" id="system_logo" 
                                       class="custom-file-input @error('system_logo') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(this, 'previewSystemLogo')">
                                <label class="custom-file-label" for="system_logo">Choose new file (leave empty to keep current)</label>
                                @error('system_logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Upload system logo for AdminLTE navbar (JPEG, PNG, JPG, GIF, SVG - Max 2MB)</small>
                            <div id="previewSystemLogo" class="mt-2" style="display: none;">
                                <img id="previewSystemLogoImg" src="" alt="Preview" style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="favicon">Favicon (Site Icon)</label>
                            @if($admin->favicon)
                                <div class="mb-2">
                                    <p>Current Favicon:</p>
                                    <img src="{{ Storage::url($admin->favicon) }}" alt="Current Favicon" 
                                         style="max-width: 32px; max-height: 32px;" class="img-thumbnail">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="favicon" id="favicon" 
                                       class="custom-file-input @error('favicon') is-invalid @enderror" 
                                       accept=".ico,.png" onchange="previewImage(this, 'previewFavicon')">
                                <label class="custom-file-label" for="favicon">Choose new file (leave empty to keep current)</label>
                                @error('favicon')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Upload favicon (ICO or PNG - Max 512KB, recommended: 32x32 or 16x16)</small>
                            <div id="previewFavicon" class="mt-2" style="display: none;">
                                <img id="previewFaviconImg" src="" alt="Preview" style="max-width: 32px; max-height: 32px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="app_name">App Name</label>
                            <input type="text" name="app_name" id="app_name" 
                                   class="form-control @error('app_name') is-invalid @enderror" 
                                   value="{{ old('app_name', $admin->app_name) }}" placeholder="e.g., B2B Portal">
                            @error('app_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">App name will be displayed after system logo in the navbar</small>
                        </div>

                        <div class="form-group">
                            <label for="address_line1">Address Line 1</label>
                            <input type="text" name="address_line1" id="address_line1" 
                                   class="form-control @error('address_line1') is-invalid @enderror" 
                                   value="{{ old('address_line1', $admin->address_line1) }}">
                            @error('address_line1')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address_line2">Address Line 2</label>
                            <input type="text" name="address_line2" id="address_line2" 
                                   class="form-control @error('address_line2') is-invalid @enderror" 
                                   value="{{ old('address_line2', $admin->address_line2) }}">
                            @error('address_line2')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="district">District</label>
                            <input type="text" name="district" id="district" 
                                   class="form-control @error('district') is-invalid @enderror" 
                                   value="{{ old('district', $admin->district) }}">
                            @error('district')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" name="country" id="country" 
                                   class="form-control @error('country') is-invalid @enderror" 
                                   value="{{ old('country', $admin->country) }}">
                            @error('country')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mobile_number">Mobile Number</label>
                            <input type="text" name="mobile_number" id="mobile_number" 
                                   class="form-control @error('mobile_number') is-invalid @enderror" 
                                   value="{{ old('mobile_number', $admin->mobile_number) }}" placeholder="e.g., +880 1234567890">
                            @error('mobile_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="{{ route('admin.profile.change-password') }}" class="btn btn-secondary">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('js')
    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var previewDiv = document.getElementById(previewId);
                    var previewImg = previewDiv.querySelector('img');
                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewDiv.style.display = 'block';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.querySelectorAll('.custom-file-input').forEach(function(input) {
            input.addEventListener('change', function(e) {
                var fileName = e.target.files[0]?.name || 'Choose file';
                e.target.nextElementSibling.textContent = fileName;
            });
        });
    </script>
    @stop
@stop
