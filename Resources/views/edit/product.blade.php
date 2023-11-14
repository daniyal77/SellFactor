@push('style')
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endpush

<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row row-sm">
                    <div class="col-12">
                        <table class="table mb-1" id="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{__('message.description_of_goods_services')}} <span class="text-danger">*</span>
                                </th>
                                <th> تعداد <span class="text-danger">*</span></th>
                                <th>قیمت تمام سطح ها</th>
                                <th>{{__('message.unit_price')}}</th>
                                <th>تخفیف درصد</th>
                                <th>تخفیف عدد ثابت</th>
                                <th>مبلغ کل (ریال)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($factor->detail as $key=>$detail)

                                <tr id="row_table{{++$key}}">
                                    @if ($detail->warehouse_id != null)
                                        <td>
                                            <span class="ml-2 mb-2 cu-pointer text-danger"
                                                  onclick="delete_row({{$key}})">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </span>
                                        </td>
                                        <td id="warehouse_tr_{{$key}}" class="px-0 position-relative">
                                            <select class="form-control js-data-product-ajax"
                                                    onchange="change_product(this,{{$key}})"
                                                    required="required" data-msg="{{__('message.required')}}"
                                                    name="factor[{{$key}}][product]" aria-label="product">
                                                @foreach($products as $product)
                                                    <option @if($product->id ==$detail->product->id) selected @endif
                                                    value="product_{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            </select>
                                            <span class="d-none">&nbsp;</span>

                                        </td>
                                        <td>
                                            <input id="count{{$key}}" data-msg="{{__('message.required')}}"
                                                   onkeyup="totalPriceConverter({{$key}}),separateNum(this.value,this);"
                                                   class="form-control" required="required"
                                                   value="{{$detail->count}}" name="factor[{{$key}}][count]"
                                                   aria-label="count{{$key}}">
                                        </td>
                                        <td class="w-190px">
                                            <select class="form-control" onchange="changePrice({{$key}})"
                                                    id="all_product_price_{{$key}}"
                                                    aria-label="all_price">
                                                <option value="{{ $detail->product->sell_level_one}}">
                                                    سطح 1 ({{ number_format($detail->product->sell_level_one)}})
                                                </option>
                                                <option value="{{ $detail->product->sell_level_two}}">
                                                    سطح 2 (2{{ number_format($detail->product->sell_level_two)}})
                                                </option>
                                                <option value="{{ $detail->product->sell_level_three}}">
                                                    سطح 3 ({{ number_format($detail->product->sell_level_three)}} )
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input id="price{{$key}}" data-msg="{{__('message.required')}}"
                                                   onkeyup="totalPriceConverter({{$key}}),separateNum(this.value,this);"
                                                   class="form-control" required="required"
                                                   value="{{number_format($detail->unit_price)}}"
                                                   name="factor[{{$key}}][price]" aria-label="price{{$key}}">
                                        </td>
                                        <td>
                                            <input class="form-control" onkeyup="discount_percent({{$key}})"
                                                   id="discount_percent{{$key}}" required="required"
                                                   data-msg="{{__('message.required')}}"
                                                   value="0" aria-label="discount_percent{{$key}}">
                                        </td>
                                        <td>
                                            <input class="form-control" name="factor[{{$key}}][discount]"
                                                   required="required" data-msg="{{__('message.required')}}"
                                                   onkeyup="discount_const({{$key}}),separateNum(this.value,this);"
                                                   id="discount_const{{$key}}" aria-label="discount_const"
                                                   value="{{number_format($detail->discount)}}">
                                        </td>
                                        <td>
                                            <input name="factor[{{$key}}][total_price]" readonly
                                                   value="{{number_format($detail->total_price)}}"
                                                   class="total-price readonly form-control" required="required"
                                                   data-msg="{{__('message.required')}}" id="total-price{{$key}}"
                                                   aria-label="total-price{{$key}}">
                                        </td>
                                    @else
                                        <td>
                                           <span class="ml-2 mb-2 cu-pointer text-danger"
                                                 onclick="delete_row({{$key}})">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </span>
                                        </td>
                                        <td class="row w-350">
                                            <select class="form-control js-data-product-ajax"
                                                    onchange="changeproduct(this,{{$key}})"
                                                    required="required" data-msg="{{__('message.required')}}"
                                                    name="factor[{{$key}}][product]" aria-label="product">
                                                <option
                                                    value="{{optional($detail->services)->id}}">{{optional($detail->services)->name}}</option>
                                            </select>
                                        </td>
                                        <td></td>
                                        <td class="w-85">
                                            <input id="count{{$key}}" data-msg="{{__('message.required')}}"
                                                   onkeyup="totalPriceConverter({{$key}}),separateNum(this.value,this);"
                                                   class="form-control" required="required" value="{{$detail->count}}"
                                                   name="factor[{{$key}}][count]" aria-label="count{{$key}}">
                                        </td>

                                        <td>
                                            <input id="price{{$key}}" data-msg="{{__('message.required')}}"
                                                   onkeyup="totalPriceConverter({{$key}}),separateNum(this.value,this);"
                                                   class="form-control" required="required"
                                                   value="{{number_format($detail->unit_price)}}"
                                                   name="factor[{{$key}}][price]" aria-label="price{{$key}}">
                                        </td>
                                        <td>
                                            <input class="form-control" onkeyup="discount_percent({{$key}})"
                                                   id="discount_percent{{$key}}" required="required" value="0"
                                                   data-msg="{{__('message.required')}}"
                                                   aria-label="discount_percent{{$key}}">
                                        </td>
                                        <td>
                                            <input class="form-control" name="factor[{{$key}}][discount]"
                                                   required="required" data-msg="{{__('message.required')}}"
                                                   onkeyup="discount_const({{$key}}),separateNum(this.value,this);"
                                                   id="discount_const{{$key}}"
                                                   value="{{number_format($detail->discount)}}"
                                                   aria-label="discount_const">
                                        </td>
                                        <td>
                                            <input name="factor[{{$key}}][total_price]" readonly
                                                   class="total-price readonly form-control"
                                                   required="required" data-msg="{{__('message.required')}}"
                                                   value="{{number_format($detail->total_price)}}"
                                                   aria-label="total-price{{$key}}" id="total-price{{$key}}">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <span onclick="addRow()" class="btn mb-1 btn-primary btn-sm add-btn"><i class="fas fa-plus"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <div class="form-group  {{ $errors->has('intro') ? 'has-danger' : '' }}">
                            <label for="intro">{{__('message.intro')}}
                                {!! $errors->first('intro', '<span class="help-block">:message</span>') !!}
                            </label>
                            <textarea id="intro" class="form-control" name="intro" maxlength="255"
                                      rows="5">{{ $factor->intro ??  old('intro') }}</textarea>
                        </div>
                        <span>{!! is_active_company()->company->intro_sell ?? '' !!}</span>
                    </div>
                    <div class="col-12 col-md-5">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>{{__('message.sum_of_the_total_amount_rials')}} :</td>
                                <td colspan="2"><span id="total_price_factor">0</span></td>
                            </tr>
                            <tr>
                                <td>تخفیف (ریال) :</td>
                                <td>
                                    <div class="form-group">
                                        <label for="discount_all_factor">درصد تخفیف</label>
                                        <input id="discount_all_factor" onkeyup="discount_percent_total_factor()"
                                               class="form-control">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="discount_all_factor_const">تخفیف مبلغ ثابت</label>
                                        <input onkeyup="discount_const_total_factor(),separateNum(this.value,this);"
                                               id="discount_all_factor_const" name="discount"
                                               value="{{number_format($factor->discount)}}"
                                               required="required" data-msg="{{__('message.required')}}"
                                               class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td> مالیات (ریال) :</td>
                                <td>
                                    <div class="form-group">
                                        <label for="tax_all_factor">درصد مالیات</label>
                                        <input id="tax_all_factor" onkeyup="tax_percent_total_factor()"
                                               class="form-control">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="tax_all_factor_const">عدد ثابت</label>
                                        <input onkeyup="separateNum(this.value,this);"
                                               id="tax_all_factor_const" name="tax" class="form-control readonly"
                                               value="{{number_format($factor->tax)}}" readonly
                                               required="required" data-msg="{{__('message.required')}}">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td> {{__('message.payable_amount_Rials')}}:</td>
                                <td colspan="2"><span
                                        id="total_price_factor_done">{{number_format($factor->price)}}</span></td>
                                <input type="hidden" value="{{$factor->price}}" name="total_price"
                                       id="total_price_factor_done_hidden">
                            </tr>
                            </tbody>
                        </table>
                        <button class="btn ripple d-none btn-primary" id="spinnerBtn" type="button">
                                        <span aria-hidden="true" class="spinner-border spinner-border-sm"
                                              role="status"></span>
                            <span class="sr-only">درحال ارسال...</span>
                        </button>

                        <button id="submitBtn" class="btn btn-primary">{{__('message.submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('style')
    <link href="{{asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>

    <script>
        let sum = 0;
        let total_all_factor = 0;
        let total_all_factor_after_discount = 0;
        let products = {!! $products !!};

        $('.js-data-product-ajax').select2();

        function totalPriceConverter(id) {
            let count = parseInt($('#count' + id).val().replace(/,/g, ""));
            let price = parseInt($('#price' + id).val().replace(/,/g, ""));
            sum = count * price
            if (isNaN(sum)) {
                $('#total-price' + id).val("مقدار نامعتبر هست")

            } else {
                $('#total-price' + id).val(sum.toLocaleString())
            }
            $('#discount_const' + id).val(0);
            $('#discount_percent' + id).val(0);
            $('#tax_all_factor_const').val(0);
            $('#tax_all_factor').val(0);
            $('#discount_all_factor_const').val(0);
            $('#discount_all_factor').val(0);
            $('#total_price_factor_done').text(0)
            get_price_total_factor()
        }

        function change_product(selectObject, id) {

            let product_id = selectObject.value;
            let [type, pid] = selectObject.value.split("_");
            let ProductDetail = products.filter(product => product.id == pid)[0];
            if (type === 'service') {
                $('#warehouse' + id).remove()
                $('#all_product_price_' + id).remove()
            }
            $('#price' + id).val(0);
            $('#all_product_price_' + id).empty()
            let [personal_id, level] = $('#personal_id').find(':selected').val().split('--')
            if (type === 'product') {
                let selectOne, selectTwo, selectThree = '';
                if (level === 'one') {
                    selectOne = 'selected="selected"';
                    $('#price' + id).val(parseInt(ProductDetail.sell_level_one).toLocaleString());
                }
                if (level === 'two') {
                    selectTwo = 'selected="selected"';
                    $('#price' + id).val(parseInt(ProductDetail.sell_level_two).toLocaleString());
                }
                if (level === 'three') {
                    selectThree = 'selected="selected"';
                    $('#price' + id).val(parseInt(ProductDetail.sell_level_three).toLocaleString());
                }
                $('#all_product_price_' + id).append(`
                        <option value="${parseInt(ProductDetail.sell_level_one).toLocaleString()}" ${selectOne}>سطح 1 (${parseInt(ProductDetail.sell_level_one).toLocaleString()})</option>
                        <option value="${parseInt(ProductDetail.sell_level_two).toLocaleString()}" ${selectTwo}>سطح 2 (${parseInt(ProductDetail.sell_level_two).toLocaleString()})</option>
                        <option value="${parseInt(ProductDetail.sell_level_three).toLocaleString()}"  ${selectThree}>سطح 3(${parseInt(ProductDetail.sell_level_three).toLocaleString()})</option>`)
            } else {
                $('#price' + id).val(parseInt(data).toLocaleString());
            }
            totalPriceConverter(id)
            get_price_total_factor()
        }

        function discount_percent(id) {
            let discount = $('#discount_percent' + id).val();
            let discount_price = ((discount * sum) / 100);
            let total_price = (sum - discount_price);
            $('#total-price' + id).val(total_price.toLocaleString())
            $('#discount_const' + id).val(discount_price.toLocaleString())
            $('#discount_all_factor_const').val(0);
            $('#discount_all_factor').val(0);
            get_price_total_factor()
        }

        function discount_const(id) {
            let discount = $('#discount_const' + id).val().replace(/,/g, "");
            let percent = discount / sum
            $('#discount_percent' + id).val(percent.toFixed(2) * 100);
            let total_price = sum - discount;
            $('#total-price' + id).val(total_price.toLocaleString())
            $('#discount_all_factor_const').val(0);
            $('#discount_all_factor').val(0);
            get_price_total_factor()
        }

        function addRow() {
            let table_rows = 1 + $('#table tbody tr').length;
            console.log(table_rows)
            let selectedValue = $(`#warehouse_tr_${table_rows - 1} > select`).val();
            if (selectedValue == '') {
                showAlert();
                return
            }
            let array_selected_product_id = [];
            $('[id^=warehouse_tr_] > span').removeClass('d-none')
            $("[id^=warehouse_tr_] > span").addClass('disabled_selected_2')
            $('span[id^="select2-factor"]').addClass('disabled_selected_2_content');
            for (const warehouse_tr of $('[id^=warehouse_tr_] > select')) {
                array_selected_product_id.push($(warehouse_tr).val())
            }
            $('#table tbody').append(`
                <tr id="row_table${table_rows}">
                   <td>
                        <span class="ml-2 cu-pointer text-danger" onclick="delete_row(${table_rows})"><i class="fa fa-trash" aria-hidden="true"></i></span>
                   </td>
                   <td  id="warehouse_tr_${table_rows}" class="px-0 position-relative">
                      <select class="js-data-product-ajax${table_rows} form-control" onchange="change_product(this,${table_rows})"
                               required="required"  data-msg="{{__('message.required')}}"
                               name="factor[${table_rows}][product]" aria-label="product">
                            <option value="">{{__('message.Please choose')}}</option>
                      </select>
                      <span class="d-none">&nbsp;</span>
                   </td>
                   <td>
                        <input id="count${table_rows}" onkeyup="totalPriceConverter(${table_rows}),separateNum(this.value,this);"
                           class="form-control" required="required"  data-msg="{{__('message.required')}}"
                           value="1" name="factor[${table_rows}][count]" aria-label="count">
                   </td>
                    <td class="w-190px">
                         <select class="form-control"  onchange="changePrice(${table_rows})" id="all_product_price_${table_rows}" aria-label="all_price">
                         </select>
                    </td>
                    <td>
                        <input id="price${table_rows}" onkeyup="totalPriceConverter(${table_rows}),separateNum(this.value,this);"
                               class="form-control" required="required"
                                data-msg="{{__('message.required')}}"
                               value="1" name="factor[${table_rows}][price]" aria-label="price">
                    </td>
                    <td>
                        <input class="form-control" onkeyup="discount_percent(${table_rows})" id="discount_percent${table_rows}"
                               required="required"  data-msg="{{__('message.required')}}"
                               value="0" aria-label="discount_percent">
                    </td>
                    <td>
                        <input class="form-control" name="factor[${table_rows}][discount]"
                               required="required"  data-msg="{{__('message.required')}}"
                               onkeyup="discount_const(${table_rows}),separateNum(this.value,this);"
                               id="discount_const${table_rows}" value="0" aria-label="discount_const">
                    </td>
                    <td>
                        <input name="factor[${table_rows}][total_price]" readonly class="total-price readonly form-control"
                               required="required"  data-msg="{{__('message.required')}}"
                               id="total-price${table_rows}" type="text">
                    </td>
                </tr>
            `)
            for (const product of products) {
                if (array_selected_product_id.indexOf('product_' + product.id) == -1) {
                    $(`#warehouse_tr_${table_rows}>select`).append(`<option value="product_${product.id}">${product.name}</option>`)
                }
            }
            regenerateIds()
            $(".js-data-product-ajax" + table_rows).select2();
        }

        function get_price_total_factor() {
            let totalSum = 0;
            $(".total-price").each(function (index) {
                totalSum += +$(this).val().replace(/,/g, "") || 0;
            });
            total_all_factor = totalSum;
            $('#total_price_factor').text(totalSum.toLocaleString())
            $('#total_price_factor_done').text(totalSum.toLocaleString())
            $('#total_price_factor_done_hidden').val(totalSum.toLocaleString())
        }

        function tax_percent_total_factor() {

            let tax_all_factor = $('#tax_all_factor').val();
            let pricess = total_all_factor_after_discount
            if (pricess == 0) {
                pricess = $('#total_price_factor').text().replace(/,/g, "");
            }
            let discount_price = ((tax_all_factor * pricess) / 100)
            let total_price = (parseInt(pricess) + parseInt(discount_price))
            $('#total_price_factor_done').text(total_price.toLocaleString())
            $('#tax_all_factor_const').val(discount_price.toLocaleString());
            $('#tax_all_factor_const').addClass('readonly')
            $('#tax_all_factor').removeClass('readonly')

        }

        function discount_percent_total_factor() {
            let totalPrice = $('#total_price_factor').text().replace(/,/g, "");
            let discount_all_factor = $('#discount_all_factor').val();
            let discount_price = ((discount_all_factor * totalPrice) / 100);
            let total_price = totalPrice - discount_price
            $('#total_price_factor_done').text(total_price.toLocaleString())
            $('#discount_all_factor_const').val(discount_price.toLocaleString())
            $('#tax_all_factor').val(0)
            $('#tax_all_factor_const').val(0)
            total_all_factor_after_discount = total_price
        }

        function discount_const_total_factor() {
            let totalPrice = $('#total_price_factor').text().replace(/,/g, "");
            let discount_all_factor = $('#discount_all_factor_const').val().replace(/,/g, "");
            let percent = (discount_all_factor / totalPrice)
            let total_price_after_dis = (totalPrice - discount_all_factor)
            $('#discount_all_factor').val(percent.toFixed(2) * 100);
            $('#total_price_factor_done').text(total_price_after_dis.toLocaleString())
            $('#tax_all_factor').val(0)
            $('#tax_all_factor_const').val(0)
            total_all_factor_after_discount = total_price_after_dis
        }

        function delete_row(count) {
            $('#row_table' + count).remove();
            get_price_total_factor()
            regenerateIds()
        }

        function changePrice(id) {
            let val = $('#all_product_price_' + id).val()
            $('#price' + id).val(val);
            totalPriceConverter(id)
            get_price_total_factor()
        }

        function showAlert() {
            swal({
                title: "هشدار",
                text: "لطفا ابتدا کالا رامشخص نمایید",
                type: "info",
                confirmButtonText: '{{__('message.ok')}}',
            })
        }

        function regenerateIds() {
            var tableRows = $('#table tbody tr');
            tableRows.each(function (index) {
                var newIndex = (index + 1);
                $(this).attr('id', "row_table" + newIndex);
                $(this).find('.cu-pointer').attr('onclick', `delete_row(${newIndex})`)
                $(this).find('[class^="js-data-product-ajax"]').attr('onchange', `change_product(this,${newIndex})`)
                $(this).find('[class^="js-data-product-ajax"]').attr('name', `factor[${newIndex}][product]`)
                $(this).find('[id^="count"]').attr('id', `count${newIndex}`)
                $(this).find('[id^="count"]').attr('name', `factor[${newIndex}][count]`)
                $(this).find('[id^="count"]').attr('onkeyup', `totalPriceConverter(${newIndex}),separateNum(this.value,this);`)

                $(this).find('[id^="all_product_price"]').attr('id', `all_product_price_${newIndex}`)
                $(this).find('[id^="all_product_price"]').attr('onchange', `changePrice(${newIndex})`)

                $(this).find('[id^="price"]').attr('id', `price${newIndex}`)
                $(this).find('[id^="price"]').attr('onkeyup', `totalPriceConverter(${newIndex}),separateNum(this.value,this);`)
                $(this).find('[id^="price"]').attr('name', `factor[${newIndex}][price]`)

                $(this).find('[id^="discount_percent"]').attr('id', `discount_percent${newIndex}`)
                $(this).find('[id^="discount_percent"]').attr('onkeyup', `discount_percent(${newIndex})`)

                $(this).find('[id^="discount_const"]').attr('onkeyup', `discount_const(${newIndex}),separateNum(this.value,this);`)
                $(this).find('[id^="discount_const"]').attr('id', `discount_const${newIndex}`)
                $(this).find('[id^="discount_const"]').attr('name', `factor[${newIndex}][discount]`)

                $(this).find('[id^="total-price"]').attr('id', `total-price${newIndex}`)
                $(this).find('[id^="warehouse_tr"]').attr('id', `warehouse_tr_${newIndex}`)
                $(this).find('[id^="total-price"]').attr('name', `factor[${newIndex}][total_price]`)

            });
        }
    </script>
@endpush
