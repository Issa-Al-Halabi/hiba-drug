@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Plans List'))
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/brands.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/fontawesome.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Heading -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('Plans')}}</li>
        </ol>
    </nav>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                        <div class="col-12 mb-1 col-md-4">
                            <h5 class="flex-between">
                                <div>{{\App\CPU\translate('Plans_table')}} ({{ $plans->total() }})</div>
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
                                    <input  type="search" name="search" class="form-control" placeholder="{{\App\CPU\translate('Search_Saler_Name')}}" aria-label="Search plans" value="{{ $search }}" required>
                                    <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                                </div>
                            </form>
                            <!-- End Search -->
                        </div>
                        <div class="col-12 col-md-3">
                            <a href="{{route('admin.sales-man.work-plan-add')}}" class="btn btn-primary  float-right">
                                <i class="tio-add-circle"></i>
                                <span class="text">{{\App\CPU\translate('Add new plan')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" style="width: 100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{\App\CPU\translate('SL#')}}</th>
                                    <th>{{\App\CPU\translate('Saler_name')}}</th>
                                    <th>{{\App\CPU\translate('Begin_Plan')}}</th>
                                    <th>{{\App\CPU\translate('End_Plan')}}</th>
                                    <th>{{\App\CPU\translate('Note')}}</th>
                                    <th>{{\App\CPU\translate('Active')}} {{\App\CPU\translate('status')}}</th>
                                    <th style="width: 5px" class="text-center">{{\App\CPU\translate('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $p)
                                @php
                                $date1 = Carbon\Carbon::now();
                                $date2 = $p['end_plan'];
                                $refreshing = $date1->gt($date2);
                                //$refreshing=false;
                                @endphp
                                <tr>
                                    <th scope="row">{{$plans->firstItem()}}</th>
                                    <td>
                                        <a href="{{route('admin.sales-man.preview',[$p['saler_id']])}}">
                                            {{\Illuminate\Support\Str::limit($p['saler_name'],20)}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$p['begin_plan']}}
                                    </td>
                                    <td>
                                        {{$p['end_plan']}}
                                    </td>
                                    <td>
                                        {{\Illuminate\Support\Str::limit($p['note'],20)}}
                                    </td>
                                    <td>
                                        <label class="switch switch-status">
                                            <input type="checkbox" class="status" id="{{$p['id']}}" {{$p->status_plan== 1?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>

                                        <a class="btn btn-secondary btn-sm" href="{{route('admin.sales-man.work-plan-tasks',[$p['id']])}}">
                                            <i class="tio-visible"></i>{{\App\CPU\translate('Tasks')}}
                                        </a>

                                        <a class="btn btn-success btn-sm" href="{{route('admin.sales-man.work-plan-details',[$p['id']])}}">
                                            <i class="tio-visible"></i>{{\App\CPU\translate('Details')}}
                                        </a>

                                        @if($refreshing)
                                        <a class="btn btn-danger btn-sm" href="{{route('admin.sales-man.work-plan-refresh',[$p['id']])}}">
                                          {{-- <i class="fa-duotone fa-lock"></i> --}}
                                             <i class="tio-refresh"></i>
                                        </a>
                                        @else
                                        <a class="btn btn-success btn-sm" href="{{route('admin.sales-man.work-plan-refresh',[$p['id']])}}">
                                            {{-- <i class="fa-regular fa-rotate"></i> --}}
                                            <i class="tio-refresh"></i>
                                        </a>
                                        @endif

                                        {{-- <a class="btn btn-primary btn-sm" href="{{route('admin.sales-man.work-plan-edit',[$p['id']])}}">
                                            <i class="tio-edit"></i>
                                        </a> --}}
                                        <a class="btn btn-danger btn-sm" href="javascript:" onclick="form_alert('plan-{{$p['id']}}','Want to delete this item ?')">
                                            <i class="tio-add-to-trash"></i>{{--{{\App\CPU\translate('Delete')}}--}}
                                        </a>
                                        <form action="{{route('admin.sales-man.work-plan-delete',[$p['id']])}}" method="post" id="plan-{{$p['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{$plans->links()}}
                </div>
                @if(count($plans)==0)
                <div class="text-center p-4">
                    <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                    <p class="mb-0">{{\App\CPU\translate('No plans to show')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Page level plugins -->
<script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Page level custom scripts -->
<script>
    // Call the dataTables jQuery plugin
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $(document).on('change', '.status', function() {
        var id = $(this).attr("id");
        if ($(this).prop("checked") == true) {
            var status = 1;
        } else if ($(this).prop("checked") == false) {
            var status = 0;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.sales-man.work-plan-activation')}}"
            , method: 'POST'
            , data: {
                id: id
                , status: status
            }
            , success: function(data) {
                if (data.success == true) {
                    toastr.success('{{\App\CPU\translate('
                        Status updated successfully ')}}');
                } else if (data.success == false) {
                    toastr.error('{{\App\CPU\translate('
                        Status updated failed.Plan must be approved ')}}');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            }
        });
    });

</script>
@endpush
