<?php

namespace Modules\SellFactor\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Product\Entities\Product;
use Modules\Service\Entities\Service;
use Modules\Setting\Entities\Warehouse;

class SellFactorDetail extends Model
{
    use HasFactory;

    protected $appends = ['factorType'];

    protected $fillable = [
        'factor_id', 'product_id', 'warehouse_id', 'count', 'unit_price', 'discount', 'total_price', 'status',
        'user_id', 'fiscal_year_id', 'company_id', 'intro'
    ];

    protected static function newFactory ()
    {
        return \Modules\WareHouse\Database\factories\SellFactorDetailFactory::new();
    }


    /**
     * @return HasOne
     */
    public function parent (): HasOne
    {
        return $this->hasOne(SellFactor::class, 'id', 'factor_id');
    }

    /**
     * @return HasOne
     */
    public function product (): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * @param $query
     * @param $arg
     * @return mixed
     */
    public function scopeMyCompany ($query, $arg): mixed
    {
        return $query->where('fiscal_year_id', $arg['fiscal_year_id'])->where('company_id', $arg['company_id']);
    }

    /**
     * @return string
     */
    public function isComing (): string
    {
        return 'خروج از' . $this->warehouse->name;
    }

    /**
     * @return string
     */
    public function factorType (): string
    {
        return 'فروش';
    }

    /**
     * @return string
     */
    public function getFactorTypeAttribute (): string
    {
        return "sell";
    }

    /**
     * @return HasOne
     */
    public function warehouse (): HasOne
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    /**
     * @return HasOne
     */
    public function services (): HasOne
    {
        return $this->hasOne(Service::class, 'id', 'product_id');
    }
}
