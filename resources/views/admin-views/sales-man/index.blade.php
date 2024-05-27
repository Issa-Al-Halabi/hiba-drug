@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Add new sales-man'))

@push('css_or_js')
@endpush
@php
$chars=['A','B','C','D','E','F','G','H'];
@endphp
@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="align-items-center">
                        <div class="col-sm mb-2 mb-sm-0">
                            <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i>
                                {{ \App\CPU\translate('add') }} {{ \App\CPU\translate('new') }}
                                {{ \App\CPU\translate('salesman') }}</h1>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->
                <div class="card-body">
                    <form action="{{ route('admin.sales-man.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{
                                        \App\CPU\translate('first') }}
                                        {{ \App\CPU\translate('name') }}</label>
                                    <input type="text" name="f_name" class="form-control" placeholder="{{ \App\CPU\translate('first') }} {{ \App\CPU\translate('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{
                                        \App\CPU\translate('last') }}
                                        {{ \App\CPU\translate('name') }}</label>
                                    <input type="text" name="l_name" class="form-control" placeholder="{{ \App\CPU\translate('last') }} {{ \App\CPU\translate('name') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{
                                        \App\CPU\translate('email') }}</label>
                                    <input type="email" name="email" class="form-control" placeholder="Ex : ex@example.com" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{
                                        \App\CPU\translate('phone') }}</label>
                                    <input type="text" name="phone" class="form-control" placeholder="Ex : 017********" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">

                                <div class="form-group">
                                    <label for="">{{ \App\CPU\translate('Choose_City') }}</label>
                                    <select name="city_id" class="form-control js-example-responsive @error('city') is-invalid @enderror">
                                        <option value="">{{ \App\CPU\translate('select')}}</option>
                                        @foreach (App\Model\City::all() as $key => $city)
                                        <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                </div>
                                <div class="form-group">
                                    <label for="">{{ \App\CPU\translate('Choose_area') }}</label>
                                    <select name="area_id" class="form-control js-example-responsive @error('area') is-invalid @enderror">
                                        <option value="">{{ \App\CPU\translate('select')}}</option>
                                    </select>
                                </div>


                            </div>


                            <div class="col-md-6 col-12">

                                <div class="form-group">
                                    <label for="">{{ \App\CPU\translate('Choose_group')}}</label>
                                    <select name="group_id" class="form-control js-example-responsive @error('group') is-invalid @enderror" required>
                                        <option value="0" selected disabled>{{ \App\CPU\translate('select')}}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="">{{ \App\CPU\translate('Choose_Team')}}</label>
                                    <select name="team_char" class="form-control js-example-responsive  @error('group') is-invalid @enderror" required>
                                        @for ($i=0;$i<count($chars);$i++) <option value="{{$chars[$i]}}">Team&nbsp;{{$chars[$i]}}</option>
                                            @endfor
                                    </select>
                                </div>

                            </div>

                            <div class="col-md-6 col-12">

                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{
                                        \App\CPU\translate('password') }}</label>
                                    <input type="text" name="password" class="form-control" placeholder="Ex : password" required>
                                </div>

                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{
                                            \App\CPU\translate('Account_number') }}</label>
                                    <input type="number" name="account_number" class="form-control" placeholder="Ex : 46578275" required>
                                </div>

                            </div>


                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')

<script type="text/javascript">
    $(".js-example-theme-single").select2({
        theme: "classic"
    });

    $(".js-example-responsive").select2({
        width: 'resolve'
    });


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
                        $('select[name="group_id"]').append('<option value="">{{ \App\CPU\translate('select ')}}</option>');
                        $.each(data.groups, function(index, group) {
                            $('select[name="group_id"]').append('<option value="' +
                                group.id + '">' + group.group_name + '</option>'
                            );
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
                        $('select[name="area_id"]').append('<option value="">{{ \App\CPU\translate('select ')}}</option>');
                        $.each(data.areas, function(index, area) {
                            $('select[name="area_id"]').append('<option value="' +
                                area.id + '">' + area.area_name + '</option>');
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

<script src="{{ asset('public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $("#coba").spartanMultiImagePicker({
            fieldName: 'identity_image[]'
            , maxCount: 5
            , rowHeight: '120px'
            , groupClassName: 'col-2'
            , maxFileSize: ''
            , placeholderImage: {image: '{{ asset('public / assets / back - end / img / 400 x400 / img2.jpg ') }}', width: '100%'}
            , dropFileLabel: "Drop Here"
            , onAddRow: function(index, file) {

            }
            , onRenderedPreview: function(index) {

            }
            , onRemoveRow: function(index) {

            }
            , onExtensionErr: function(index, file) {
                toastr.error('Please only input png or jpg type file', {CloseButton: true, ProgressBar: true});
            }
            , onSizeErr: function(index, file) {
                toastr.error('File size too big', {CloseButton: true, ProgressBar: true
                });
            }
        });
    });

</script>
@endpush
