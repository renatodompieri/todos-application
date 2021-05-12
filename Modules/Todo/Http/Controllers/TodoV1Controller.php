<?php

namespace Modules\Todo\Http\Controllers;

use App\Enums\CrudActionEnum;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Todo\Http\Requests\TodoStoreRequest;
use Modules\Todo\Http\Requests\TodoUpdateRequest;
use Modules\Todo\Repositories\TodoRepository;

class TodoV1Controller extends Controller
{
    private $repo;
    private $userRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TodoRepository $repo, UserRepository $userRepository)
    {
        $this->repo = $repo;
        $this->userRepository = $userRepository;
    }

    /**
     * Used to get all Todos
     * @get ("/api/v1/todo")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $request->get('filter');

        return $this->ok($this->repo->findByFilterAndQuery($filter)->jsonSerialize());
    }

    /**
     * Used to store Todo
     * @post ("/api/v1/todo")
     * @param TodoStoreRequest $request
     * @return JsonResponse
     * @Parameter("title", type="string", required="true", description="Title of Todo"),
     * @Parameter("date", type="date", required="true", description="Due date of Todo"),
     * })
     */
    public function store(TodoStoreRequest $request): JsonResponse
    {
        $parameters = $this->repo->formatAttributes($request->all(), CrudActionEnum::STORE());
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
     */
    public function show($id): JsonResponse
    {
        try {
            $todo = $this->repo->findOrFail($id);
            return $this->ok($todo->toArray());
        } catch (ValidationException $e) {
            return $this->error(["message" => $e->getMessage()]);
        }
    }

    /**
     * Used to update Todo status
     * @post ("/api/v1/todo/{id}/status")
     * @param ({
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * })
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $todo = $this->repo->findOrFail($id);
            $todo = $this->repo->toggle($todo);
            return $this->success(['message' => trans('todo.updated'), 'data' => $todo]);
        } catch (ValidationException $e) {
            return $this->error(["message" => $e->getMessage()]);
        }
    }

    /**
     * Used to prepare all select elements to the front-end
     * Might be: tags, users, etc.
     *
     * @get ("/api/v1/todo/prepare-elements")
     * @return JsonResponse
     */
    public function prepareSelectElements(): JsonResponse
    {
        $users = $this->userRepository->all();
        return $this->success(['message' => trans('todo.success'), 'users' => $users]);
    }

    /**
     * Used to update Todo
     * @patch ("/api/v1/todo/{id}")
     * @param ({
     * @param TodoUpdateRequest $request
     * @return JsonResponse
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * @Parameter("title", type="string", required="true", description="Title of Todo"),
     * @Parameter("date", type="date", required="true", description="Due date of Todo"),
     * })
     */
    public function update($id, TodoUpdateRequest $request): JsonResponse
    {
        try {
            $attributes = $this->repo->formatAttributes($request->all(), CrudActionEnum::UPDATE());
            $todo = $this->repo->findOrFail($id);
            $todo = $this->repo->update($todo, $attributes);
            return $this->success(['data' => $todo, 'message' => trans('todo.updated')]);
        } catch (ValidationException $e) {
            return $this->error(["message" => $e->getMessage()]);
        }
    }

    /**
     * Used to delete Todo
     * @delete ("/api/v1/todo/{id}")
     * @param ({
     * @Parameter("id", type="integer", required="true", description="Id of Todo"),
     * })
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $todo = $this->repo->findOrFail($id);
            $this->repo->delete($todo->id);
            return $this->success(['message' => trans('todo.deleted')]);
        } catch (ValidationException $e) {
            return $this->error(["message" => $e->getMessage()]);
        }
    }
}
