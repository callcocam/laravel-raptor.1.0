<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Database\Factories;

use Callcocam\LaravelRaptor\Enums\PermissionStatus;
use Callcocam\LaravelRaptor\Support\Shinobi\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $slug = str_replace(' ', '.', $name);

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->sentence(),
            'status' => PermissionStatus::Published->value,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PermissionStatus::Draft->value,
        ]);
    }
}
