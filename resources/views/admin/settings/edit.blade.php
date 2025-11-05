@extends('adminlte::page')

@push('meta')
    @include('components.favicon')
@endpush

@section('title', 'Settings')

@section('content_header')
    <h1><i class="fas fa-cog"></i> Settings</h1>
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
                    <h3 class="card-title">System Settings</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="apps_home_url">Apps Home URL</label>
                            <input type="url" name="apps_home_url" id="apps_home_url" 
                                   class="form-control @error('apps_home_url') is-invalid @enderror" 
                                   value="{{ old('apps_home_url', $admin->apps_home_url) }}" 
                                   placeholder="https://example.com">
                            @error('apps_home_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">The URL for the "Apps Home" menu item (appears under Dashboard for both Admin and Customer)</small>
                        </div>

                        <hr>

                        <h5><strong>Footer Settings</strong></h5>

                        <div class="form-group">
                            <label for="footer_copyright_text">Copyright Text (Left Aligned)</label>
                            <input type="text" name="footer_copyright_text" id="footer_copyright_text" 
                                   class="form-control @error('footer_copyright_text') is-invalid @enderror" 
                                   value="{{ old('footer_copyright_text', $admin->footer_copyright_text) }}" 
                                   placeholder="Copyright © {{ date('Y') }} {{ $admin->app_name ?? 'App Name' }}">
                            @error('footer_copyright_text')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">This text will appear on the left side of the footer. App name will be automatically added.</small>
                        </div>

                        <div class="form-group">
                            <label for="footer_developer_name">Developer Name (Right Aligned)</label>
                            <input type="text" name="footer_developer_name" id="footer_developer_name" 
                                   class="form-control @error('footer_developer_name') is-invalid @enderror" 
                                   value="{{ old('footer_developer_name', $admin->footer_developer_name) }}" 
                                   placeholder="Developer Name">
                            @error('footer_developer_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Developer name will appear on the right side with "Developed by" prefix</small>
                        </div>

                        <div class="form-group">
                            <label for="footer_developer_link">Developer Link</label>
                            <input type="url" name="footer_developer_link" id="footer_developer_link" 
                                   class="form-control @error('footer_developer_link') is-invalid @enderror" 
                                   value="{{ old('footer_developer_link', $admin->footer_developer_link) }}" 
                                   placeholder="https://developer-website.com">
                            @error('footer_developer_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Link for the developer name (optional)</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
