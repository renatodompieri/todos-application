<?php

namespace Modules\Todo\Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Todo\Database\factories\TodoFactory;
use Tests\TestCase;

class DeleteTodoTest extends TestCase
{
    use DatabaseTransactions;

    public function testDeleteTodoSuccessfully()
    {
        $attributes = [];
        $user = (new UserFactory())->create();
        $todo = (new TodoFactory())->create();

        $todo->user_id = $user->id;
        $todo->save();

        $this->actingAs($user, 'api')->json(
            'POST',
            'api/v1/todo/' . $todo->id,
            $attributes,
            ['Accept' => 'application/json']
        )->assertStatus(200);
    }

    public function testDeleteTodoError()
    {
        $attributes = [];

        $user = (new UserFactory())->create();
        $todo = (new TodoFactory())->create();

        $todo->user_id = $user->id + 1;
        $todo->save();

        $this->actingAs($user, 'api')->json(
            'POST',
            'api/v1/todo/' . $todo->id,
            $attributes,
            ['Accept' => 'application/json']
        )->assertStatus(403);

    }
}
