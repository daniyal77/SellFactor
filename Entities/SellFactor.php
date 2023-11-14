<?php

namespace Modules\SellFactor\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Modules\CashDesk\Entities\CashDesk;
use Modules\Company\Entities\Company;
use Modules\Personal\Entities\Personal;
use Modules\WareHouse\Database\factories\SellFactorFactory;
use Modules\WareHouse\Entities\FactorRemittance;
use function __;
use function route;

class SellFactor extends Model
{
    use HasFactory;

    protected $appends = ['factorType'];

    protected $fillable = [
        'factor_id', 'personal_id', 'action_date', 'tax', 'status', 'discount', 'price', 'intro', 'user_id',
        'fiscal_year_id', 'company_id', 'pre_invocie_id', 'status', 'is_pos', 'is_cash_desc', 'fake_id'
    ];

    /**
     * @return SellFactorFactory
     */
    protected static function newFactory (): SellFactorFactory
    {
        return SellFactorFactory::new();
    }

    /**
     * @return HasOne
     */
    public function personal (): HasOne
    {
        return $this->hasOne(Personal::class, 'id', 'personal_id');
    }

    /**
     * @return HasMany
     */
    public function detail (): HasMany
    {
        return $this->hasMany(SellFactorDetail::class, 'factor_id', 'id');
    }

    /**
     * @return string
     */
    public function status (): string
    {
        return match ($this->status) {
            'pending' => __('message.pending'),
            'draft' => __('message.draft'),
            'publish' => __('message.published'),
        };
    }

    /**
     * @return string
     */
    public function action (): string
    {
        $button = '';
        if (Auth::user()->can('sales_invoice')) {
            $button .= "<a title='مشاهده' class='btn ml-1 btn-sm btn-primary' href='" . route('sell.show', $this->factor_id) . "'><i class='fas fa-search'></i></a>";
        }
        if (Auth::user()->can('sales_invoice_edit')) {
            $button .= "<a  title='ویرایش' class='btn ml-1 btn-sm btn-info'  href='" . route('sell.edit', $this->factor_id) . "'><i class='fas fa-edit'></i></a>";
        }
        if (Auth::user()->can('sales_invoice_delete')) {
            $button .= "<span class='btn btn-sm ml-1 btn-danger' title='حذف'  onclick='deleteCheque(" . $this->factor_id . ")'><i class='fas fa-trash'></i></span>";
        }
        if (Auth::user()->can('sales_invoice')) {
            $button .= "<a title='پرینت' class='btn btn-sm ml-1 btn-success' href='" . route('sell.show.factor', $this->factor_id) . "'><i class='fas fa-print'></i></a>";
        }
        return $button;
    }

    /**
     * @return string
     */
    public function actionPos (): string
    {
        $button = '';
        if (Auth::user()->can('pos_edit')) {
            $button .= "<a title='مشاهده' class='btn ml-1 btn-sm btn-primary' href='" . route('sell.show', $this->factor_id) . "'><i class='fas fa-search'></i></a>";
        }
        if (Auth::user()->can('pos_edit')) {
            $button .= "<a  title='ویرایش' class='btn ml-1 btn-sm btn-info'  href='" . route('pos.edit', $this->factor_id) . "'><i class='fas fa-edit'></i></a>";
        }
        if (Auth::user()->can('pos_delete')) {
            $button .= "<span class='btn btn-sm ml-1 btn-danger' title='حذف'  onclick='deleteCheque(" . $this->factor_id . ")'><i class='fas fa-trash'></i></span>";
        }
        if (Auth::user()->can('pos_edit')) {
            $button .= "<a title='پرینت' class='btn btn-sm ml-1 btn-success' href='" . route('sell.show.factor', $this->factor_id) . "'><i class='fas fa-print'></i></a>";
        }
        return $button;
    }

    /**
     * @return string
     */
    public function actionCashDesk (): string
    {
        $button = '';
        if (Auth::user()->can('product_fund')) {
            $button .= "<a  title='ویرایش' class='btn ml-1 btn-sm btn-info'  href='" . route('cash.desk.edit', $this->factor_id) . "'><i class='fas fa-edit'></i></a>";
            $button .= "<span class='btn btn-sm ml-1 btn-danger' title='حذف'  onclick='deleteCheque(" . $this->factor_id . ")'><i class='fas fa-trash'></i></span>";
            $button .= "<a title='پرینت' class='btn btn-sm ml-1 btn-success' href='" . route('cash.desk.invoice.print', $this->factor_id) . "'><i class='fas fa-print'></i></a>";
        }
        return $button;
    }

    /**
     * @return string
     */
    public function getFactorTypeAttribute (): string
    {
        return "sell";
    }

    /**
     * @return string
     */
    public function factorType (): string
    {
        return ' فاکتور فروش ';
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
        return 'خروج از انبار';
    }

    /**
     * @param $id
     * @return string
     */
    public function showMore ($id): string
    {
        return route('sell.show', $id);
    }

    /**
     * @return HasOne
     */
    public function company (): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    /**
     * @return HasMany
     */
    public function factorRemittances (): HasMany
    {
        return $this->hasMany(FactorRemittance::class, 'factor_parent_id', 'id')->where('factor_type', 'Sell');
    }

    /**
     * @return string
     */
    public function factorRemittancesStatus (): string
    {
        $factors = $this->factorRemittances;
        $active = 0;
        foreach ($factors as $factor) {
            if ($factor->status == 'published') {
                $active++;
            }
        }
        return $active . " از " . sizeof($factors) . " " . __('message.published');
    }

    /**
     * @return mixed
     */
    public function factorRemittancesStatusActiveCount (): mixed
    {
        $factors = $this->factorRemittances;
        $active = 0;
        foreach ($factors as $factor) {
            if ($factor->status == 'published') {
                $active++;
            }
        }
        return $factors;
    }

    public function cashDesk (): HasOne
    {
        return $this->hasOne(CashDesk::class, 'sell_id');
    }
}
