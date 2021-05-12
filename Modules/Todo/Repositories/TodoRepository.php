<?php

namespace Modules\Todo\Repositories;

use App\Enums\CrudActionEnum;
use App\Repositories\CrudRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Todo\Entities\Todo;

class TodoRepository extends CrudRepository
{
    public function getModel(): Model
    {
        return new Todo();
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

    /**
     * Toggle given todo status.
     *
     * @param Todo $todo
     * @return Todo
     */
    public function toggle(Todo $todo): Model
    {
        $todo->forceFill([
            'completed_at' => (!$todo->status) ? Carbon::now() : null,
            'status' => !$todo->status
        ])->save();

        return $todo;
    }

    public function findByFilterAndQuery(?string $filter = null, ?string $q = null)
    {
        $query = $this->getQuery();

        if ($filter === 'important') {
            $query = $query->where('important', 1);
        }

        if ($filter === 'completed') {
            $query = $query->where([['completed_at', '!=', null]]);
        }

        if ($filter === 'deleted') {
            $query = $query->where('status', 0);
        }

        if (!empty($q)) {
            $query = $query
                ->where('title', 'like', '%' . $q . '%')
                ->orWhere('description', 'like', '%' . $q . '%');
        }

        return $query->with('assignee')->get();
    }
}
