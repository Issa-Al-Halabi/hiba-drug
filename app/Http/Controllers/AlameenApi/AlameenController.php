<?php

namespace App\Http\Controllers\AlameenApi;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Brand;
use App\Model\Store;
use App\Model\Group;
use App\Model\City;
use App\Model\Area;
use App\Http\Traits\GeneralTrait;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\BrandsRequest;
use App\Http\Requests\PharmicesRequest;
use App\Http\Requests\StoresRequest;
use App\Model\UserImportExcel;
use App\Model\OrderAlameen;
use Illuminate\Support\Str;
use App\CPU\ImageManager;
use Illuminate\Support\Facades\DB;

class AlameenController extends Controller
{
    use GeneralTrait;
    private $normal_offer;
    private $q_normal_offer;
    private $featured_offer;
    private $q_featured_offer;
    private $l_name;
    private $f_name;
    //Done
    function __construct()
    {
        $this->normal_offer = 0;
        $this->q_normal_offer = 0;
        $this->featured_offer = 0;
        $this->q_featured_offer = 0;
        $this->l_name = "";
        $this->f_name = "";
    }

      public function StoreProducts(ProductRequest $request)
    {
        try {
            foreach ($request->products as $product) {
                $this->normal_offer = 0;
                $this->q_normal_offer = 0;
                $this->featured_offer = 0;
                $this->q_featured_offer = 0;

                $store_id = $this->checkIsStoreFound($product['store_id']);
                $brand_id = $this->checkIsBrandFound($product['brand_id']);
                // if ($store_id == 0)
                //     return $this->returnError("رمز المستودع غير مسجل يرجى إدخال سجلات المستودعات المفقودة");
                // if ($brand_id == 0)
                //     return $this->returnError("رمز المجموعة غير مسجل يرجى إدخال سجلات المجموعات المفقودة ");

                if ($brand_id != 0 && $store_id != 0) {
                    $category = [];
                    array_push($category, [
                        'id' => 9999999,
                        'position' => 10,
                    ]);
                    if (isset($product['normal_offer']) && isset($product['q_normal_offer'])) {
                        $this->normal_offer = $product['normal_offer'];
                        $this->q_normal_offer = $product['q_normal_offer'];
                    }
                    if (isset($product['featured_offer']) && isset($product['q_featured_offer'])) {
                        $this->featured_offer = $product['featured_offer'];
                        $this->q_featured_offer = $product['q_featured_offer'];
                    }
                    $data = [];
                    $productOld = Product::where('num_id', $product['num_id'])->get()->first();
                    if (isset($productOld)) {
                        $productOld->unit_price = $product['unit_price'];
                        $productOld->name = $product['name'];
                        $productOld->purchase_price = $product['purchase_price'];
                        $productOld->current_stock =  $product['quantity'] ?? 0;
                        $productOld->details = $product['notes'] ?? "";
                        $productOld->scientific_formula = $product['Scientific_formula'] ?? "";
                        $productOld->q_normal_offer =  $this->q_normal_offer;
                        $productOld->normal_offer = $this->normal_offer;
                        $productOld->q_featured_offer = $this->q_featured_offer;
                        $productOld->featured_offer = $this->featured_offer;
                        $productOld->expiry_date =  $product['expiry_date'] ?? 0000 - 00 - 00;
                        $productOld->demand_limit =  $product['demand_limit'] ?? 0;
                        $productOld->store_id = $store_id;
                        $productOld->brand_id = $brand_id;
                        $productOld->featured = 0;
                        $productOld->save();
                    } else {
                        array_push($data, [
                            'num_id' => $product['num_id'],
                            'name' => $product['name'],
                            'unit_price' => $product['unit_price'],
                            'purchase_price' => $product['purchase_price'],
                            'current_stock' => $product['quantity'] ?? 0,
                            'details' =>  $product['notes'] ?? "",
                            'scientific_formula' => $product['Scientific_formula'] ?? "",
                            'q_normal_offer' => $this->q_normal_offer,
                            'q_featured_offer' => $this->q_featured_offer,
                            'normal_offer' => $this->normal_offer,
                            'featured_offer' => $this->featured_offer,
                            'expiry_date' => $product['expiry_date'] ?? 0000 - 00 - 00,
                            'production_date' => 0000 - 00 - 00,
                            'demand_limit' => $product['demand_limit'] ?? 0,
                            'brand_id' => $brand_id,
                            'store_id' => $store_id,

                            //By defult
                            'unit' => "pc",
                            'category_ids' => json_encode($category),
                            'refundable' => false,
                            'video_provider' => 'youtube',
                            'thumbnail' => 'def.png',
                            'images' => json_encode(['def.png']),
                            'slug' => Str::slug($product['name'], '-') . '-' . Str::random(6),
                            'status' => 1,
                            'request_status' => 1,
                            'colors' => json_encode([]),
                            'attributes' => json_encode([]),
                            'choice_options' => json_encode([]),
                            'variation' => json_encode([]),
                            'featured_status' => 1,
                            'added_by' => 'admin',
                            'user_id' => 1,
                            'featured' => 0,
                        ]);
                    }

                    if (count($data) > 0) {
                        DB::table('products')->insert($data);
                    }
                }
            }
            return $this->returnSuccessMessage(' products details stored successfully');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    //Done
    public function StoreBrands(BrandsRequest $request)
    {
        try {
            foreach ($request->brands as $brand) {
                $brandActive = Brand::where('id', $brand['brand_id'])->get()->first();
                if (isset($brandActive)) {
                    $brandActive->name = $brand['brand_name'];
                    $brandActive->status = 1;
                    $brandActive->save();
                } else {
                    $b = new Brand();
                    $b->id = $brand['brand_id'];
                    $b->name = $brand['brand_name'];
                    if ($request->file('brand_image')) {
                        $b->image = ImageManager::upload('brand/', 'png', $request->file('brand_image'));
                    }
                    $b->status = 1;
                    $b->save();
                }
            }
            return $this->returnSuccessMessage(' brands details stored successfully');
        } catch (\Exception $e) {
            return $this->returnError($e);
        }
    }

    //Done
    public function SaveStores(StoresRequest $request)
    {
        try {
            foreach ($request->stores as $store) {

                $storeActive = Store::where('id', $store['store_id'])->get()->first();
                if (isset($storeActive)) {
                    $storeActive->store_name = $store['store_name'];
                    $storeActive->store_status = 1;
                    $storeActive->save();
                } else {
                    $s = new Store();
                    $s->id = $store['store_id'];
                    $s->store_name = $store['store_name'];
                    if ($request->file('store_image')) {
                        $s->store_image = ImageManager::upload('store/', 'png', $request->file('store_image'));
                    }
                    $s->store_status = 1;
                    $s->save();
                }
            }
            return $this->returnSuccessMessage('stores details stored successfully');
        } catch (\Exception $e) {
            return $this->returnError($e);
        }
    }

    //Done
    public function SavePharmices(PharmicesRequest $request)
    {
        try {

            $data = [];

            foreach ($request->Pharmacies as $pharmacy) {


                $city_id = $this->compare_city($pharmacy['city']);
                $group_id = $this->compare_group($pharmacy['group'], $city_id);
                $area_id = $this->compare_area($pharmacy['region'], $group_id);
                $is_active = $this->compare_active($pharmacy['is_active']);

                $user = UserImportExcel::where('num_id', '=', $pharmacy['num_id'])->get()->first();

                $this->l_name = "";
                $this->f_name = "";
                if (isset($pharmacy['f_name'])) {
                    $this->f_name = $pharmacy['f_name'];
                }
                if (isset($pharmacy['l_name'])) {
                    $this->l_name = $pharmacy['l_name'];
                }

                if (isset($user)) {
                    $user->num_id = $pharmacy['num_id'];
                    $user->card_number = $pharmacy['card_number'];
                    $user->pharmacy_name = $pharmacy['name'];
                    $user->f_name = $this->f_name;
                    $user->l_name = $this->l_name;
                    $user->land_number = $pharmacy['land_number'];
                    $user->phone2 = $pharmacy['phone'];
                    $user->phone1 = $pharmacy['phone'];
                    $user->city_id = $city_id;
                    $user->group_id = $group_id;
                    $user->area_id = $area_id;
                    $user->street_address = $pharmacy['address'];
                    $user->is_active = $is_active;
                    $user->save();
                } else {
                    array_push($data, [
                        'num_id' => $pharmacy['num_id'],
                        'f_name' => $pharmacy['f_name'],
                        'l_name' => $pharmacy['l_name'],
                        'card_number' => $pharmacy['card_number'],
                        'pharmacy_name' => $pharmacy['name'],
                        'land_number' => $pharmacy['land_number'],
                        'phone1' => $pharmacy['phone'],
                        'phone2' => $pharmacy['phone'],
                        'group_id' => $group_id,
                        'city_id' => $city_id,
                        'area_id' => $area_id,
                        'street_address' => $pharmacy['address'],
                        'is_active' => $is_active,
                    ]);
                }
            }

            DB::table('user_import_excel')->insert($data);
            return $this->returnSuccessMessage(' Pharmacies details stored successfully');
        } catch (\Exception $e) {
            return $this->returnError($e);
        }
    }

    public function getOrders()
    {
        $details = OrderAlameen::get()->all();
        foreach ($details as $p) {
            $p->product_details = json_decode($p['product_details'], true);
        }
        return $this->returnSuccessMessage($details);
    }

    public function separate_plus_left($string)
    {
        $stringNew = trim($string, " \t.");
        $p = explode("+", $stringNew);
        return $p[0];
    }

    public function separate_plus_right($string)
    {
        $stringNew = trim($string, " \t.");
        $p = explode("+", $stringNew);
        return $p[1];
    }

    public function  compare_city($city)
    {
        $name1 = trim($city, " \t.");
        $cityDB = City::where('city_name', '=', $name1)->get()->first();
        if (isset($cityDB)) {
            return $cityDB->id;
        } else {
            $cityNew = new City();
            $cityNew->city_name = $city;
            $cityNew->city_status = 1;
            $cityNew->save();
            return $cityNew->id;
        }
    }

    public function  compare_group($group, $cityId)
    {
        $name1 = trim($group, " \t.");
        $groupDB = Group::where('group_name', '=', $name1)->get()->first();
        if (isset($groupDB)) {
            return $groupDB->id;
        } else {
            $groupNew = new Group();
            $groupNew->group_name = $group;
            $groupNew->group_status = 1;
            $groupNew->city_id = $cityId;
            $groupNew->save();
            return $groupNew->id;
        }
    }

    public function  compare_area($area, $groupId)
    {
        $name1 = trim($area, " \t.");
        $areaDB = Area::where('area_name', '=', $name1)->get()->first();
        if (isset($areaDB)) {
            return $areaDB->id;
        } else {
            $areaNew = new Area();
            $areaNew->area_name = $area;
            $areaNew->area_status = 1;
            $areaNew->group_id = $groupId;
            $areaNew->save();
            return $areaNew->id;
        }
    }

    public function  compare_active($active)
    {
        // Declaration of strings
        $name1 = $active;
        $name1 = trim($name1, " \t.");
        // Use strcmp() function
        if (strcmp($name1, "مغلق") == 0 || strcmp($name1, "خارج الخدمة") == 0 || strcmp($name1, "خارج الخدمه") == 0) {
            return 0;
        } else {
            return 1;
        }
    }


    public function  checkIsStoreFound($storeId)
    {
        if (isset($storeId)) {
            $store = Store::where('id', '=', $storeId)->get()->first();
            if (isset($store)) return $store->id;
        }
        return 0;
    }

    public function  checkIsBrandFound($brandId)
    {
        if (isset($brandId)) {
            $brand = Brand::where('id', $brandId)->get()->first();
            if (isset($brand)) return $brand->id;
        }
        return 0;
    }
}
