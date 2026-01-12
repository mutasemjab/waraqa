<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('purchase-edit');
    }

    public function rules()
    {
        return [
            'provider_id' => 'required|exists:providers,id',
            'expected_delivery_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'provider_id.required' => __('validation.required', ['attribute' => __('messages.Provider')]),
            'provider_id.exists' => __('validation.exists', ['attribute' => __('messages.Provider')]),
            'expected_delivery_date.date' => __('validation.date', ['attribute' => __('messages.Expected_Delivery_Date')]),
            'expected_delivery_date.after_or_equal' => __('validation.after_or_equal', ['attribute' => __('messages.Expected_Delivery_Date'), 'date' => 'today']),
            'notes.string' => __('validation.string', ['attribute' => __('messages.notes')]),
            'notes.max' => __('validation.max.string', ['attribute' => __('messages.notes'), 'max' => 1000]),
        ];
    }
}
