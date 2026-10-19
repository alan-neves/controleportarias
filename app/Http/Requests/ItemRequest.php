<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('admin');
    }

    protected function prepareForValidation(): void
    {
        // sem documento não faz sentido guardar o tipo (o select sempre envia um valor)
        if (blank($this->documento)) {
            $this->merge(['tipo_documento' => null]);
        }
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
            'documento'      => 'required_without:codpes|nullable|string|max:50',
            'tipo_documento' => ['nullable', 'required_with:documento', Rule::in(Item::TIPOS_DOCUMENTO)],
            'patrimonio'     => 'nullable',
            'numero_serie'   => 'nullable',
            'observacao'     => 'nullable',
            'fotos'          => 'nullable|array|max:10',
            'fotos.*'        => 'nullable|string|starts_with:data:image/jpeg;base64,,data:image/png;base64,|max:2800000',
        ];
    }

    public function messages(): array
    {
        return [
            'origem.required' => 'O campo origem é obrigatório.',
            'destino.required' => 'O campo destino é obrigatório.',
            'codpes.integer' => 'O NºUSP deve ser um número inteiro.',
            'nome.required_without' => 'O nome é obrigatório quando não há NºUSP.',
            'documento.required_without' => 'O documento é obrigatório quando não há NºUSP.',
            'tipo_documento.required_with' => 'Informe o tipo do documento.',
            'tipo_documento.in' => 'Tipo de documento inválido.',
            'fotos.max' => 'Você pode enviar no máximo 10 fotos por vez.',
            'fotos.*.starts_with' => 'Uma das fotos enviadas não está em um formato válido. (formatos aceitos jpeg e png)',
            'fotos.*.max' => 'Uma das fotos enviadas excede o tamanho máximo permitido (2MB).',
        ];
    }
}