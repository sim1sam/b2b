<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger Report - {{ $user->business_name ?? $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        .text-right {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: auto;
        }
        table thead {
            display: table-header-group;
        }
        table tbody tr {
            page-break-inside: avoid;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
        }
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <!-- Ledger Header (3-Row Layout) -->
    <div class="row mb-4" style="min-height: 120px; page-break-inside: avoid;">
        <!-- Left Column - Logo in middle row -->
        <div class="col-md-6" style="display: flex; flex-direction: column; justify-content: center;">
            <div style="flex: 1;"></div>
            <div style="flex: 1; display: flex; align-items: center;">
                @if($admin && $admin->admin_logo)
                    @php
                        $logoPath = storage_path('app/public/' . $admin->admin_logo);
                        if (file_exists($logoPath)) {
                            $logoBase64 = base64_encode(file_get_contents($logoPath));
                            $logoExtension = pathinfo($admin->admin_logo, PATHINFO_EXTENSION);
                            $logoMime = $logoExtension === 'svg' ? 'image/svg+xml' : 'image/' . $logoExtension;
                            $logoDataUri = 'data:' . $logoMime . ';base64,' . $logoBase64;
                        }
                    @endphp
                    @if(isset($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo" style="max-height: 114px; max-width: 286px;">
                    @endif
                @endif
            </div>
            <div style="flex: 1;"></div>
        </div>
        <!-- Right Column - 3 rows -->
        <div class="col-md-6 text-right" style="display: flex; flex-direction: column; justify-content: space-between;">
            <!-- Row 1: Ledger Report Title -->
            <div>
                <h3 class="mb-0"><strong>Ledger Report</strong></h3>
            </div>
            <!-- Row 2: Empty space (aligns with logo) -->
            <div style="flex: 1;"></div>
            <!-- Row 3: Client and Period Info -->
            <div>
                <p class="mb-1"><strong>Client:</strong> {{ $user->business_name ?? $user->name }}</p>
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

    <div class="section-title">Transaction History</div>
    @if($transactions->count() > 0)
        <table>
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
        <p>No transactions found.</p>
    @endif

    <div class="section-title">Purchase History</div>
    @if($purchases->count() > 0)
        <table>
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
        <p>No purchases found.</p>
    @endif

    <div class="section-title">Invoice History</div>
    @if($invoices->count() > 0)
        <table>
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
        <p>No invoices found.</p>
    @endif

    <div class="footer">
        <p>This is a computer-generated report. Generated on {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>

