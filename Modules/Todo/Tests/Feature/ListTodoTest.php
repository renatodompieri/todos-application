<?php

namespace Modules\Todo\Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Todo\Database\factories\TodoFactory;
use Tests\TestCase;

class ListTodoTest extends TestCase
{
    use DatabaseTransactions;

    public function testListTodoSuccessfully()
    {
        $user = (new UserFactory())->create();
        $todo = (new TodoFactory())->create();

        $todo->user_id = $user->id;
        $todo->save();

        $this->actingAs($user, 'api')->json(
            'GET',
            'api/v1/todo',
            [],
            ['Accept' => 'application/json']
        )->assertStatus(200);
    }
}
