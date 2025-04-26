@extends('admin::layouts.content')

@section('page_title')
    {{ __('mpesa::app.admin.transactions.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('mpesa::app.admin.transactions.title') }}</h1>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('admin.mpesa.transactions.index') }}"></datagrid-plus>
        </div>
    </div>
@stop

@push('scripts')
    @include('admin::export.export', ['gridName' => app('Webkul\Admin\DataGrids\MpesaTransactionDataGrid')])
@endpush