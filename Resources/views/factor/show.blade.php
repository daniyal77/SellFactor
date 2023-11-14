<!DOCTYPE html>
<html lang="en" dir="rtl">
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<head>
    <style>
        :root {
            --primary-color: #56368b;
        }
    </style>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <link rel="icon" href="{{ asset('/assets/images/logo.ico') }}" type="image/x-icon">
    <title>{{__('message.adrian_modiran')}} |
        پرینت فاکتور فروش
    </title>
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/web-fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css-rtl/style/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css-rtl/colors/default.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css-rtl/sidemenu/sidemenu.css') }}" rel="stylesheet">
    <style>
        @media print {
            body {
                background: white;
            }
        }

        body {
            background: white;
        }

        .barcode span {
            font-size: 60px;
            font-family: 'Libre Barcode EAN13 Text', cursive;

        }

        .border {
            border: 1px solid #1c1c1c !important;
        }


        .border-bottom-0 {
            border-bottom: 0 !important;
        }

        .table thead th {
            border-bottom: 1px solid #1c1c1c !important;
        }

        .table-bordered, .table-bordered td, .table-bordered th {
            border: 1px solid #1c1c1c !important;
        }

        .rotate-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }

        .my-box {
            background-color: #E5E7EB;
            background-image: linear-gradient(rgb(229 231 235), rgb(229 231 235));
            print-color-adjust: exact;
        }

        .border-b-0 {
            border-bottom: 0 !important;
        }

        .txt-factor {
            position: absolute;
            right: 0;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: flex-start;
            flex-direction: column;
        }
    </style>
</head>
<body class="main-body leftmenu">
<div id="global-loader">
    <img src="{{ asset('assets/img/loader.svg') }}" class="loader-img" alt="لودر">
</div>
<div class="page">
    <div>
        <div class="container-fluid">
            <div class="inner-body">
                <div class="row row-sm">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-header d-print-none">
                                <a href="{{ route('sell.index') }}"
                                   class="border-left btn btn-info">{{__('message.back')}}</a>
                                <span onclick="window.print()"
                                      class="btn my-2 btn-primary">{{__('message.print')}}</span>
                            </div>
                            <div class="card-body">
                                <div class="col-12 border border-bottom-0">
                                    <div class="row">
                                        <div class="col-4 d-flex my-3">
                                            @if ($factor->company->logo != null)
                                                <img style="width: 150px" class="my-3"
                                                     src="{{asset($factor->company->logo)}}" alt="">
                                            @endif
                                        </div>
                                        <div class="col-4 justify-content-center align-items-center d-flex my-3">
                                            <h1>{{__('message.factor_sell')}}</h1>
                                        </div>
                                        <div
                                            class="col-4 d-flex my-3 justify-content-center align-items-end flex-column text-left">
                                            <p class="mb-1 my-3">
                                                <span class="font-weight-bold">{{__('message.date')}}:</span>
                                                {{verta($factor->action_date)->format('Y/m/d') }}
                                            </p>
                                            <p class="mb-0">
                                                <span class="font-weight-bold">{{__('message.number_factor')}}:</span>
                                                {{$factor->factor_id }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom-0 table-responsive-md">
                                    <table class="table table-invoice table-bordered border-b-0 mb-0">
                                        <tr>
                                            <th class="wd-5p rotate-text my-box"
                                                style="border-bottom: 0 !important;">
                                                <p style="font-size: 8pt"
                                                   class="text-dark mb-0">{{__('message.seller_details')}}</p>
                                            </th>
                                            <td style="border-bottom: 0 !important;">
                                                <p class="mb-1">
                                                    <span class="tx-bold">{{__('message.address')}} :</span>
                                                    {{$factor->company->address}} {{$factor->company->postal_code}}
                                                </p>
                                                <p class="mb-1">
                                                    <span class="tx-bold">{{__('message.phone')}} :</span>
                                                    {{$factor->company->phone}}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="border-bottom-0 table-responsive-md">
                                    <table class="table table-invoice table-bordered border-b-0 mb-0">
                                        <tr>
                                            <th class="wd-5p rotate-text my-box" style="border-bottom: 0 !important;">
                                                <p style="font-size: 8pt"
                                                   class="text-dark mb-0">{{__('message.buyer_details')}}</p>
                                            </th>
                                            <td style="border-bottom: 0 !important;">
                                                <p class="mb-1">
                                                    <span class="tx-bold">{{__('message.seller')}} :</span>
                                                    {{$factor->personal->typeSearchRemittance()}}
                                                </p>
                                                <p class="mb-1">
                                                    <span class="tx-bold">{{__('message.address')}} :</span>
                                                    {{$factor->personal->address}} {{$factor->personal->postal_code}}
                                                </p>
                                                <p class="mb-1">
                                                    <span class="tx-bold">{{__('message.phone')}} :</span>
                                                    {{$factor->personal->phone }} / {{$factor->personal->mobile}}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="table-responsive-md">
                                    <table class="table table-invoice table-striped table-bordered text-center">
                                        <thead>
                                        <tr>
                                            <th class="bordered-r my-box wd-5p">{{__('message.row')}}</th>
                                            <th class="bordered-r my-box wd-30p">{{__('message.description_of_goods_services')}}</th>
                                            <th class="bordered-r my-box wd-5p">{{__('message.count')}}</th>
                                            <th class="bordered-r my-box wd-10p">{{__('message.amount')}}</th>
                                            <th class="bordered-r my-box wd-10p">{{__('message.amount_of_discount')}}</th>
                                            <th class="bordered-r my-box wd-10p">{{__('message.tax_amount')}}</th>
                                            <th class="bordered-r my-box wd-15p">مبلغ کل (ریال)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $price = 0; ?>
                                        @foreach($factor->detail as $key=>$detail)
                                            @if ($detail->warehouse_id != null)
                                                <tr>
                                                    <td class="bordered-r">{{++$key}}</td>
                                                    <td>{{optional($detail->product)->name}}</td>
                                                    <td>{{$detail->count}}</td>
                                                    <td>{{number_format($detail->unit_price)}}</td>
                                                    <td>{{number_format($detail->discount)}}</td>
                                                    <td class="bordered-r">0</td>
                                                    <td>{{number_format($detail->total_price)}}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="bordered-r">{{++$key}}</td>
                                                    <td>{{optional($detail->services)->name}}</td>
                                                    <td>{{$detail->count}}</td>
                                                    <td>{{number_format($detail->unit_price)}}</td>
                                                    <td>{{number_format($detail->discount)}}</td>
                                                    <td class="bordered-r">0</td>
                                                    <td>{{number_format($detail->total_price)}}</td>
                                                </tr>
                                            @endif
                                            <?php $price += $detail->total_price; ?>
                                        @endforeach
                                        <tr>
                                            <td class="valign-middle position-relative" colspan="4" rowspan="4">
                                                <div class=" m-2 txt-factor">
                                                    <span class="mb-2">{{$factor->intro}}</span>
                                                    <span>{!!  $factor->company->intro_sell !!}</span>
                                                </div>
                                            </td>
                                            <td class="tx-right" colspan="2">جمع مبلغ کالا / خدمات</td>
                                            <td class="tx-right">{{number_format($price)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="tx-right" colspan="2">تخفیف</td>
                                            <td class="tx-right">{{number_format($factor->discount)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="tx-right" colspan="2">مالیات</td>
                                            <td class="tx-right">{{number_format($factor->tax)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="tx-right tx-uppercase tx-bold tx-inverse" colspan="2">
                                                {{__('message.payable_amount_Rials')}}
                                            </td>
                                            <td class="tx-right">
                                                <h5>{{number_format($factor->price)}}</h5>
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
        </div>
    </div>
</div>
<a href="#top" id="back-to-top"><i class="fe fe-arrow-up"></i></a>
<!-- Jquery js-->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap js-->
<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap-rtl.js') }}"></script>
<!-- Perfect-scrollbar js -->
<script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min-rtl.js') }}"></script>
<!-- Sidemenu js -->
<script src="{{ asset('assets/plugins/sidemenu/sidemenu-rtl.js') }}"></script>
<!-- Sidebar js -->
<script src="{{ asset('assets/plugins/sidebar/sidebar-rtl.js') }}"></script>
<!-- Sticky js -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>
<!-- Custom js -->
<script src="{{ asset('assets/js/custom.js') }}"></script>
</body>
</html>
