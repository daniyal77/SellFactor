@extends('admin::layouts.master')
@section('title', __('message.new_sales_invoice'))
@section('last-action', __('message.new_sales_invoice'))
@push('btn-board')
    @can('sales_invoice')
        <a href="{{ route('sell.index') }}" class="btn mb-2 btn-primary">{{__('message.list')}}</a>
        <a href="{{ route('sell.remove.factor') }}" class="btn mb-2 btn-info">{{__('message.list_remove')}}</a>
    @endcan
@endpush
@section('content')
    <form id="form_validation" class="form-validate-summernote" method="post" action="{{ route('sell.store') }}">
        @csrf
        <div class="row row-sm">
            <div class="col-lg-12 col-md-12">
                <div class="card custom-card">
                    <div class="card-body">
                        @include('sellfactor::create.information')
                        @include('sellfactor::create.product')
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    <script>
        $(function () {
            /* Summernote Validation */
            var summernoteForm = $('.form-validate-summernote');
            var summernoteValidator = summernoteForm.validate({
                errorElement: "div",
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                ignore: ':hidden:not(.summernote),.note-editable.card-block',
                errorPlacement: function (error, element) {
                    // Add the `help-block` class to the error element
                    error.addClass("invalid-feedback");
                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.siblings("label"));
                    } else if (element.hasClass("summernote")) {
                        error.insertAfter(element.siblings(".note-editor"));
                    } else {
                        error.insertAfter(element);
                    }
                }, submitHandler: function (e) {
                    let input1 = $('#discount_all_factor_const');
                    let input2 = $('#tax_all_factor_const');
                    input1.val(input1.val().replace(/,/g, ''));
                    input2.val(input2.val().replace(/,/g, ''));
                    let price = $('#total_price_factor_done').text().replace(/,/g, '');
                    let table = $('#table').find("[id^= count]")
                    for (const tableElement of table) {
                        var inputValue = $(tableElement).val().replace(/,/g, '');
                        if (inputValue == 0) {
                            $(tableElement).addClass('err-is-float')
                            return false;
                        } else {
                            $(tableElement).removeClass('err-is-float')
                        }
                        if (isFloats(inputValue)) {
                            $(tableElement).addClass('err-is-float')
                            return false;
                        } else {
                            $(tableElement).removeClass('err-is-float')
                        }
                    }
                    if (price < 0) {
                        $('#total_price_factor_done').css('color', 'red')
                        return false;
                    }
                    $('#total_price_factor_done_hidden').val(price)
                    $('#spinnerBtn').removeClass('d-none');
                    $('#submitBtn').addClass('d-none');
                    $('#submitBtnPrint').addClass('d-none');

                    return true;
                }

            });
        });
    </script>
@endpush
