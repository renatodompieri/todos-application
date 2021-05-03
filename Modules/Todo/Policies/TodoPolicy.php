<?php

namespace Modules\Todo\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Todo\Entities\Todo;

class TodoPolicy
{
    use HandlesAuthorization;

    public function index(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the todo.
     *
     * @param User $user
     * @param Todo $todo
     * @return bool
     */
    public function show(User $user, Todo $todo): bool
    {
        return $todo->user_id === $user->id || $todo->assignee_id === $user->id;
    }

    /**
     * Determine whether the user can update the todo.
     *
     * @param User $user
     * @param Todo $todo
     * @return bool
     */
    public function update(User $user, Todo $todo): bool
    {
        return $todo->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the todo.
     *
     * @param User $user
     * @param Todo $todo
     * @return bool
     */
    public function destroy(User $user, Todo $todo): bool
    {
        return $todo->user_id === $user->id;
    }
}
