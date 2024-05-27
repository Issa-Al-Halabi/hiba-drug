@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Bag Points Edit'))
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {

        background-color: #377dff !important;

    }


    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {

        color: #ffffff !important;

    }

    .select2-selection {
        /* .selection{ */

        height: 41.89px !important;
        border: 0.0625rem solid #e7eaf3 !important;

    }
</style>
@push('css_or_js')
    {{-- <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/> --}}
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('store')}}</li>
        </ol>
    </nav>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ \App\CPU\translate('Add')}} {{ \App\CPU\translate('new')}} {{ \App\CPU\translate('Product Points')}}
                </div>
                <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <form action="{{route('admin.points.bag_points_update',['id'=>$productpoint->id])}}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <label for="store_name">{{ \App\CPU\translate('Bags')}}</label>
                                <select required class="js-example-basic-multiple" name="products[]" multiple="multiple">
                                    @foreach($old_products as $product)
                                    <option selected value="{{$product->id}}">{{$product->bag_name}}</option>
                                    @endforeach
                                    @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->bag_name}}</option>
                                    @endforeach
                                  </select>
                            </div>
                            <div class="col-md-6">
                                {{-- @foreach(json_decode($language) as $lang)
                                    <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                            id="{{$lang}}-form">
                                        <label for="quantity">{{ \App\CPU\translate('Quantity')}}</label>
                                        <input type="number" name="quantity" class="form-control" id="quantity" value="{{$productpoint->quantity}}" placeholder="{{\App\CPU\translate('Ex')}} : {{\App\CPU\translate('2')}}" {{$lang == $default_lang? 'required':''}}>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach --}}
                                <div class="form-group">
                                    <label for="store_name">{{ \App\CPU\translate('Points')}}</label>
                                    <div class="custom-file" style="text-align: left" required>
                                        <input type="number" name="points" class="form-control" id="points" value="{{$productpoint->points}}">

                                    </div>
                                </div>


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
    <script>
        $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
    </script>
@endpush
