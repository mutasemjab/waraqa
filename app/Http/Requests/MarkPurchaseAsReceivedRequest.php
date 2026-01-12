<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkPurchaseAsReceivedRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('purchase-receive');
    }

    public function rules()
    {
        return [
            'received_date' => 'required|date|before_or_equal:today',
        ];
    }

    public function messages()
    {
        return [
            'received_date.required' => __('validation.required', ['attribute' => __('messages.Received_Date')]),
            'received_date.date' => __('validation.date', ['attribute' => __('messages.Received_Date')]),
            'received_date.before_or_equal' => __('validation.before_or_equal', ['attribute' => __('messages.Received_Date'), 'date' => 'today']),
        ];
    }
}
