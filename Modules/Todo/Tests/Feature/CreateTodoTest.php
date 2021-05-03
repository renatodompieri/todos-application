<?php

namespace Modules\Todo\Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTodoTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateTodoSuccessfully()
    {
        $attributes = [
            'title' => 'test title',
            'description' => 'test description',
            'status' => 0,
            'date' => '2021-05-01',
            'completed_at' => ''
        ];

        $user = (new UserFactory())->create();
        $this->actingAs($user, 'api')->json(
            'POST',
            'api/v1/todo',
            $attributes,
            ['Accept' => 'application/json']
        )->assertStatus(200);
    }

    public function testCreateTodoError()
    {
        $attributes = [
            'description' => 'test description',
            'status' => 0,
            'date' => '2021-05-01',
        ];

        $user = (new UserFactory())->create();

        $this->actingAs($user, 'api')->json(
            'POST',
            'api/v1/todo',
            $attributes,
            ['Accept' => 'application/json']
        )->assertStatus(422);

    }
}
