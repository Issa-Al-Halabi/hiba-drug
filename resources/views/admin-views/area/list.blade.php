@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('City Areas List'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('public/assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
<link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">{{\App\CPU\translate('group_areas_list')}} </h1>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">

                    {{-- <div > --}}
                    <form class="row w-100 align-items-center" action="{{route('admin.city.area-store',[$group_id])}}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-4 d-flex align-items-center w-100">
                            <input type="text" placeholder="Area name" name="area_name" class="w-100 js-example-basic-multiple form-control" style="width: 100%;" required>
                        </div>

                        <div class="col-md-4 d-flex align-items-center w-100">
                            <input type="number" placeholder="Number" name="area_num" class="w-100 js-example-basic-multiple form-control" style="width: 100%;">
                        </div>

                        <div class="col-md-4 w-100">
                            <button type="submit" class="btn btn-primary w-100" style="border: none;appearance: none;border-radius: 5px;height:40px">
                                {{\App\CPU\translate('Add_Area')}}
                            </button>
                        </div>

                    </form>

                    {{-- </div> --}}
                </div>


                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left' }};" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px">
                                        {{ \App\CPU\translate('area')}} {{ \App\CPU\translate('ID')}}
                                    </th>
                                    <th scope="col">{{ \App\CPU\translate('Name')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Num')}}</th>
                                    <th>
                                        {{ \App\CPU\translate('Action')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                $i=1;
                                @endphp

                                @foreach($group_areas as $b)
                                <tr>
                                    <td class="text-center">{{$i}}</td>
                                    <td>
                                        {{$b['area_name']}}
                                    </td>
                                    <td>{{$b['area_num']}}</td>
                                    <td>
                                        <button href="" id="editCompany" data-toggle="modal" data-target='#practice_modal' class="btn btn-primary btn-sm " data-area_id="{{$b['area_id']}}"><i class="tio-edit"></i></button>

                                        <a class="btn btn-danger btn-sm delete" id="{{$b['area_id']}}">
                                            <i class="tio-add-to-trash"></i> {{ \App\CPU\translate('Delete')}}
                                        </a>
                                    </td>


                                </tr>
                                @php
                                $i++;
                                @endphp
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="practice_modal">
    <div class="modal-dialog">
        <form class="row w-100 align-items-center" action="{{route('admin.city.update-area')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="min-height: 150px; padding: 20px">
                <input type="hidden" id="area_id" name="area_id" value="">
                <div class="modal-body mb-4" style="padding: 0px;">
                    <div>
                        <p> {{\App\CPU\translate('name')}}</p>
                    </div>
                    <input type="text" name="area_name" id="area_name" value="" class="form-control">
                </div>
                <input type="submit" value="Submit" id="submit" class="btn btn-sm btn-primary py-0" style="font-size: 1.2em; height: 30px; width: 100%;">
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: `{{ \App\CPU\translate("Are_you_sure_delete_this_area")}}?`
                , text: `{{ \App\CPU\translate("All_users_belonging_to_this_area_will_be_removed")}}!!`
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
                        url: "{{route('admin.city.area-delete')}}"
                        , method: 'POST'
                        , data: {
                            id: id
                        }
                        , success: function() {
                            toastr.success(`{{ \App\CPU\translate("Area_deleted_successfully")}}`);
                            location.reload();
                        }
                    });
                }
            })
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
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
            var area_id = $(this).data('area_id');
            $.ajax({
                url: 'edit/' + area_id
                , type: "GET"
                , dataType: 'json'
                , success: function(data) {
                    $('#userCrudModal').html("Edit Area Name");
                    $('#submit').val("Edit Area Name");
                    $('#area_id').val(data.data.id);
                    $('#area_name').val(data.data.area_name);
                }
            });

        });

    });

</script>


@endpush
