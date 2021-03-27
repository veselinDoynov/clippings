<?php

namespace App\Http\Requests;


class Calculate
{
    /**
     * @return array
     */
    public static function rules()
    {
        return [
            'file' => 'required',
            'currencies' => 'required',
            'outputCurrency' => 'required',
            'vatNumber' => 'nullable',
        ];
    }
}