@extends('layouts.back-end.app')

@section('title',\App\CPU\translate('Salesmen List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i>
                        {{\App\CPU\translate('salesman')}} {{\App\CPU\translate('list')}}
                        ( {{ $sales_men->total() }} )
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="row" style="width: 100%">
                            <div class="col-12 mb-1 col-md-4">
                                <form action="{{url()->current()}}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="Search" aria-label="Search" value="{{$search}}" required>
                                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>

                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>

                            <div class="col-12 col-md-8 text-right">
                                <a href="{{route('admin.sales-man.add')}}" class="btn btn-primary pull-right"><i
                                        class="tio-add-circle"></i> {{\App\CPU\translate('add')}} {{\App\CPU\translate('salesman')}}
                                </a>
                            </div>
                        </div>

                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{\App\CPU\translate('#')}}</th>
                                <th>{{\App\CPU\translate('name')}}</th>
                                <th>{{\App\CPU\translate('Team')}}</th>
                                <th>{{\App\CPU\translate('email')}}</th>
                                <th>{{\App\CPU\translate('phone')}}</th>
                          
                                <th></th>
                                <th>{{\App\CPU\translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($sales_men as $key=>$sm)
                                <tr>
                                    <td>{{$sales_men->firstitem()+$key}}</td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$sm['f_name'].' '.$sm['l_name']}}
                                        </span>
                                    </td>

                                    <td>
                                        {{$sm['team']}}
                                    </td>


                                    <td>
                                        {{$sm['email']}}
                                    </td>
                                    <td>
                                        {{$sm['phone']}}
                                    </td>

                                    <td>
                                        <a class="btn btn-info btn-sm"
                                           href="{{route('admin.sales-man.preview',[$sm['id']])}}">
                                            <i class="tio-visible"></i>{{\App\CPU\translate('view')}}
                                        </a>
                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"
                                                   href="{{route('admin.sales-man.edit',[$sm['id']])}}">{{\App\CPU\translate('edit')}}</a>
                                                <a class="dropdown-item" href="javascript:"
                                                   onclick="form_alert('sales-man-{{$sm['id']}}','Want to remove this information ?')">{{\App\CPU\translate('delete')}}</a>
                                                <form action="{{route('admin.sales-man.delete',[$sm['id']])}}"
                                                      method="post" id="sales-man-{{$sm['id']}}">
                                                    @csrf @method('delete')
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

                        <div class="page-area">
                            <table>
                                <tfoot>
                                {!! $sales_men->links() !!}
                                </tfoot>
                            </table>
                        </div>

                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection


