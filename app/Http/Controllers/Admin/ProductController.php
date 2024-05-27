<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\BaseController;
use App\Model\Brand;
use App\Model\Bag;
use App\Model\Marketing;
use App\Model\Category;
use App\Model\Banner;
use App\Model\Color;
use App\Model\BagProduct;
use App\Model\DealOfTheDay;
use App\Model\FlashDealProduct;
use App\Model\Product;
use App\Model\Store;
use App\Model\Review;
use App\Model\Translation;
use App\Model\Wishlist;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;
use App\Model\Cart;
use App\CPU\ProductManager;
use Doctrine\Common\Collections\Collection;
use DateTime;
use DateTimeZone;


class ProductController extends BaseController
{
    public function add_new()
    {
        $cat = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        $st = Store::orderBY('store_name', 'ASC')->get();
        return view('admin-views.product.add-new', compact('cat', 'br', 'st'));
    }

    public function featured_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->featured = ($product['featured'] == 0 || $product['featured'] == null) ? 1 : 0;
        $product->save();
        $data = $request->status;
        return response()->json($data);
    }

    public function approve_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = ($product['request_status'] == 0) ? 1 : 0;
        $product->save();
        return redirect()->route('admin.product.list', ['seller', 'status' => $product['request_status']]);
    }

    public function deny(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = 2;
        $product->denied_note = $request->denied_note;
        $product->save();
        return redirect()->route('admin.product.list', ['seller', 'status' => 2]);
    }

    public function view($id)
    {
        $product = Product::with(['reviews'])->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $id])->paginate(Helpers::pagination_limit());
        $brand = Brand::where(['id' => $product->brand_id])->first();
        $store = Store::where(['id' => $product->store_id])->first();
        return view('admin-views.product.view', compact('product', 'reviews', 'brand', 'store'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'brand_id' => 'required',
            'store_id' => 'required',
            'unit' => 'required',
            'images' => 'required',
            'image' => 'required',
            'tax' => 'min:0',
            'unit_price' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:1',
            'discount' => 'required|gt:-1',


            'demand_limit' => 'required|gt:-1|numeric',
            'expiry_date' => 'required|date',
            //'production_date' => 'required|date',
            'q_featured_offer' => 'required|numeric',
            'featured_offer' => 'required|numeric',
            'q_normal_offer' => 'required|numeric',
            'normal_offer' => 'required|numeric',
            'scientific_formula' => 'required',
            'num_id' => 'required'
        ], [
            'images.required' => 'Product images is required!',
            'image.required' => 'Product thumbnail is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
            'unit.required' => 'Unit  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['unit_price'] <= $dis) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'unit_price',
                    'Discount can not be more or equal to the price!'
                );
            });
        }
        $p = new Product();
        $p->featured = 0;
        $p->user_id = auth('admin')->id();
        $p->added_by = "admin";
        $p->name = $request->name[array_search('en', $request->lang)];
        $p->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);
        $p->store_id = $request->store_id;

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        $p->category_ids = json_encode($category);
        $p->brand_id = $request->brand_id;
        $p->unit = $request->unit;
        $p->details = $request->description[array_search('en', $request->lang)];

        $p->num_id = $request->num_id;
        $p->demand_limit = $request->demand_limit;
        $p->expiry_date = $request->expiry_date;
        $p->production_date = 2022 - 11 - 02;
        $p->q_featured_offer = $request->q_featured_offer;
        $p->featured_offer = $request->featured_offer;
        $p->normal_offer = $request->normal_offer;
        $p->q_normal_offer = $request->q_normal_offer;
        $p->scientific_formula = $request->scientific_formula;
        //add colors
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $p->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $p->colors = json_encode($colors);
        }



        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', $request[$str]));
                array_push($choice_options, $item);
            }
        }
        $p->choice_options = json_encode($choice_options);
        //combinations start
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options

        $combinations = Helpers::combinations($options);

        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
                $stock_count += $item['qty'];
            }
        } else {
            $stock_count = (int)$request['current_stock'];
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $p->variation = json_encode($variations);
        $p->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $p->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $p->tax_type = "flat";
        $p->tax = 0;
        $p->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->attributes = json_encode($request->choice_attributes);
        $p->current_stock = abs($stock_count);

        $p->video_provider = 'youtube';
        $p->video_url = $request->video_link;
        $p->request_status = 1;
        $p->shipping_cost = BackEndHelper::currency_to_usd($request->shipping_cost);
        $p->multiply_qty = $request->multiplyQTY == 'on' ? 1 : 0;

        if ($request->ajax()) {
            return response()->json([], 200);
        } else {


            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $p->images = json_encode($product_images);
            }
            $p->thumbnail = ImageManager::upload('product/thumbnail/', 'png', $request->image);
            $p->meta_title = $request->meta_title;
            $p->meta_description = $request->meta_description;
            $p->meta_image = ImageManager::upload('product/meta/', 'png', $request->meta_image);
            $p->save();
            //Add Translation (Table translations)
            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }


                if ($request->description[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
            Translation::insert($data);
            //End Translation

            Toastr::success(translate('Product added successfully!'));
            return redirect()->route('admin.product.list', ['in_house']);
        }
    }

    function list(Request $request, $type)
    {
        $query_param = [];
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Product::where(['added_by' => 'admin']);
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.list', compact('pro', 'search', 'request_status'));
    }

    public function updated_product_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = Product::where(['added_by' => 'seller'])
                ->where('is_shipping_cost_updated', 0)
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('name', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('is_shipping_cost_updated', 0);
        }
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.product.updated-product-list', compact('pro', 'search'));
    }

    public function stock_limit_list(Request $request, $type)
    {
        $stock_limit = Helpers::get_business_settings('stock_limit');
        $sort_oqrderQty = $request['sort_oqrderQty'];
        $query_param = $request->all();
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Product::where(['added_by' => 'admin']);
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];

        $pro = $pro->withCount('order_details')->when($request->sort_oqrderQty == 'quantity_asc', function ($q) use ($request) {
            return $q->orderBy('current_stock', 'asc');
        })
            ->when($request->sort_oqrderQty == 'quantity_desc', function ($q) use ($request) {
                return $q->orderBy('current_stock', 'desc');
            })
            ->when($request->sort_oqrderQty == 'order_asc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'asc');
            })
            ->when($request->sort_oqrderQty == 'order_desc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'desc');
            })
            ->when($request->sort_oqrderQty == 'default', function ($q) use ($request) {
                return $q->orderBy('id');
            })->where('current_stock', '<', $stock_limit);

        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.stock-limit-list', compact('pro', 'search', 'request_status', 'sort_oqrderQty'));
    }

    public function update_quantity(Request $request)
    {
        $variations = [];
        $stock_count = $request['current_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $product = Product::find($request['product_id']);
        if ($stock_count >= 0) {
            $product->current_stock = $stock_count;
            $product->variation = json_encode($variations);
            $product->save();
            Toastr::success(\App\CPU\translate('product_quantity_updated_successfully!'));
            return back();
        } else {
            Toastr::warning(\App\CPU\translate('product_quantity_can_not_be_less_than_0_!'));
            return back();
        }
    }

    public function status_update(Request $request)
    {
        $product = Product::where(['id' => $request['id']])->first();
        $success = 1;
        if ($request['status'] == 1) {
            if ($product->added_by == 'seller' && $product->request_status == 0) {
                $success = 0;
            } else {
                $product->status = $request['status'];
            }
        } else {
            $product->status = $request['status'];
        }
        $product->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }


    public function pure_price_status_update(Request $request)
    {
        $product = Product::where(['id' => $request['id']])->first();
        $success = 1;
        if ($request['status'] == 1) {
            if ($product->added_by == 'seller' && $product->request_status == 0) {
                $success = 0;
            } else {
                $product->pure_price_status = $request['status'];
            }
        } else {
            $product->pure_price_status = $request['status'];
        }
        $product->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }


    public function updated_shipping(Request $request)
    {

        $product = Product::where(['id' => $request['product_id']])->first();
        if ($request->status == 1) {
            $product->shipping_cost = $product->temp_shipping_cost;
            $product->is_shipping_cost_updated = $request->status;
        } else {
            $product->is_shipping_cost_updated = $request->status;
        }

        $product->save();
        return response()->json([], 200);
    }

    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'select_tag' => $res,
        ]);
    }

    public function sku_combination(Request $request)
    {
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name[array_search('en', $request->lang)];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = Helpers::combinations($options);
        return response()->json([
            'view' => view('admin-views.product.partials._sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'))->render(),
        ]);
    }

    public function get_variations(Request $request)
    {
        $product = Product::find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    public function edit($id)
    {
        $product = Product::withoutGlobalScopes()->with('translations')->find($id);
        // $product->images=json_decode($product->images,true);
        $product_category = json_decode($product->category_ids);
        $product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        $st = Store::orderBY('store_name', 'ASC')->get();

        return view('admin-views.product.edit', compact('categories', 'st', 'br', 'product', 'product_category'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'unit' => 'required',
            //'tax' => 'required|min:0',
            'unit_price' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:1',
            'discount' => 'required|gt:-1',
            //'shipping_cost' => 'required|gt:-1',

            'demand_limit' => 'required|gt:-1|numeric',
            'expiry_date' => 'required|date',
            // 'production_date' => 'required|date',
            'q_featured_offer' => 'required|numeric',
            'featured_offer' => 'required|numeric',
            'q_normal_offer' => 'required|numeric',
            'normal_offer' => 'required|numeric',
            'scientific_formula' => 'required',
            'store_id' => 'required',
            'num_id' => 'required'


        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
            'unit.required' => 'Unit  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['unit_price'] <= $dis) {
            $validator->after(function ($validator) {
                $validator->errors()->add('unit_price', 'Discount can not be more or equal to the price!');
            });
        }

        $product = Product::find($id);
        if ($product->featured == null)
            $product->featured = 0;

        $product->name = $request->name[array_search('en', $request->lang)];
        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }
        $product->category_ids = json_encode($category);
        $product->brand_id = $request->brand_id;
        $product->store_id = $request->store_id;
        $product->unit = $request->unit;
        $product->details = $request->description[array_search('en', $request->lang)];


        $product->demand_limit = $request->demand_limit;
        $product->expiry_date = $request->expiry_date;
        $product->q_featured_offer = $request->q_featured_offer;
        $product->featured_offer = $request->featured_offer;
        $product->normal_offer = $request->normal_offer;
        $product->q_normal_offer = $request->q_normal_offer;
        $product->scientific_formula = $request->scientific_formula;
        $product->num_id = $request->num_id;

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $product->colors = json_encode($colors);
        }
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', $request[$str]));
                array_push($choice_options, $item);
            }
        }
        $product->choice_options = json_encode($choice_options);
        $variations = [];
        //combinations start
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
                $stock_count += $item['qty'];
            }
        } else {
            $stock_count = (int)$request['current_stock'];
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)
                ->withInput();
        }

        //combinations end
        $product->variation = json_encode($variations);
        $product->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $product->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $product->tax = 0;
        // $product->tax = $request->tax == 'flat' ? BackEndHelper::currency_to_usd($request->tax) : $request->tax;
        // $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $product->attributes = json_encode($request->choice_attributes);
        $product->discount_type = $request->discount_type;
        $product->current_stock = abs($stock_count);

        $product->video_provider = 'youtube';
        $product->video_url = $request->video_link;
        if ($product->added_by == 'seller' && $product->request_status == 2) {
            $product->request_status = 1;
        }

        $product->shipping_cost = BackEndHelper::currency_to_usd($request->shipping_cost);
        $product->multiply_qty = $request->multiplyQTY == 'on' ? 1 : 0;
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {

                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $product->images = json_encode($product_images);
            } else {
                $product_images = json_decode($product->images);
            }

            if ($request->file('image')) {
                $product->thumbnail = ImageManager::update('product/thumbnail/', $product->thumbnail, 'png', $request->file('image'));
            }

            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            if ($request->file('meta_image')) {
                $product->meta_image = ImageManager::update('product/meta/', $product->meta_image, 'png', $request->file('meta_image'));
            }

            $product->save();

            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Model\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'name'
                        ],
                        ['value' => $request->name[$index]]
                    );
                }
                if ($request->description[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Model\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $request->description[$index]]
                    );
                }
            }
            Toastr::success('Product updated successfully.');
            return back();
        }
    }


    public function remove_image(Request $request)
    {
        ImageManager::delete('/product/' . $request['image']);
        $product = Product::find($request['id']);
        $array = [];

        $countImages = count(json_decode($product['images']));
        if ($countImages != 0) {
            if ($countImages < 2) {
                Toastr::warning('You cannot delete all images!');
                return back();
            }

            foreach (json_decode($product['images']) as $image) {
                if ($image != $request['name']) {
                    array_push($array, $image);
                }
            }
        }

        Product::where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success('Product image removed successfully!');
        return back();
    }


    public function delete($id)
    {
        $product = Product::find($id);
        $translation = Translation::where('translationable_type', 'App\Model\Product')
            ->where('translationable_id', $id);
        $translation->delete();

        Cart::where('product_id', $product->id)->delete();
        Wishlist::where('product_id', $product->id)->delete();
        Banner::where('resource_id', $product->id)->delete();
        ImageManager::delete('/product/thumbnail/' . $product['thumbnail']);
        $product->delete();
        FlashDealProduct::where(['product_id' => $id])->delete();
        DealOfTheDay::where(['product_id' => $id])->delete();
        $bags = BagProduct::where('product_id', '=', $id)->get();
        foreach ($bags as $bag) {
            $price = DB::table('products_bag')->where('bag_id', $bag->bag_id)->sum('product_total_price');
            $bag = Bag::findOrFail($bag->bag_id);
            $bag->total_price_offer = $price;
            $bag->save();
        }
        BagProduct::where('product_id', '=', $id)->delete();

        $prodMark = Marketing::where('item_id', '=', $id)->get()->first();
        if (isset($prodMark)) $prodMark->delete();
        ProductManager::remove_bounses($id);
        ProductManager::remove_points($id);

        Toastr::success('Product removed successfully!');
        return back();
    }

    public function bulk_import_index()
    {
        return view('admin-views.product.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        $countUpdate = 0;
        $data = [];
        $fields = [
            'المجموعة',
            'رمز المستودع',
            'المستودع',
            'رمز المجموعة',
            'رمز المادة ',
            'تاريخ الصلاحية',
            'اسم المادة',
            'السعر',
            'الكمية',
            'الملاحظات',
            'التركيبة العلمية',
            'العرض لل',
            'العرض مميز لل',
            'العرض',
            'العرض المميز',
            'حد الطلب',
        ];
        foreach ($collections as $collection) {

            for ($i = 0; $i < count($fields); $i++) {
                if (!isset($collection[$fields[$i]])) {
                    Toastr::error(' الحقل ' . $fields[$i] . ' غير موجود ');
                    return back();
                }
            }

            if ($this->checkFieldsAreValid($fields, $collection)) {
                $storeId = trim($collection['رمز المستودع'], " \t\n.");
                $store = Store::where('id', '=',$storeId)->get()->first();
                if (isset($store)) {
                    $store_id = $store->id;
                } else {
                    $NewStore = new Store();
                    $NewStore->id = $collection['رمز المستودع'];
                    $NewStore->store_name = $collection['المستودع'];
                    $NewStore->store_image = 'def.png';
                    $NewStore->store_status = 1;
                    $NewStore->save();
                    $store_id = $NewStore->id;
                }
                $brandId = trim($collection['رمز المجموعة'], " \t\n.");
                $brand = Brand::where('id', '=',$brandId)->get()->first();
                if (isset($brand)) {
                    $brand_id = $brand->id;
                } else {
                    $NewBrand = new Brand();
                    $NewBrand->id = $collection['رمز المجموعة'];
                    $NewBrand->name = $collection['المجموعة'];
                    $NewBrand->image = 'def.png';
                    $NewBrand->status = 1;
                    $NewBrand->save();
                    $brand_id = $NewBrand->id;
                }

                $category = [];
                array_push($category, [
                    'id' => 9999999,
                    'position' => 10,
                ]);

                $dateNew = $this->rev_date($collection['تاريخ الصلاحية']);
                $productId = trim($collection['رمز المادة '], " \t\n.");
                $product = Product::where('num_id', '=',$productId)->get()->first();
              if (isset($product)) {
                    $product->unit_price = $collection['السعر'];             
                    $product->current_stock = $collection['الكمية'];
                    $product->name = $collection['اسم المادة'];
                    $product->slug = Str::slug($collection['اسم المادة'], '-') . '-' . Str::random(6);
                    $product->details = $collection['الملاحظات'];
                    $product->scientific_formula = $collection['التركيبة العلمية'];
                    $product->q_normal_offer = $collection['العرض لل'];
                    $product->normal_offer = $collection['العرض'];
                    $product->q_normal_offer = $collection['العرض لل'];
                    $product->q_featured_offer = $collection['العرض مميز لل'];
                    $product->featured_offer = $collection['العرض المميز'];
                    $product->expiry_date = $dateNew;
                    $product->demand_limit = $collection['حد الطلب'];
                    $product->store_id = $store_id;
                    $product->brand_id = $brand_id;
                    $product->featured = 0;
                    $product->save();
                    $countUpdate++;
                } else {
                    array_push($data, [
                        'num_id' => $collection['رمز المادة '],
                        'brand_id' => $brand_id,
                        'name' => $collection['اسم المادة'],
                        'unit_price' => $collection['السعر'],
                        'current_stock' => $collection['الكمية'],
                        'details' => $collection['الملاحظات'],
                        'scientific_formula' => $collection['التركيبة العلمية'],
                        'q_normal_offer' => $collection['العرض لل'],
                        'q_featured_offer' => $collection['العرض مميز لل'],
                        'normal_offer' => $collection['العرض'],
                        'featured_offer' => $collection['العرض المميز'],
                        'demand_limit' => $collection['حد الطلب'],
                        'expiry_date' => $dateNew,

                        //By defult
                        'store_id' => $store_id,
                        'unit' => "pc",
                        'category_ids' => json_encode($category),
                        'refundable' => false,
                        'video_provider' => 'youtube',
                        'thumbnail' => 'def.png',
                        'images' => json_encode(['def.png']),
                        'slug' => Str::slug($collection['اسم المادة'], '-') . '-' . Str::random(6),
                        'status' => 1,
                        'request_status' => 1,
                        'colors' => json_encode([]),
                        'attributes' => json_encode([]),
                        'choice_options' => json_encode([]),
                        'variation' => json_encode([]),
                        'featured_status' => 1,
                        'featured' => 0,
                        'added_by' => 'admin',
                        'user_id' => 1,
                    ]);
                }
            }
        }

        if (count($data) > 0) {
            DB::table('products')->insert($data);
        }
        Toastr::success(count($data) . ' - Products imported successfully! and (' . $countUpdate . ') Products updated successfully!');
        return back();
    }

    //public function rev_date($date)
   // {
    //    $array = explode("-", $date);
    //    $rev = array_reverse($array);
    //    $date = implode("-", $rev);
    //    return $date;
   // }
      //new
    public function rev_date($date)
    {
        if ($date instanceof DateTime) {
            $date = $date->format('Y-m-d');
        }

        $array = explode("-", $date);
        $rev = array_reverse($array);
        $date = implode("-", $rev);
        
        return $date;
    }

    //new

    public function checkFieldsAreValid($fields, $collection)
    {
        for ($i = 0; $i < count($fields); $i++) {
            $fild = $collection[$fields[$i]];
            if (is_null($fild)) {
                return false;
            }
        }
        return true;
    }


    public function bulk_export_data()
    {
        $products = Product::where(['added_by' => 'admin'])->get();
        //export from product
        $storage = [];
        foreach ($products as $item) {
            $brand = Brand::where('id', '=', $item->brand_id)->get()->first();
            if (!isset($brand))
                $brand_name = "غير معرف";
            else
                $brand_name = $brand->name;

            $storage[] = [
                'رمز المادة' => $item->num_id,
                'المجموعة' => $brand_name,
                'اسم المادة' => $item->name,
                'الكمية' => $item->current_stock,
                'السعر' => $item->unit_price,
                'العرض لل' => $item->q_normal_offer,
                'العرض' => $item->normal_offer,
                'العرض مميز لل' => $item->q_featured_offer,
                'العرض المميز' => $item->featured_offer,
                'تاريخ الصلاحية' => $item->expiry_date,
                'الملاحظات' => $item->details,
                'التركيبة العلمية' => $item->scientific_formula,
                'حد الطلب' => $item->demand_limit,
            ];
        }
        return (new FastExcel($storage))->download('inhouse_products.xlsx');
    }

    public function bulk_import_data_purchase_price(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }
        try {
            $statusUpdate = 0;
            $fields = [
                'السعر',
                'رمز المادة',
            ];
            foreach ($collections as $collection) {
                for ($i = 0; $i < count($fields); $i++) {
                    if (!isset($collection[$fields[$i]])) {
                        Toastr::error(' الحقل ' . $fields[$i] . ' غير موجود ');
                        return back();
                    }
                }
                if($this->checkFieldsAreValid($fields, $collection))
                {
                    $product = Product::where('num_id', '=', $collection['رمز المادة'])->get()->first();
                    if (isset($product)) {
                        $product->purchase_price = $collection['السعر'];          //سعر العموم
                        $product->save();
                        $statusUpdate++;
                    }
                }
            }
            Toastr::success('(' . $statusUpdate . ') Products purchase price updated successfully!');
            return back();
        } catch (\Exception $exception) {
            Toastr::success('حدث خطأ ما يرجى التأكد من حقول الملف المدخل');
            return back();
        }
    }
}
