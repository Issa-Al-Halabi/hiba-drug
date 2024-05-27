@extends('layouts.back-end.app')

@section('title',\App\CPU\translate('Update bag'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CPU\translate('update')}}
                    {{\App\CPU\translate('Bag')}}</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.bag.update',[$b['id']])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{\App\CPU\translate('Name')}}</label>
                                    <input type="text" value="{{$b['bag_name']}}" name="bag_name" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- 
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{\App\CPU\translate('Description')}}</label>
                                    <input type="text" value="{{$b['bag_description']}}" name="bag_description"
                                        class="form-control" required>
                                </div>
                            </div>
							-->

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{\App\CPU\translate('Expiry_date')}}</label>
                                    <input type="date" value="{{$b['end_date']}}" name="end_date" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{\App\CPU\translate('Demand_limit')}}</label>
                                    <input type="number" value="{{$b['demand_limit']}}" name="demand_limit"
                                        class="form-control" required>
                                </div>
                            </div>

                        </div>


                        {{-- <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{\App\CPU\translate('Total_price_offer')}}</label>
                                    <input type="number" value="{{$b['total_price_offer']}}" name="total_price_offer" class="form-control"
                                        required>
                                </div>
                            </div>
                        </div> --}}

                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('update')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')
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

        $("#customFileEg1").change(function () {
            readURL(this);
        });
</script>

<script src="{{asset('public/assets/back-end/js/spartan-multi-image-picker.js')}}"></script>
<script type="text/javascript">
    $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '120px',
                groupClassName: 'col-2',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('Please only input png or jpg type file', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('File size too big', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
</script>
@endpush
