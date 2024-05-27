@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Inhouse product sale Report'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@php
    $Totalprice=0;
    $BagTotalprice=0;
@endphp
@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <!-- Nav -->
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <ul class="nav nav-tabs page-header-tabs" id="projectsTab" role="tablist">
                <li class="nav-item">
                    <a  class="nav-link active" id="product" href="javascript:">{{\App\CPU\translate('InHouse product sale report')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bag" href="javascript:">{{\App\CPU\translate('InHouse bag sale report')}}</a>
                </li>
            </ul>

        </div>

        <!-- End Nav -->
    </div>
    <!-- End Page Header -->

    <div class="row" id="dataTable1" style="display: block">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <form style="width: 100%;" action="{{route('admin.report.inhoue-product-sale')}}">
                        @csrf
                        <div class="flex-between row align-items-center">
                            <div class="col-2 text-center">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{\App\CPU\translate('Category')}}</label>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <select class="js-select2-custom form-control" name="category_id">
                                            <option value="all">{{\App\CPU\translate('All')}}</option>
                                            @foreach($categories as $c)
                                            <option value="{{$c['id']}}" {{$category_id==$c['id']? 'selected' : '' }}>
                                                {{$c['name']}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                   {{-- <div class="col-6">
                                            <input type="date" name="from_date"
                                                value="{{ isset($from_date) ? $from_date : '' }}" id="from_date"a
                                                class="form-control">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" name="to_date"
                                                value="{{ isset($to_date) ? $to_date : '' }}" id="to_date"
                                                class="form-control">
                                        </div> --}}
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                              <div class="row">
  			
                                <div class="col-12">
                                  <button type="submit" class="btn btn-primary btn-block">
                                      {{\App\CPU\translate('Filter')}}
                                  </button>
                                </div>
                              
                                 <div class="col-12 py-2">
                                    <a class="btn btn-success btn-block"
                                        href="{{ route('admin.report.generateExcel', request()->query()) }}">
                                        <i class="tio-print mr-1"></i> {{ \App\CPU\translate('Export') }}
                                    </a>
                                </div>
                              </div>

                              
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body" style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left'
                    }};">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    {{\App\CPU\translate('Product Name')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>
                                <th scope="col">
                                    {{\App\CPU\translate('Total Sale')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>
                                <th scope="col">
                                    {{\App\CPU\translate('Total Sale offers')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>
                                <th scope="col">
                                    {{\App\CPU\translate('Total Price')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key=>$data)
                            @foreach ($data->order_delivered as $order)
                             @php
                                 $Totalprice+=$order->qty*$order->price;
                             @endphp
                            @endforeach
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td>{{$data['name']}}</td>
                                <td>{{$data->order_delivered->sum('qty')}}</td>
                                <td>{{$data->order_delivered_offers->sum('total_qty')}}</td>
                                <td>{{$Totalprice}}</td>
                            </tr>
                            @php
                                $Totalprice=0;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                    <table>
                        <tfoot>
                            {!!$products->links() !!}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Stats -->



    <div class="row" id="dataTable2" style="display: none">
        <div class="col-12">
            <div class="card">
 				<div class="card-header">
                        <form style="width: 100%;" action="{{ route('admin.report.inhoue-product-sale') }}">
                            @csrf
                            <div class="flex-between row align-items-center">
                                <div class="col-7">
                                    <div class="row">
                                        <div class="col-12">
                                          <span>من تاريخ</span>
                                            <input type="date" name="from_date_bag"
                                                value="{{ isset($from_date_bag) ? $from_date_bag : '' }}" id="from_date_bag"
                                                class="form-control">
                                        </div>
                                        <div class="col-12">
                                          <span>الى تاريخ</span>
                                            <input type="date" name="to_date_bag"
                                                value="{{ isset($to_date_bag) ? $to_date_bag : '' }}" id="to_date_bag"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="row">

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                {{ \App\CPU\translate('Filter') }}
                                            </button>
                                        </div>

                                        <div class="col-12 py-2">
                                            <a class="btn btn-success btn-block"
                                                href="{{ route('admin.report.generateExcelBag', request()->query()) }}">
                                                <i class="tio-print mr-1"></i> {{ \App\CPU\translate('Export') }}
                                            </a>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </form>
                    </div>
                <div class="card-body" style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left'
                    }};">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    {{\App\CPU\translate('Bag Name')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>
                                <th scope="col">
                                    {{\App\CPU\translate('Total Sale')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>

                                <th scope="col">
                                    {{\App\CPU\translate('Total Price')}} <label class="badge badge-success ml-3"
                                        style="cursor: pointer">{{\App\CPU\translate('ASE/DESC')}}</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bags as $key=>$bag)
                            @foreach ($bag->bag_order_delivered as $bag_order)
                            @php
                                $BagTotalprice+=$bag_order->bag_qty*$bag_order->bag_price;
                            @endphp
                           @endforeach
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td>{{$bag['bag_name']}}</td>
                                <td>{{$bag->bag_order_delivered->sum('bag_qty')}}</td>
                                <td>{{$BagTotalprice}}</td>
                            </tr>
                             @php
                                $BagTotalprice=0;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <table>
                        <tfoot>
                            {!! $bags->links() !!}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>





</div>
@endsection

@push('script')


<script>

    $('#product').on('click', function() {

        document.getElementById("dataTable1").style.display = "block";
        document.getElementById("dataTable2").style.display = "none";
        document.getElementById("bag").classList.remove("active");
        document.getElementById("product").classList.add("active");
   });


    $('#bag').on('click', function() {

        document.getElementById("dataTable1").style.display = "none";
        document.getElementById("dataTable2").style.display = "block";
        document.getElementById("bag").classList.add("active");
        document.getElementById("product").classList.remove("active");
    });

</script>

@endpush

@push('script_2')

@endpush
