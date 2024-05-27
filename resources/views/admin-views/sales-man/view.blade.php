@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Salesman Details'))
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

</style>

<!-- Jquery -->
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-print-none pb-2">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item">
                            <a class="breadcrumb-link" href="{{ route('admin.sales-man.list') }}">
                                {{ \App\CPU\translate('Salesman') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ \App\CPU\translate('Salesman details') }}</li>
                    </ol>
                </nav>

                <div class="d-sm-flex align-items-sm-center">
                    <h1 class="page-header-title">{{ \App\CPU\translate('Salesman ID') }} #{{ $sm['id'] }}</h1>
                    <span class="{{ Session::get('direction') === 'rtl' ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3' }}">
                        <i class="tio-date-range">
                        </i> {{ \App\CPU\translate('Joined At') }} :
                        {{ date('d M Y H:i:s', strtotime($sm['created_at'])) }}
                    </span>
                </div>
                <div class="row border-top pt-3 mt-3 mb-5">
                    <div class="col-md-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="tio-home-outlined"></i> {{ \App\CPU\translate('Dashboard') }}
                        </a>
                    </div>

                    <div class="col-md-8">
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#exampleModal" style="width: 160px">
                            <i class="tio-add-circle"></i> {{ \App\CPU\translate('assign') }}
                            {{ \App\CPU\translate('Pharmacy') }}
                        </button>

                        <button type="button" class="btn btn-danger pull-right mx-3" data-toggle="modal" data-target="#exampleModalArea" style="width: 140px">
                            <i class="tio-add-circle"></i> {{ \App\CPU\translate('assign') }}
                            {{ \App\CPU\translate('Area') }}
                        </button>

                        <button type="button" class="btn btn-success pull-right mx-3" data-toggle="modal" data-target="#exampleModalGroup" style="width: 140px">
                            <i class="tio-add-circle"></i> {{ \App\CPU\translate('assign') }}
                            {{ \App\CPU\translate('Group') }}
                        </button>


                        <!-- Modal Pharmecy -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"><i class="tio-add-circle mr-2"></i>Assign Pharmacy</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="font-size: 25px;">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{route('admin.sales-man.assign',['id'=>$sm->id])}}" method="POST">
                                        @csrf
                                        <div class="modal-body">

                                            <select class="js-example-basic-multiple" name="assigned_pharmacies[]" multiple="multiple">
                                                @foreach($all_pharmacies as $allp)
                                                <option value="{{$allp->id}}">{{$allp->name}}</option>
                                                @endforeach

                                            </select>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Area -->
                        <div class="modal fade" id="exampleModalArea" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"><i class="tio-add-circle mr-2"></i>Assign Areas</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="font-size: 25px;">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{route('admin.sales-man.assign-area',['id'=>$sm->id])}}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <select class="js-example-basic-multiple" name="assigned_areas[]" multiple="multiple">
                                                @foreach($all_areas as $alla)
                                                <option value="{{$alla->id}}">{{$alla->area_name}}</option>
                                                @endforeach

                                            </select>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Group -->
                        <div class="modal fade" id="exampleModalGroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"><i class="tio-add-circle mr-2"></i>Assign Groups</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="font-size: 25px;">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{route('admin.sales-man.assign-group',['id'=>$sm->id])}}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <select class="js-example-basic-multiple" name="assigned_groups[]" multiple="multiple">
                                                @foreach($all_groups as $allg)
                                                <option value="{{$allg->id}}">{{$allg->group_name}}</option>
                                                @endforeach

                                            </select>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- End Page Header -->

    <div class="row" id="printableArea">
        <div class="col-lg-8 mb-3 mb-lg-0">

            <h5 class="card-header-title" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">{{ \App\CPU\translate('Groups') }}</h5>
            <div class="card">
                <div class="card-header">

                    <div class="table-responsive ">
                        <table id="groups_table" style="width: 100%" class="display table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ \App\CPU\translate('#') }}</th>
                                    <th style="width: 30%">{{ \App\CPU\translate('city') }}</th>

                                    <th>{{ \App\CPU\translate('group') }}</th>

                                    <th>{{ \App\CPU\translate('action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($all_groups_assign as $city_group)
                                <tr>
                                    <td>
                                        {{ $city_group['group_id'] }}
                                    </td>
                                    <td>
                                        {{ $city_group['city_name'] }}
                                    </td>
                                    <td>
                                        {{ $city_group['group_name'] }}
                                    </td>

                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                <a class="dropdown-item" href="javascript:" onclick="form_alert('sales-man-{{$city_group['group_id'] }}','Want to unassign this information ?')">{{
                                                    \App\CPU\translate('unassign') }}</a>
                                                <form action="{{ route('admin.sales-man.unassign-group', [$city_group['group_id']]) }}" method="post" id="sales-man-{{ $city_group['group_id'] }}">
                                                    @csrf
                                                    <input type="hidden" value="{{Crypt::encrypt($sm->id)}}" name="saler_id">
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>


                    </div>

                </div>

            </div>

            <h5 class="card-header-title" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">{{ \App\CPU\translate('Regions') }}</h5>
            <div class="card">
                <div class="card-header">

                    <div class="table-responsive">
                        <table id="areas_table" style="width: 100%" class="display table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ \App\CPU\translate('#') }}</th>
                                    <th style="width: 30%">{{ \App\CPU\translate('group') }}</th>

                                    <th>{{ \App\CPU\translate('region') }}</th>

                                    <th>{{ \App\CPU\translate('action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($all_areas_assign as $city_area)
                                <tr>
                                    <td>
                                        {{ $city_area['area_id'] }}
                                    </td>
                                    <td>
                                        {{ $city_area['group_name'] }}
                                    </td>
                                    <td>
                                        {{ $city_area['area_name'] }}
                                    </td>

                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                <a class="dropdown-item" href="javascript:" onclick="form_alert('sales-man-{{$city_area['area_id'] }}','Want to unassign this information ?')">{{
                                                    \App\CPU\translate('unassign') }}</a>
                                                <form action="{{ route('admin.sales-man.unassign-area', [$city_area['area_id']]) }}" method="post" id="sales-man-{{ $city_area['area_id'] }}">
                                                    @csrf
                                                    <input type="hidden" value="{{Crypt::encrypt($sm->id)}}" name="saler_id">
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>

                    </div>

                </div>

            </div>

            <h5 class="card-header-title" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">{{ \App\CPU\translate('Pharmacies') }}</h5>
            <div class="card">
                <div class="card-header">

                    <div class="table-responsive">
                        <table id="pharmacies_table" style="width: 100%" class="display table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ \App\CPU\translate('#') }}</th>
                                    <th style="width: 30%">{{ \App\CPU\translate('name') }}</th>

                                    <th>{{ \App\CPU\translate('region') }}</th>

                                    <th>{{ \App\CPU\translate('action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($pharmacies as $pharma)
                                <tr>

                                    <td>
                                        {{ $pharma['id'] }}
                                    </td>
                                    <td>
                                        {{ $pharma['name'] }}
                                    </td>
                                    <td>
                                        {{ $pharma['region'] }}
                                    </td>


                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                <a class="dropdown-item" href="javascript:" onclick="form_alert('sales-man-{{ $pharma['id'] }}','Want to unassign this information ?')">{{
                                                    \App\CPU\translate('unassign') }}</a>
                                                <form action="{{ route('admin.sales-man.unassign', [$pharma['id']]) }}" method="post" id="sales-man-{{ $pharma['id'] }}">
                                                    @csrf
                                                    <input type="hidden" value="{{Crypt::encrypt($sm->id)}}" name="saler_id">
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>

                    </div>

                </div>

            </div>

        </div>

        {{-- saler details --}}
        <div class="col-lg-4">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">{{ \App\CPU\translate('Salesman') }}</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->


                @if ($sm)
                <div class="card-body">
                    <div class="media align-items-center" href="javascript:">

                        <div class="media-body">
                            <span class="text-body text-hover-primary">{{ $sm['f_name'] . ' ' . $sm['l_name'] }}</span>
                        </div>
                        <div class="media-body text-right">
                            {{-- <i class="tio-chevron-right text-body"></i> --}}
                        </div>
                    </div>

                    <hr>

                    <div class="media align-items-center" href="javascript:">
                        <div class="icon icon-soft-info icon-circle {{ Session::get('direction') === 'rtl' ? 'ml-3' : 'mr-3' }}">
                            <i class="tio-shopping-basket-outlined"></i>
                        </div>

                        <div class="media-body text-right">
                            {{-- <i class="tio-chevron-right text-body"></i> --}}
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{ \App\CPU\translate('Contact info') }}</h5>
                    </div>

                    <ul class="list-unstyled list-unstyled-py-2">
                        <li>
                            <i class="tio-online {{ Session::get('direction') === 'rtl' ? 'ml-2' : 'mr-2' }}"></i>
                            {{ $sm['email'] }}
                        </li>
                        <li>
                            <i class="tio-android-phone-vs {{ Session::get('direction') === 'rtl' ? 'ml-2' : 'mr-2' }}"></i>
                            {{ $sm['phone'] }}
                        </li>
                    </ul>

                    <hr>


                </div>
                @endif

                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>

    </div>
    <!-- End Row -->
</div>
@endsection





@push('script_2')
<script>
    $(document).on('ready', function() {
        // INITIALIZATION OF DATATABLES
        // =======================================================
        var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        $('#column1_search').on('keyup', function() {
            datatable
                .columns(1)
                .search(this.value)
                .draw();
        });


        $('#column3_search').on('change', function() {
            datatable
                .columns(2)
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
    var content = [{
            id: 0
            , text: "Nitin Shah"
            , email: "Nitin@xy"
        }
        , {
            id: 1
            , text: "Suhash Shah "
            , email: "Suhash@xy"
        }
        , {
            id: 2
            , text: "Soumil Shah  "
            , email: "Soumil@xy"
        }
    , ];


    $(".prompt").select2({
        data: content
        , width: '100%'
        , multiple: true
        , placeholder: "Enter First Name"
    , });


    function btn_handler() {
        var names = $('.prompt').select2('data');
        for (let name of names) {

            console.log(name)
            console.log(name.text)
            console.log(name.email)

            alert(`${name.text} - ${name.email}`)
        }
    }

</script>

<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

</script>

<script>

    var lang = "{{ Session::get('direction') }}";
    console.log(lang);
    if(lang === 'rtl')
     url = "//cdn.datatables.net/plug-ins/1.13.1/i18n/ar.json";
     else
     url = "";

     $(document).ready(function() {
        $('#pharmacies_table').DataTable({
            "language": {
                "url": url
            }
            , dom: 'Bfrtip'
            , buttons: [
                'excel'
            ]
            , searching: true
            , ordering: true
            , paging: true
        });
    });

    $(document).ready(function() {
        $('#areas_table').DataTable({
            "language": {
                "url": url
            }
            , dom: 'Bfrtip'
            , buttons: [
                'excel'
            ]
            , searching: true
            , ordering: true
            , paging: true
        });
    });

    $(document).ready(function() {
        $('#groups_table').DataTable({
            "language": {
                "url": url
            }
            , dom: 'Bfrtip'
            , buttons: [
                'excel'
            ]
            , searching: true
            , ordering: true
            , paging: true
        });
    });

</script>


@endpush
