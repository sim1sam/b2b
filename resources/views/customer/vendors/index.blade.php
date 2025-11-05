@extends('adminlte::page')

@section('title', 'My Vendors')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-store"></i> My Vendors</h1>
        <a href="{{ route('customer.vendors.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add
        </a>
    </div>
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

    <div class="card">
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="d-none d-md-block">
                <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendor Name</th>
                            <th>GSTIN</th>
                            <th>Contact Number</th>
                            <th>Account Details</th>
                            <th>Payment Number</th>
                            <th>QR Code</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            <tr>
                                <td>{{ $vendor->id }}</td>
                                <td title="{{ $vendor->vendor_name }}"><strong>{{ $vendor->vendor_name }}</strong></td>
                                <td title="{{ $vendor->gstin ?? 'N/A' }}">{{ $vendor->gstin ?? 'N/A' }}</td>
                                <td title="{{ $vendor->contact_number }}">
                                    {{ Str::limit($vendor->contact_number, 11) }}
                                </td>
                                <td style="max-width: 250px; word-wrap: break-word;">
                                    @if($vendor->account_details)
                                        <div class="d-flex align-items-start">
                                            <span class="mr-2" id="account-details-{{ $vendor->id }}" style="word-break: break-word; white-space: pre-wrap;">{{ $vendor->account_details }}</span>
                                            <button type="button" class="btn btn-sm btn-info copy-btn flex-shrink-0" data-copy-target="account-details-{{ $vendor->id }}" title="Copy to clipboard">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vendor->payment_number)
                                        <span class="badge badge-info" title="{{ $vendor->payment_number }}">
                                            {{ Str::limit($vendor->payment_number, 11) }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td style="padding: 8px;">
                                    @if($vendor->qr_code)
                                        <img src="{{ Storage::url($vendor->qr_code) }}" alt="QR Code" style="width: 90px; height: 90px; object-fit: cover; cursor: pointer;" class="img-thumbnail" onclick="window.open('{{ Storage::url($vendor->qr_code) }}', '_blank')">
                                    @else
                                        <span class="text-muted">No QR Code</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vendor->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <a href="{{ route('customer.vendors.show', $vendor) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customer.vendors.edit', $vendor) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customer.vendors.destroy', $vendor) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vendor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No vendors found. <a href="{{ route('customer.vendors.create') }}">Create one now</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($vendors as $vendor)
                    <div class="card mb-3 border-left-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 font-weight-bold">
                                        {{ $vendor->vendor_name }}
                                    </h5>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-hashtag"></i> ID: {{ $vendor->id }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($vendor->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">GSTIN</small>
                                    <span>{{ $vendor->gstin ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Contact</small>
                                    <span>{{ $vendor->contact_number }}</span>
                                </div>
                            </div>

                            @if($vendor->payment_number)
                            <div class="mb-2">
                                <small class="text-muted d-block">Payment Number</small>
                                <span class="badge badge-info">{{ $vendor->payment_number }}</span>
                            </div>
                            @endif

                            @if($vendor->account_details)
                            <div class="mb-2">
                                <small class="text-muted d-block">Account Details</small>
                                <div class="d-flex align-items-start">
                                    <span class="mr-2 flex-grow-1" id="account-details-mobile-{{ $vendor->id }}" style="word-break: break-word; white-space: pre-wrap; font-size: 0.875rem;">{{ $vendor->account_details }}</span>
                                    <button type="button" class="btn btn-sm btn-info copy-btn flex-shrink-0" data-copy-target="account-details-mobile-{{ $vendor->id }}" title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            @endif

                            @if($vendor->qr_code)
                            <div class="mb-2">
                                <small class="text-muted d-block">QR Code</small>
                                <img src="{{ Storage::url($vendor->qr_code) }}" alt="QR Code" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" class="img-thumbnail" onclick="window.open('{{ Storage::url($vendor->qr_code) }}', '_blank')">
                            </div>
                            @endif

                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('customer.vendors.show', $vendor) }}" class="btn btn-sm btn-info flex-fill">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('customer.vendors.edit', $vendor) }}" class="btn btn-sm btn-warning flex-fill">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('customer.vendors.destroy', $vendor) }}" method="POST" class="flex-fill" onsubmit="return confirm('Are you sure you want to delete this vendor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No vendors found.</p>
                        <a href="{{ route('customer.vendors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create one now
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="card-footer">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    /* Desktop table styles */
    @media (min-width: 768px) {
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            padding: 8px 6px;
            font-size: 0.875rem;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody td {
            padding: 8px 6px;
            font-size: 0.875rem;
        }
    }
    /* Remove scrollbars */
    .card-body {
        overflow-x: hidden;
    }
    .table-responsive {
        overflow-x: visible;
    }
    /* Mobile styles */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 0.75rem;
        }
        .card.mb-3 {
            margin-bottom: 0.75rem !important;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }
    @media (max-width: 575.98px) {
        .flex-fill {
            flex: 1 1 auto;
        }
        .d-flex.gap-2 {
            flex-wrap: wrap;
        }
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Copy to clipboard functionality
        $('.copy-btn').on('click', function() {
            const targetId = $(this).data('copy-target');
            const textToCopy = $('#' + targetId).text();
            
            // Create temporary textarea element
            const tempTextarea = $('<textarea>');
            tempTextarea.val(textToCopy);
            $('body').append(tempTextarea);
            tempTextarea.select();
            
            try {
                document.execCommand('copy');
                // Show feedback
                const originalHtml = $(this).html();
                $(this).html('<i class="fas fa-check"></i>');
                $(this).removeClass('btn-info').addClass('btn-success');
                
                setTimeout(() => {
                    $(this).html(originalHtml);
                    $(this).removeClass('btn-success').addClass('btn-info');
                }, 2000);
            } catch (err) {
                alert('Failed to copy text');
            }
            
            tempTextarea.remove();
        });
    });
</script>
@stop

