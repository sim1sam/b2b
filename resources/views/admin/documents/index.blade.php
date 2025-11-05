@extends('adminlte::page')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Documents')

@section('content_header')
    <h1><i class="fas fa-file-alt"></i> Documents</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">All Purchase Requests</h3>
                <form action="{{ route('admin.documents.index') }}" method="GET" class="d-flex">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Search PO Number, Client, Vendor..." 
                           value="{{ request('search') }}"
                           style="width: 300px;">
                    <button type="submit" class="btn btn-sm btn-primary ml-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-secondary ml-2">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 15%;">PO Number</th>
                            <th style="width: 20%;">Client</th>
                            <th style="width: 20%;">Vendor</th>
                            <th style="width: 15%;">PO Amount (INR)</th>
                            <th style="width: 20%;">Invoice</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $document)
                            <tr class="{{ !$document->invoice ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $document->po_number }}</strong>
                                    @if($document->request_number)
                                        <br><small class="text-muted">{{ $document->request_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($document->user)
                                        <strong>{{ $document->user->name }}</strong>
                                        <br><small class="text-muted">{{ $document->user->email }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($document->vendor)
                                        <strong>{{ $document->vendor->vendor_name }}</strong>
                                        @if($document->vendor->gstin)
                                            <br><small class="text-muted">GSTIN: {{ $document->vendor->gstin }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format((float)$document->amount_inr, 2, '.', '') }}</strong>
                                </td>
                                <td>
                                    @if($document->invoice)
                                        @php
                                            $fileExtension = strtolower(pathinfo($document->invoice, PATHINFO_EXTENSION));
                                        @endphp
                                        @if($fileExtension === 'pdf')
                                            <a href="{{ Storage::url($document->invoice) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-file-pdf"></i> View Invoice (PDF)
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($document->invoice) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-file-image"></i> View Invoice (Image)
                                            </a>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> 
                                            {{ $document->updated_at->format('d M Y, h:i A') }}
                                        </small>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-exclamation-triangle"></i> No Invoice Uploaded
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.purchase-requests.show', $document->id) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No documents found.</p>
                                    @if(request('search'))
                                        <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-primary mt-2">
                                            View All Documents
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($documents->hasPages())
            <div class="card-footer">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
@stop

@section('css')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table-danger {
            background-color: #f8d7da !important;
        }
        .table-danger td {
            background-color: transparent;
        }
        .table-danger:hover {
            background-color: #f5c6cb !important;
        }
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                gap: 10px;
            }
            .card-header form {
                width: 100%;
            }
            .card-header input {
                width: 100% !important;
            }
        }
    </style>
@stop

