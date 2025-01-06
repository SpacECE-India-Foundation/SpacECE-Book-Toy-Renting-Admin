<?php

namespace App\Http\Requests;

use App\Rules\EmailValidateRule;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png',
            'type' => 'required',
        ];

        if ($this->type === 'mobile') {
            $rules['mobile'] = 'required|digits_between:10,12|unique:users,mobile';
        }

        if ($this->type === 'email') {
            $rules['email'] = ['required', 'unique:users,email', new EmailValidateRule()];
        }

        if ($this->email) {
            $rules['email'] = ['required', 'unique:users,email', new EmailValidateRule()];
        }

        if (! $this->type || $this->type === 'contact') {
            if (filter_var($this->contact, FILTER_VALIDATE_EMAIL)) {
                $rules['email'] = 'required|unique:users|email';
                $this['email'] = $this->contact;
            } else {
                $rules['mobile'] = 'required|digits_between:10,12|unique:users';
                $this['mobile'] = $this->contact;
            }
        }

        return $rules;
    }
}
