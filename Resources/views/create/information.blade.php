@push('style')
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/persian-datepicker/persian-datepicker.min.css') }}">
@endpush
<div class="row pb-4 mb-4 border-bottom row-sm">
    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="form-group {{ $errors->has('personal_id') ? 'has-danger' : '' }}">
            <label for="personal_id"> مشخصات خریدار
                <span class="text-danger">*</span>
            </label>
            <select id="personal_id" name="personal_id" class="form-control select-two-custom  js-data-example-ajax"
                    onchange="getUserDetail()" required="required" data-msg="انتخاب مشخصات اجباری میباشد">
                <option value="" disabled selected>{{__('message.please_select')}}</option>
                @foreach($personals as $personal)
                    <option
                        value="{{$personal->id }}--{{$personal->level}}">{{$personal->typeSearchRemittance()}}</option>
                @endforeach
            </select>
            {!! $errors->first('personal_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="form-group  {{ $errors->has('action_date') ? 'has-danger' : '' }}">
            <label for="buy_date"> تاریخ صدور فاکتور فروش
                <span class="text-danger">*</span>
            </label>
            <input id="buy_date_real" name="action_date" type="hidden"
                   value="{{ old('buy_date') }}">
            <input type="text" class="buy_date form-control" id="buy_date" required="required"
                   data-msg="{{__('message.required')}}" readonly>
            {!! $errors->first('action_date', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-lg-4 col-md-3 col-sm-6 col-12">
        <div class="form-group  {{ $errors->has('warehouse') ? 'has-danger' : '' }}">
            <label for="warehouse">انبار<span class="text-danger">*</span></label>
            <select required="required" data-msg="انتخاب انبار اجباری میباشد" name="warehouse" id="warehouse"
                    class="form-control">
                @foreach($warehouses as $warehouse)
                    <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                @endforeach
            </select>
            {!! $errors->first('warehouse', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div id="userDetail" class="col-12">
        <div class="d-md-flex">
            <div class="mg-md-l-20 mg-b-10">
                <div class="main-profile-social-list">
                    <div class="media">
                        <div class="media-icon bg-primary-transparent text-primary">
                            <i class="icon fa fa-phone"></i>
                        </div>
                        <div class="media-body"><span>شماره تلفن</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mg-md-l-20 mg-b-10">
                <div class="main-profile-social-list">
                    <div class="media">
                        <div class="media-icon bg-primary-transparent text-primary">
                            <i class="icon fa fa-phone"></i>
                        </div>
                        <div class="media-body"><span>{{__('message.mobile')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mg-md-l-20 mg-b-10">
                <div class="main-profile-social-list">
                    <div class="media">
                        <div class="media-icon bg-danger-transparent text-danger">
                            <i class="icon fa fa-link"></i>
                        </div>
                        <div class="media-body"><span>{{__('message.address')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script src="{{ asset('/assets/plugins/persian-datepicker/persian-date.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/persian-datepicker/persian-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{ asset('/assets/js/factor.js') }}"></script>
    <script type="text/javascript">
        let personals = {!! $personals !!};

        function getUserDetail() {
            let personalId = $('#personal_id').val().split("--");
            let ProductDetail = personals.filter(personal => personal.id == personalId[0])[0];
            $('#userDetail').empty()
            $('#detail-factor').removeClass('d-none')
            $('#userDetail').append(`
                    <div class="d-md-flex">
                            <div class="mg-md-l-20 mg-b-10">
                                <div class="main-profile-social-list">
                                    <div class="media">
                                        <div class="media-icon bg-primary-transparent text-primary">
                                            <i class="icon fa fa-phone"></i>
                                        </div>
                                        <div class="media-body"><span>شماره تلفن</span>
                                            <a href="tel:${ProductDetail.phone}">${ProductDetail.phone == null ? '---' : ProductDetail.phone}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mg-md-l-20 mg-b-10">
                                <div class="main-profile-social-list">
                                    <div class="media">
                                        <div class="media-icon bg-primary-transparent text-primary">
                                            <i class="icon fa fa-phone"></i>
                                        </div>
                                        <div class="media-body"><span>{{__('message.mobile')}}</span>
                                            <a href="tel:${ProductDetail.mobile}">${ProductDetail.mobile == null ? '---' : ProductDetail.mobile}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mg-md-l-20 mg-b-10">
                                <div class="main-profile-social-list">
                                    <div class="media">
                                        <div class="media-icon bg-danger-transparent text-danger">
                                            <i class="icon fa fa-link"></i>
                                        </div>
                                        <div class="media-body"><span>{{__('message.address')}}</span>
                                            <span>${ProductDetail.address == null ? '---' : ProductDetail.address}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
            `)
        }

        $(document).ready(function () {
            $('.buy_date').persianDatepicker({
                format: 'YYYY/MM/DD',
                altField: '#buy_date_real',
                initialValue: true,
                observer: true,
                altFormat: 'X',
                autoClose: true
            });
        });
    </script>
@endpush
