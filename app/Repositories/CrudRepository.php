<?php

namespace App\Repositories;

use App\Enums\CrudActionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * This is still work in progress...
*/
abstract class CrudRepository
{
    abstract public function getModel(): Model;

    /**
     * Get query string for model
    */
    public function getQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    /**
     * Find object with given id or throw an error.
     *
     * @param integer $id
     * @return Model
     * @throws ValidationException
     */

    public function findOrFail(int $id): Model
    {
        $model = $this->getQuery()->find($id);

        if (!$model) {
            throw ValidationException::withMessages(['message' => trans('todo.could_not_find')]);
        }

        return $model;
    }

    /**
     * Paginate all objects using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate(array $params = []): LengthAwarePaginator
    {
        $sortBy = $params['sort_by'] ?? 'created_at';
        $order = $params['order'] ?? 'desc';
        $pageLength = $params['page_length'] ?? config('config.page_length');

        return $this->getQuery()
            ->orderBy($sortBy, $order)
            ->paginate($pageLength);
    }

    /**
     * Create a new object
     *
     * @param array $params
     * @return Model
     */
    public function create(array $params = []): Model
    {
        return $this->getQuery()->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param CrudActionEnum|null $action
     * @return array
     */
    private function formatParams(array $params, ?CrudActionEnum $action = null): array
    {
        $formatted = [];
        foreach ($this->getModel()->getFillable() as $element) {
            if ($action->value === CrudActionEnum::UPDATE && !isset($params[$element])) {
                continue;
            }

            $formatted[$element] = $params[$element] ?? null;
        }

        return $formatted;
    }

    /**
     * Update given object
     *
     * @param Model $model
     * @param array $params
     *
     * @return Model
     */
    public function update(Model $model, array $params = []): Model
    {
        $model->forceFill(
            $this->formatParams($params, CrudActionEnum::UPDATE())
        )->save();

        return $model;
    }

    /**
     * Delete object
     *
     * @param Model $model
     * @return bool|null
     */
    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }

    /**
     * Delete multiple objects
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple(array $ids): ?bool
    {
        return $this->getQuery()->whereIn('id', $ids)->delete();
    }
}
