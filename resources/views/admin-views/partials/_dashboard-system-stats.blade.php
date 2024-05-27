<div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
    <!-- Card -->
    <div class="card card-hover-shadow h-100" style="background: #FFFFFF">
        <div class="card-body">
            <div class="flex-between align-items-center mb-1">
                <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <h6 class="card-subtitle" style="color: #413d4b!important;">{{\App\CPU\translate('visitors')}}</h6>
                    <span class="card-title h2" style="color: #413d4b!important;">
                        {{$data['visitors']}}
                    </span>
                </div>
                <div class="mt-2">
                    <i class="tio-incognito nav-icon" style="font-size: 30px;color: #413d4b;"></i>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </div>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
    <!-- Card -->
    <div class="card card-hover-shadow h-100" style="background: #FFFFFF;">
        <div class="card-body">
            <div class="flex-between align-items-center mb-1">
                <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <h6 class="card-subtitle" style="color: #413d4b!important;">{{\App\CPU\translate('pharmacies')}}</h6>
                     <span class="card-title h2" style="color: #413d4b!important;">
                        {{$data['pharmacies']}}
                     </span>
                </div>

                <div class="mt-2">
                    <i class="tio-pharmacy" style="font-size: 30px;color: #413d4b"></i>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </div>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
    <!-- Card -->
    <div class="card card-hover-shadow h-100"  style="background: #FFFFFF">
        <div class="card-body">
            <div class="flex-between align-items-center gx-2 mb-1">
                <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <h6 class="card-subtitle" style="color: #413d4b!important;">{{\App\CPU\translate('Sales_Man')}}</h6>
                    <span class="card-title h2" style="color: #413d4b!important;">
                        {{$data['salers']}}
                    </span>
                </div>

                <div class="mt-2">
                    <i class="fa fa-user-md" style="font-size: 30px;color: #413d4b"></i>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </div>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
    <!-- Card -->
    <div class="card card-hover-shadow h-100"  style="background: #FFFFFFff">
        <div class="card-body">
            <div class="flex-between align-items-center gx-2 mb-1">
                <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <h6 class="card-subtitle" style="color: #413d4b!important;">{{\App\CPU\translate('Delivery_Man')}}</h6>
                    <span class="card-title h2" style="color: #413d4b!important;">
                        {{$data['deliveries']}}
                    </span>
                </div>

                <div class="mt-2">
                    <i class="tio-bike" style="font-size: 30px;color: #413d4b"></i>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </div>
    <!-- End Card -->
</div>

