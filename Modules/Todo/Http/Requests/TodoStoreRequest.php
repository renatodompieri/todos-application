<?php

namespace Modules\Todo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'date' => 'sometimes|required|date_format:Y-m-d',
            'assignee_id' => 'sometimes|integer|exists:user,id',
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title' => trans('todo.title'),
            'date' => trans('todo.date'),
            'description' => trans('todo.description'),
            'assignee_id' => trans('todo.assignee_id'),
            'user_id' => trans('todo.user_id'),
            'tags' => trans('todo.tags'),
        ];
    }
}
