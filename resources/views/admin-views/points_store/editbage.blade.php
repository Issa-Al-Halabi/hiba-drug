@extends('layouts.back-end.app')

@section('title',\App\CPU\translate('Update Points'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CPU\translate('update')}} {{\App\CPU\translate('Points')}}</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.reward-item.updateBag',[$rewardItemall['id']])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input style="visibility: hidden;" type="text" value="{{$rewardItemall['id']}}" name="id" class="form-control" placeholder="New sales-man">
                            <input style="visibility: hidden;" type="text" value="{{$rewardItemall['bag_id']}}" name="bag_id" class="form-control" placeholder="New sales-man">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('name')}}</label>
                                    {{-- <input type="text" value="{{$rewardItemall['name']}}" name="name" class="form-control" placeholder="name"> --}}
                                    <div class="form-control" readonly>{{ $rewardItemall['name'] }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('Points')}}</label>
                                    <input type="text" value="{{$rewardItemall['cost']}}" name="cost" class="form-control" placeholder="Points" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
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
