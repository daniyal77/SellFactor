@extends('admin::layouts.master')
@section('title', 'لیست فاکتور های فروش')
@section('last-action', 'لیست فاکتور های فروش')
@push('style')
    <link href="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min-rtl.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/datatable/responsivebootstrap4.min.css') }}" rel="stylesheet"/>
@endpush
@push('btn-board')
    @can('sales_invoice_insert')
        <a href="{{ route('sell.create') }}" class="btn mb-2 btn-primary">{{__('message.new')}}</a>
    @endcan
    @can('sales_invoice')
        <a href="{{ route('sell.index') }}" class="btn mb-2 btn-primary">{{__('message.list')}}</a>
        <a href="{{ route('sell.remove.factor') }}" class="btn mb-2 btn-info">{{__('message.list_remove')}}</a>
    @endcan
@endpush
@section('content')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">

                    <div class="table-md-responsive">
                        <table class="table" id="factor">
                            <thead>
                            <tr>
                                <th>تاریخ صدور</th>
                                <th>{{__('message.invoice_number')}}</th>
                                <th>{{__('message.account_party')}}</th>
                                <th>{{__('message.remittance_status')}}</th>
                                <th>{{__('message.action')}}</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@push('script')
    <script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/fileexport/dataTables.buttons.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#factor').DataTable({
                order: [[0, 'desc']],
                iDisplayLength: 100,
                language: {
                    url: '{{asset('assets/js/fa-data-table.json')}}',
                },
                processing: true,
                serverSide: true,
                ajax: "{{route('sell.get.factor')}}",
                columns: [
                    {data: 'action_date'},
                    {data: 'factor_id'},
                    {data: 'name'},
                    {data: 'status'},
                    {data: 'action'},
                ]
            });
        });
    </script>
    @can('sales_invoice_delete')
        <script>
            function deleteCheque(id) {
                let deleteUrl = "{{route('sell.draft')}}"
                swal({
                    title: "{{ __('message.are_you_sure')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: '{{__('message.ok')}}',
                    closeOnConfirm: false,
                    confirmButtonColor: "#ff2a01",
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: deleteUrl,
                            method: 'post',
                            data: {'factor_id': id, "_token": "{{csrf_token()}}"},
                            success: function (data) {
                                location.reload()
                            }
                        })
                    }
                });
            }
        </script>
    @endcan
@endpush
