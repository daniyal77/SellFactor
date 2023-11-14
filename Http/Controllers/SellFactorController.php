<?php

namespace Modules\SellFactor\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Personal\Entities\Personal;
use Modules\Product\Entities\Product;
use Modules\SellFactor\Entities\SellFactor;
use Modules\SellFactor\Entities\SellFactorDetail;
use Modules\SellFactor\Http\Requests\SaveSellFactor;
use Modules\Service\Entities\Service;
use Modules\Setting\Entities\Warehouse;
use Modules\WareHouse\Entities\FactorRemittance;
use Modules\WareHouse\Entities\NumberFactor;
use function __;
use function redirect;
use function view;


class SellFactorController extends Controller
{
    protected $dataCondition;

    /**
     * WareHouseController constructor.
     */
    public function __construct ()
    {
        $this->middleware('can:sales_invoice_insert')->only(['create', 'store']);
        $this->middleware('can:sales_invoice_edit')->only(['edit', 'update']);
        $this->middleware('can:sales_invoice_delete')->only(['draft']);
        $this->middleware(function ($request, $next) {
            $user_id = Auth::user()->id;
            $this->dataCondition = getActiveCompanyUserConditions($user_id);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function create (): Renderable
    {
        $warehouses = Warehouse::MyCompany($this->dataCondition)->get();
        $services = Service::MyCompany($this->dataCondition)->get();
        $personals = Personal::MyCompany($this->dataCondition)->get();
        $products = Product::MyCompany($this->dataCondition)->get();
        return view('sellfactor::create', compact('services', 'warehouses', 'personals', 'products'));
    }

    /**
     * @param saveSellFactor $request
     * @return RedirectResponse
     */
    public function store (SaveSellFactor $request): RedirectResponse
    {
        if (!$request->has('factor')) {
            return redirect()->back()->with('err', 'لطفا ابتدا محصولات فاکتور را مشخص نمایید');
        }
        $numberFactor = NumberFactor::MyCompany($this->dataCondition)->where('type', 'sell')->first();
        $minFactorBuyId = $numberFactor->numbers + 1 ?? 0;
        DB::transaction(function () use ($request, $numberFactor, $minFactorBuyId) {
            $numberFactor->increment('numbers');
            $factorId = $this->createdFactor($request, $minFactorBuyId);
            $this->createdDetail($request->factor, $factorId, $request->warehouse);
            JustDecrementPrice(Personal::class, $request->personal_id, $request->total_price, 'wallet');
        });
        if ($request->has('print')) {
            return redirect()->route('sell.show.factor', $minFactorBuyId);
        } else {
            return redirect()->route('sell.index')->with('suc', __('message.the_sales_invoice_was_created_successfully'));
        }

    }

    /**
     * @param $buy_factor
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit ($buy_factor): View|Factory|RedirectResponse|Application
    {
        $factor = SellFactor::MyCompany($this->dataCondition)->where('factor_id', $buy_factor)
            ->with('personal')->firstOrFail();
        if ($this->isReturnBack($factor)) {
            return redirect()->back()->with('info',
                __('message.the_transfer_is_confirmed_and_it_is_not_possible_to_edit_the_invoice'));
        }

        $personals = Personal::MyCompany($this->dataCondition)->get();
        $products = Product::MyCompany($this->dataCondition)->get();
        $warehouses = Warehouse::MyCompany($this->dataCondition)->get();
        return view('sellfactor::edit', compact('factor', 'warehouses', 'personals', 'products'));
    }

    /**
     * @param saveSellFactor $request
     * @param $factor_id
     * @return RedirectResponse
     */
    public function update (SaveSellFactor $request, $factor_id): RedirectResponse
    {
        $factorAction = SellFactor::MyCompany($this->dataCondition)->where('factor_id', $factor_id)->firstOrFail();
        if ($this->isReturnBack($factorAction)) {
            return redirect()->back()->with('info',
                __('message.the_transfer_is_confirmed_and_it_is_not_possible_to_edit_the_invoice'));
        }
        DB::transaction(function () use ($request, $factorAction) {
            if ($request->personal_id != $factorAction->personal_id) {
                JustIncrementPrice(Personal::class, $factorAction->personal_id, $factorAction->price, 'wallet');
                JustDecrementPrice(Personal::class, $request->personal_id, $request->total_price, 'wallet');
            }
            if ($request->personal_id == $factorAction->personal_id) {
                $oldPrice = $factorAction->price - $request->total_price;
                JustIncrementPrice(Personal::class, $request->personal_id, $oldPrice, 'wallet');
            }
            $factor = [
                'personal_id' => $request->personal_id,
                'action_date' => $request->action_date,
                'tax'         => $request->tax,
                'discount'    => $request->discount,
                'price'       => $request->total_price,
                'intro'       => $request->intro,
            ];
            $factorAction->update($factor);
            $newProductFactor = [];
            $newProductFactorRe = [];

            $oldProductFactor = SellFactorDetail::where('factor_id', $factorAction->id)->pluck('id')->toArray();

            $factor_remittance_id = FactorRemittance::MyCompany($this->dataCondition)
                ->where('factor_parent_id', $factorAction->id)->first()->factor_remittance_id;
            $oldProductFactorRemittance = FactorRemittance::MyCompany($this->dataCondition)->where('factor_parent_id', $factorAction->id)->pluck('id')->toArray();

            foreach ($request->factor as $factor) {
                $product = $factor['product'];
                if (stristr($factor['product'], 'product_')) {
                    $product = str_replace('product_', '', $product);
                    $newOrder = SellFactorDetail::updateOrCreate([
                        'factor_id'  => $factorAction->id,
                        'product_id' => $product
                    ], [
                        'factor_id'      => $factorAction->id,
                        'product_id'     => $product,
                        'warehouse_id'   => $request->warehouse,
                        'count'          => str_replace(',', '', $factor['count']),
                        'unit_price'     => str_replace(',', '', $factor['price']),
                        'discount'       => str_replace(',', '', $factor['discount']),
                        'total_price'    => str_replace(',', '', $factor['total_price']),
                        'user_id'        => $this->dataCondition['user_id'],
                        'fiscal_year_id' => $this->dataCondition['fiscal_year_id'],
                        'company_id'     => $this->dataCondition['company_id'],
                    ]);

                    $newOrderFactorRemittance = FactorRemittance::updateOrCreate([
                        'factor_id'            => $newOrder->id,
                        'factor_parent_id'     => $factorAction->id,
                        'factor_type'          => 'Sell',
                        'factor_remittance_id' => $factor_remittance_id,
                    ], [
                        'distance_ware_house'  => $request->warehouse,
                        'factor_id'            => $newOrder->id,
                        'factor_type'          => 'Sell',
                        'factor_parent_id'     => $factorAction->id,
                        'factor_remittance_id' => $factor_remittance_id,
                        'user_id'              => $this->dataCondition['user_id'],
                        'fiscal_year_id'       => $this->dataCondition['fiscal_year_id'],
                        'company_id'           => $this->dataCondition['company_id'],
                    ]);
                }
                if (stristr($factor['product'], 'service_')) {
                    $product = str_replace('service_', '', $product);
                    $newOrder = SellFactorDetail::updateOrCreate([
                        'factor_id'  => $factorAction->id,
                        'product_id' => $product
                    ], [
                        'factor_id'      => $factorAction->id,
                        'product_id'     => $product,
                        'warehouse_id'   => null,
                        'count'          => str_replace(',', '', $factor['count']),
                        'unit_price'     => str_replace(',', '', $factor['price']),
                        'discount'       => str_replace(',', '', $factor['discount']),
                        'total_price'    => str_replace(',', '', $factor['total_price']),
                        'user_id'        => $this->dataCondition['user_id'],
                        'fiscal_year_id' => $this->dataCondition['fiscal_year_id'],
                        'company_id'     => $this->dataCondition['company_id'],
                    ]);
                }
                $newProductFactor[] = $newOrder->id;
                $newProductFactorRe[] = $newOrderFactorRemittance->id;
            }


            $diff_identifiers = array_diff($oldProductFactor, $newProductFactor);
            if (count($diff_identifiers)) {
                foreach ($diff_identifiers as $id) {
                    SellFactorDetail::find($id)->delete();
                }
            }
            $diff_identifiers_s = array_diff($oldProductFactorRemittance, $newProductFactorRe);
            if (count($diff_identifiers_s)) {
                foreach ($diff_identifiers_s as $id) {
                    FactorRemittance::find($id)->delete();
                }
            }

        });
        return redirect()->route('sell.index')->with('suc', 'فاکتور فروش با موفقیت ویرایش شد.');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function draft (Request $request): RedirectResponse
    {
        $factor = SellFactor::MyCompany($this->dataCondition)->where('factor_id', $request->factor_id)
            ->with('personal')->firstOrFail();
        if ($this->isReturnBack($factor)) {
            return redirect()->back()->with('info',
                __('message.the_transfer_is_confirmed_and_it_is_not_possible_to_edit_the_invoice'));
        }
        DB::transaction(function () use ($factor) {
            JustIncrementPrice(Personal::class, $factor->personal_id, $factor->price, 'wallet');
            FactorRemittance::where('factor_parent_id', $factor->id)->update(['status' => 'draft']);
            $factor->update(['status' => 'draft']);
        });
        return redirect()->route('sell.index')->with('suc', 'فاکتور فروش با موفقیت حذف  شد.');
    }

    /**
     * @param array $data
     * @param int $factorId
     * @param int $warehouse_id
     * @return void
     */
    private function createdDetail (array $data, int $factorId, int $warehouse_id)
    {
        $getTypeWareHouse = $this->getTypeWareHouse($data);
        if (isset($getTypeWareHouse['factor']['product'])) {
            $originalRemittance = NumberFactor::MyCompany($this->dataCondition)
                ->where('type', 'original_remittance_number')->first();
            $originalRemittanceNumberId = $originalRemittance->numbers + 1;
            $originalRemittance->increment('numbers');
            foreach ($getTypeWareHouse['factor']['product'] as $record) {
                $factors = [
                    'factor_id'      => $factorId,
                    'product_id'     => str_replace('product_', '', $record['product']),
                    'warehouse_id'   => $warehouse_id,
                    'count'          => str_replace(',', '', $record['count']),
                    'unit_price'     => str_replace(',', '', $record['price']),
                    'discount'       => str_replace(',', '', $record['discount']),
                    'total_price'    => str_replace(',', '', $record['total_price']),
                    'user_id'        => $this->dataCondition['user_id'],
                    'fiscal_year_id' => $this->dataCondition['fiscal_year_id'],
                    'company_id'     => $this->dataCondition['company_id'],
                ];
                $buyfactorDetail = SellFactorDetail::create($factors);
                $FactorRemittance = [
                    'factor_id'            => $buyfactorDetail->id,
                    'factor_type'          => 'Sell',
                    'factor_parent_id'     => $factorId,
                    'distance_ware_house'  => $warehouse_id,
                    'factor_remittance_id' => $originalRemittanceNumberId,
                    'user_id'              => $this->dataCondition['user_id'],
                    'fiscal_year_id'       => $this->dataCondition['fiscal_year_id'],
                    'company_id'           => $this->dataCondition['company_id'],
                ];
                FactorRemittance::create($FactorRemittance);
            }
        }
        if (isset($getTypeWareHouse['factor']['service'])) {
            foreach ($getTypeWareHouse['factor']['service'] as $record) {
                $factors = [
                    'factor_id'      => $factorId,
                    'product_id'     => str_replace('service_', '', $record['product']),
                    'warehouse_id'   => null,
                    'count'          => str_replace(',', '', $record['count']),
                    'unit_price'     => str_replace(',', '', $record['price']),
                    'discount'       => str_replace(',', '', $record['discount']),
                    'total_price'    => str_replace(',', '', $record['total_price']),
                    'user_id'        => $this->dataCondition['user_id'],
                    'fiscal_year_id' => $this->dataCondition['fiscal_year_id'],
                    'company_id'     => $this->dataCondition['company_id'],
                ];
                SellFactorDetail::create($factors);
            }
        }
    }

    /**
     * @param Request $request
     * @param $minFactorBuyId
     * @return mixed
     */
    private function createdFactor (Request $request, $minFactorBuyId): mixed
    {
        $data = [
            'factor_id'      => $minFactorBuyId,
            'personal_id'    => $request->personal_id,
            'action_date'    => $request->action_date,
            'tax'            => $request->tax,
            'discount'       => $request->discount,
            'price'          => $request->total_price,
            'intro'          => $request->intro,
            'user_id'        => $this->dataCondition['user_id'],
            'fiscal_year_id' => $this->dataCondition['fiscal_year_id'],
            'company_id'     => $this->dataCondition['company_id'],
        ];
        return SellFactor::create($data)->id;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getTypeWareHouse (array $data): array
    {
        $array = [];
        foreach ($data as $factor) {
            $product = explode('_', $factor['product']);
            if (isset($product[0])) {
                $array['factor'][$product[0]][] = $factor;
            }
        }
        return $array;
    }

    /**
     * @param $factor
     * @return bool|void
     */
    private function isReturnBack ($factor)
    {
        if (in_array('published', $factor->factorRemittances->pluck('status')->toArray())) {
            return true;
        }
        if ($factor->status == 'draft') {
            return true;
        }
    }
}
