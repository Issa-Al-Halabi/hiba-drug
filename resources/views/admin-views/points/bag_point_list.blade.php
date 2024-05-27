<?php
use \App\Model\Bag;
?>
@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Bag Points List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-black-50">{{\App\CPU\translate('points')}} <span style="color: rgb(252, 59, 10);">({{ $productpoint->total() }})</span></h1>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
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
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="{{ \App\CPU\translate('Search')}} {{ \App\CPU\translate('Points')}}" aria-label="Search orders" value="{{ $search }}" required>
                                <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Search')}}</button>
                            </div>
                        </form>
                        <!-- End Search -->


                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                 
                                    <th scope="col">{{ \App\CPU\translate('Bag_name')}}</th>
                                    {{-- <th scope="col">{{ \App\CPU\translate('Quantity')}}</th> --}}
                                    <th scope="col">{{ \App\CPU\translate('Points')}}</th>
                                    <th scope="col" style="width: 100px" class="text-center">
                                        {{ \App\CPU\translate('action')}}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($productpoint as $k=>$b)
                                
                                    <tr>
                                        
                                        {{-- <td class="text-center">{{$productpoint->firstItem()+$k}}</td> --}}
                                        @php
                                            $it = json_decode($b->type_id);
                                            $name = Bag::whereIn('id',$it)->get()->toArray();
                                        @endphp
                                        
                                          <td>
                                        @foreach($name as $n)
                                      
                                            {{$n['bag_name']}}<br>
                                        
                                        @endforeach
                                    </td>
                                    {{-- <td>
                                        {{$b['quantity']}}
                                    </td> --}}
                                        
                                        <td>{{$b['points']}}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                               href="{{route('admin.points.bag_points_edit',['id'=>$b['id']])}}">
                                                <i class="tio-edit"></i> {{ \App\CPU\translate('Edit')}}
                                            </a>
                                            <a class="btn btn-danger btn-sm delete"
                                               id="{{$b['id']}}">
                                                <i class="tio-add-to-trash"></i> {{ \App\CPU\translate('Delete')}}
                                            </a>
                                        </td>
                                        
                                    </tr>
                                    
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="card-footer">
                        {{$productpoint->links()}}
                    </div>
                    @if(count($productpoint)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_bag_points')}}?',
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
                        url: "{{route('admin.points.bag_points_delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{ \App\CPU\translate('store_deleted_successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
