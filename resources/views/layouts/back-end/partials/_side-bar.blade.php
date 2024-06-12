@php
    $user = auth('admin')->user();
@endphp

@if(!$user)
    <script>
        window.location.href = "{{ route('admin.auth.login') }}";
    </script>
@else
<style>
    .navbar-vertical .nav-link {
        color: #041562;
        font-weight: bold;
    }

    .navbar .nav-link:hover {
        color: #041562;
    }

    .navbar .active>.nav-link,
    .navbar .nav-link.active,
    .navbar .nav-link.show,
    .navbar .show>.nav-link {
        color: #F14A16;
    }

    .navbar-vertical .active .nav-indicator-icon,
    .navbar-vertical .nav-link:hover .nav-indicator-icon,
    .navbar-vertical .show>.nav-link>.nav-indicator-icon {
        color: #F14A16;
    }

    .nav-subtitle {
        display: block;
        color: #041562;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03125rem;
    }

    .side-logo {
        background-color: #ffffff;
    }

    .nav-sub {
        background-color: #ffffff !important;
    }

    .nav-indicator-icon {
        margin-left: {
                {
                Session: :get('direction')==='rtl'? '6px': ''
            }
        }

        ;
    }

</style>


<div id="sidebarMain" class="d-none">
    <aside style="background: #ffffff!important; text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};" class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <!-- Logo -->
                    @php($e_commerce_logo = \App\Model\BusinessSetting::where(['type' =>
                    'company_web_logo'])->first()->value)
                    <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}" aria-label="Front">
                        <img style="max-height: 114px" onerror="this.src='{{ asset('public/assets/back-end/img/900x400/2022-09-06-6317208618c8b.png') }}'" class="navbar-brand-logo-mini for-web-logo" src="{{ asset("
                            storage/app/public/company/$e_commerce_logo") }}" alt="Logo">
                    </a>
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content mt-2">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('Dashboard') }}
                                </span>
                            </a>
                        </li>

                        <!-- End Dashboards -->
                        <!-- POS -->
                        @if (\App\CPU\Helpers::module_permission_check('pos_management'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ \App\CPU\translate('pos') }}
                                {{ \App\CPU\translate('system') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/pos/*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('POS') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/pos/*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/pos/') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pos.index') }}" title="{{ \App\CPU\translate('pos') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('pos') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/pos/orders') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pos.orders') }}" title="{{ \App\CPU\translate('orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('orders') }}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{ \App\Model\Order::where(['seller_is' =>
                                                'admin'])->where('order_type', 'POS')->where(['order_status' =>
                                                'delivered'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!-- End POS -->
                        @if (\App\CPU\Helpers::module_permission_check('order_management'))
                        <li class="nav-item {{ Request::is('admin/orders*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('order_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Order -->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/orders*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('orders') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/order*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/orders/list/all') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.orders.list', ['all']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('All') }}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type', 'default_type')->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/pending') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['pending']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('pending') }}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'pending'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/confirmed') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['confirmed']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('confirmed') }}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'confirmed'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/processing') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['processing']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('Processing') }}
                                            <span class="badge badge-warning badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'processing'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/out_for_delivery') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['out_for_delivery']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('out_for_delivery') }}
                                            <span class="badge badge-warning badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'out_for_delivery'])->count()
                                                }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/delivered') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['delivered']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('delivered') }}
                                            <span class="badge badge-success badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'delivered'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/returned') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['returned']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('returned') }}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'returned'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/failed') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['failed']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('failed') }}
                                            <span class="badge badge-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'failed'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/orders/list/canceled') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['canceled']) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('canceled') }}
                                            <span class="badge badge-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where('order_type',
                                                'default_type')->where(['order_status' => 'canceled'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!--order management ends-->

                        @if (\App\CPU\Helpers::module_permission_check('product_management'))
                        <li class="nav-item {{ Request::is('admin/brand*') || Request::is('admin/category*') || Request::is('admin/sub*') || Request::is('admin/attribute*') || Request::is('admin/product*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('product_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Pages -->


                        <!-- Store Managment -->
                        <!--Store section-->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/store*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('Stores') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/store*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/store/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.store.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.store.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('List') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End Store Managment -->

                        <!-- Store Pharmacy -->
                        <!--Pharmacy section-->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/pharmacy*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pharmacy nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('Pharmacies') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/pharmacy*') ? 'block' : 'none' }}">


                                <li class="nav-item {{ Request::is('admin/pharmacy/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pharmacy.list', ['Pending']) }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Pending') }}</span>
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\User::where('is_active',0)->where('user_type','pharmacist')->count()
                                            }}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/pharmacy/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pharmacy.list', ['Confirmed']) }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Confirmed') }}</span>
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\User::where('is_active',1)->where('user_type','pharmacist')->count()
                                            }}
                                        </span>
                                    </a>
                                </li>



                                <li class="nav-item {{ Request::is('admin/pharmacy/bulk-import') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pharmacy.bulk-import') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('bulk_import') }}</span>
                                    </a>
                                </li>



                            </ul>
                        </li>
                        <!-- End Pharmacy Managment -->


                        <!-- pharma brands Managment -->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/brand*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-apple-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('pharma brands') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/brand*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/brand/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.brand.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/brand/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.brand.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('List') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End pharma brands Managment -->




                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/category*') || Request::is('admin/sub*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-filter-list nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('categories') }}

                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/category*') || Request::is('admin/sub*') ? 'block' : '' }}">
                                <li class="nav-item {{ Request::is('admin/category/view') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.category.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('category') }}</span>
                                    </a>

                                </li>


                            </ul>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/product/list/in_house') || Request::is('admin/product/bulk-import') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="fa fa-plus-square nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="text-truncate">{{ \App\CPU\translate('InHouse Products') }}</span>
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/product/list/in_house') || Request::is('admin/product/stock-limit-list/in_house') || Request::is('admin/product/bulk-import') ? 'block' : '' }}">
                                <li class="nav-item {{ Request::is('admin/product/list/in_house') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.product.list', ['in_house', '']) }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Products') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/product/stock-limit-list/in_house') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.product.stock-limit-list', ['in_house', '']) }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('stock_limit_products')
                                            }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/product/bulk-import') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.product.bulk-import') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('bulk_import') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/product/bulk-export') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.product.bulk-export') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('bulk_export') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>




                        {{-- Bag Products --}}
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/bag/add-new') || Request::is('admin/bag/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="fa fa-medkit nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="text-truncate">{{ \App\CPU\translate('Bag Products') }}</span>
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/bag*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/bag/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.bag.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/product/bag/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.bag.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('List') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        @endif
                        <!--product management ends-->



                        @if (\App\CPU\Helpers::module_permission_check('marketing_section'))
                        <li class="nav-item {{ Request::is('admin/banner*') || Request::is('admin/coupon*') || Request::is('admin/notification*') || Request::is('admin/deal*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('Marketing_Section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/banner*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.banner.list') }}">
                                <i class="tio-photo-square-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('banners') }}</span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/coupon*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.coupon.add-new') }}">
                                <i class="tio-credit-cards nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('coupons') }}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/marketing*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.marketing.list') }}">
                                <i class="tio-smile nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('Most requested products') }}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.notification.add-new')}}" title="">
                                <i class="tio-notifications-on-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('push_notification')}}
                                </span>
                            </a>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/store*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('Points Section') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/store*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/store/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.points.points_create') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Add Products Points') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.points.bag_points_create') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Add Bags Points') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.points.points_list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Products Points') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.points.bag_points_list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Bags Points') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.points.order_points') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Set Order Points') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.points.pharmacies_points') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Pharmacies Points') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/store*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('Points store') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/store*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/store/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.reward-item.showProduct') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Products') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.reward-item.showBag') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Bags') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/store*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{
                                    \App\CPU\translate('Bonus Section') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/store*') ? 'block' : 'none' }}">

                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.bonuses.bonuses_list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Bonus') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/store/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.bonuses.create') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Create Bonus') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>


                        @endif

                        <!--marketing section ends here-->

                        @if (\App\CPU\Helpers::module_permission_check('business_section'))
                        <li class="nav-item {{ Request::is('admin/report/product-in-wishlist') || Request::is('admin/transaction/refund-list') || Request::is('admin/reviews*') || Request::is('admin/sellers/withdraw_list') || Request::is('admin/report/product-stock') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('business_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        {{-- seller withdraw --}}
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/stock/product-stock') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.stock.product-stock') }}">
                                <i class="tio-fullscreen-1-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('product') }} {{ \App\CPU\translate('stock') }}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/reviews*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.reviews.list') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('Customers Reviews') }}
                                </span>
                            </a>
                        </li>


                        {{-- bag --}}
                        <li class="navbar-vertical-aside-has-menu {{  Request::is('admin/city/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-photo-square-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="text-truncate">{{ \App\CPU\translate('Cities') }}</span>
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/city*') ? 'block' : 'none' }}">

                                <li class="nav-item {{ Request::is('admin/product/city-list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.city.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('List') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/stock/product-in-wishlist')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.stock.product-in-wishlist')}}">
                                <i class="tio-heart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('Favorite Products')}}
                                </span>
                            </a>
                        </li>

                        @endif
                        <!--business section ends here-->

                        @if (\App\CPU\Helpers::module_permission_check('user_section'))
                        <li class="nav-item {{ Request::is('admin/customer/list') || Request::is('admin/sellers/seller-add') || Request::is('admin/sellers/seller-list') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('user_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>



                        <li class="nav-item {{ Request::is('admin/customer/list') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.customer.list') }}">
                                <span class="tio-poi-user nav-icon"></span>
                                <span class="text-truncate">{{ \App\CPU\translate('customers') }} </span>
                            </a>
                        </li>
                        @endif
                        <!--user section ends here-->

                        @if (\App\CPU\Helpers::module_permission_check('support_section'))
                        <li class="nav-item {{ Request::is('admin/support-ticket*') || Request::is('admin/contact*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('support_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/contact*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.contact.list') }}">
                                <i class="tio-messages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('messages') }}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/support-ticket*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.support-ticket.view') }}">
                                <i class="tio-chat nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('support_ticket') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        <!--support section ends here-->

                        @if (\App\CPU\Helpers::module_permission_check('business_settings'))
                        <li class="nav-item {{ Request::is('admin/currency/view') || Request::is('admin/business-settings/refund*') || Request::is('admin/business-settings/language*') || Request::is('admin/business-settings/shipping-method*') || Request::is('admin/business-settings/payment-method') || Request::is('admin/business-settings/seller-settings*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ \App\CPU\translate('business_settings') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/language*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.language.index') }}" title="{{ \App\CPU\translate('languages') }}">
                                <i class="tio-book-opened nav-icon"></i>
                                <span class="text-truncate">{{ \App\CPU\translate('languages') }}</span>
                            </a>
                        </li>


                        {{-- <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/currency/view') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.currency.view') }}">
                                <i class="tio-dollar-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('currencies') }}
                                </span>
                            </a>
                        </li> --}}

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/sms-module')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.business-settings.sms-module')}}"
                                   title="{{\App\CPU\translate('sms')}} {{\App\CPU\translate('module')}}">
                                    <i class="tio-sms-active-outlined nav-icon"></i>
                                    <span
                                        class="text-truncate">{{\App\CPU\translate('sms')}} {{\App\CPU\translate('module')}}</span>
                                </a>
                        </li>

                        @endif

                        <!--business settings ends here-->


                        @if (\App\CPU\Helpers::module_permission_check('web_&_app_settings'))


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/web-config/db-index') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.business-settings.web-config.db-index') }}">
                                <i class="tio-cloud nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('clean_database') }}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/fcm-index') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.business-settings.fcm-index') }}">
                                <i class="tio-notifications-alert nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('notification') }}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pages-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('page_setup') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/business-settings/terms-condition') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.business-settings.terms-condition') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('terms_and_condition') }}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/business-settings/privacy-policy')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.business-settings.privacy-policy')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('privacy_policy')}}
                                        </span>
                                    </a>
                                </li>


                                <li class="nav-item {{ Request::is('admin/business-settings/about-us') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.business-settings.about-us') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('about_us') }}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/helpTopic/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.helpTopic.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('faq') }}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/file-manager*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.file-manager.index') }}">
                                <i class="tio-album nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('gallery') }}
                                </span>
                            </a>
                        </li>
                        @endif


                        <!--web & app settings ends here-->

                        @if (\App\CPU\Helpers::module_permission_check('report'))
                        <li class="nav-item {{ Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-product-sale') || Request::is('admin/report/order') || Request::is('admin/report/earning') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">
                                {{ \App\CPU\translate('Report') }}& {{ \App\CPU\translate('Analytics') }}
                            </small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        {{-- <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/earning') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.report.earning') }}">
                                <i class="tio-chart-pie-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('Earning') }} {{ \App\CPU\translate('Report') }}
                                </span>
                            </a>
                        </li> --}}
                        {{-- <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/order') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.report.order') }}">
                                <i class="tio-chart-bar-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('Order') }} {{ \App\CPU\translate('Report') }}
                                </span>
                            </a>
                        </li> --}}
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-product-sale') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-chart-bar-4 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('sale_report') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-product-sale') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/report/inhoue-product-sale') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.report.inhoue-product-sale') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CPU\translate('inhouse') }} {{ \App\CPU\translate('sale') }}
                                        </span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        @endif

                        <!--reporting and analysis ends here-->

                        @if (\App\CPU\Helpers::module_permission_check('employee_section'))
                        <li class="nav-item {{ Request::is('admin/employee*') || Request::is('admin/custom-role*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle">{{ \App\CPU\translate('employee_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/custom-role*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.custom-role.create') }}">
                                <i class="tio-incognito nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('employee_role') }}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::is('admin/employee/list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.employee.list') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="text-truncate">{{ \App\CPU\translate('employees') }}</span>
                            </a>
                        </li>


                        {{-- <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/employee*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('employees') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/employee*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/employee/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.employee.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/employee/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.employee.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('List') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li> --}}
                        @endif



                        @if (\App\CPU\Helpers::module_permission_check('delivery_man_management'))
                        <li class="nav-item {{ Request::is('admin/delivery-man*') || Request::is('admin/delivery-trip*') ? 'scroll-here' : '' }}">
                            <h4 class="nav-subtitle">{{ \App\CPU\translate('delivery_man_management') }}</h4>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="nav-item {{ Request::is('admin/delivery-man/list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.delivery-man.list') }}">
                                {{-- <span class="tio-circle nav-indicator-icon"></span> --}}
                                <i class="tio-user nav-icon"></i>
                                <span class="text-truncate">{{ \App\CPU\translate('delivery-man') }}</span>
                            </a>
                        </li>
                        {{-- <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man/add') || Request::is('admin/delivery-man/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('delivery-man') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/delivery-man/add') || Request::is('admin/delivery-man/list')  ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/delivery-man/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.delivery-man.add') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/delivery-man/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.delivery-man.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('List') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li> --}}


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-trip*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-car nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('orders_scheduling') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/delivery-trip*') ? 'block' : 'none' }}">

                                <li class="nav-item {{ Request::is('admin/delivery-trip/scheduling') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.delivery-trip.scheduling', ['pending']) }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('scheduling') }}</span>
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Model\Order::where('order_type',
                                            'default_type')->where(['order_status' => 'pending'])->count() }}
                                        </span>
                                    </a>
                                </li>

                            </ul>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man/reviews*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.delivery-man.delivery-reviews') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('delivery_reviews') }}</span>
                            </a>
                        </li>

                        @endif




                        {{-- sales_man_management --}}
                        @if (\App\CPU\Helpers::module_permission_check('sales_man_management'))
                        <li class="nav-item {{ Request::is('admin/sales-man/add') || Request::is('admin/sales-man/work-plans/list') || Request::is('admin/sales-man/list') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle">{{ \App\CPU\translate('sales_man_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/sales-man/add') || Request::is('admin/sales-man/work-plans/list') || Request::is('admin/sales-man/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="fa fa-user-md nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('salers') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/sales-man/add')  ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/sales-man/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sales-man.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('list') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/sales-man/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.sales-man.add') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('add_new') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/sales-man/work-plans/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sales-man.work-plans') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Work_plans') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/sales-man/pharmacies/assigned') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sales-man.pharmacies-assigned') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Pharmacies') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/sales-man/work-plan') || Request::is('admin/sales-man/orders/team') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-chart-bar-4 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('reports') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/sales-man/work-plan') || Request::is('admin/sales-man/orders/team') ? 'block' : 'none' }}">

                                <li class="nav-item {{ Request::is('admin/sales-man/work-plan/report') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sales-man.work-plans-report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Plans_Report') }}</span>
                                    </a>
                                </li>


                                <li class="nav-item {{ Request::is('admin/sales-man/orders/team') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sales-man.orders-report-teams') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Salers_Report') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/sales-report/team') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sales-report.team') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CPU\translate('Teams_Report') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/sales-man/reviews') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.sales-man.salers-reviews') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CPU\translate('Salers_reviews') }}</span>
                            </a>
                        </li>

                        @endif
                        {{-- End sales_man_management --}}

                        <li class="nav-item" style="padding-top: 50px">
                            <div class="nav-divider"></div>
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>




@endif
