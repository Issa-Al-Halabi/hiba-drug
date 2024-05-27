@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Pharmacies List'))
@section('content')

<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">{{ \App\CPU\translate('Pharmacy_Not_Assigned_list') }}
            <span style="color: rgb(252, 59, 10);">
                ({{ $pharmaciesNotAssigned->count()}})
            </span>
        </h1>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table id="pharmacies_table"  class="display nowrap" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ \App\CPU\translate('pharmacy') }} {{ \App\CPU\translate('ID') }}</th>
                                    <th>{{ \App\CPU\translate('pharamacy_name') }}</th>
                                    <th >{{ \App\CPU\translate('pharamacy_city') }}</th>
                                    <th>{{ \App\CPU\translate('pharamacy_region') }}</th>
                                    <th>{{ \App\CPU\translate('pharamacy_Address') }}</th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($pharmaciesNotAssigned as $pharmacy)
                                <tr style="height: 80px !important;" >
                                    <td>{{ $pharmacy->id }}</td>
                                    <td>{{ $pharmacy->name }}</td>
                                    <td>{{ $pharmacy->city }}</td>
                                    <td>{{ $pharmacy->region }}</td>
                                    <td>{{ $pharmacy->Address }}</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            @if ($pharmaciesNotAssigned->count() == 0)
            <div class="text-center p-4">
                <img class="mb-3" src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
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
    var lang = "{{ Session::get('direction') }}";
    if (lang === 'rtl')
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

</script>

@endpush
