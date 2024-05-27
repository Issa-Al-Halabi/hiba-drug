<!-- Header -->
<div class="card-header">
    <h5 class="card-header-title">
        <i class="tio-company"></i> {{\App\CPU\translate('top_brands')}}
    </h5>
    <i class="tio-company" style="font-size: 45px"></i>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="row">
        @foreach($top_brands as $key=>$item)
           @php($brand=\App\Model\Brand::find($item['brand_id']))
            @if(isset($brand))
                <div class="col-6 col-md-4 mt-2"
                     onclick="location.href='{{route('admin.customer.view',[$item['brand_id']])}}'"
                     style="padding-left: 6px;padding-right: 6px;cursor: pointer">
                    <div class="grid-card" style="min-height: 170px">
                        <div class="label_1 row-center">
                            <div class="mx-1">{{\App\CPU\translate('orders')}} : </div>
                            <div>{{$item['count']}}</div>
                        </div>
                        <div class="text-center mt-3">
                            <img style="border-radius: 50%;width: 60px;height: 60px;border:2px solid #80808082;"
                                 onerror="this.src='{{asset('public/assets/back-end/img/160x160/img1.jpg')}}'"
                                 src="{{asset('storage/app/public/brand/'.$brand->image??'')}}">
                        </div>
                        <div class="text-center mt-2">
                            <span style="font-size: 10px">{{$brand->name??'Not exist'}}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
<!-- End Body -->
