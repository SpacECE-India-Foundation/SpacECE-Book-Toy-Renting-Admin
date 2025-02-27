<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdditionalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:256',
            'title_bn' => 'nullable|string|max:256',
            'price' => 'required',
            'description' => 'nullable',
            'description_bn' => 'nullable',
            'service_id' => 'required',
        ];
    }
}
