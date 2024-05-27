@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Plans Archive'))
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@push('css_or_js')

<style>
    td {
        font-size: 14px;
    }

    h3 {
        margin-right: 20px;
        background: #f8fafd;
        padding: 10px 20px;
        border: .0625rem solid rgba(231, 234, 243, .7);
    }

    i {
        font-size: 25px;
        width: 30px;
        height: 30px;
    }

</style>
@endpush

@php
$chars=['A','B','C','D','E','F','G','H'];
@endphp

@section('content')
<div class="content container-fluid">


    {{-- Header --}}
    <div class="page-header">
        <div class="media mb-3">
            <!-- Avatar -->
            <div class="avatar avatar-xl avatar-4by3 {{Session::get('direction') === "rtl" ? 'ml-2' : 'mr-2'}}">
                <img class="avatar-img" src="{{asset('public/assets/back-end')}}/svg/illustrations/order.png" alt="Image Description">
            </div>
            <!-- End Avatar -->

            <div class="media-body">
                <div class="row">
                    <div class="col-lg mb-3 mb-lg-0 {{Session::get('direction') === "rtl" ? 'mr-2' : 'ml-2'}}" style="display: block; text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <div>
                            <h1 class="page-header-title">{{\App\CPU\translate('Plan List Archive')}}</h1>
                        </div>

                        <div class="row align-items-center">


                            <div class="col-auto">
                                <div class="row align-items-center g-0">
                                    <h5 class="text-muted col-auto {{Session::get('direction') === "rtl" ? 'pl-2' : 'pr-2'}}">{{\App\CPU\translate('Date')}}</h5>

                                    <!-- Flatpickr -->
                                    <h5 class="text-muted">( {{session('plan_from_date')}} - {{session('plan_to_date')}})</h5>
                                    <hr>

                                    <!-- End Flatpickr -->
                                </div>

                                <div class="row align-items-center g-0">
                                    <h5 class="text-muted col-auto {{Session::get('direction') === "rtl" ? 'pl-2' : 'pr-2'}}">{{\App\CPU\translate('Team')}}</h5>
                                    <h5 class="text-muted">({{session('plan_team_char')}})</h5>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-auto">
                        <div class="d-flex">
                            <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                                <i class="tio-home-outlined"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Media -->

        <!-- Nav -->
        <!-- Nav -->
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <span class="hs-nav-scroller-arrow-prev" style="display: none;">
                <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                    <i class="tio-chevron-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"></i>
                </a>
            </span>

            <span class="hs-nav-scroller-arrow-next" style="display: none;">
                <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                    <i class="tio-chevron-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}"></i>
                </a>
            </span>

            <ul class="nav nav-tabs page-header-tabs" id="projectsTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:">{{\App\CPU\translate('Overview')}}</a>
                </li>
            </ul>
        </div>
        <!-- End Nav -->
    </div>


    {{-- Orders counts report --}}
    <div class="row border-bottom border-right border-left border-top">
        <div class="col-lg-12">
            <form action="{{route('admin.sales-man.plan-set-date')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">{{\App\CPU\translate('Show data by date range')}}</label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <input type="date" value="{{date('Y-m-d',strtotime(session('plan_from_date')))}}" name="plan_from_date" id="plan_from_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <input type="date" name="plan_to_date" value="{{date('Y-m-d',strtotime(session('plan_to_date')))}}" id="plan_to_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="mb-3">
                            <select name="plan_team_char" class="form-control @error('group') is-invalid @enderror" required>
                                @for ($i=0;$i<count($chars);$i++) <option value="{{$chars[$i]}}" {{ session('plan_team_char') == $chars[$i] ? 'selected' : '' }}>Team &nbsp;{{$chars[$i]}}</option>
                                    @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary btn-block">{{\App\CPU\translate('Show')}}</button>
                        </div>
             	 </div>
              
                   <div class="col-2">
                        <a style="margin-top:0px;width: fit-content;" class="btn btn-success btn-block mx-2"
                            target="_blank"
                            href={{ route('admin.sales-man.generate_excel_report') }}>
                            <i class="tio-print mr-1"></i> {{ \App\CPU\translate('Export') }}
                  	</a>
             	 </div>
              
             
                </div>
            </form>
    
        </div>

        {{-- orders counts  --}}
        <div class="col-sm-2 col-lg-2 mb-3 mb-lg-2">
            <!-- Card -->
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <!-- Media -->
                            <div class="media">
                                <i class="tio-shopping-cart nav-icon"></i>

                                <div class="media-body">
                                    <h4 class="mb-1">{{\App\CPU\translate('Orders')}}</h4>
                                    <span class="font-size-sm text-success">
                                        <i class="tio-trending-up"></i> {{$data['total']}}
                                    </span>
                                </div>
                            </div>
                            <!-- End Media -->
                        </div>

                        <div class="col-auto">
                            <!-- Circle -->
                            <div class="js-circle" data-hs-circles-options='{
                               "value": {{round(($data['total']/$data['totalRange'])*100)}},
                               "maxValue": 100,
                               "duration": 2000,
                               "isViewportInit": true,
                               "colors": ["#e7eaf3", "green"],
                               "radius": 25,
                               "width": 3,
                               "fgStrokeLinecap": "round",
                               "textFontSize": 14,
                               "additionalText": "%",
                               "textClass": "circle-custom-text",
                               "textColor": "green"
                             }'></div>
                            <!-- End Circle -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
            </div>
            <!-- End Card -->
        </div>


        {{-- orders deleviry counts  --}}
        <div class="col-sm-2 col-lg-2 mb-3 mb-lg-2">

            <!-- Card -->
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <!-- Media -->
                            <div class="media">
                                <i class="tio-shopping-cart nav-icon"></i>

                                <div class="media-body">
                                    <h4 class="mb-1">{{\App\CPU\translate('Delivered')}}</h4>
                                    <span class="font-size-sm text-success">
                                        <i class="tio-trending-up"></i> {{$data['delivered']}}
                                    </span>
                                </div>
                            </div>
                            <!-- End Media -->
                        </div>

                        <div class="col-auto">
                            <!-- Circle -->
                            <div class="js-circle" data-hs-circles-options='{
                                       "value": {{round(($data['delivered']/$data['totalRange'])*100)}},
                                       "maxValue": 100,
                                       "duration": 2000,
                                       "isViewportInit": true,
                                       "colors": ["#e7eaf3", "green"],
                                       "radius": 25,
                                       "width": 3,
                                       "fgStrokeLinecap": "round",
                                       "textFontSize": 14,
                                       "additionalText": "%",
                                       "textClass": "circle-custom-text",
                                       "textColor": "green"
                                     }'></div>
                            <!-- End Circle -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
            </div>
            <!-- End Card -->
        </div>



        <div class="col-sm-2 col-lg-2 mb-3 mb-lg-2">

            <!-- Card -->
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <!-- Media -->
                            <div class="media">
                                <i class="tio-shopping-cart-off nav-icon"></i>

                                <div class="media-body">
                                    <h4 class="mb-1">{{\App\CPU\translate('Returned')}}</h4>
                                    <span class="font-size-sm text-warning">
                                        <i class="tio-trending-up"></i> {{$data['returned']}}
                                    </span>
                                </div>
                            </div>
                            <!-- End Media -->
                        </div>

                        <div class="col-auto">
                            <!-- Circle -->
                            <div class="js-circle" data-hs-circles-options='{
                           "value": {{round(($data['returned']/$data['totalRange'])*100)}},
                           "maxValue": 100,
                           "duration": 2000,
                           "isViewportInit": true,
                           "colors": ["#e7eaf3", "#ec9a3c"],
                           "radius": 25,
                           "width": 3,
                           "fgStrokeLinecap": "round",
                           "textFontSize": 14,
                           "additionalText": "%",
                           "textClass": "circle-custom-text",
                           "textColor": "#ec9a3c"
                         }'></div>
                            <!-- End Circle -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
            </div>
            <!-- End Card -->
        </div>



        <div class="col-sm-2 col-lg-2 mb-3 mb-lg-2">

            <!-- Card -->
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <!-- Media -->
                            <div class="media">
                                <i class="tio-message-failed nav-icon"></i>

                                <div class="media-body">
                                    <h4 class="mb-1">{{\App\CPU\translate('Failed')}}</h4>
                                    <span class="font-size-sm text-danger">
                                        <i class="tio-trending-up"></i> {{$data['failed']}}
                                    </span>
                                </div>
                            </div>
                            <!-- End Media -->
                        </div>

                        <div class="col-auto">
                            <!-- Circle -->
                            <div class="js-circle" data-hs-circles-options='{
                           "value": {{round(($data['failed']/$data['totalRange'])*100)}},
                           "maxValue": 100,
                           "duration": 2000,
                           "isViewportInit": true,
                           "colors": ["#e7eaf3", "darkred"],
                           "radius": 25,
                           "width": 3,
                           "fgStrokeLinecap": "round",
                           "textFontSize": 14,
                           "additionalText": "%",
                           "textClass": "circle-custom-text",
                           "textColor": "darkred"
                         }'></div>
                            <!-- End Circle -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
            </div>
            <!-- End Card -->
        </div>



        <div class="col-sm-2 col-lg-2 mb-3 mb-lg-2">

            <!-- Card -->
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <!-- Media -->
                            <div class="media">
                                <i class="tio-flight-cancelled nav-icon"></i>

                                <div class="media-body">
                                    <h4 class="mb-1">{{\App\CPU\translate('Processing')}}</h4>
                                    <span class="font-size-sm text-muted">
                                        <i class="tio-trending-up"></i> {{$data['processing']}}
                                    </span>
                                </div>
                            </div>
                            <!-- End Media -->
                        </div>

                        <div class="col-auto">
                            <!-- Circle -->
                            <div class="js-circle" data-hs-circles-options='{
                           "value": {{round(($data['processing']/$data['totalRange'])*100)}},
                           "maxValue": 100,
                           "duration": 2000,
                           "isViewportInit": true,
                           "colors": ["#e7eaf3", "gray"],
                           "radius": 25,
                           "width": 3,
                           "fgStrokeLinecap": "round",
                           "textFontSize": 14,
                           "additionalText": "%",
                           "textClass": "circle-custom-text",
                           "textColor": "gray"
                         }'></div>
                            <!-- End Circle -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
            </div>
            <!-- End Card -->
        </div>



        <div class="col-sm-2 col-lg-2 mb-3 mb-lg-2">

            <!-- Card -->
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <!-- Media -->
                            <div class="media">
                                <i class="tio-message-failed nav-icon"></i>

                                <div class="media-body">
                                    <h4 class="mb-1">{{\App\CPU\translate('Canceled')}}</h4>
                                    <span class="font-size-sm text-muted">
                                        <i class="tio-trending-up"></i> {{$data['canceled']}}
                                    </span>
                                </div>
                            </div>
                            <!-- End Media -->
                        </div>

                        <div class="col-auto">
                            <!-- Circle -->
                            <div class="js-circle" data-hs-circles-options='{
                           "value": {{round(($data['canceled']/$data['totalRange'])*100)}},
                           "maxValue": 100,
                           "duration": 2000,
                           "isViewportInit": true,
                           "colors": ["#e7eaf3", "gray"],
                           "radius": 25,
                           "width": 3,
                           "fgStrokeLinecap": "round",
                           "textFontSize": 14,
                           "additionalText": "%",
                           "textClass": "circle-custom-text",
                           "textColor": "gray"
                         }'></div>
                            <!-- End Circle -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
            </div>
            <!-- End Card -->
        </div>

    </div>


    {{-- Table --}}
    <div class="row border-bottom border-right border-left border-top" style="margin-top: 2%">
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{\App\CPU\translate('#')}}</th>
                        <th>{{\App\CPU\translate('Begin_plan')}}</th>
                        <th>{{\App\CPU\translate('End_plan')}}</th>
                        <th>{{\App\CPU\translate('Selar_name')}}</th>
                        <th>{{\App\CPU\translate('Visit_count')}}</th>
                        <th>{{\App\CPU\translate('Pharmacies_count')}}</th>
                        <th>{{\App\CPU\translate('Orders_count')}}</th>
                        <th>{{\App\CPU\translate('Actions')}}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($plansArchive as $key=>$planArchive)
                    <tr>
                        <td>{{$plansArchive->firstitem()+$key}}</td>

                        <td>
                            {{$planArchive->begin_date}}
                        </td>

                        <td>
                            {{$planArchive->end_date}}
                        </td>

                        <td>
                            {{$planArchive->saler_name}}
                        </td>

                        <td>
                            {{$planArchive->pharmancies_visit_num}}
                        </td>

                        <td>
                            {{$planArchive->pharmacies_num}}
                        </td>

                        <td>
                            {{$planArchive->orders_num}}
                        </td>

                        <td>

                            <a class="btn btn-success btn-sm" id="editCompany" data-toggle="modal" data-target='#practice_modal' data-end_date="{{$planArchive['end_date']}}" data-begin_date="{{$planArchive['begin_date']}}" data-id="{{ $planArchive['id'] }}">
                                <i class="tio-visible"></i>
                                {{\App\CPU\translate('Details')}}
                            </a>

                            <a class="btn btn-danger  btn-sm" href="javascript:"
                            onclick="form_alert('plan-{{$planArchive['id']}}','Want to delete this plan ?')">
                                <i class="tio-add-to-trash"></i> {{\App\CPU\translate('delete')}}
                            </a>
                            <form action="{{route('admin.sales-man.plan-archive-remove',[$planArchive['id']])}}"
                                  method="get" id="plan-{{$planArchive['id']}}">
                                @csrf @method('delete')
                            </form>



                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>

            <div class="page-area">
                <table>
                    <tfoot>
                        {!! $plansArchive->links() !!}
                    </tfoot>
                </table>
            </div>

            @if(count($plansArchive)==0)
            <div class="text-center p-4">
                <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                <p class="mb-0">{{\App\CPU\translate('No plans to show')}}</p>
            </div>
            @endif


        </div>
    </div>

</div>


{{-- Table Modal archive details --}}
<div class="modal fade" id="practice_modal">
    <div class="modal-dialog" style="max-width: 800px;">

        <div class="modal-content" style="height: 500px; padding: 20px; overflow-y: scroll;">
            <div class="card-body" style="padding: 0">
                {{-- <div class="d-flex justify-content-between" style="margin-bottom: 20px;"> --}}
                <div class="d-flex align-items-center" style="margin-bottom: 20px;">
                    <i class="fa-solid fa-money-bill-1-wave"></i>
                    <h3>{{\App\CPU\translate('TOTAL_AMOUNT')}}:&nbsp;<span id="total_amount">***</span>&nbsp;SYP</h3>
                    <i class="fa-solid fa-cubes-stacked"></i>
                    <h3>{{\App\CPU\translate('TOTAL_ORDERS')}}:&nbsp;<span id="total_orders">***</span></h3>
                </div>
                <div class="table-responsive">
                    <table id="sublawmasterdata" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};  width: 100%;" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ \App\CPU\translate('Order_Number') }}</th>
                                <th scope="col">{{ \App\CPU\translate('payment_status') }}</th>
                                <th scope="col">{{ \App\CPU\translate('order_status') }}</th>
                                <th scope="col">{{ \App\CPU\translate('price') }}</th>
                                <th scope="col">{{ \App\CPU\translate('created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody id="exampleid">
                            <tr>
                                <td>*******</td>
                                <td>*******</td>
                                <td>*******</td>
                                <td>*******</td>
                                <td>*******</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script')

<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '#editCompany', function(event) {
            event.preventDefault();
            var archive_id = $(this).data('id');
            var begin_date = $(this).data('begin_date');
            var end_date = $(this).data('end_date');


            $.ajax({
                url: 'plan/report/details/' + archive_id
                , type: "POST"
                , data: {
                    begin_date: begin_date
                    , end_date: end_date
                , }
                , success: function(data) {
                    $("#sublawmasterdata tbody").empty();
                    $("#exampleid").append(data.data);
                    console.log(data.data);
                    document.getElementById("total_amount").innerHTML = data.total_price;
                    document.getElementById("total_orders").innerHTML = data.total_orders;

                }
            });

        });

    });

</script>

@endpush
