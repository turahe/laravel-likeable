<?php

namespace Turahe\Likeable;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_id = $this->faker->randomElement(User::pluck('id')->toArray());
        return [
            'user_id' => $user_id,
        ];
    }
}
