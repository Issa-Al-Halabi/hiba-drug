@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Plan Add'))
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
    .form-control11{

        width: calc(1.6em) !important;
        height: calc(1.6em) !important;
        margin-left: 30px;
        margin-right: 30px;
    }
</style>

@push('css_or_js')
<link href="{{asset('public/assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
<link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a>
            </li>
            <li class="breadcrumb-item" aria-current="page"><a
                    href="{{route('admin.sales-man.work-plans')}}">{{\App\CPU\translate('Plans')}}</a>
            </li>
            <li class="breadcrumb-item">{{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} </li>
        </ol>
    </nav>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <form class="plan-form" action="{{route('admin.sales-man.work-plan-store')}}" method="POST"
                style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left' }};"
                enctype="multipart/form-data" id="plan_form">
                @csrf


                <div class="card mt-2 rest-part">
                    <div class="card-header">
                        <h4>{{\App\CPU\translate('General Info')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row" style="text-align: {{Session::get('direction') == "rtl" ? 'right' : 'left' }};">
                                <div class="col-md-6 col-sm-12">

                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1" >{{
                                            \App\CPU\translate('Begin_date') }}</label>
                                        <input type="date" name="begin_date" class="form-control"
                                            placeholder="Begin Date" required>
                                    </div>

                                </div>

                                {{-- <div class="col-md-6 col-sm-12">

                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{
                                            \App\CPU\translate('End_date') }}</label>
                                        <input type="date" name="end_date" class="form-control" placeholder="End Date"
                                            required>
                                    </div>

                                </div> --}}

                                <div class="col-md-6 col-sm-12">

                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{
                                            \App\CPU\translate('Note') }}</label>
                                        <input type="text" name="note" class="form-control" placeholder="Note" required>
                                    </div>

                                </div>

                                <div class="col-md-6 col-sm-12">

                                    <label for="name">{{\App\CPU\translate('Sales Man')}}</label>
                                    <select
                                        class="js-example-basic-multiple js-states js-example-responsive form-control"
                                        name="saler_id" required style="width: 100%;">
                                        <option value="{{ null }}" selected disabled>
                                            ---{{ \App\CPU\translate('Select') }}---</option>
                                        @foreach ($salesman as $s)
                                        <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-12 col-sm-12">
                                    <label for="name">{{\App\CPU\translate('Pharamcies')}}</label>
                                    <select class="js-example-basic-single" multiple="multiple" name="pharamcies_ids[]">
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-6 d-flex mt-4" style="flex-direction: {{Session::get('direction') == "rtl" ? 'row-reverse' : 'row' }};
                                justify-content: {{Session::get('direction') == "rtl" ? 'right' : 'left' }};">
                                    <label for="name">{{\App\CPU\translate('Check_All')}}</label>
                                    <input class="form-control11" type="checkbox" name="check_all">
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="card card-footer">
                    <div class="row">
                        <div class="col-md-12" style="padding-top: 20px">
                            <button type="submit" onclick="check()"
                                class="btn btn-primary">{{\App\CPU\translate('Submit')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{asset('public/assets/back-end')}}/js/tags-input.min.js"></script>
<script src="{{asset('public/assets/back-end/js/spartan-multi-image-picker.js')}}"></script>


<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });

    $("document").ready(function() {
        $('select[name="saler_id"]').on('change', function() {
            var saler_id = $(this).val();
            if (saler_id) {
                $.ajax({
                    url: '/admin/sales-man/work-plan/get/pharmacies/' + saler_id
                    , type: "GET"
                    , dataType: "json"
                    , success: function(data) {
                        $.each(data.pharmacies, function(index, pharamcy) {
                            $('select[name="pharamcies_ids[]"]').append('<option value="' + pharamcy.id + '">' + pharamcy.name + '</option>');
                        })
                    }
                })
            } else {
                $('select[name="pharamcies_ids"]').empty();
            }
        });
    });

</script>


@endpush
