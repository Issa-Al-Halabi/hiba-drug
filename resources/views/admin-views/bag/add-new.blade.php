@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Bag Add'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush
<style>
    .style{
        padding:10px
    }
    </style>
@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('bag')}}</li>
        </ol>
    </nav>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ \App\CPU\translate('Add')}} {{ \App\CPU\translate('new')}} {{ \App\CPU\translate('bag')}}
                </div>
                <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <form action="{{route('admin.bag.store')}}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">

                                    <div class="style">
                                        <label for="bag_name">{{ \App\CPU\translate('Name')}}</label>
                                        <input type="text" name="bag_name" class="form-control" id="bag_name" required>
                                    </div>
                              
   									 <!-- 
                                    <div class="style">
                                        <label for="bag_description">{{ \App\CPU\translate('Description')}}</label>
                                        <input type="text" name="bag_description" class="form-control" id="bag_description" required >
                                    </div>
									 -->
                                    <div class="style">
                                        <label for="demand_limit">{{ \App\CPU\translate('Demand limit')}}</label>
                                        <input type="number" name="demand_limit" class="form-control" id="demand_limit" required>
                                    </div>

                                    
                                    <div class="style">
                                        <label for="end_date">{{ \App\CPU\translate('Expiry Date')}}</label>
                                        <input type="date" name="end_date" class="form-control" id="end_date" required>
                                    </div>



                                <div  class="form-group style">
                                    <label for="bag_name">{{ \App\CPU\translate('bag_image')}}</label><span class="badge badge-soft-danger">( {{\App\CPU\translate('ratio')}} 1:1 )</span>
                                    <div class="custom-file" style="text-align: left" required>
                                        <input type="file" name="bag_image" id="customFileUpload" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileUpload">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <center>
                                    <img style="border-radius: 10px; max-height:200px;" id="viewer"
                                        src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}" alt="banner image"/>
                                </center>
                            </div>


                        </div>


                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>

        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
    <script src="{{asset('public/assets/back-end')}}/js/select2.min.js"></script>
    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function () {
            readURL(this);
        });


        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{\App\CPU\translate('are_you_sure?')}}',
                text: "{{\App\CPU\translate('You_will_not_be_able_to_revert_this!')}}",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{\App\CPU\translate('Yes')}} {{\App\CPU\translate('delete_it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.brand.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{\App\CPU\translate('Brand_deleted_successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
