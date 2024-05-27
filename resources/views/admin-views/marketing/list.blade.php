@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Marketing List'))

@push('css_or_js')
<link href="{{asset('public/assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
<link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
<style>
    .select2-selection{
        display: flex !important;
        justify-content: center  !important;
    }
    .select2-selection .select2-selection__rendered{
        font-size: 15px !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center mb-2">
        <i style="font-size: 30px" class="fa-solid fa-store"></i>
        <h1 class="h3 mb-0 text-black-50" style="margin: 0px 20px">{{\App\CPU\translate('marketing_list')}} </h1>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <form class="row w-100 align-items-center" action="{{route('admin.marketing.store')}}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-4 d-flex align-items-center w-100">

                            <select required class="js-example-basic-multiple" name="product_id" >
                                @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    <b style="text-align: center;font-size:1200px">{{ $product->name }}</b>
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 w-100">
                            <button type="submit" class="btn btn-primary w-100" style="border: none;appearance: none;border-radius: 5px;height:40px">
                                {{\App\CPU\translate('submit')}}
                            </button>
                        </div>

                    </form>
                </div>

                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left' }};" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px">
                                        {{ \App\CPU\translate('ID')}}
                                    </th>

                                    <th scope="col">{{ \App\CPU\translate('Name')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Image')}}</th>
                                    <th>
                                        {{ \App\CPU\translate('Action')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                $i=1;
                                @endphp

                                @foreach($lists as $list)
                                <tr>
                                    <td class="text-center">{{$i}}</td>
                                    <td> <a href="{{route('admin.product.view',[$list['id']])}}">
                                            {{\Illuminate\Support\Str::limit($list['name'],55)}}
                                        </a></td>
                                    <td>
                                        <img style="width: 60px;height: 60px" onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'" src="{{asset("storage/app/public/product/thumbnail")}}/{{$list['thumbnail']}}">
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-sm delete" id="{{$list['item_id']}}">
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
@endsection

@push('script')
<script src="{{ asset('public/assets/back-end') }}/js/select2.min.js"></script>
<script>
    $(".js-example-theme-single").select2({
        theme: "classic"
    });

    $(".js-example-responsive").select2({
        width: 'resolve'
    });

</script>
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

</script>

<script>
    $(document).on('click', '.delete', function () {
        var id = $(this).attr("id");
        Swal.fire({
            title: '{{ \App\CPU\translate('Are_you_sure_delete_this_product')}}?',
            text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this')}}!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ \App\CPU\translate('Yes')}}, {{ \App\CPU\translate('delete_it')}}!'
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('admin.marketing.delete')}}",
                    method: 'POST',
                    data: {id: id},
                    success: function () {
                        toastr.success('{{ \App\CPU\translate('deleted_successfully')}}');
                        location.reload();
                    }
                });
            }
        })
    });
</script>


@endpush
