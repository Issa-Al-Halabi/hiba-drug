@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Order Details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .sellerName {
        height: fit-content;
        margin-top: 10px;
        margin-left: 10px;
        font-size: 16px;
        border-radius: 25px;
        text-align: center;
        padding-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid" style="direction: {{ Session::get('direction') === 'rtl' ? 'rtl' : 'ltr' }}; text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
    <!-- Page Header -->
    <div class="page-header d-print-none p-3" style="background: white">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                href="{{route('admin.orders.list',['status'=>'all'])}}">{{\App\CPU\translate('Orders')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{\App\CPU\translate('Order_details')}}
                           </li>
                    </ol>
                </nav>

                <div class="d-sm-flex align-items-sm-center">
                    <h1 class="page-header-title">{{\App\CPU\translate('Order')}} #{{$order['id']}}</h1>

                    @if($order['payment_status']=='paid')
                    <span class="badge badge-soft-success ml-sm-3">
                        <span class="legend-indicator bg-success"></span>{{\App\CPU\translate('Paid')}}
                    </span>
                    @else
                    <span class="badge badge-soft-danger ml-sm-3">
                        <span class="legend-indicator bg-danger"></span>{{\App\CPU\translate('Unpaid')}}
                    </span>
                    @endif

                    @if($order['order_status']=='pending')
                    <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-info text"></span>{{str_replace('_','
                        ',$order['order_status'])}}
                    </span>
                    @elseif($order['order_status']=='failed')
                    <span class="badge badge-danger ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-info"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                    <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-warning"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @elseif($order['order_status']=='delivered' || $order['order_status']=='confirmed')
                    <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-success"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @else
                    <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @endif
                    <span class="ml-2 ml-sm-3">
                        <i class="tio-date-range"></i> {{date('d M Y H:i:s',strtotime($order['created_at']))}}
                    </span>

                    @if(\App\CPU\Helpers::get_business_settings('order_verification'))
                    <span class="ml-2 ml-sm-3">
                        <b>
                            {{\App\CPU\translate('order_verification_code')}} : {{$order['verification_code']}}
                        </b>
                    </span>
                    @endif

                </div>

                <div class="row" >
                    <div class="col-12 col-md-6">
                        <div class="hs-unfold float-right col-6">
                            <div class="dropdown">
                                <select name="order_status" onchange="order_status(this.value)"
                                    class="status form-control" data-id="{{$order['id']}}">

                                    <option value="pending" {{$order->order_status == 'pending'?'selected':''}} >
                                        {{\App\CPU\translate('Pending')}}</option>
                                    <option value="confirmed" {{$order->order_status == 'confirmed'?'selected':''}} >
                                        {{\App\CPU\translate('Confirmed')}}</option>
                                    <option value="processing" {{$order->order_status == 'processing'?'selected':''}}
                                        >{{\App\CPU\translate('Processing')}} </option>
                                    <option class="text-capitalize" value="out_for_delivery" {{$order->order_status ==
                                        'out_for_delivery'?'selected':''}} >{{\App\CPU\translate('out_for_delivery')}}
                                    </option>
                                    <option value="delivered" {{$order->order_status == 'delivered'?'selected':''}}
                                        >{{\App\CPU\translate('Delivered')}} </option>
                                    <option value="returned" {{$order->order_status == 'returned'?'selected':''}} >
                                        {{\App\CPU\translate('Returned')}}</option>
                                    <option value="failed" {{$order->order_status == 'failed'?'selected':''}}
                                        >{{\App\CPU\translate('Failed')}} </option>
                                    <option value="canceled" {{$order->order_status == 'canceled'?'selected':''}}
                                        >{{\App\CPU\translate('Canceled')}} </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 hs-unfold float-right pr-2">
                            <div class="dropdown">
                                <select name="payment_status" class="payment_status form-control"
                                    data-id="{{$order['id']}}">

                                    <option
                                        onclick="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'paid'])}}','Change status to paid ?')"
                                        href="javascript:" value="paid" {{$order->payment_status ==
                                        'paid'?'selected':''}} >
                                        {{\App\CPU\translate('Paid')}}
                                    </option>
                                    <option value="unpaid" {{$order->payment_status == 'unpaid'?'selected':''}} >
                                        {{\App\CPU\translate('Unpaid')}}
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 mt-2">
                        <a class="text-body mr-3" target="_blank"
                            href={{route('admin.orders.generate-excel',[$order['id']])}}>
                            <i class="tio-print mr-1"></i> {{\App\CPU\translate('Export')}} {{\App\CPU\translate('excel')}}
                        </a>
                    </div>


                </div>
                <!-- End Unfold -->
            </div>
        </div>
    </div>

    <!-- End Page Header -->


    <div class="row" id="printableArea">
        <div class="col-lg-8 mb-3 mb-lg-0">

            <!-- Card bag-->
            <div class="card mb-3 mb-lg-5">
                <!-- Header -->
                <div class="card-header" style="display: block!important;">
                    <div class="row">
                        <div class="col-12 pb-2 border-bottom">
                            <h4 class="card-header-title">
                                {{\App\CPU\translate('Bags_Order_details')}}
                                <span
                                    class="badge badge-soft-dark rounded-circle ml-1">{{$bagsOrder->count()}}</span>
                            </h4>
                        </div>
                    </div>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <div class="media">


                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-3 product-name">
                                    <p> {{\App\CPU\translate('Bag Name')}}</p>
                                </div>

                                <div class="col col-md-2 align-self-center p-0 ">
                                    <p> {{\App\CPU\translate('price')}}</p>
                                </div>

                                <div class="col col-md-1 align-self-center">
                                    <p>{{\App\CPU\translate('Q')}}</p>
                                </div>

                                <div class="col col-md-1 align-self-center  p-0 product-name">
                                    <p> {{\App\CPU\translate('Q_Offer')}}</p>
                                </div>

                                <div class="col col-md-2 d-flex align-self-center justify-content-end p-0 product-name">
                                    <p> {{\App\CPU\translate('Discount')}}</p>
                                </div>

                                <div class="col col-md-2 align-self-center text-right  ">
                                    <p> {{\App\CPU\translate('Subtotal')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($bagSubtotal=0)
                    @foreach($bagsOrder as $bagOrder)
                    <!-- Media -->
                    <div class="media">
                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-3 mb-3 mb-md-0 product-name">
                                    <a href="" id="editCompany" data-toggle="modal" data-target='#practice_modal'
                                        data-product_id="{{$bagOrder['order_id']}}"
                                        data-id="{{ $bagOrder['order_id'] }}">{{substr($bagOrder['bag_name'],0,45)}}{{strlen($bagOrder['bag_name'])>25?'...':''}}</a>
                                </div>

                                <div class="col col-md-2 align-self-center p-0 ">
                                    <h6>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($bagOrder['bag_price']))}}
                                    </h6>
                                </div>

                                <div class="col col-md-1 align-self-center">

                                    <h5>{{$bagOrder->bag_qty}}</h5>
                                </div>

                                <div class="col col-md-1 align-self-center  p-0 product-name">

                                    <h5>{{$bagOrder['total_qty']}}</h5>
                                </div>
                                <div
                                    class="col col-md-2 align-self-center d-flex  justify-content-end p-0 product-name">

                                    <h5>
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($bagOrder['bag_discount']))}}
                                    </h5>
                                </div>

                                <div class="col col-md-2 align-self-center text-right  ">
                                    @php($bagSubtotal=$bagOrder['bag_price']*$bagOrder->bag_qty+$bagOrder['bag_tax']-$bagOrder['bag_discount'])

                                    <h5 style="font-size: 12px">
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($bagSubtotal))}}
                                    </h5>
                                </div>


                            </div>
                        </div>
                    </div>

                    @endforeach

                    <!-- End Row -->
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card bag -->

            <!-- Card -->
            <div class="card mb-3 mb-lg-5">
                <!-- Header -->
                <div class="card-header" style="display: block!important;">
                    <div class="row">
                        <div class="col-12 pb-2 border-bottom">
                            <h4 class="card-header-title">
                                {{\App\CPU\translate('Order_details')}}
                                <span
                                    class="badge badge-soft-dark rounded-circle ml-1">{{$order->details->count()}}</span>
                            </h4>
                        </div>

                        <div class="col-6 pt-2">
                            @if ($order->order_note !=null)
                            <span class="font-weight-bold text-capitalize">
                                {{\App\CPU\translate('order_note')}} :
                            </span>
                            <p class="pl-1">
                                {{$order->order_note}}
                            </p>
                            @endif
                        </div>

                        <div class="col-6 pt-2">
                            @if ($order->delivery_date !=null)
                            <span class="font-weight-bold text-capitalize">
                                {{\App\CPU\translate('delivery_date')}} :
                            </span>
                            <p class="pl-1">
                                {{$order->delivery_date}}
                            </p>
                            @endif
                        </div>


                    </div>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <div class="media">


                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-3 product-name">
                                    <p> {{\App\CPU\translate('Name')}}</p>
                                </div>

                                <div class="col col-md-2 align-self-center p-0 ">
                                    <p> {{\App\CPU\translate('price')}}</p>
                                </div>

                                <div class="col col-md-1 align-self-center">
                                    <p> {{\App\CPU\translate('Q')}}</p>
                                </div>

                                <div class="col col-md-1 align-self-center  p-0 product-name">
                                    <p> {{\App\CPU\translate('Q_Offer')}}</p>
                                </div>

                                <div class="col col-md-2 d-flex align-self-center justify-content-end p-0 product-name">
                                    <p> {{\App\CPU\translate('Discount')}}</p>
                                </div>

                                <div class="col col-md-2 align-self-center text-right  ">
                                    <p> {{\App\CPU\translate('Subtotal')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($subtotal=0)
                    @php($total=0)
                    @php($shipping=0)
                    @php($discount=0)
                    @php($tax=0)
                    @foreach($order->details as $key=>$detail)

                    @if($detail->product)

                    <!-- Media -->
                    <div class="media">

                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-3 mb-3 mb-md-0 product-name">
                                    <a href="{{route('admin.product.view',[$detail['product_id']])}}" target="_blank"
                                    rel="noopener noreferrer">{{substr($detail->product['name'],0,100)}}</a>

                                    {{-- <a href="{{route('admin.product.view',[$detail['product_id']])}}" target="_blank"
                                        rel="noopener noreferrer">{{substr($detail->product['name'],0,55)}}{{strlen($detail->product['name'])>35?'':''}}</a> --}}

                                </div>

                                <div class="col col-md-2 align-self-center p-0 ">
                                    <h6>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($detail['price']))}}
                                    </h6>
                                </div>

                                <div class="col col-md-1 align-self-center">

                                    <h5>{{$detail->qty}}</h5>
                                </div>


                                <div class="col col-md-1 align-self-center  p-0 product-name">

                                    <h5>{{$detail['total_qty']}}</h5>
                                </div>
                                <div
                                    class="col col-md-2 align-self-center d-flex  justify-content-end p-0 product-name">

                                    <h5>
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($detail['discount']))}}
                                    </h5>
                                </div>

                                <div class="col col-md-2 align-self-center text-right  ">
                                    @php($subtotal=$detail['price']*$detail->qty+$detail['tax']-$detail['discount'])

                                    <h5 style="font-size: 12px">
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($subtotal))}}
                                    </h5>
                                </div>


                            </div>
                        </div>
                    </div>
                    {{-- seller info old --}}

                    @php($discount+=$detail['discount'])
                    @php($tax+=$detail['tax'])
                    @php($total+=$subtotal)
                    <!-- End Media -->
                    <hr>
                    @endif
                    @php($sellerId=$detail->seller_id)
                    @endforeach
                    @php($shipping=$order['shipping_cost'])
                    @php($coupon_discount=$order['discount_amount'])
                    {{-- <div>

                    </div> --}}
                    <div class="row justify-content-md-end mb-3">
                        <div class="col-md-9 col-lg-8">
                            <dl class="row text-sm-right">
                                <dt class="col-sm-6">{{\App\CPU\translate('Shipping')}}</dt>
                                <dd class="col-sm-6 border-bottom">
                                    <strong>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</strong>
                                </dd>

                                <dt class="col-sm-6">{{\App\CPU\translate('coupon_discount')}}</dt>
                                <dd class="col-sm-6 border-bottom">
                                    <strong>-
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($coupon_discount))}}</strong>
                                </dd>

                                <dt class="col-sm-6">{{\App\CPU\translate('Total')}}</dt>
                                <dd class="col-sm-6">
                                    <strong>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total+$shipping-$coupon_discount))}}</strong>
                                </dd>
								@if($order['order_status']=='delivered')
                                <dt class="col-sm-6">{{\App\CPU\translate('Amount received')}}</dt>
                                <dd class="col-sm-6">
                                    <strong>{{ number_format((float)trim($order['order_receive_price'], ', ')) }} SP</strong>
                                </dd>

                                <dt class="col-sm-6">{{\App\CPU\translate('Notes on the amount received')}}</dt>
                                <dd class="col-sm-6">
                                    <strong>{{$order['order_note_price']}}</strong>
                                </dd>
								@endif
                                @if($order['order_status']=='returned')
                                <dt class="col-sm-6">{{\App\CPU\translate('order_note_returned')}}</dt>
                                  <dd class="col-sm-6">
                                      <strong>{{$order['order_note_price']}}</strong>
                                  </dd>
                              	</dl>
                           		@endif
                            <!-- End Row -->
                        </div>
                    </div>
                    <!-- End Row -->
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->

        </div>

        <div class="col-lg-4">
            <div class="card mb-2">
                <div class="card-header">
                    <h4>{{\App\CPU\translate('shipping_info')}}</h4>
                </div>
                <div class="card-body text-capitalize">
                    <ul class="list-unstyled list-unstyled-py-2">

                        <li>
                            <select class="form-control text-capitalize" name="delivery_type"
                                onchange="choose_delivery_type(this.value)">
                                <option value="0">
                                    {{\App\CPU\translate('choose_delivery_type')}}
                                </option>

                                <option value="self_delivery" {{$order->delivery_type=='self_delivery'?'selected':''}}>
                                    {{\App\CPU\translate('by_self_delivery_man')}}
                                </option>

                            </select>
                        </li>
                        <li id="choose_delivery_man">
                            <label for="">
                                {{\App\CPU\translate('choose_delivery_man')}}
                            </label>
                            <select class="form-control text-capitalize js-select2-custom" name="delivery_man_id"
                                onchange="addDeliveryMan(this.value)">
                                <option value="0">{{\App\CPU\translate('select')}}</option>
                                @foreach($delivery_men as $deliveryMan)
                                <option value="{{$deliveryMan['id']}}"
                                    {{$order['delivery_man_id']==$deliveryMan['id']?'selected':''}}>
                                    {{$deliveryMan['f_name'].' '.$deliveryMan['l_name'].' ('.$deliveryMan['phone'].'
                                    )'}}
                                </option>
                                @endforeach
                            </select>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header">
                    @if ($status==true)
                    <h4 class="card-header-title">{{\App\CPU\translate('Sales Man info :')}}</h4>
                    @else
                    <h4 class="card-header-title">{{\App\CPU\translate('Pharmacy info :')}}</h4>
                    @endif

                </div>
                <!-- End Header -->

                <!-- Body -->
                @if($order->customer)
                <div class="card-body">
                    <div class="media align-items-center" href="javascript:">
                        <div class="avatar avatar-circle mr-3">
                            <img class="avatar-img" style="width: 75px;height: 42px"
                                onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                src="{{asset('storage/app/public/profile/'.$order->customer->image)}}" alt="Image">
                        </div>
                        <div class="media-body">
                            <span class="text-body text-hover-primary">{{$order->customer['f_name'].'
                                '.$order->customer['l_name']}}</span>
                        </div>

                        <div class="media-body text-right">
                            {{--<i class="tio-chevron-right text-body"></i>--}}
                        </div>
                    </div>

                    <hr>

                    <div class="media align-items-center" href="javascript:">
                        <div class="icon icon-soft-info icon-circle mr-3">
                            <i class="tio-shopping-basket-outlined"></i>
                        </div>
                        <div class="media-body">
                            <span class="text-body text-hover-primary">
                                {{\App\Model\Order::where('customer_id',$order['customer_id'])->count()}}
                                {{\App\CPU\translate('orders')}}</span>
                        </div>
                        <div class="media-body text-right">
                            {{--<i class="tio-chevron-right text-body"></i>--}}
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{\App\CPU\translate('Contact_info')}}</h5>
                    </div>

                    <ul class="list-unstyled list-unstyled-py-2">
                        <li>
                            <i class="tio-online mr-2"></i>
                            {{$order->customer['email']}}
                        </li>
                        <li>
                            <i class="tio-android-phone-vs mr-2"></i>
                            {{$order->customer['phone']}}
                        </li>
                    </ul>

                    <hr>

                    @if($status==true && $UserPharmacy!=null && $pharmacy!=null)

                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{\App\CPU\translate('pharmacy_info')}} {{$UserPharmacy['name']}}
                        </h5>

                    </div>

                    <ul class="list-unstyled list-unstyled-py-2">
                        <li>
                            <i class="tio-online mr-2"></i>
                            <span>{{\App\CPU\translate('Name: ')}}</span>
                            {{$UserPharmacy['name']}}
                        </li>
                        <li>
                            <i class="tio-android-phone-vs mr-2"></i>
                            <span>{{\App\CPU\translate('phone: ')}}</span>
                            {{$UserPharmacy['phone']}}
                        </li>
                       <li>
                            <i class="tio-android-phone-vs mr-2"></i>
                            <span>{{\App\CPU\translate('Name: ')}}</span>
                            {{$pharmacy['name']}}
                        </li>

                    </ul>
                    <hr>
                    @else
                    <div class="card-body">
                        <div class="media align-items-center">
                            <span>{{\App\CPU\translate('no_pharmacy_found')}}</span>
                        </div>
                    </div>
                    @endif

                </div>
                @else
                <div class="card-body">
                    <div class="media align-items-center">
                        <span>{{\App\CPU\translate('no_customer_found')}}</span>
                    </div>
                </div>
                @endif
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>
    </div>
    <!-- End Row -->
</div>

<div class="modal fade" id="practice_modal">
    <div class="modal-dialog">

        <div class="modal-content" style="min-height: 150px; padding: 20px">
            <div class="card-body" style="padding: 0">
                <div class="table-responsive">
                    <table id="sublawmasterdata"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};  width: 100%;"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ \App\CPU\translate('product_name') }}</th>
                                <th scope="col">{{ \App\CPU\translate('brand_name') }}</th>
                                <th scope="col">{{ \App\CPU\translate('Q') }}</th>
                                <th scope="col">{{ \App\CPU\translate('price') }}</th>
                                <th scope="col">{{ \App\CPU\translate('total_price') }}</th>
                                <th scope="col">{{ \App\CPU\translate('note') }}</th>
                            </tr>
                        </thead>
                        <tbody id="exampleid">
                            <tr>
                                <td>*******</td>
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

@push('script_2')


<script>
    $(document).on('change', '.payment_status', function () {
            var id = $(this).attr("data-id");
            var value = $(this).val();
            Swal.fire({
                title: '{{\App\CPU\translate('Are you sure Change this')}}?',
                text: "{{\App\CPU\translate('You will not be able to revert this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{\App\CPU\translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.payment-status')}}",
                        method: 'POST',
                        data: {
                            "id": id,
                            "payment_status": value
                        },
                        success: function (data) {
                            toastr.success('{{\App\CPU\translate('Status Change successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });

        function order_status(status) {

            if (status == 'delivered') {
            Swal.fire({
                title: '{{\App\CPU\translate('Order is already delivered, and transaction amount has been disbursed, changing status can be the reason of miscalculation')}}!',
                text: "{{\App\CPU\translate('Think before you proceed')}}.",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{\App\CPU\translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{\App\CPU\translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                toastr.success('{{\App\CPU\translate('Status Change successfully')}}!');
                                location.reload();
                            }

                        }
                    });
                }
            })
        }
        if (status == 'confirmed') {
            Swal.fire({
                title: `{{\App\CPU\translate("Are_you_sure_to_confirm_the_order_?")}}`
                , html: `<div class="form-group">
                <input type="number" id="delivery_order" class="form-control" placeholder="{{\App\CPU\translate("delivery_order")}}">
                <input type="number" id="cost_center" class="form-control" placeholder="{{\App\CPU\translate("cost_center")}}">
                <input type="date" id="delivery_date" class="form-control" placeholder="{{\App\CPU\translate("delivery_date")}}"></div>`
                , showCancelButton: true
                , cancelButtonText: `{{\App\CPU\translate("Cancel")}}`
                , confirmButtonColor: '#377dff'
                , cancelButtonColor: 'secondary'
                , confirmButtonText: `{{\App\CPU\translate("Yes, Confirm")}}!`
                ,preConfirm: () => {
                const delivery_order = Swal.getPopup().querySelector('#delivery_order').value
                const cost_center = Swal.getPopup().querySelector('#cost_center').value
                const delivery_date = Swal.getPopup().querySelector('#delivery_date').value
                if (!delivery_order || !cost_center || !delivery_date) {
                     Swal.showValidationMessage(`Please enter missing data`)
                }
                return { delivery_order: delivery_order, cost_center: cost_center, delivery_date: delivery_date}
            }

            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status,
                            "delivery_order":result.value.delivery_order,
                            "cost_center":result.value.cost_center,
                            "delivery_date":result.value.delivery_date,
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{\App\CPU\translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                toastr.success('{{\App\CPU\translate('Status Change successfully')}}!');
                                location.reload();
                            }

                        }
                    });
                }
            })
        }else{
            if (status == 'failed') {
            Swal.fire({
                title: `{{\App\CPU\translate("Are you sure Change this")}}<br>{{\App\CPU\translate("You will not be able to revert this")}}`
                , html: `<input type="text" id="note" class="swal2-input" placeholder="{{\App\CPU\translate("note")}}">`
                , showCancelButton: true
                , cancelButtonText: `{{\App\CPU\translate("Cancel")}}`
                , confirmButtonColor: '#377dff'
                , cancelButtonColor: 'secondary'
                , confirmButtonText: `{{\App\CPU\translate("Yes, Change it")}}!`
                ,preConfirm: () => {
                const note = Swal.getPopup().querySelector('#note').value
                if (!note) {
                     Swal.showValidationMessage(`Please enter note`)
                }
                return { note: note}
            }

            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status,
                            "note_notify":result.value.note
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{\App\CPU\translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                toastr.success('{{\App\CPU\translate('Status Change successfully')}}!');
                                location.reload();
                            }

                        }
                    });
                }
            })
        }
        else{
            Swal.fire({
                title: '{{\App\CPU\translate('Are you sure Change this')}}?',
                text: "{{\App\CPU\translate('You will not be able to revert this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{\App\CPU\translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{\App\CPU\translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                toastr.success('{{\App\CPU\translate('Status Change successfully')}}!');
                                location.reload();
                            }

                        }
                    });
                }
            })
        }
        }



        }
</script>


<script>
    $( document ).ready(function() {
        let delivery_type = '{{$order->delivery_type}}';


        if(delivery_type === 'self_delivery'){
            $('#choose_delivery_man').show();
            $('#by_third_party_delivery_service_info').hide();
        }else if(delivery_type === 'third_party_delivery')
        {
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').show();
        }else{
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').hide();
        }
    });
</script>


<script>
    function choose_delivery_type(val)
    {

        if(val==='self_delivery')
        {
            $('#choose_delivery_man').show();
            $('#by_third_party_delivery_service_info').hide();
        }else if(val==='third_party_delivery'){
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').show();
            $('#shipping_chose').modal("show");
        }else{
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').hide();
        }

    }
</script>


<script>
    function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/add-delivery-man/{{$order['id']}}/' + id,
                data: {
                    'order_id': '{{$order['id']}}',
                    'delivery_man_id': id
                },
                success: function (data) {
                    if (data.status == true) {
                        toastr.success('Delivery man successfully assigned/changed', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        toastr.error('Deliveryman man can not assign/change in that status', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function () {
                    toastr.error('Add valid data', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        function waiting_for_location() {
            toastr.warning('{{\App\CPU\translate('waiting_for_location')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
</script>

<script>
    $(document).ready(function () {

    $.ajaxSetup({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
    });

    $('body').on('click', '#editCompany', function (event) {
        event.preventDefault();
        var id = $(this).data('id');
        var product_id = $(this).data('product_id');
        console.log(product_id);
        $.ajax({
          url: 'bagsProducts/'+product_id,
          type: "POST",
          data: {
            product_id: product_id,
          },
          success: function (data) {
            $("#sublawmasterdata tbody").empty();
            $("#exampleid").append(data.data);
          }
      });

    });

   });
</script>

@endpush
