@extends('admin::layouts.content')

@section('page_title')
    {{ __('mpesa::app.admin.transactions.view_title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.mpesa.transactions.index') }}'"></i>
                    {{ __('mpesa::app.admin.transactions.view_title') }}
                </h1>
            </div>
        </div>

        <div class="page-content">
            <div class="sale-container">
                <div class="sale-section">
                    <div class="secton-title">
                        <span>{{ __('mpesa::app.admin.transactions.transaction_info') }}</span>
                    </div>

                    <div class="section-content">
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.transaction_id') }}
                            </span>
                            <span class="value">
                                {{ $transaction->transaction_id ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.checkout_request_id') }}
                            </span>
                            <span class="value">
                                {{ $transaction->checkout_request_id ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.order_id') }}
                            </span>
                            <span class="value">
                                @if($transaction->order_id)
                                    <a href="{{ route('admin.sales.orders.view', $transaction->order_id) }}">
                                        {{ $transaction->order_id }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.phone_number') }}
                            </span>
                            <span class="value">
                                {{ $transaction->phone_number ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.amount') }}
                            </span>
                            <span class="value">
                                {{ core()->formatBasePrice($transaction->amount) }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.status') }}
                            </span>
                            <span class="value">
                                <span class="badge badge-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.result_code') }}
                            </span>
                            <span class="value">
                                {{ $transaction->result_code ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.result_desc') }}
                            </span>
                            <span class="value">
                                {{ $transaction->result_desc ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.transaction_date') }}
                            </span>
                            <span class="value">
                                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y H:i:s') : 'N/A' }}
                            </span>
                        </div>
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.created_at') }}
                            </span>
                            <span class="value">
                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i:s') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($transaction->raw_request || $transaction->raw_response)
                <div class="sale-section">
                    <div class="secton-title">
                        <span>{{ __('mpesa::app.admin.transactions.technical_details') }}</span>
                    </div>
                    <div class="section-content">
                        @if($transaction->raw_request)
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.raw_request') }}
                            </span>
                            <span class="value">
                                <pre>{{ json_encode(json_decode($transaction->raw_request), JSON_PRETTY_PRINT) }}</pre>
                            </span>
                        </div>
                        @endif
                        
                        @if($transaction->raw_response)
                        <div class="row">
                            <span class="title">
                                {{ __('mpesa::app.admin.transactions.raw_response') }}
                            </span>
                            <span class="value">
                                <pre>{{ json_encode(json_decode($transaction->raw_response), JSON_PRETTY_PRINT) }}</pre>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@stop