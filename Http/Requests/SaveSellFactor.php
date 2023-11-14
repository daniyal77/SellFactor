<?php

namespace Modules\SellFactor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use function __;

class SaveSellFactor extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules (): array
    {
        return [
            'parent_factor' => 'nullable|integer',
            'personal_id'   => 'required|integer',
            'action_date'   => 'required|date',
            'intro'         => 'nullable|max:255',
            'discount'      => 'required|integer|min:-1',
            'tax'           => 'required|integer|min:-1',
            'total_price'   => 'required|integer|min:-1',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize (): bool
    {
        return true;
    }

    public function messages (): array
    {
        return [
            'personal_id.required' => __('message.required'),
            'action_date.required' => __('message.required'),
            'intro.max'            => __('message.max_255_character'),
            'discount.required'    => __('message.required'),
            'tax.required'         => __('message.required'),
            'total_price.required' => __('message.required'),
            'action_date.date'     => 'فرمت تاریخ اشتباه هست',
            'personal_id.integer'  => 'عدد وارد نمایید',
            'discount.integer'     => 'عدد وارد نمایید',
            'tax.integer'          => 'عدد وارد نمایید',
            'total_price.integer'  => 'عدد وارد نمایید',
            'discount.min'         => 'نمیتواند منفی باشد',
            'tax.min'              => 'نمیتواند منفی باشد',
            'total_price.min'      => 'نمیتواند منفی باشد',
        ];
    }
}
