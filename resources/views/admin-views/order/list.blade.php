@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Order List'))

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

@push('css_or_js')
<style>
    .main-title {
        direction: rtl;
        text-align: center;
        margin-bottom: 30px;
        font-weight: 800;
        border-bottom: solid 2px black;
        padding: 12px;
    }

</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-1">
        <div class="flex-between align-items-center">
            <div>
                <h1 class="page-header-title">{{\App\CPU\translate('Orders')}} <span class="badge badge-soft-dark mx-2">{{$orders->total()}}</span></h1>
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
                    <a class="nav-link active" href="#">{{\App\CPU\translate('order_list')}}</a>
                </li>
            </ul>
            <!-- End Nav -->
        </div>
        <!-- End Nav Scroller -->
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card">
        <!-- Header -->
         <div class="card-header">
                <div class="row flex-between justify-content-between flex-grow-1">

                  <div class="col-12 col-md-6">
                    <form action="{{ url()->current() }}" method="GET">
                      <!-- Search -->
                      <div class="input-group input-group-merge input-group-flush">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <i class="tio-search"></i>
                          </div>
                        </div>
                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                               placeholder="{{ \App\CPU\translate('Search orders') }}" aria-label="Search orders"
                               value="{{ $search }}" required>
                        <button style="margin-left: 20px !important;" type="submit"
                                class="btn btn-primary">{{ \App\CPU\translate('search') }}</button>
                      </div>
                      <!-- End Search -->
                    </form>
                  </div>


                  <div class="col-12 col-md-10 mt-2 mt-md-0">

                    <form style="width: 100%;" action="{{ url()->current() }}">
                      <div
                           class="row justify-content-start text-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                        <div style="width: 500px;" class="d-flex">
                          <select class="js-select2-custom form-control" name="customer_type">
                            <option value="all" {{ $customer_type == 'all' ? 'selected' : '' }}>
                              {{ \App\CPU\translate('All') }}
                            </option>

                            <option value="salesman" {{ $customer_type == 'salesman' ? 'selected' : '' }}>
                              {{ \App\CPU\translate('Salers') }}
                            </option>

                            <option value="pharmacist"
                                    {{ $customer_type == 'pharmacist' ? 'selected' : '' }}>
                              {{ \App\CPU\translate('Pharmacies') }}
                            </option>
                          </select>

                          <select class="js-select2-custom form-control" name="customer_id">
                            <option value=0 {{ $customer_id != 0 ? '' : 'selected' }}>
                              {{ \App\CPU\translate('Salers') }}
                            </option>
                            @foreach ($salers as $saler)
                            <option value="{{ $saler->id }}"
                                    {{ $customer_id != $saler->id ? '' : 'selected' }}>
                              {{ $saler->name }}
                            </option>
                            @endforeach

                          </select>

                          <div style="border: .0625rem solid #e7eaf3;border-radius: .3125rem;" class="input-group input-group-merge input-group-flush">
                            <input type="date" name="from_date"
                                   value="{{ isset($from_date) ? $from_date : '' }}" id="from_date"
                                   class="form-control">
                          </div>
                          <div style="border: .0625rem solid #e7eaf3;border-radius: .3125rem;" class="input-group input-group-merge input-group-flush">
                            <input type="date" name="to_date"
                                   value="{{ isset($to_date) ? $to_date : '' }}" id="to_date"
                                   class="form-control">
                          </div>

                          <button style="margin-left: 20px !important;" type="submit"
                                  class="btn btn-primary">
                            {{ \App\CPU\translate('Filter') }}
                          </button>
                        </div>

                      </div>
                    </form>

                  </div>

                    <div class="col-12 col-md-2 mt-2 mt-md-0">
                        <a style="margin-top:0px;width: fit-content;" class="btn btn-success btn-block mx-2"
                            href="{{ route('admin.orders.generate_excel_report', ['status' => $status] + request()->query()) }}">
                            <i class="tio-print mr-1"></i> {{ \App\CPU\translate('Export') }}
                        </a>
                    </div>
                </div>
                <!-- End Row -->
            </div>
        <!-- End Header -->

        <!-- Table -->
        <div class="table-responsive " style="min-height:150px">

            <table  class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" style="width: 100%; text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">

                <thead class="thead-light">
                    <tr>
                        <th class="">
                            {{\App\CPU\translate('SL')}}#
                        </th>
                        <th class=" ">{{\App\CPU\translate('Order')}}</th>
                        <th>{{\App\CPU\translate('Date')}}</th>
                        <th>{{\App\CPU\translate('pharmacy_name')}}</th>
                        <th>{{\App\CPU\translate('customer_name')}}</th>
                        <th>{{\App\CPU\translate('customer_type')}}</th>
                        <th>{{\App\CPU\translate('Status')}}</th>
                        <th>{{\App\CPU\translate('Total')}}</th>
                        <th>{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Status')}} </th>
                        <th>{{\App\CPU\translate('Action')}}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $key=>$order)

                    <tr class="status-{{$order['order_status']}} class-all">

                        <td class="">
                            {{$orders->firstItem()+$key}}
                        </td>

                        <td class="table-column-pl-0">
                            <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                        </td>

                        <td>{{date('d M Y',strtotime($order['created_at']))}}</td>

                      {{-- new --}}
                        {{-- cus name --}}
                      <td>
                            @if($order->customer)

                            <a href="" id="editCompany" data-toggle="modal" data-target='#practice_modal' data-order_id="{{$order['id']}}">
                                @if(isset($order->pharmacy->name))
                                {{$order->pharmacy->name}}
                                @else
                                {{$order['pharmacy_name']}}
                                @endif
                            </a>

                            @else
                            <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                            @endif
                        </td>
                      <!--  
                      <td>
                            @if($order->customer)

                            <a href="" id="editCompany" data-toggle="modal" data-target='#practice_modal' data-order_id="{{$order['id']}}">
                                {{$order['pharmacy_name']}}
                            </a>

                            @else
                            <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                            @endif
                        </td>
-->
                        <!--<td>
                         //   @if($order->customer)

                          //  <a href="" id="editCompany" data-toggle="modal" data-target='#practice_modal' data-order_id="{{$order['id']}}">
                                {{$order->customer['f_name']}}
                         //   </a>

                          //  @else
                          //  <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                         //   @endif
                       // </td> -->
                      
                        <td>
                            @if($order->customer)
                            <a href="{{route('admin.customer.view',['user_id'=>$order->customer['id']])}}" >
                                {{$order->customer['f_name']}}
                            </a>

                            @else
                            <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                            @endif
                        </td>
                        {{-- new --}}


                        <td>
                            @if($order->customer)
                            <a class="text-body text-capitalize" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{\App\CPU\translate($order->customer['user_type'])}}</a>
                            @else
                            <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                            @endif
                        </td>

                        <td>
                            @if($order->payment_status=='paid')
                            <span class="badge badge-soft-success">
                                <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('paid')}}
                            </span>
                            @else
                            <span class="badge badge-soft-danger">
                                <span class="legend-indicator bg-danger" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('unpaid')}}
                            </span>
                            @endif
                        </td>
                        <td> {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount))}}</td>
                        <td class="text-capitalize">
                            @if($order['order_status']=='pending')
                            <span class="badge badge-soft-info ml-2 ml-sm-3">
                                <span class="legend-indicator bg-info" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate($order['order_status'])}}
                            </span>

                            @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                <span class="legend-indicator bg-warning" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate($order['order_status'])}}
                            </span>
                            @elseif($order['order_status']=='confirmed')
                            <span class="badge badge-soft-success ml-2 ml-sm-3">
                                <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate($order['order_status'])}}
                            </span>
                            @elseif($order['order_status']=='failed')
                            <span class="badge badge-danger ml-2 ml-sm-3">
                                <span class="legend-indicator bg-warning" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate($order['order_status'])}}
                            </span>
                            @elseif($order['order_status']=='delivered')
                            <span class="badge badge-soft-success ml-2 ml-sm-3">
                                <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate($order['order_status'])}}
                            </span>
                            @else
                            <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                <span class="legend-indicator bg-danger" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate($order['order_status'])}}
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="tio-settings"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i class="tio-visible"></i> {{\App\CPU\translate('view')}}</a>

                                    <a class="dropdown-item" target="_blank" href="{{route('admin.orders.edit-order',[$order['id']])}}"><i class="tio-edit"></i> {{\App\CPU\translate('Edit')}}</a>

                                    <a class="dropdown-item" target="_blank" href={{ route('admin.orders.generate-excel', [$order['id']]) }}>
                                        <i class="tio-print mr-1"></i> {{ \App\CPU\translate('Export') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- End Table -->

        <!-- Footer -->
        <div class="card-footer">
            <!-- Pagination -->
            <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                <div class="col-sm-auto">
                    <div class="d-flex justify-content-center justify-content-sm-end">
                        <!-- Pagination -->
                        {!! $orders->links() !!}
                    </div>
                </div>
            </div>
            <!-- End Pagination -->
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->
</div>

<div class="modal fade" id="practice_modal">
    <div class="modal-dialog">
        <div class="modal-content" style="min-height: 150px; padding: 20px">
            <div class="modal-body mb-4" style="padding: 0px;">
                <div class="main-title">
                    <h3>{{\App\CPU\translate('Pharmacy_Deatils')}}</h3>
                </div>
                <div class="row" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                    <div class="col-md-12 mb-3">
                        <div>
                            <p style="margin-bottom: 5px"> {{\App\CPU\translate('Name')}} :</p>
                        </div>
                        <div id="pharmacy_name"></div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div>
                            <p style="margin-bottom: 5px"> {{\App\CPU\translate('Area')}} :</p>
                        </div>
                        <div id="pharmacy_area"></div>
                    </div>


                    <div class="col-md-12 mb-3">

                        <div>
                            <p style="margin-bottom: 5px"> {{\App\CPU\translate('Address')}} :</p>
                        </div>
                        <div id="pharmacy_address"></div>
                    </div>


                    <div class="col-md-12 mb-3">

                        <div>
                            <p style="margin-bottom: 5px"> {{\App\CPU\translate('City')}} :</p>
                        </div>
                        <div id="pharmacy_city"></div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('script_2')
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '#editCompany', function(event) {
            event.preventDefault();
            var order_id = $(this).data('order_id');
            $.ajax({
                url: 'pharmacy/details/' + order_id
                , type: "GET"
                , dataType: 'json'
                , success: function(data) {
                    $('#pharmacy_area').html("<p class='form-control'>" + data.data.region + "</p>");
                    $('#pharmacy_name').html("<p class='form-control'>" + data.data.name + "</p>");
                    $('#pharmacy_address').html("<p class='form-control'>" + data.data.Address + "</p>");
                    $('#pharmacy_city').html("<p class='form-control'>" + data.data.city + "</p>");
                }
            });

        });

    });

</script>




@endpush
