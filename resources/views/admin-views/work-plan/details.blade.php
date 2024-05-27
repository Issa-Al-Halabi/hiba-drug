@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Plan Details'))
@section('content')

@push('css_or_js')
<link rel="stylesheet" href="{{asset('public/css/planDetails.css')}}">
@endpush

<div class="content container-fluid">
    <!-- Page Header -->

    <div class="page-header mb-1">
        <div class="flex-between align-items-center">
            <div>
                <h1 class="page-header-title">{{\App\CPU\translate('Plan Pharmacies')}}<span class="badge badge-soft-dark mx-2">{{$PharmaciesPlan->count()}}</span></h1>
                <div class="row align-items-center " style="margin-left: 20px"><i class="fa fa-user" style="margin-right: 10px; font-size: 20px"></i>
                    <h1 class="page-header-title">{{$workPlan->saler_name}}</h1>
                </div>
            </div>
            <div>
                <i class="tio-shopping-cart" style="font-size: 30px"></i>
            </div>
        </div>
        <!-- End Row -->

        <!-- Nav Scroller -->
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <span class="hs-nav-scroller-arrow-prev" style="display: none;">
                <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                    <i class="tio-chevron-left"></i>
                </a>
            </span>

            <span class="hs-nav-scroller-arrow-next" style="display: none;">
                <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                    <i class="tio-chevron-right"></i>
                </a>
            </span>

            <!-- Nav -->
            <ul class="nav nav-tabs page-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">{{\App\CPU\translate('plan_list')}}</a>
                </li>
            </ul>
            <!-- End Nav -->
        </div>
        <!-- End Nav Scroller -->
    </div>
    <!-- End Page Header -->


    <!-- Statistic -->
    <div class="whole_card">
        <form class="form_allinput" action="{{ url()->current() }}" method="GET">
            <div class="all_input">
                <input type="date" value={{$datePlanRange['fromDate']}} name="fromDate" id="fromDate" />
                <input type="date" value={{$datePlanRange['toDate']}} name="toDate" id="toDate" />
                <button type="submit" name="filter" class="filterr"> <i class="fa fa-filter"></i>{{\App\CPU\translate('Filter')}}</button>
            </div>
            <i class="fas fa-angle-down arr" id="myButton"></i>
        </form>
        <div id="myElement" class="staticc">

            <div class="all_one_static">
                <span class="info_title">{{\App\CPU\translate('Count_of_plan_pharmacies')}}</span>
                <div class="one_staticc">
                    <i class="fa fa-pie-chart"></i>
                    <div class="staticc_info">
                        <span class="info_num">{{$statistics['countPharmaciesInPlan']}}</span>
                    </div>
                </div>
            </div>

            <div class="all_one_static">
                <span class="info_title">{{\App\CPU\translate('Count_of_visited_pharmacies')}}</span>
                <div class="one_staticc">
                    <i class="fa fa-pie-chart"></i>
                    <div class="staticc_info">
                        <span class="info_num">{{$statistics['countVisited']}}</span>
                    </div>
                </div>
            </div>
            <div class="all_one_static">
                <span class="info_title">{{\App\CPU\translate('Count_of_unvisited_pharmacies')}}</span>
                <div class="one_staticc">
                    <i class="fa fa-pie-chart"></i>
                    <div class="staticc_info">
                        <span class="info_num">{{$statistics['countNotVisited']}}</span>
                    </div>
                </div>
            </div>
            <div class="all_one_static">
                <span class="info_title">{{\App\CPU\translate('Count_of_orders_in_the_plan')}}</span>
                <div class="one_staticc">
                    <i class="fa fa-pie-chart"></i>
                    <div class="staticc_info">
                        <span class="info_num">{{$statistics['totalOrdersVisited']}}</span>
                    </div>
                </div>
            </div>
            <div class="all_one_static">
                <span class="info_title">{{\App\CPU\translate('Total_sales_in_the_plan')}}</span>
                <div class="one_staticc">
                    <i class="fa fa-pie-chart"></i>
                    <div class="staticc_info">
                        <span class="info_num">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($statistics['totalAmountOfOrdersVisited']))}}</span>
                    </div>
                </div>
            </div>

            <div class="all_one_static">
                <span class="info_title">{{\App\CPU\translate('Count_of_matching_pharmacies')}}</span>
                <div class="one_staticc">
                    <i class="fa fa-pie-chart"></i>
                    <div class="staticc_info">
                        <span class="info_num">{{$statistics['countVisitedMatchSite']}}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- End statistic -->


    <!-- Card -->
    <div class="card-body box_shadoww">
        <!-- Table -->
        <div class="table-responsive" style="min-height:150px">
            <div class="card" style="border: none">
                <table id="pharmaciesPlanDetailstable" style="padding-top: 30px" class="display nowrap" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                    <thead class="thead-light">
                        <tr>
                            <th>
                                {{\App\CPU\translate('SL')}}#
                            </th>
                            <th>{{\App\CPU\translate('Pharmacy_Name')}}</th>
                            <th>{{\App\CPU\translate('Note')}}</th>
                            <th>{{\App\CPU\translate('Area')}}</th>
                            <th>{{\App\CPU\translate('Street_address')}}</th>
                            <th>{{\App\CPU\translate('Visit_time ')}}</th>
                            <th>{{\App\CPU\translate('Visit_status')}}</th>
                            <th>{{\App\CPU\translate('Site_match ')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($PharmaciesPlan as $key=>$pharmacyPlan)

                        <tr class="class-all">
                            <td>
                                {{$pharmacyPlan->id}}
                            </td>
                            <td>
                                <a href="#">{{$pharmacyPlan['pharmacy_name']}}</a>
                            </td>

                            <td>
                                @if($pharmacyPlan->Wnote)
                                <label class="text-body text-capitalize">{{$pharmacyPlan->Wnote}}</label>
                                @else
                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_note')}}</label>
                                @endif
                            </td>

                            <td>
                                @if($pharmacyPlan->area)
                                <label class="text-body text-capitalize">{{$pharmacyPlan->area}}</label>
                                @else
                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_area')}}</label>
                                @endif
                            </td>

                            <td>
                                @if($pharmacyPlan->street_address)
                                <label class="text-body text-capitalize">{{$pharmacyPlan->street_address}}</label>
                                @else
                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_address')}}</label>
                                @endif
                            </td>


                            <td>
                                @if($pharmacyPlan->visit_time)
                                <label class="text-body text-capitalize">{{date('d M Y',strtotime($pharmacyPlan->visit_time))}}</label>
                                @else
                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_visit_time')}}</label>
                                @endif
                            </td>

                            <td>
                                @if($pharmacyPlan->visited=='visited' || $pharmacyPlan->visited==1)
                                <span class="badge badge-soft-success">
                                    <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('Visited')}}
                                </span>
                                @else
                                <span class="badge badge-soft-danger">
                                    <span class="legend-indicator bg-danger" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('UnVisited')}}
                                </span>
                                @endif
                            </td>


                            <td>
                                @if($pharmacyPlan->site_match==1)
                                <span class="badge badge-soft-success">
                                    <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('Matching')}}
                                </span>
                                @else
                                <span class="badge badge-soft-danger">
                                    <span class="legend-indicator bg-danger" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('Not_matching')}}
                                </span>
                                @endif
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
        <!-- End Table -->
    </div>
    <!-- End Card -->
</div>

@endsection

@push('script')
<script>
    const button = document.getElementById("myButton");
    const element = document.getElementById("myElement");

    button.addEventListener("click", () => {
        if (element.style.display === "none") {
            element.style.display = "flex";
        } else {
            element.style.display = "none";
        }
    });

</script>
<script>
    var lang = "{{ Session::get('direction') }}";
    if (lang === 'rtl')
        url = "//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json";
    else
        url = "";

    $(document).ready(function() {
        $('#pharmaciesPlanDetailstable').DataTable({
            "language": {
                "url": url
            }
            , dom: 'Bfrtip'
            , buttons: [
                'excel'
            ]
            , searching: true
            , ordering: true
            , paging: true
        });
    });

</script>

@endpush
