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

            <form class="plan-form" action="{{route('admin.bag.settings_store',[$b])}}" method="POST"
                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left' }};"
                enctype="multipart/form-data" id="plan_form">
                @csrf


                <div class="card mt-2 rest-part">
                    <div class="card-header">
                        <h4>{{\App\CPU\translate('General Info')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-12 col-sm-12 justify-content-center mb-4">

                                    {{-- <label for="name">{{\App\CPU\translate('Setting')}}</label> --}}
                                    <div class="input-group input-group-md-down-break">
                                        <!-- Custom Radio -->
                                        <div class="form-control">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" value="0" name="all"
                                                    id="all_on" {{(isset($bag->all) && $bag->all==1)?'checked':''}}>
                                                <label class="custom-control-label"
                                                    for="all_on">{{\App\CPU\translate('All')}}</label>
                                            </div>
                                        </div>
                                        <!-- End Custom Radio -->

                                        <!-- Custom Radio -->
                                        <div class="form-control">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" value="1" name="all"
                                                    id="all_off" {{(isset($bag->vip) && $bag->vip==1)?'checked':''}}>
                                                <label class="custom-control-label"
                                                    for="all_off">{{\App\CPU\translate('VIP')}}</label>
                                            </div>
                                        </div>
                                        <!-- End Custom Radio -->

                                        <div class="form-control">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" value="2" name="all"
                                                    id="all_onoff" {{(isset($bag->non_vip) && $bag->non_vip==1)?'checked':''}}>
                                                <label class="custom-control-label"
                                                    for="all_onoff">{{\App\CPU\translate('NON VIP')}}</label>
                                            </div>
                                        </div>

                                        <div class="form-control">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" value="3" name="all"
                                                    id="all_custom" {{(isset($bag->custom) && $bag->custom==1)?'checked':''}}>
                                                <label class="custom-control-label"
                                                    for="all_custom">{{\App\CPU\translate('Custom')}}</label>
                                            </div>
                                        </div>


                                        <div class="form-control">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" value="4" name="all"
                                                    id="custom_pharmacy" {{(isset($bag->custom_pharmacy) && $bag->custom_pharmacy==1)?'checked':''}}>
                                                <label class="custom-control-label"
                                                    for="custom_pharmacy">{{\App\CPU\translate('Custom_Pharmacy')}}</label>
                                            </div>
                                        </div>



                                    </div>

                                </div>

                            </div>
                         {{-- Begin row for custom group --}}
                            <div class="row"  id="dis" style="display: none">
                                <div class="col-md-6 col-sm-12">
                                    <label for="">{{\App\CPU\translate('Choose City')}}</label>
                                    <select name="city_id" class="form-control @error('city') is-invalid @enderror">
                                        <option value="">{{\App\CPU\translate('select')}}</option>
                                        @foreach (App\Model\City::all() as $key => $city)
                                        <option {{ $city->id == $city_id ? 'selected' : '' }} value="{{ $city->id }}">{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group w-100">
                                        <label for="">{{\App\CPU\translate('Choose group')}}</label>
                                        <select multiple="multiple" name="group_ids[]"
                                            class="js-example-basic-single" @error('group') is-invalid @enderror"
                                            >
                                            @foreach($groups as $p)
                                            <option value="{{$p->id }}" {{is_array($array) && in_array($p->id, $array) ? 'selected' : '' }}> {{$p->group_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                         {{-- End row custom group --}}


                       {{-- Begin row for custom pharmacy --}}
                        <div class="row"  id="dis_pharmacy" style="display: none">
                            <div class="col-md-12 col-sm-12">
                                <label for="name">{{\App\CPU\translate('Choose_pharmacies')}}</label>

                                <select id="pharamcies_ids" name="pharamcies_ids[]" class="js-example-basic-single" multiple="multiple" >
                                    @foreach ($pharmacies as $pharmacy)
                                    <option value="{{$pharmacy->id}}" {{is_array($array2) && in_array($pharmacy->id, $array2) ? 'selected' : '' }}>{{$pharmacy->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       {{-- End row custom pharmacy --}}


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
            $('select[name="city_id"]').on('change', function() {
                var cityId = $(this).val();
                if (cityId) {
                    $.ajax({
                        url: '/admin/customer/groups/' + cityId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('select[name="group_ids[]"]').empty();
                            $('select[name="area_id"]').empty();
                            $('select[name="group_ids[]"]').append(
                                '<option value="">Select</option>');
                            $.each(data.groups, function(index, group) {
                                $('select[name="group_ids[]"]').append('<option  value="' +
                                    group.id + '">' + group.group_name + '</option>'
                                    );
                            })
                        }

                    })
                } else {
                    $('select[name="group_ids[]"]').empty();
                    $('select[name="area_id"]').empty();
                }
            });
        });

</script>

<script>

    $("document").ready(function() {

        if(document.getElementById("all_custom").checked==true)
        {
            document.getElementById("dis").style.display = "flex";
        }

        if(document.getElementById("custom_pharmacy").checked==true)
        {
            document.getElementById("dis_pharmacy").style.display = "flex";
        }

    $('#all_custom').on('change', function() {
        document.getElementById("dis").style.display = "flex";
        document.getElementById("dis_pharmacy").style.display = "none";
    });

    $('#custom_pharmacy').on('change', function() {
        document.getElementById("dis_pharmacy").style.display = "flex";
        document.getElementById("dis").style.display = "none";
    });

    $('#all_on,#all_onoff,#all_off').on('change', function() {
        document.getElementById("dis").style.display = "none";
        document.getElementById("dis_pharmacy").style.display = "none";
    });

});
</script>



@endpush
