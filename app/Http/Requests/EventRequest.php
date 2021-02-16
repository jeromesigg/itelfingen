<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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

    protected $errorBag = 'event';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->redirect = url()->previous() . "#booking";

        return [
            'g-recaptcha-response' => 'required|captcha'
        ];
    }

        public function messages()
        {
            return [
                'g-recaptcha-response.required' => 'Das Captcha muss ausgewählt sein.'
            ];
        }
}
