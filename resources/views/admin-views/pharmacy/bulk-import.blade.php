@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('pharmacy Bulk Import'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a>
            </li>
            <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin.pharmacy.list', ['in_house',''])}}">{{\App\CPU\translate('pharmacy')}}</a>
            </li>
            <li class="breadcrumb-item">{{\App\CPU\translate('bulk_import')}} </li>
        </ol>
    </nav>
    <!-- Content Row -->
    <div class="row" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left' }};">
        <div class="col-12" >
            <div class="jumbotron" style="background: white" >
                <h1 class="display-4">{{\App\CPU\translate('Instructions')}} : </h1>
                <p> 1. {{\App\CPU\translate('Download the format file and fill it with proper data')}}.</p>

                <p>2. {{\App\CPU\translate('You can download the example file to understand how the data must be filled')}}.</p>

                <p>3. {{\App\CPU\translate('Once you have downloaded and filled the format file, upload it in the form below and submit')}}.</p>

            </div>
        </div>

        <div class="col-md-12">
            <form class="product-form" action="{{route('admin.pharmacy.bulk-import-excel')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mt-2 rest-part">
                    <div class="card-header">
                        <h4>{{\App\CPU\translate('Import Pharmacies File')}}</h4>
                        <a href="{{asset('public/assets/pharmacy_format.xlsx')}}" download="" class="btn btn-secondary">{{\App\CPU\translate('Download_Format')}}</a>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="file" name="pharmacies_file">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-footer">
                    <div class="row">
                        <div class="col-md-12" style="padding-top: 20px">
                            <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>




    <div class="content container-fluid">
        <!-- Page Heading -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('pharmacies')}}</li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div class="col-12 mb-1 col-md-4">
                                <h5 class="flex-between">
                                    <div>{{\App\CPU\translate('pharmacies_table')}} ({{ $pharmacies->total() }})</div>
                                </h5>
                                <h5 class="flex-between">
                                    <div>{{\App\CPU\translate('Active')}}:({{ \App\Model\UserImportExcel::where('is_active', 1)->count()
                                    }})</div>
                                </h5>
                                <h5 class="flex-between">
                                    <div>{{\App\CPU\translate('InActive')}}:({{ \App\Model\UserImportExcel::where('is_active', 0)->count()
                                    }})</div>
                                </h5>
                            </div>
                            <div class="col-12 mb-1 col-md-5" style="width: 40vw">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{\App\CPU\translate('Search pharmacy Name')}}" aria-label="Search orders" aria-label="Search pharmacies" value="{{ $search }}" required>

                                        <button type="submit" class="btn btn-primary ml-1">{{\App\CPU\translate('search')}}</button>

                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>

                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" style="width: 100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" style="width: 100px">
                                            {{ \App\CPU\translate('pharmacy') }} {{ \App\CPU\translate('ID') }}
                                        </th>
                                        <th scope="col">{{ \App\CPU\translate('pharamacy_name') }}</th>
                                        <th scope="col">{{ \App\CPU\translate('Account_number') }}</th>
                                        <th scope="col">{{ \App\CPU\translate('street_address') }}</th>
                                        <th scope="col">{{ \App\CPU\translate('confirmation') }}</th>
                                        <th scope="col" style="width: 100px" class="text-center">
                                            {{ \App\CPU\translate('action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pharmacies as $p)
                                    <tr>
                                        <td>{{ $p->id }}</td>
                                        <td>{{ $p->pharmacy_name }}</td>
                                        <td>{{ $p->num_id }}</td>
                                        <td> {{\Illuminate\Support\Str::limit($p->street_address,30)}}</td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox" disabled class="toggle-switch-input" class="toggle-switch-input" {{ $p->is_active ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="tio-settings"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                    <a class="dropdown-item" href={{route('admin.pharmacyImport.activation-export',[$p->id])}}>
                                                        <i class="fa fa-plus-square"></i> {{\App\CPU\translate('activation')}}
                                                    </a>
                                                    <a class="dropdown-item" href="{{route('admin.pharmacyImport.edit',[$p->id])}}">
                                                        <i class="tio-visible"></i> {{\App\CPU\translate('Edit')}}
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:" onclick="form_alert('pharmacy-{{$p->id}}','Want to delete this pharmacy ?')">
                                                        <i class="tio-delete"></i> {{\App\CPU\translate('delete')}}
                                                    </a>
                                                    <form action="{{route('admin.pharmacyImport.delete',[$p->id])}}" method="post" id="pharmacy-{{$p->id}}">
                                                        @csrf @method('delete')
                                                    </form>
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$pharmacies->links()}}
                    </div>
                    @if(count($pharmacies)==0)
                    <div class="text-center p-4">
                        <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                        <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>








</div>

@push('script')
<!-- Page level plugins -->
<script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>


<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $(document).on('click', '.vip', function() {
        var id = $(this).attr("id");
        Swal.fire({
            title: '{{ \App\CPU\translate('
            Are_you_sure_vip_this_pharmacy ') }}?'
            , text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!"
            , showCancelButton: true
            , confirmButtonColor: '#3085d6'
            , cancelButtonColor: '#d33'
            , confirmButtonText: '{{ \App\CPU\translate('
            Yes ') }}, {{ \App\CPU\translate('
            delete_it ') }}!'
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('admin.pharmacy.delete') }}"
                    , method: 'POST'
                    , data: {
                        id: id
                    }
                    , success: function() {
                        toastr.success(
                            '{{ \App\CPU\translate('
                            pharmacy_deleted_successfully ') }}'
                        );
                        location.reload();
                    }
                });
            }
        })
    });

</script>
<script>
    $(document).on('click', '.delete', function() {
        var id = $(this).attr("id");
        Swal.fire({
            title: '{{ \App\CPU\translate('
            Are_you_sure_delete_this_pharmacy ') }}?'
            , text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!"
            , showCancelButton: true
            , confirmButtonColor: '#3085d6'
            , cancelButtonColor: '#d33'
            , confirmButtonText: '{{ \App\CPU\translate('
            Yes ') }}, {{ \App\CPU\translate('
            delete_it ') }}!'
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('admin.pharmacy.delete') }}"
                    , method: 'POST'
                    , data: {
                        id: id
                    }
                    , success: function() {
                        toastr.success(
                            '{{ \App\CPU\translate('
                            pharmacy_deleted_successfully ') }}'
                        );
                        location.reload();
                    }
                });
            }
        })
    });

</script>
@endpush
@endsection
