@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('bags List'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">{{\App\CPU\translate('bag_list')}} <span style="color: rgb(252, 59, 10);">({{
                $br->total() }})</span></h1>
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
                                placeholder="{{ \App\CPU\translate('Search')}} {{ \App\CPU\translate('Bags')}}"
                                aria-label="Search orders" value="{{ $search }}" required>
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Search')}}</button>
                        </div>
                    </form>
                    <!-- End Search -->


                </div>
                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left' }};"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px">
                                        {{ \App\CPU\translate('bag')}} {{ \App\CPU\translate('ID')}}
                                    </th>
                                    <th scope="col">{{ \App\CPU\translate('Name')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Details')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Total_price')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Limit')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Status')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Expiry_Date')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Image')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Products')}}</th>
                                    <th scope="col" style="width: 100px" class="text-center">
                                        {{ \App\CPU\translate('Action')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($br as $k=>$b)
                                <tr>
                                    <td class="text-center">{{$br->firstItem()+$k}}</td>
                                    <td>{{$b['bag_name']}}</td>
                                    <td>
                                        {{\Illuminate\Support\Str::limit($b['bag_description'],20)}}
                                    </td>
                                    <td>{{$b['total_price_offer']}}</td>
                                    <td>{{$b['demand_limit']}}</td>

                                    <td>
                                        <label class="switch switch-status">
                                            <input type="checkbox" class="status" id="{{$b['id']}}"
                                                {{$b['bag_status']==1?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>

                                    <td>{{$b['end_date']}}</td>

                                    <td>
                                        <img style="width: 60px;height: 60px"
                                            onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                            src="{{asset('storage/app/public/bag')}}/{{$b['bag_image']}}">
                                    </td>


                                    <td>
                                        <a class="btn btn-success btn-sm"
                                            href="{{route('admin.bag.products-list',[$b['id']])}}">
                                            <i class="fa fa-eye"></i> {{ \App\CPU\translate('view')}}
                                        </a>
                                    </td>

                                    <td>
                                        <a class="btn btn-primary btn-sm" href="{{route('admin.bag.edit',[$b['id']])}}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm delete" id="{{$b['id']}}">
                                            <i class="tio-add-to-trash"></i>
                                        </a>
                                        <a class="btn btn-secondary btn-sm " href="{{route('admin.bag.settings',[$b['id']])}}">
                                            <i class="tio-settings"></i>
                                        </a>
                                    </td>


                                </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="card-footer">
                    {{$br->links()}}
                </div>
                @if(count($br)==0)
                <div class="text-center p-4">
                    <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg"
                        alt="Image Description" style="width: 7rem;">
                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_bag')}}?',
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
                        url: "{{route('admin.bag.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{ \App\CPU\translate('bag_deleted_successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });


        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.bag.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if(data.success == true) {
                        toastr.success('{{\App\CPU\translate('Status updated successfully')}}');
                    }
                    else if(data.success == false) {
                        toastr.error('{{\App\CPU\translate('Status updated failed. Product must be approved')}}');
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });



</script>
@endpush
