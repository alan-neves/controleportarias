<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'origem'         => 'required',
            'destino'        => 'required',
            'codpes'         => 'nullable|integer',
            'nome'           => 'required_without:codpes',
            'tipo_documento' => 'required_without:codpes',
            'documento'      => 'required_without:codpes',
            'patrimonio'     => 'nullable',
            'numero_serie'   => 'nullable',
            'observacao'     => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'origem.required' => 'O campo origem é obrigatório.',
            'destino.required' => 'O campo destino é obrigatório.',
            'codpes.integer' => 'O NºUSP deve ser um número inteiro.',
            'nome.required_without' => 'O nome é obrigatório quando não há NºUSP.',
            'tipo_documento.required_without' => 'O tipo do documento é obrigatório quando não há NºUSP.',
            'documento.required_without' => 'O documento é obrigatório quando não há NºUSP.',
        ];
    }
}
