@extends('layouts.back-end.app')

@section('title',\App\CPU\translate('Points list'))

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
                        {{\App\CPU\translate('Points list')}}
                        ( {{ count($rewardItem) }} )
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
                                               placeholder="Search" aria-label="Search" value="{{$search}}">
                                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>

                            <div class="col-12 col-md-8 text-right">
                                <a href="{{route('admin.reward-item.addBag')}}" class="btn btn-primary pull-right"><i
                                        class="tio-add-circle"></i> {{\App\CPU\translate('add')}}
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
                                    <th>{{\App\CPU\translate('name')}} {{\App\CPU\translate('bag')}}</th>
                                  	<th></th>
                                    <th>{{\App\CPU\translate('Points')}}</th>
                                  	<th></th>
                                    <th>{{\App\CPU\translate('Actions')}}</th>
                                  	<th></th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($rewardItem as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item['name'] }}</td>
                                 	<td></td>
                                    <td>{{ $item['cost'] }}</td>
                                  	<td></td>
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
                                                   href="{{route('admin.reward-item.showUpdateBag', [$item['id']])}}">{{\App\CPU\translate('edit')}}</a>
                                                <a class="dropdown-item" href="javascript:"
                                                   onclick="form_alert('sales-man-{{$item['id']}}','Want to remove this information ?')">{{\App\CPU\translate('delete')}}</a>
                                                <form action="{{route('admin.reward-item.destroyBag', [$item['id']])}}"
                                                      method="post" id="sales-man-{{$item['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                  	<td></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>

                        <div class="page-area">
                            <!-- Pagination links can be added here if using pagination -->
                        </div>

                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection
