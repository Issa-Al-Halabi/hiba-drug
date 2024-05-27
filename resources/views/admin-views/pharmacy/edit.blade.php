@extends('layouts.back-end.app')

@section('title',\App\CPU\translate('Update Pharmacy'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CPU\translate('update')}}
                    {{\App\CPU\translate('Pharmacy')}}</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">

                    <form action="{{route('admin.customer.update',[$user['id']])}}" method="post" enctype="multipart/form-data"  style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        @csrf

                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('General_info') }}</h4>
                            </div>
                            <div class="card-body row">

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{\App\CPU\translate('first_name')}}</label>
                                        <input type="text" value="{{$user['f_name']}}" name="f_name" class="form-control" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{\App\CPU\translate('last_name')}}</label>
                                        <input type="text" value="{{$user['l_name']}}" name="l_name" class="form-control" placeholder="Last Name" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('pharmacy name')}}</label>
                                        <input type="text" value="{{$user->pharmacy['name']}}" name="name" class="form-control" placeholder="Pharmacy name" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('email')}}</label>
                                        <input type="email" value="{{$user['email']}}" name="email" class="form-control" placeholder="Email" required>
                                    </div>
                                </div>



                            </div>
                        </div>

                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('Contact_info') }}</h4>
                            </div>
                            <div class="card-body row">

                                <div class="form-group col-md-6 col-12">
                                    <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('phone')}}</label>
                                    <input type="number" value="{{$user['phone']}}" name="phone" class="form-control" placeholder="Phone" required>
                                </div>

                                <div class="form-group col-md-6 col-12">
                                    <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('land_number')}}</label>
                                    <input type="number" value="{{$user->pharmacy['land_number']}}" name="land_number" class="form-control" placeholder="Land_number" required>
                                </div>

                            </div>
                        </div>


                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('Address_info') }}</h4>
                            </div>
                            <div class="card-body row">

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('street_address')}}</label>
                                        <input type="text" value="{{$user['street_address']}}" name="street_address" class="form-control" placeholder="Street address" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('address')}}</label>
                                        <input type="text" value="{{$user->pharmacy['Address']}}" name="Address" class="form-control" placeholder="Address" required>
                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('Pharmacy_position') }}</h4>
                            </div>
                            <div class="card-body row">

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('latitude')}}</label>
                                        <input type="text" value="{{$user->pharmacy['lat']}}" name="lat" class="form-control" placeholder="Latitude" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('longitude')}}</label>
                                        <input type="text" value="{{$user->pharmacy['lan']}}" name="lan" class="form-control" placeholder="Longitude" required>
                                    </div>
                                </div>

                            </div>
                        </div>



                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('The_first_work_period') }}</h4>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('start_time')}}</label>
                                            <input type="time" value="{{$user->pharmacy['from']}}" name="from" class="form-control" placeholder="9:00AM" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('end_time')}}</label>
                                            <input type="time" value="{{$user->pharmacy['to']}}" name="to" class="form-control" placeholder="" required>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('The_second_work_period') }}</h4>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('start_time')}}</label>
                                            <input type="time" value="{{$user->pharmacy['from_pm']}}" name="from_pm" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('end_time')}}</label>
                                            <input type="time" value="{{$user->pharmacy['to_pm']}}" name="to_pm" class="form-control" placeholder="" required>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>



                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('Regions') }}</h4>
                            </div>
                            <div class="card-body row">

                                <div class="col-md-6 col-12">

                                    <label for="">{{ \App\CPU\translate('Choose_City') }}</label>
                                    <select name="city_id" class="form-control @error('city') is-invalid @enderror" required>
                                        <option value="0" selected disabled>select</option>
                                        @foreach(App\Model\City::all() as $key=> $city)
                                        <option value="{{$city->id}}" {{ $city->id==$cus_city->id ? 'selected' : ''}}>{{$city->city_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                </div>

                                <div class="col-md-6 col-12">
                                    <label for="">{{ \App\CPU\translate('Choose_group') }}</label>
                                    <select name="group_id" class="form-control @error('group') is-invalid @enderror" required>
                                        <option value="0" selected disabled>select</option>
                                        <option value="{{$cus_group->id}}" {{ $cus_group->id==$cus_group->id ?
                                            'selected' : ''}}>{{$cus_group->group_name}}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="">{{ \App\CPU\translate('Choose_area') }}</label>
                                        <select name="area_id" class="form-control @error('area') is-invalid @enderror" required>
                                            {{-- <option value="">select</option> --}}
                                            <option value="{{$cus_area->id}}">{{$cus_area->area_name}}</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="card mt-2 rest-part">
                            <div class="card-header">
                                <h4>{{ \App\CPU\translate('Account_info') }}</h4>
                            </div>
                            <div class="card-body row">

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('Account Number')}}<span style="color: red;">*</span></label>
                                        <input type="number" value="{{$user['pharmacy_id']}}" name="num_id" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{\App\CPU\translate('New Password')}}</label>
                                        <input type="password" placeholder="HJW22DzEz" name="password" class="form-control">
                                    </div>
                                </div>

                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('update')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')

<script type="text/javascript">
    $("document").ready(function() {
        $('select[name="city_id"]').on('change', function() {
            var cityId = $(this).val();
            if (cityId) {
                $.ajax({
                    url: '/admin/customer/groups/' + cityId
                    , type: "GET"
                    , dataType: "json"
                    , success: function(data) {
                        $('select[name="group_id"]').empty();
                        $('select[name="area_id"]').empty();
                        $('select[name="group_id"]').append('<option value="">Select Group</option>');
                        $.each(data.groups, function(index, group) {
                            $('select[name="group_id"]').append('<option value="' + group.id + '">' + group.group_name + '</option>');
                        })
                    }

                })
            } else {
                $('select[name="group_id"]').empty();
                $('select[name="area_id"]').empty();
            }
        });



    });

    $("document").ready(function() {
        $('select[name="group_id"]').on('change', function() {

            var groupId = $(this).val();
            console.log(groupId);
            if (groupId) {
                $.ajax({
                    url: '/admin/customer/areas/' + groupId
                    , type: "GET"
                    , dataType: "json"
                    , success: function(data) {
                        $('select[name="area_id"]').empty();
                        $('select[name="area_id"]').append('<option value="">Select Area</option>');
                        $.each(data.areas, function(index, area) {
                            $('select[name="area_id"]').append('<option value="' + area.id + '">' + area.area_name + '</option>');
                        })
                    }
                })
            } else {
                $('select[name="area_id"]').empty();
            }
        });
    });

</script>





<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#viewer').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function() {
        readURL(this);
    });

</script>

<script src="{{asset('public/assets/back-end/js/spartan-multi-image-picker.js')}}"></script>
<script type="text/javascript">
    $(function() {
        $("#coba").spartanMultiImagePicker({
            fieldName: 'identity_image[]'
            , maxCount: 5
            , rowHeight: '120px'
            , groupClassName: 'col-2'
            , maxFileSize: ''
            , placeholderImage: {
                image: '{{asset('
                public / assets / back - end / img / 400 x400 / img2.jpg ')}}'
                , width: '100%'
            }
            , dropFileLabel: "Drop Here"
            , onAddRow: function(index, file) {

            }
            , onRenderedPreview: function(index) {

            }
            , onRemoveRow: function(index) {

            }
            , onExtensionErr: function(index, file) {
                toastr.error('Please only input png or jpg type file', {
                    CloseButton: true
                    , ProgressBar: true
                });
            }
            , onSizeErr: function(index, file) {
                toastr.error('File size too big', {
                    CloseButton: true
                    , ProgressBar: true
                });
            }
        });
    });

</script>
@endpush
