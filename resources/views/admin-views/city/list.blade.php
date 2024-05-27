@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('City List'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">{{\App\CPU\translate('City_list')}} <span style="color: rgb(252, 59, 10);">({{ $br->total() }})</span></h1>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">


                    <form class="row w-100 align-items-center" action="{{route('admin.city.store')}}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-4 d-flex align-items-center w-100">
                            <input type="text" placeholder="Name" name="city_name" class="w-100 js-example-basic-multiple form-control" style="width: 100%;" required>
                        </div>

                        <div class="col-md-4 w-100">
                            <button type="submit" class="btn btn-primary w-100" style="border: none;appearance: none;border-radius: 5px;height:40px">
                                {{\App\CPU\translate('Add_city')}}
                            </button>
                        </div>

                    </form>


                    <!-- Search -->
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ \App\CPU\translate('Search')}} {{ \App\CPU\translate('City')}}" aria-label="Search orders" value="{{ $search }}" required>
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Search')}}</button>
                        </div>
                    </form>
                    <!-- End Search -->


                </div>
                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px">
                                        {{ \App\CPU\translate('City')}} {{ \App\CPU\translate('ID')}}
                                    </th>
                                    <th scope="col">{{ \App\CPU\translate('Name')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Status')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Group')}}</th>
                                    <th scope="col" style="width: 100px" class="text-center">
                                        {{ \App\CPU\translate('Action')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($br as $k=>$b)
                                <tr>
                                    <td class="text-center">{{$br->firstItem()+$k}}</td>
                                    <td>{{$b['city_name']}}</td>
                                    <td>
                                        <label class="switch switch-status">
                                            <input type="checkbox" class="status" id="{{$b['id']}}" {{$b['city_status'] == 1?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>

                                    <td>
                                        <a class="btn btn-success btn-sm" href="{{route('admin.city.group-list',[$b['id']])}}">
                                            <i class="fa fa-eye"></i> {{ \App\CPU\translate('view')}}
                                        </a>
                                    </td>

                                    <td>
                                        <button href="" id="editCompany" data-toggle="modal" data-target='#practice_modal' class="btn btn-primary btn-sm " data-city_id="{{$b['id']}}"><i class="tio-edit"></i></button>

                                        <a class="btn btn-danger btn-sm delete" id="{{$b['id']}}">
                                            <i class="tio-add-to-trash"></i> {{ \App\CPU\translate('Delete')}}
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
                    <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="practice_modal">
    <div class="modal-dialog">
        <form class="row w-100 align-items-center" action="{{route('admin.city.update-city')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="min-height: 150px; padding: 20px">
                <input type="hidden" id="city_id" name="city_id" value="">
                <div class="modal-body mb-4" style="padding: 0px;">
                    <div>
                        <p> {{\App\CPU\translate('name')}}</p>
                    </div>
                    <input type="text" name="city_name" id="city_name" value="" class="form-control">
                </div>
                <input type="submit" value="Submit" id="submit" class="btn btn-sm btn-primary py-0" style="font-size: 1.2em; height: 30px; width: 100%;">
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).on('change', '.status', function() {
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
            url: "{{route('admin.city.status-update')}}"
            , method: 'POST'
            , data: {
                id: id
                , status: status
            }
            , success: function(data) {
                if (data.success == true) {
                    toastr.success(`{{\App\CPU\translate("Status updated successfully")}}`);
                } else if (data.success == false) {
                    toastr.error(`{{\App\CPU\translate("Status updated failed.Product must be approved")}}`);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            }
        });
    });

</script>

<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '#editCompany', function(event) {
            event.preventDefault();
            var city_id = $(this).data('city_id');
            $.ajax({
                url: 'edit/' + city_id
                , type: "GET"
                , dataType: 'json'
                , success: function(data) {
                    $('#userCrudModal').html("Edit City Name");
                    $('#submit').val("Edit City Name");
                    $('#city_id').val(data.data.id);
                    $('#city_name').val(data.data.city_name);
                }
            });

        });

    });

</script>

<script>

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: `{{ \App\CPU\translate("Are_you_sure_delete_this_city")}}?`
                , text: `{{ \App\CPU\translate("All_users_belonging_to_this_city_will_be_removed")}}!!`
                , showCancelButton: true
                , confirmButtonColor: '#3085d6'
                , cancelButtonColor: '#d33'
                , confirmButtonText: `{{ \App\CPU\translate("Yes")}}, {{ \App\CPU\translate("delete_it")}}!`
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.city.delete')}}"
                        , method: 'POST'
                        , data: {
                            id: id
                        }
                        , success: function(data) {
                            console.log(data.groups);
                            toastr.success(`{{ \App\CPU\translate("city_deleted_successfully")}}`);
                            location.reload();
                        }
                    });
                }
            })
        });


</script>
@endpush
