@extends('admin::layouts.master')
@section('title', __('message.edit_sales_invoice'))
@section('last-action', __('message.edit_sales_invoice'))

@section('content')
    <form id="form_validation" class="form-validate-summernote" method="post"
          action="{{ route('sell.update', $factor->factor_id) }}">
        @csrf
        @include('sellfactor::edit.information')
        @include('sellfactor::edit.product')
    </form>
@endsection
@push('script')
    <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    <script>
        $(function () {
            var summernoteForm = $('.form-validate-summernote');
            var summernoteValidator = summernoteForm.validate({
                errorElement: "div",
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                ignore: ':hidden:not(.summernote),.note-editable.card-block',
                errorPlacement: function (error, element) {
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
                    return true;
                }
            });
        });
    </script>
@endpush
