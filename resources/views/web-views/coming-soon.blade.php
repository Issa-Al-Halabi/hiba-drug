<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('public/css/coming.css') }}" rel="stylesheet">
    <title>Hiba Store</title>
</head>
<body>
    <div class="background">
        <div class="waiting">
            @php($e_commerce_logo=\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
            <a class="d-flex justify-content-center mb-5" href="javascript:">
                <img class="z-index-2" src="{{asset("storage/app/public/company/".$e_commerce_logo)}}" alt="Logo"
                onerror="this.src='{{asset('public/assets/back-end/img/900x400/2022-09-06-6317208618c8b.png')}}'"
                     style="width: 13rem;">
            </a>
            <p class="name_store">HIBA STORE</p>
            <p class="wating_title">Were Coming Soon</p>
            <span class="waiting_desc">Website coming soon, wait for us!</span>
            <img class="waiting_img" src="{{ asset('public/images/waiting.svg') }}" alt="tag">
        </div>
    </div>
</body>
</html>
