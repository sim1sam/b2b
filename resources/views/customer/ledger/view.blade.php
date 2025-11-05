@extends('adminlte::page')

@section('title', 'Ledger Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"><i class="fas fa-book"></i> Ledger Report</h1>
        <a href="{{ route('customer.ledger.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
@stop

@section('content')
    <div class="card" style="background: white;">
        <div class="card-body">
            <!-- Ledger Header (3-Row Layout) -->
            <div class="row mb-4" style="min-height: 120px;">
                <!-- Left Column - Logo in middle row -->
                <div class="col-md-6" style="display: flex; flex-direction: column; justify-content: center;">
                    <div style="flex: 1;"></div>
                    <div style="flex: 1; display: flex; align-items: center;">
                        @if($admin && $admin->admin_logo)
                            <img src="{{ Storage::url($admin->admin_logo) }}" alt="Logo" style="max-height: 114px; max-width: 286px;">
                        @endif
                    </div>
                    <div style="flex: 1;"></div>
                </div>
                <!-- Right Column - 3 rows -->
                <div class="col-md-6 text-right" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <!-- Row 1: Ledger Report Title and Action Buttons -->
                    <div style="display: flex; justify-content: flex-end; align-items: flex-start; margin-bottom: 10px;">
                        <div style="flex: 1;"></div>
                        <div>
                            <h3 class="mb-0"><strong>Ledger Report</strong></h3>
                        </div>
                        <div style="margin-left: 15px;">
                            <div class="btn-group">
                                <a href="{{ route('customer.ledger.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                                <button onclick="window.print()" class="btn btn-sm btn-primary">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Row 2: Empty space (aligns with logo) -->
                    <div style="flex: 1;"></div>
                    <!-- Row 3: Client and Period Info -->
                    <div style="margin-top: 10px;">
                        <p class="mb-1"><strong>Client:</strong> {{ $user->name }}</p>
                        @if($startDate || $endDate)
                            <p class="mb-0"><strong>Period:</strong> 
                                {{ $startDate ? date('d M Y', strtotime($startDate)) : 'All' }} 
                                to 
                                {{ $endDate ? date('d M Y', strtotime($endDate)) : 'All' }}
                            </p>
                        @else
                            <p class="mb-0"><strong>Period:</strong> All Time</p>
                        @endif
                    </div>
                </div>
            </div>

            <hr style="border-top: 2px solid #000; margin: 20px 0;">

            <!-- Transactions Table -->
            <div class="mb-4">
                <h4 class="mb-3"><strong>Transaction History</strong></h4>
                @if($transactions->count() > 0)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if($transaction->type === 'deposit')
                                            Deposit
                                        @elseif($transaction->type === 'purchase')
                                            Purchase
                                        @elseif($transaction->type === 'invoice_payment')
                                            Invoice Payment
                                        @else
                                            Withdrawal
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($transaction->type === 'deposit')
                                            +{{ number_format($transaction->amount, 2) }}
                                        @else
                                            -{{ number_format(abs($transaction->amount), 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->status === 'approved')
                                            Approved
                                        @elseif($transaction->status === 'rejected')
                                            Rejected
                                        @else
                                            Pending
                                        @endif
                                    </td>
                                    <td>{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No transactions found.</p>
                @endif
            </div>

            <!-- Purchases Table -->
            <div class="mb-4">
                <h4 class="mb-3"><strong>Purchase History</strong></h4>
                @if($purchases->count() > 0)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Request #</th>
                                <th>Vendor</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->created_at->format('d M Y') }}</td>
                                    <td>{{ $purchase->request_number }}</td>
                                    <td>{{ $purchase->vendor->vendor_name ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($purchase->amount, 2) }}</td>
                                    <td>
                                        @if($purchase->status === 'approved')
                                            Approved
                                        @elseif($purchase->status === 'rejected')
                                            Rejected
                                        @elseif($purchase->status === 'completed')
                                            Completed
                                        @else
                                            Pending
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No purchases found.</p>
                @endif
            </div>

            <!-- Invoices Table -->
            <div class="mb-4">
                <h4 class="mb-3"><strong>Invoice History</strong></h4>
                @if($invoices->count() > 0)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th class="text-right">Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td class="text-right">{{ number_format($invoice->rounded_total, 2) }}</td>
                                    <td>
                                        @if($invoice->payment_status === 'paid')
                                            Paid
                                        @else
                                            Pending
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->order_status === 'completed')
                                            Completed
                                        @elseif($invoice->dispute_status === 'open')
                                            Dispute
                                        @elseif($invoice->delivery_status === 'delivered')
                                            Delivered
                                        @else
                                            Pending
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No invoices found.</p>
                @endif
            </div>

            <div class="text-center mt-4" style="border-top: 1px solid #000; padding-top: 15px;">
                <p style="font-size: 10px; margin: 0;">Generated on {{ date('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    @media print {
        .btn, .main-header, .main-sidebar, .main-footer, .navbar, .content-header {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .card-body {
            padding: 20px !important;
            background: white !important;
        }
        img {
            max-width: 286px !important;
            max-height: 114px !important;
            height: auto !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        table td, table th {
            border: 1px solid #000 !important;
            padding: 8px !important;
        }
        @page {
            margin: 1cm;
            size: A4;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        * {
            color: #000 !important;
            background: white !important;
        }
    }
</style>
@stop

