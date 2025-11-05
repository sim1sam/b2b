@extends('adminlte::page')

@section('title', 'My Disputes')

@section('content_header')
    <h1><i class="fas fa-exclamation-triangle"></i> My Disputes</h1>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($disputes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No disputes found.
                </div>
            @else
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Dispute Status</th>
                            <th>Dispute Note</th>
                            <th>Opened At</th>
                            <th>Time Remaining</th>
                            <th>Admin Response</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($disputes as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>
                                    @if($invoice->dispute_status === 'open')
                                        <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> Open</span>
                                    @elseif($invoice->dispute_status === 'resolved')
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Resolved</span>
                                    @else
                                        <span class="badge badge-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->dispute_note)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $invoice->dispute_note }}">
                                            {{ \Illuminate\Support\Str::limit($invoice->dispute_note, 50) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->dispute_opened_at)
                                        {{ $invoice->dispute_opened_at->format('d M Y, h:i A') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->dispute_status === 'open' && $invoice->dispute_opened_at)
                                        @php
                                            $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
                                            $totalMinutesRemaining = (48 * 60) - $minutesPassed;
                                            if ($totalMinutesRemaining > 0) {
                                                $hoursRemaining = floor($totalMinutesRemaining / 60);
                                                $minutesRemaining = $totalMinutesRemaining % 60;
                                            } else {
                                                $hoursRemaining = 0;
                                                $minutesRemaining = 0;
                                            }
                                        @endphp
                                        @if($hoursRemaining > 0 || $minutesRemaining > 0)
                                            <span class="badge badge-warning">{{ str_pad($hoursRemaining, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutesRemaining, 2, '0', STR_PAD_LEFT) }}</span>
                                        @else
                                            <span class="badge badge-secondary">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->admin_response)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customer.disputes.show', $invoice->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $disputes->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
