@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('SMS Module Setup'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-sm-0">
                    <h1 class="page-header-title">{{\App\CPU\translate('sms')}} {{\App\CPU\translate('gateway')}} {{\App\CPU\translate('setup')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" style="padding-bottom: 20px">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}" style="padding: 20px">
                        <h5 class="text-center">{{\App\CPU\translate('mtn_sms')}}</h5>
                        <span class="badge badge-soft-info mb-3">NB : #OTP# will be replace with otp</span>
                        @php($config=\App\CPU\Helpers::get_business_settings('mtn_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.sms-module-update',['mtn_sms']):'javascript:'}}"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">{{\App\CPU\translate('mtn_sms')}}</label>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <input type="radio" name="status" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('active')}}</label>
                                <br>
                            </div>
                            <div class="form-group mb-2">
                                <input type="radio" name="status" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('inactive')}} </label>
                                <br>
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('api_key')}}</label><br>
                                <input type="text" class="form-control" name="api_key"
                                       value="{{env('APP_MODE')!='demo'?$config['api_key']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('from')}}</label><br>
                                <input type="text" class="form-control" name="from"
                                       value="{{env('APP_MODE')!='demo'?$config['from']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('user_name')}}</label><br>
                                <input type="text" class="form-control" name="user_name"
                                       value="{{env('APP_MODE')!='demo'?$config['user_name']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('password')}}</label><br>
                                <input type="text" class="form-control" name="password"
                                       value="{{env('APP_MODE')!='demo'?$config['password']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('code_number')}}</label><br>
                                <input type="text" class="form-control" name="code_number"
                                       value="{{env('APP_MODE')!='demo'?$config['code_number']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('otp_template')}}</label><br>
                                <input type="text" class="form-control" name="otp_template"
                                       value="{{env('APP_MODE')!='demo'?$config['otp_template']??"":''}}">
                            </div>

                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary mb-2">{{\App\CPU\translate('save')}}</button>
                        </form>
                    </div>
                </div>
            </div>

             <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}" style="padding: 20px">
                        <h5 class="text-center">{{\App\CPU\translate('syriatel_sms')}}</h5>
                        <span class="badge badge-soft-info mb-3">NB : #OTP# will be replace with otp</span>
                        @php($config=\App\CPU\Helpers::get_business_settings('syriatel_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.sms-module-update',['syriatel_sms']):'javascript:'}}"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">{{\App\CPU\translate('syriatel_sms')}}</label>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <input type="radio" name="status" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('active')}}</label>
                                <br>
                            </div>
                            <div class="form-group mb-2">
                                <input type="radio" name="status" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('inactive')}} </label>
                                <br>
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('api_key')}}</label><br>
                                <input type="text" class="form-control" name="api_key"
                                       value="{{env('APP_MODE')!='demo'?$config['api_key']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('from')}}</label><br>
                                <input type="text" class="form-control" name="from"
                                       value="{{env('APP_MODE')!='demo'?$config['from']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('user_name')}}</label><br>
                                <input type="text" class="form-control" name="user_name"
                                       value="{{env('APP_MODE')!='demo'?$config['user_name']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('password')}}</label><br>
                                <input type="text" class="form-control" name="password"
                                       value="{{env('APP_MODE')!='demo'?$config['password']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('code_number')}}</label><br>
                                <input type="text" class="form-control" name="code_number"
                                       value="{{env('APP_MODE')!='demo'?$config['code_number']??"":''}}">
                            </div>

                            <div class="form-group mb-2">
                                <label style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{\App\CPU\translate('otp_template')}}</label><br>
                                <input type="text" class="form-control" name="otp_template"
                                       value="{{env('APP_MODE')!='demo'?$config['otp_template']??"":''}}">
                            </div>



                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary mb-2">{{\App\CPU\translate('save')}}</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script_2')

@endpush
