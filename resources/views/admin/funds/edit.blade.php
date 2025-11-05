@extends('adminlte::page')

@section('title', 'Edit Fund Transaction')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Edit Fund Transaction</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction #{{ $fundTransaction->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Customer:</strong> {{ $fundTransaction->user->name }} ({{ $fundTransaction->user->email }})
                    </div>

                    <form action="{{ route('admin.funds.update', $fundTransaction) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"></span>
                                </div>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $fundTransaction->amount) }}" 
                                       step="0.01" min="0.01" required>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Original amount: {{ number_format($fundTransaction->amount, 2) }}</small>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" 
                                    class="form-control @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $fundTransaction->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $fundTransaction->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $fundTransaction->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group" id="adminNoteGroup">
                            <label for="admin_note">Admin Note</label>
                            <textarea name="admin_note" id="admin_note" rows="4" 
                                      class="form-control @error('admin_note') is-invalid @enderror" 
                                      placeholder="Enter admin note (required if rejecting)">{{ old('admin_note', $fundTransaction->admin_note) }}</textarea>
                            @error('admin_note')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <span id="noteHelp">Optional note for internal use or customer communication.</span>
                                <span id="rejectHelp" style="display: none;" class="text-danger">Required when rejecting a transaction.</span>
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Changing the status to "Approved" will add funds to the customer's balance. 
                            Rejecting will notify the customer with the admin note.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Transaction
                            </button>
                            <a href="{{ route('admin.funds.show', $fundTransaction->id) }}" class="btn btn-secondary">
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
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const adminNoteGroup = document.getElementById('adminNoteGroup');
            const adminNote = document.getElementById('admin_note');
            const noteHelp = document.getElementById('noteHelp');
            const rejectHelp = document.getElementById('rejectHelp');

            if (status === 'rejected') {
                adminNoteGroup.querySelector('label').innerHTML = 'Rejection Reason <span class="text-danger">*</span>';
                adminNote.setAttribute('required', 'required');
                noteHelp.style.display = 'none';
                rejectHelp.style.display = 'inline';
            } else {
                adminNoteGroup.querySelector('label').innerHTML = 'Admin Note';
                adminNote.removeAttribute('required');
                noteHelp.style.display = 'inline';
                rejectHelp.style.display = 'none';
            }
        });

        // Trigger on page load
        document.getElementById('status').dispatchEvent(new Event('change'));
    </script>
    @stop
@stop

