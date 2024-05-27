@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('scheduling List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-black-50">{{\App\CPU\translate('scheduling_list : ')}}{{\App\CPU\translate('Orders (')}}{{\App\CPU\translate('Confirmed |')}}{{\App\CPU\translate('Processing )')}} <span style="color: rgb(252, 59, 10);">({{ $orders->total() }})</span></h1>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">


                        <!-- Search -->
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="{{ \App\CPU\translate('Search')}} {{ \App\CPU\translate('Scheduling')}}" aria-label="Search orders" value="{{ $search }}" required>
                                <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Search')}}</button>
                            </div>
                        </form>
                        <!-- End Search -->


                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px">
                                        {{ \App\CPU\translate('order')}} {{ \App\CPU\translate('ID')}}
                                    </th>
                                    <th scope="col">{{ \App\CPU\translate('customer_name')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('customer_type')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('delivery_date')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('pharmacy_location')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('delivery_Man_name')}}</th>
                                    <th scope="col" style="width: 100px" class="text-center">
                                        {{ \App\CPU\translate('action')}}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($orders as $key=>$order)
                                    <tr>
                                        <td class="text-center"> <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a></td>

                                        <td>
                                            @if($order->customer)
                                                <a class="text-body text-capitalize"
                                                   href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                            @else
                                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                                            @endif
                                        </td>


                                        <td>
                                            @if($order->customer)
                                                <a class="text-body text-capitalize"
                                                   href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order->customer['user_type']}}</a>
                                            @else
                                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                                            @endif
                                        </td>

                                         <td>{{date('d M Y',strtotime($order['delivery_date']))}}</td>

                                         <td>
                                            @if($order->customer)
                                                <a class="text-body text-capitalize"
                                                   href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order->customer['city']}}</a>
                                            @else
                                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_customer_data')}}</label>
                                            @endif
                                        </td>

                                        <td>
                                            @if($order->delivery_man)
                                            <a class="text-body text-capitalize"
                                            href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order->delivery_man['f_name'].' '.$order->delivery_man['l_name']}}</a>
                                            @else
                                                <label class="badge badge-danger">{{\App\CPU\translate('invalid_delivery_man_data')}}</label>
                                            @endif
                                        </td>

                                        @if($order->scheduling==0)
                                        <td>
                                            <a class="btn btn-danger btn-sm"
                                               href="{{route('admin.delivery-trip.scheduling-edit',[$order['id']])}}">
                                                <i  class="fa fa-calendar" aria-hidden="true"></i> {{ \App\CPU\translate('scheduling')}}
                                            </a>
                                        </td>
                                        @else

                                        <td>
                                            <a class="btn btn-success btn-sm"
                                               href="{{route('admin.delivery-trip.scheduling-edit',[$order['id']])}}">
                                                <i  class="tio-edit"></i> {{ \App\CPU\translate('scheduled')}}
                                            </a>
                                        </td>

                                        @endif



                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="card-footer">
                        {{$orders->links()}}
                    </div>
                    @if(count($orders)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
@push('script')
    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_store')}}?',
                text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ \App\CPU\translate('Yes')}}, {{ \App\CPU\translate('delete_it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.store.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{ \App\CPU\translate('store_deleted_successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush --}}
