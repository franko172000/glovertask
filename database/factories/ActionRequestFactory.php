<?php

namespace Database\Factories;

use App\Persistence\Models\ActionRequest;
use App\Persistence\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class ActionRequestFactory extends Factory
{
    protected $model = ActionRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }
}
