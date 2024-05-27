@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Orders_Seller_Report_:').$seller->name)
@section('content')

@push('css_or_js')
<link rel="stylesheet" href="{{asset('public/css/planDetails.css')}}">
@endpush

<div class="content container-fluid">
    <!-- Page Header -->

    <div class="page-header mb-1">
        <div class="flex-between align-items-center">
            <div>
                <h1 class="page-header-title">{{\App\CPU\translate('Orders_Seller_Report')}}:{{$seller->name}}<span class="badge badge-soft-dark mx-2">{{count($ordersSellerTeamReport)}}</span></h1>
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
                    <a class="nav-link active" href="#">{{\App\CPU\translate('Orders_Seller_report_list')}}</a>
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
                <input type="date" value={{$dateReportRange['fromDate']}} name="fromDate" id="fromDate" />
                <input type="date" value={{$dateReportRange['toDate']}} name="toDate" id="toDate" />
                <button type="submit" name="filter" class="filterr"> <i class="fa fa-filter"></i>{{\App\CPU\translate('Filter')}}</button>
            </div>
            <i class="fas fa-angle-down arr" id="myButton"></i>
        </form>

    </div>
    <!-- End statistic -->


    <!-- Card -->
    <div class="card-body box_shadoww">

        <!-- Table -->
        <div class="table-responsive" style="min-height:150px">
            <div class="card" style="border: none">
                <table id="ordersTeamReportTable" style="padding-top: 30px" class="display nowrap" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                    <thead class="thead-light">
                        <tr>
                            <th>
                                {{\App\CPU\translate('SL')}}#
                            </th>
                            <th>{{\App\CPU\translate('order_number')}}</th>
                            <th>{{\App\CPU\translate('pharmacy_name')}}</th>
                            <th>{{\App\CPU\translate('Total_sales')}}</th>
                            <th>{{\App\CPU\translate('Detection_number')}}</th>
                            <th>{{\App\CPU\translate('cost_center')}}</th>

                        </tr>
                    </thead>

                    <tbody>
                        @foreach($ordersSellerTeamReport as $orderSellerTeamReport)

                        <tr class="class-all">
                            <td> {{$orderSellerTeamReport->num_id}}</td>

                            <td>
                                <label class="text-body text-capitalize"> {{ $orderSellerTeamReport->orderId }}</label>
                            </td>

                            <td>
                                <label class="text-body text-capitalize"> {{ $orderSellerTeamReport->pharmacyName }}</label>

                            </td>

                            <td>
                                <label class="text-body text-capitalize"> {{ $orderSellerTeamReport->orderTotalPrice }}</label>

                            </td>

                            <td>
                                <label class="text-body text-capitalize"> {{ $orderSellerTeamReport->detectionNumber }}</label>

                            </td>

                            <td>
                                <label class="text-body text-capitalize"> {{ $orderSellerTeamReport->costCenter }}</label>

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
        $('#ordersTeamReportTable').DataTable({
            "language": {
                "url": url
            }
            , dom: 'Bfrtip'
            , buttons: [{
                extend: 'excel'
                , exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            }]
            , searching: true
            , ordering: true
            , paging: true
        });
    });

</script>

@endpush
