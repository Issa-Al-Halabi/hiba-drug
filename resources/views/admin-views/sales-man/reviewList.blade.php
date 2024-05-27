@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Review List'))

@push('css_or_js')

<style>
    .rate {
        float: left;
        height: 46px;
        padding: 0 10px;
    }

    .rate:not(:checked)>input {
        position: absolute;
        display: none;
    }

    .rate:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ccc;
    }

    .rated:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ccc;
    }

    .rate:not(:checked)>label:before {
        content: '★ ';
    }

    .rate>input:checked~label {
        color: #ffc700;
    }

    .rate:not(:checked)>label:hover,
    .rate:not(:checked)>label:hover~label {
        color: #deb217;
    }

    .rate>input:checked+label:hover,
    .rate>input:checked+label:hover~label,
    .rate>input:checked~label:hover,
    .rate>input:checked~label:hover~label,
    .rate>label:hover~input:checked~label {
        color: #c59b08;
    }

    .star-rating-complete {
        color: #c59b08;
    }

    .rating-container .form-control:hover,
    .rating-container .form-control:focus {
        background: #fff;
        border: 1px solid #ced4da;
    }

    .rating-container textarea:focus,
    .rating-container input:focus {
        color: #000;
    }

    .rated {
        float: left;
        height: 46px;
        padding: 0 10px;
    }

    .rated:not(:checked)>input {
        position: absolute;
        display: none;
    }

    .rated:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ffc700;
    }

    .rated:not(:checked)>label:before {
        content: '★ ';
    }

    .rated>input:checked~label {
        color: #ffc700;
    }

    .rated:not(:checked)>label:hover,
    .rated:not(:checked)>label:hover~label {
        color: #deb217;
    }

    .rated>input:checked+label:hover,
    .rated>input:checked+label:hover~label,
    .rated>input:checked~label:hover,
    .rated>input:checked~label:hover~label,
    .rated>label:hover~input:checked~label {
        color: #c59b08;
    }

    .contentModal {
        max-width: 600;
    }

</style>

@endpush



@section('content')

<div class="content container-fluid">
    <!-- Page Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('reviews')}}</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-header">
            <!-- Search -->
            <form action="{{ url()->current() }}" method="GET">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="tio-search"></i>
                        </div>
                    </div>
                    <input id="datatableSearch_" type="search" name="search" class="form-control" aria-label="Search orders" value="{{ $search }}" required>
                    <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Search')}}</button>
                </div>
            </form>
            <!-- End Search -->


        </div>
        <div class="card-body" style="padding: 0">
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true
                                }'>
                    <thead class="thead-light">
                        <tr>
                            <th>#{{ \App\CPU\translate('sl')}}</th>
                            <th>{{ \App\CPU\translate('First_name')}}</th>
                            <th>{{ \App\CPU\translate('Last_name')}}</th>
                            <th>{{ \App\CPU\translate('Phone')}}</th>
                            <th>{{ \App\CPU\translate('Personal_image')}}</th>
                            <th>{{ \App\CPU\translate('Actions')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($lists as $key=>$list)
                        <tr>
                            <td class="text-center">{{$lists->firstItem()+$key}}</td>
                            <td>{{$list['f_name']}}</td>
                            <td>{{$list['l_name']}}</td>
                            <td>{{$list['phone']}}</td>

                            <td>
                                <img style="width: 60px;height: 60px" onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'" src="{{asset('storage/app/public/delivery-man')}}/{{$list['image']}}">
                            </td>
                            <td>
                                <button href="" id="editCompany" data-toggle="modal" data-target='#practice_modal' class="btn btn-primary btn-sm " data-saler_id="{{$list['id']}}">
                                    <i class="tio-star"></i>&nbsp;&nbsp;{{\App\CPU\translate('Evaluation')}}</button>
                                <a href="{{route('admin.sales-man.review',[$list['id']])}}" class="btn btn-success btn-sm">
                                    <i class="fa fa-eye"></i>&nbsp;&nbsp;{{ \App\CPU\translate('Reviews')}}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
        <div class="card-footer">
            {{$lists->links()}}
        </div>
        @if(count($lists)==0)
        <div class="text-center p-4">
            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
        </div>
        @endif
    </div>

</div>


<div class="container-fluid modal fade" id="practice_modal">
    <div class="modal-dialog">

        <div class="container">
            <div class="row">
                <div class="col mt-4">
                    <form action="{{route('admin.sales-man.store-review')}}" class="py-2 px-4" method="POST" autocomplete="off">
                        @csrf
                        <div class="modal-content" style="min-height: 150px; padding: 20px">
                            <p class="font-weight-bold ">{{ \App\CPU\translate('Review')}}</p>
                            <div class="form-group row">
                                <input type="hidden" name="saler_id" id="saler_id">
                                <div class="col">
                                    <div class="rate">
                                        <input type="radio" id="star5" class="rate" name="rating" value="5" />
                                        <label for="star5" title="text">5 stars</label>
                                        <input type="radio" checked id="star4" class="rate" name="rating" value="4" />
                                        <label for="star4" title="text">4 stars</label>
                                        <input type="radio" id="star3" class="rate" name="rating" value="3" />
                                        <label for="star3" title="text">3 stars</label>
                                        <input type="radio" id="star2" class="rate" name="rating" value="2">
                                        <label for="star2" title="text">2 stars</label>
                                        <input type="radio" id="star1" class="rate" name="rating" value="1" />
                                        <label for="star1" title="text">1 star</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mt-4">
                                <div class="col">
                                    <textarea class="form-control" name="comment" rows="6 " placeholder={{ \App\CPU\translate('Comment')}} maxlength="200"></textarea>
                                </div>
                            </div>
                            <div class="mt-3 text-right">
                                <button class="btn btn-sm btn-primary py-0" style="font-size: 1.2em; height: 30px; width: 100%;" type="submit" value="Submit" id="submit">
                                    {{ \App\CPU\translate('submit')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <div>


        @endsection

        @push('script_2')



        <script>
            $(document).on('ready', function() {
                // INITIALIZATION OF DATATABLES
                // =======================================================
                // var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

                $('#column1_search').on('keyup', function() {
                    datatable
                        .columns(1)
                        .search(this.value)
                        .draw();
                });

                $('#column2_search').on('keyup', function() {
                    datatable
                        .columns(2)
                        .search(this.value)
                        .draw();
                });

                $('#column3_search').on('change', function() {
                    datatable
                        .columns(3)
                        .search(this.value)
                        .draw();
                });

                $('#column4_search').on('keyup', function() {
                    datatable
                        .columns(4)
                        .search(this.value)
                        .draw();
                });


                // INITIALIZATION OF SELECT2
                // =======================================================
                $('.js-select2-custom').each(function() {
                    var select2 = $.HSCore.components.HSSelect2.init($(this));
                });
            });

        </script>

        <script>
            $(document).ready(function() {
                $('body').on('click', '#editCompany', function(event) {
                    event.preventDefault();
                    var saler_id = $(this).data('saler_id');
                    $('#saler_id').val(saler_id);
                    console.log(saler_id);
                });
            });

        </script>

        @endpush

