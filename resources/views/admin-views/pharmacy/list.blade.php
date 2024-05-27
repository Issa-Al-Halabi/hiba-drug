@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Pharmacies List'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-black-50">{{ \App\CPU\translate($status.'_pharmacy__list ') }}
                <span style="color: rgb(252, 59, 10);">
                    @if ($status=="Pending")
                    ({{ \App\User::where('is_active', 0)->where('user_type','=','pharmacist')->count() }})
                    @else
                    ({{ \App\User::where('is_active', 1)->where('user_type','=','pharmacist')->count() }})
                    @endif
                </span></h1>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header row">


                        <!-- Search -->
                        <form action="{{ url()->current() }}" method="GET" class="col-md-6">

                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="{{ \App\CPU\translate('Search') }} {{ \App\CPU\translate('Pharmacies') }}"
                                    aria-label="Search orders" value="{{ $search }}" required>
                                <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Search') }}</button>
                            </div>

                        </form>

                        <div class="col-md-6 d-flex justify-content-right">
                            <a class="text-body mr-3" target="_blank" href={{route('admin.pharmacy.exports-pharmacies')}}>
                                <i class="tio-print mr-1"></i> {{\App\CPU\translate('Export')}} {{\App\CPU\translate('excel')}}
                            </a>
                        </div>
                        <!-- End Search -->


                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" style="width: 100px">
                                            {{ \App\CPU\translate('pharmacy') }} {{ \App\CPU\translate('ID') }}
                                        </th>
                                        <th scope="col">{{ \App\CPU\translate('pharamacy_name') }}</th>
                                        <th scope="col">{{ \App\CPU\translate('pharamacy_user_type') }}</th>
                                        <th scope="col">{{ \App\CPU\translate('pharamacy_city') }}</th>
                                        <th scope="col">{{ \App\CPU\translate('pharamacy_region') }}</th>
                                        @if ($pending != false)
                                            <th scope="col">{{ \App\CPU\translate('confirmation') }}</th>
                                        @else
                                            <th scope="col">{{ \App\CPU\translate('VIP') }}</th>
                                        @endif
                                        <th scope="col" style="width: 100px" class="text-center">
                                            {{ \App\CPU\translate('action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody >

                                    @foreach ($pharmacies as $p)
                                        <tr style="height: 80px !important;">
                                            <td>{{ $p->pharmacy->id }}</td>
                                            <td class="table-column-pl-0">
                                                <a href="{{route('admin.customer.view',[$p['id']])}}">
                                                    {{\Illuminate\Support\Str::limit($p['f_name']." ".$p['l_name'],20)}}
                                                </a>
                                            </td>

                                            <td>{{ $p->pharmacy->user_type_id }}</td>
                                            <td>{{ $p->pharmacy->city }}</td>
                                            <td>{{ $p->pharmacy->region }}</td>
                                            @if ($pending != false)
                                            <td>
                                                <label class="toggle-switch toggle-switch-sm">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                        onclick="location.href='{{ route('admin.pharmacy.activation', [$p->id, $p->is_active ? 0 : 1]) }}'"
                                                        class="toggle-switch-input"
                                                        {{ $p->is_active ? 'checked' : '' }}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </td>
                                            @else
                                            <td>
                                                <label class="toggle-switch toggle-switch-sm">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                        onclick="location.href='{{ route('admin.pharmacy.vip', [$p->pharmacy->id, $p->pharmacy->vip ? 0 : 1]) }}'"
                                                        class="toggle-switch-input"
                                                        {{ $p->pharmacy->vip ? 'checked' : '' }}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </td>
                                            @endif

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="tio-settings"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                        <a class="dropdown-item" href={{route('admin.pharmacy.export',[$p['id']])}}>
                                                            <i class="tio-visible"></i> {{\App\CPU\translate('Export')}}
                                                        </a>
                                                        <a class="dropdown-item" href="{{route('admin.customer.edit',[$p['id']])}}">
                                                            <i class="tio-visible"></i> {{\App\CPU\translate('Edit')}}
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:"
                                                        onclick="form_alert('customer-{{$p['id']}}','Want to delete this customer ?')">
                                                            <i class="tio-delete"></i> {{\App\CPU\translate('delete')}}
                                                        </a>
                                                        <form action="{{route('admin.customer.delete',[$p['id']])}}"
                                                              method="post" id="customer-{{$p['id']}}">
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
                        {{ $pharmacies->links() }}
                    </div>
                    @if (\App\User::where('is_active', 1)->count() == 0)
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
        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete_this_pharmacy') }}?',
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
                        url: "{{ route('admin.pharmacy.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                '{{ \App\CPU\translate('pharmacy_deleted_successfully') }}'
                            );
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
