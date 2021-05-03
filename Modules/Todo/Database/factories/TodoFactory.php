<?php


namespace Modules\Todo\Database\factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Todo\Entities\Todo;

class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $users = User::pluck('id')->toArray();

        return [
            'user_id' => $this->faker->randomElement($users),
            'assignee_id' => $this->faker->randomElement($users),
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => mt_rand(0, 1),
            'date' => $this->faker->date(),
            'completed_at' => $this->faker->date(),
            'tags' => "{}",
        ];
    }
}
