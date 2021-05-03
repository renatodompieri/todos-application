<?php

namespace Modules\Todo\Http\Controllers;

use App\Enums\CrudActionEnum;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Modules\Todo\Entities\Todo;
use Modules\Todo\Http\Requests\TodoStoreRequest;
use Modules\Todo\Http\Requests\TodoUpdateRequest;
use Modules\Todo\Repositories\TodoRepository;
use Prettus\Validator\Exceptions\ValidatorException;

class TodoV1Controller extends Controller
{
    private $repo;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TodoRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Used to get all Todos
     * @get ("/api/v1/todo")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->ok($this->repo->all()->jsonSerialize());
    }

    /**
     * Used to store Todo
     * @post ("/api/v1/todo")
     * @param TodoStoreRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidatorException
     * @Parameter("title", type="string", required="true", description="Title of Todo"),
     * @Parameter("date", type="date", required="true", description="Due date of Todo"),
     * })
     */
    public function store(TodoStoreRequest $request): JsonResponse
    {
        $this->authorize(CrudActionEnum::CREATE, Todo::class);
        $parameters = $this->repo->formatAttributes($request->all(), CrudActionEnum::CREATE());
        $todo = $this->repo->create($parameters);

        return $this->success(['data' => $todo, 'message' => trans('todo.added')]);
    }

    /**
     * Used to get Todo detail
     * @get ("/api/v1/todo/{id}")
     * @param ({
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * })
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show($id): JsonResponse
    {
        $todo = $this->repo->find($id);

        $this->authorize(CrudActionEnum::UPDATE, $todo);

        return $this->ok($todo);
    }

    /**
     * Used to update Todo status
     * @post ("/api/v1/todo/{id}/status")
     * @param ({
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * })
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function toggleStatus($id): JsonResponse
    {
        $todo = $this->repo->find($id);

        $this->authorize(CrudActionEnum::UPDATE, $todo);

        $todo = $this->repo->toggle($todo);

        return $this->success(['message' => trans('todo.updated'), 'data' => $todo]);
    }

    /**
     * Used to update Todo
     * @patch ("/api/v1/todo/{id}")
     * @param ({
     * @param TodoUpdateRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidatorException
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * @Parameter("title", type="string", required="true", description="Title of Todo"),
     * @Parameter("date", type="date", required="true", description="Due date of Todo"),
     * })
     */
    public function update($id, TodoUpdateRequest $request): JsonResponse
    {
        $todo = $this->repo->find($id);

        $this->authorize(CrudActionEnum::UPDATE, $todo);
        $attributes = $this->repo->formatAttributes($request->all());
        $todo = $this->repo->update($attributes, $id);

        return $this->success(['data' => $todo, 'message' => trans('todo.updated')]);
    }

    /**
     * Used to delete Todo
     * @delete ("/api/v1/todo/{id}")
     * @param ({
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * })
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($id): JsonResponse
    {
        $todo = $this->repo->find($id);
        $this->authorize(CrudActionEnum::DESTROY, $todo);

        $this->repo->delete($todo->id);

        return $this->success(['message' => trans('todo.deleted')]);
    }
}
