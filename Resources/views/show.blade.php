@extends('admin::layouts.master')
@section('title', __('message.sales_invoice_details'))
@section('last-action',  __('message.sales_invoice_details'))
@push('btn-board')
    <div class="d-flex">
        @if ($factors->status == 'pending')
            <form class="ml-2" action="{{ route('sell.draft') }}" method="post">
                @csrf()
                <input type="hidden" name="factor_id" value="{{$factors->factor_id}}">
                <button class="btn btn-danger">{{__('message.remove_factor')}}</button>
            </form>
        @endif
        <a class="btn btn-info" href="{{ route('sell.show.factor',$factors->factor_id) }}">{{__('message.print')}}</a>
    </div>
@endpush
@section('content')

    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row row-sm">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="personal_id"> مشخصات خریدار</label>
                                <p>{{$factors->personal->typeSearchRemittance() ?? '---'}}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <div class="form-group ">
                                <label for="buy_date">تاریخ صدور فاکتور</label>
                                <p>{{verta($factors->action_date)->format('Y/m/d') ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <div class="form-group ">
                                <label for="buy_date">تاریخ ثبت سیستمی</label>
                                <p>{{verta($factors->created_at)->format('Y/m/d H:i') ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="intro">{{__('message.number_factor')}}</label>
                                <p>{{$factors->factor_id ?? '----'}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row row-sm">
                        <div class="col-12">
                            <table class="table" id="table">
                                <thead>
                                <tr>
                                    <th>{{__('message.description_of_goods_services')}}</th>
                                    <th>{{__('message.warehouse')}}</th>
                                    <th> تعداد</th>
                                    <th>{{__('message.unit_price')}}</th>
                                    <th>تخفیف عدد ثابت</th>
                                    <th>مبلغ کل (ریال)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($factors->detail as $detail)
                                    @if ($detail->warehouse_id != null)
                                        <tr>
                                            <td>{{optional($detail->product)->name}}</td>
                                            <td>{{optional($detail->warehouse)->name}}</td>
                                            <td>{{$detail->count}}</td>
                                            <td>{{number_format($detail->unit_price)}}</td>
                                            <td>{{number_format($detail->discount)}}</td>
                                            <td>{{number_format($detail->total_price)}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{optional($detail->services)->name}}</td>
                                            <td>{{__('message.services')}}</td>
                                            <td>{{$detail->count}}</td>
                                            <td>{{number_format($detail->unit_price)}}</td>
                                            <td>{{number_format($detail->discount)}}</td>
                                            <td>{{number_format($detail->total_price)}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <div class="form-group  {{ $errors->has('intro') ? 'has-danger' : '' }}">
                                <label for="intro">{{__('message.intro')}}</label>
                                <span>{{$factors->intro}}</span>
                            </div>
                            <span>{!! is_active_company()->company->intro_sell ?? '' !!}</span>
                        </div>
                        <div class="col-12 col-md-5">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>تخفیف (ریال) :</td>
                                    <td><span>{{number_format($factors->discount)}}</span></td>
                                </tr>
                                <tr>
                                    <td> مالیات (ریال) :</td>
                                    <td><span>{{number_format($factors->tax)}}</span></td>
                                </tr>
                                <tr>
                                    <td> {{__('message.payable_amount_Rials')}} :</td>
                                    <td>
                                        <span class="text-center">{{number_format($factors->price)}}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-md-responsive">
                        <table class="table" id="example1">
                            <thead>
                            <tr>
                                <th>{{__('message.remittance_number')}}</th>
                                <th>تاریخ صدور</th>
                                <th>تاریخ ثبت سیستمی</th>
                                <th>{{__('message.status_remittance')}}</th>
                                <th>{{__('message.view')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($factors->factorRemittances->groupBy('factor_remittance_id') as $factorRemittances)
                                <tr>
                                    <td>{{$factorRemittances->first()->factor_remittance_id}}</td>
                                    <td>
                                        @if ($factorRemittances->first()->action_date != null)
                                            {{ verta($factorRemittances->first()->action_date)->format('Y/m/d') }}
                                        @endif
                                    </td>
                                    <td>{{ verta($factorRemittances->first()->created_at) }}</td>
                                    <td>{{$factorRemittances->first()->status()}}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                           href="{{ route('warehouse.remittance.show',$factorRemittances->first()->factor_remittance_id) }}">
                                            <i class="fas text-white fa-search"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
