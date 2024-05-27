@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Update scheduling'))



@push('css_or_js')
@endpush
<style>


.toggle-switch-label {
    background-color: #e23160 !important;
}

.toggle-switch-input:checked + .toggle-switch-label {
    background-color: #24af17 !important;
}

.toggle-switch-sm .toggle-switch-label {
    width: 5rem !important;
    height: 2.5rem !important;
}

.toggle-switch-sm .toggle-switch-indicator {
    width: 1.5rem !important;
    height: 1.5rem !important;
}

.toggle-switch-indicator {
    left: 0.5rem !important;
}



.toggle-switch-sm .toggle-switch-input:checked + .toggle-switch-label .toggle-switch-indicator {
    -webkit-transform: translate3d(2.35rem, 50%, 0) !important;
    transform: translate3d(2.35rem, 50%, 0) !important;
}




    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ed4c78 !important;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #387a32 !important;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #387a32 !important;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{ \App\CPU\translate('scheduling') }}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.delivery-trip.scheduling-update', [$order['id']]) }}" method="post">
                            @csrf

                            <div class="row">

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('Order_number') }}</label>
                                        <input type="text" name="order_number" value="{{ $order['order_number'] }}"
                                            class="form-control" placeholder="Ex : 017********" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('delivery_date') }}</label>
                                        <input type="date" value="{{ $order['delivery_date'] }}" name="delivery_date"
                                            class="form-control" placeholder="Last Name" required>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('Detection_number') }}</label>
                                        <input type="text" name="Detection_number"
                                            value="{{ $order['Detection_number'] }}" class="form-control"
                                            placeholder="Ex : 017********" required>
                                    </div>
                                </div>


                                <div class="col-md-6 col-12">

                                    <label class="input-label mt-1"
                                        for="exampleFormControlInput1">{{ \App\CPU\translate('scheduling') }}</label>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input"
                                            onclick="location.href='{{ route('admin.delivery-trip.scheduling-change', [$order['id'],$order['scheduling'] ? 0 : 1]) }}'"
                                            class="toggle-switch-input" {{ $order['scheduling']? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>


                                </div>


                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkbox = document.querySelector('input[type="checkbox"]');

        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                // do this
                var order_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/changeScheduling',
                    data: {
                        'status': true,
                        'order_id': order_id
                    },
                    success: function(data) {
                        console.log(data.success)
                    }
                });
            } else {
                // do that
                var order_id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/changeScheduling',
                    data: {
                        'status': false,
                        'order_id': order_id
                    },
                    success: function(data) {
                        console.log(data.success)
                    }
                });
            }
        });
    });

</script> --}}
