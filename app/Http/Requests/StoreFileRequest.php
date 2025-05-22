<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function getPg(){
        
        return $this->pg;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        if($this->pg == 'manual'){
            return [
                'file' => 'required|file|mimes:jpg,jpeg,png'
            ];
        }
        return [
            'file' => 'file|mimes:jpg,jpeg,png'
        ];
    }
}
