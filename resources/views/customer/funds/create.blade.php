@extends('adminlte::page')

@section('title', 'Add Funds')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Add Funds</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fund Deposit Request</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Current Available Balance:</strong> ৳{{ number_format($availableBalance, 2) }}
                    </div>

                    <form action="{{ route('customer.funds.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">৳</span>
                                </div>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" 
                                       step="0.01" min="1" required placeholder="Enter amount">
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Minimum amount: ৳1.00</small>
                        </div>

                        <div class="form-group">
                            <label for="payment_screenshot">Payment Screenshot/Slip <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="payment_screenshot" id="payment_screenshot" 
                                       class="custom-file-input @error('payment_screenshot') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(this)" required>
                                <label class="custom-file-label" for="payment_screenshot">Choose file (JPEG, PNG, JPG, GIF, SVG - Max 5MB)</label>
                                @error('payment_screenshot')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Upload payment screenshot or bank slip</small>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="preview" src="" alt="Preview" style="max-width: 300px; max-height: 300px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Payment reference number, transaction ID, or any additional notes">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Your deposit request will be reviewed by admin. Once approved, funds will be added to your account.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Deposit Request
                            </button>
                            <a href="{{ route('customer.funds.index') }}" class="btn btn-secondary">
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

