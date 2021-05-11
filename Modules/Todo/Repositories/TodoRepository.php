<?php

namespace Modules\Todo\Repositories;

use App\Enums\CrudActionEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Todo\Entities\Todo;
use Prettus\Repository\Eloquent\BaseRepository;

class TodoRepository extends BaseRepository
{
    public function model(): string
    {
        return Todo::class;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param CrudActionEnum|null $action
     * @return array
     */
    public function formatAttributes(array $params, CrudActionEnum $action = null): array
    {
        if (\is_null($action)) {
            return [];
        }

        $formatted = [
            'title' => $params['title'] ?? null,
            'assignee_id' => $params['assignee_id'] ?? null,
            'description' => $params['description'] ?? null,
            'date' => isset($params['date']) ? date('Y-m-d', strtotime($params['date'])) : null,
            'status' => $params['status'] ?? false,
            'completed_at' => $params['completed_at'] ?? null,
            'tags' => $params['tags'] ?? null,
        ];

        if ($action->value === CrudActionEnum::STORE) {
            $formatted['user_id'] = auth()->user()->id;
        }

        return $formatted;
    }


    public function deleteMultiple(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Toggle given todo status.
     *
     * @param Todo $todo
     * @return Todo
     */
    public function toggle(Todo $todo): Todo
    {
        $todo->forceFill([
            'completed_at' => (!$todo->status) ? Carbon::now() : null,
            'status' => !$todo->status
        ])->save();

        return $todo;
    }

    public function findByFilter(?string $filter)
    {
        if ($filter === 'important') {
            return $this->findByField('important', 1);
        }

        if ($filter === 'completed') {
            return $this->findWhere([['completed_at', '!=', null]]);
        }

        if ($filter === 'deleted') {
            return $this->findByField('status', 0);
        }

        return $this->with('assignee')->all();
    }
}
