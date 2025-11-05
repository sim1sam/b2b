@extends('adminlte::page')

@section('title', 'My Vendors')

@section('content_header')
    <h1><i class="fas fa-store"></i> My Vendors</h1>
    <a href="{{ route('customer.vendors.create') }}" class="btn btn-primary float-right">
        <i class="fas fa-plus"></i> Add New Vendor
    </a>
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
        <div class="card-body">
            <table class="table table-bordered table-striped" style="table-layout: auto;">
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
                            <td><strong>{{ $vendor->vendor_name }}</strong></td>
                            <td>{{ $vendor->gstin ?? 'N/A' }}</td>
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

            {{ $vendors->links() }}
        </div>
    </div>
@stop

@section('css')
<style>
    .table tbody tr {
        min-height: 60px;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 12px 8px;
    }
    /* Account Details column - wider */
    .table thead th:nth-child(5),
    .table tbody td:nth-child(5) {
        width: 250px;
        max-width: 250px;
        min-width: 250px;
    }
    /* Contact Number column - narrower, show only 11 digits */
    .table thead th:nth-child(4),
    .table tbody td:nth-child(4) {
        width: 120px;
        max-width: 120px;
        min-width: 120px;
    }
    /* Payment Number column - narrower, show only 11 digits */
    .table thead th:nth-child(6),
    .table tbody td:nth-child(6) {
        width: 120px;
        max-width: 120px;
        min-width: 120px;
    }
    /* QR Code column - extended */
    .table thead th:nth-child(7),
    .table tbody td:nth-child(7) {
        width: 100px;
        max-width: 100px;
        min-width: 100px;
    }
    /* Actions column - reduced to fit 3 buttons */
    .table thead th:nth-child(9),
    .table tbody td:nth-child(9) {
        width: 110px;
        max-width: 110px;
        min-width: 110px;
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

