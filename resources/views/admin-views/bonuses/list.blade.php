<?php
use App\Model\Product;
?>
@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Bonuses List'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-black-50">{{ \App\CPU\translate('bonuses') }} <span
                    style="color: rgb(252, 59, 10);">({{ $bonuses->total() }})</span></h1>
        </div>
        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>

                                        <!--<th scope="col">{{ \App\CPU\translate('Main Product') }}</th>-->
                                        <th scope="col">{{ \App\CPU\translate('Product') }}</th>
                                        <!--<th scope="col">{{ \App\CPU\translate('Quantity') }}</th>-->
                                        {{-- <th scope="col">{{ \App\CPU\translate('Second Products')}}</th>
                                    <th scope="col">{{ \App\CPU\translate('Second Products Quantity')}}</th> --}}
                                        <th scope="col" style="width: 100px" class="text-center">
                                            {{ \App\CPU\translate('action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($bonuses as $k => $b)
                                        <tr>


                                            @php

                                                $master_name = Product::where('id', $b->master_product_id)
                                                    ->pluck('name')
                                                    ->first();
                                                $slave_name = Product::where('id', $b->salve_product_id)
                                                    ->pluck('name')
                                                    ->first();

                                                //
                                                $idx = json_decode($b->master_product_id);
                                                $idx2 = json_decode($b->master_product_quatity);

                                                //

                                            @endphp
                                            <td>Bonus ({{$b->id}})</td>

                                            <td>

                                                <button type="button" class="btn btn-sm btn-primary view"
                                                    data-toggle="modal" data-target="#exampleModal"
                                                    data-id="{{ $b['id'] }}">

                                                    <i class="tio-add-circle"></i>
                                                    {{ \App\CPU\translate('View') }}
                                                </button>
                                                <a class="btn btn-danger btn-sm delete1"
                                                    id="{{ $b['id'] }}">
                                                    <i class="tio-add-to-trash"></i> {{ \App\CPU\translate('Delete') }}
                                                </a>

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card-footer">
                        {{ $bonuses->links() }}
                    </div>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog centered" id ="eee" style="justify-content: center">
                            <div class="modal-content" style="height: 400px;width:600px">

                                <div class="modal-header">

                                    <div class="table-responsive">
                                        <table
                                            style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                            <thead class="thead-light">
                                                <tr>


                                                    <th scope="col">{{ \App\CPU\translate('Main Products') }}</th>
                                                    <th scope="col">{{ \App\CPU\translate('Main Products Quantity') }}
                                                    </th>
                                                    <th scope="col" style="width: 100px" class="text-center">
                                                        {{ \App\CPU\translate('action') }}
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody id="bodyData1">


                                            </tbody>
                                        </table>

                                    </div>
                                </div>


                                <div class="modal-header">

                                    <div class="table-responsive">
                                        <table
                                            style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                            <thead class="thead-light">
                                                <tr>


                                                    <th scope="col">{{ \App\CPU\translate('Second Products') }}</th>
                                                    <th scope="col">{{ \App\CPU\translate('Second Products Quantity') }}
                                                    </th>
                                                    <th scope="col" style="width: 100px" class="text-center">
                                                        {{ \App\CPU\translate('action') }}
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody id="bodyData">

                                            </tbody>
                                        </table>

                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>
                    @if (count($bonuses) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

        $('#exampleModal').on('hidden.bs.modal', function (e) {
        // console.log("Hey !! I am unloading... ");
        var table = document.getElementById('bodyData');
        table.innerHTML = "";
        // table.removeChild(table);

        });
        $('#exampleModal').on('hidden.bs.modal', function (e) {
        // console.log("Hey !! I am unloading... ");
        var table = document.getElementById('bodyData1');
        table.innerHTML = "";
        // table.removeChild(table);

        });



        $(document).on('click', '.view', function() {
            var id = $(this).attr("data-id");
            console.log(id);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    var url1 = '{{ route('admin.bonuses.get_salve_products', 'id:id') }}';
                    url1 = url1.replace('id:id', 'id=' + id);

                    var url2 = '{{ route('admin.bonuses.get_main_products', 'id:id') }}';
                    url2 = url2.replace('id:id', 'id=' + id);
                    $.ajax({

                        url: url1,
                        method: 'GET',
                        dataType: 'json',

                        success: function(dataResult) {

                            var resultData = dataResult;
                            // console.log(dataResult);
                            var bodyData = '';
                            var i = 1;
                            $.each(resultData, function(index, row) {
                                var editUrl = '/' + row.id + "/edit";
                                bodyData += "<tr>"
                                bodyData += "<td>"
                                $.each(row.salve_name, function(index, id){

                                    bodyData +=  id  +"<br>"
                                })


                                bodyData += "</td>"
                                bodyData += "<td>"
                                $.each(row.salve_product_quatity, function(index, id){
                                    // console.log('id'+id);
                                    bodyData +=  id  +"<br>"
                                })
                                bodyData += "</td>"
                                bodyData += "<td>"
                                $.each(row.salve_product_id, function(index, id){

                                    //bodyData +=   "<button class='btn btn-danger btn-sm delete' value='"+id+"'> Delete</button>";
                                    bodyData +=  "<br>"

                                })

                                bodyData += "</tr>";

                            })
                            $("#bodyData").append(bodyData);


                        }
                    });
                    $.ajax({

                        url: url2,
                        method: 'GET',
                        dataType: 'json',

                        success: function(dataResult) {

                            var resultData = dataResult;
                            console.log(dataResult);
                            var bodyData = '';
                            var i = 1;
                            $.each(resultData, function(index, row) {
                                var editUrl = '/' + row.id + "/edit";
                                bodyData += "<tr>"
                                bodyData += "<td>"
                                $.each(row.master_name, function(index, id){

                                    bodyData +=  id  +"<br>"
                                })


                                bodyData += "</td>"
                                bodyData += "<td>"
                                $.each(row.master_product_quatity, function(index, id){
                                    // console.log('id'+id);
                                    bodyData +=  id  +"<br>"
                                })
                                bodyData += "</td>"
                                bodyData += "<td>"
                                $.each(row.master_product_id, function(index, id2){
                                    //bodyData +=   "<button class='btn btn-danger btn-sm delete' data-main_id ="+id+" value='"+id2+"'> Delete</button>";
                                    bodyData +=  "<br>"
                                })

                                bodyData += "</tr>";

                            })
                            $("#bodyData1").append(bodyData);

                        }
                    });
        });
    </script>
    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).attr("value");
            var main = $(this).attr("data-main_id");


            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_product') }}?',
                text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ \App\CPU\translate('Yes') }}, {{ \App\CPU\translate('delete_it') }}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.bonuses.delete_sec') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            main: main
                        },
                        success: function() {
                            toastr.success(
                                '{{ \App\CPU\translate('store_deleted_successfully') }}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
    <script>
        $(document).on('click', '.delete1', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_product') }}?',
                text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ \App\CPU\translate('Yes') }}, {{ \App\CPU\translate('delete_it') }}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.bonuses.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                '{{ \App\CPU\translate('store_deleted_successfully') }}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
