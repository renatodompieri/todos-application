<?php

namespace Modules\Todo\Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Todo\Database\factories\TodoFactory;
use Tests\TestCase;

class UpdateTodoTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateTodoSuccessfully()
    {
        $attributes = [
            'title' => 'test title',
            'description' => 'test description',
            'status' => 0,
            'date' => '2021-05-01',
            'completed_at' => ''
        ];

        $user = (new UserFactory())->create();
        $todo = (new TodoFactory())->create();

        $todo->user_id = $user->id;
        $todo->save();

        $this->actingAs($user, 'api')->json(
            'PATCH',
            'api/v1/todo/' . $todo->id,
            $attributes,
            ['Accept' => 'application/json']
        )->assertStatus(200);
    }

    public function testUpdateTodoError()
    {
        $attributes = [
            'description' => 'test description',
        ];

        $user = (new UserFactory())->create();
        $todo = (new TodoFactory())->create();

        $todo->user_id = $user->id + 1;
        $todo->save();

        $this->actingAs($user, 'api')->json(
            'PATCH',
            'api/v1/todo/' . $todo->id,
            $attributes,
            ['Accept' => 'application/json']
        )->assertStatus(403);

    }
}
