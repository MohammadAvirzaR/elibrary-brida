<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Type;
use App\Models\Unit;
use App\Models\License;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? 1,
            'title' => $this->faker->sentence(5),
            'author' => $this->faker->name(),
            'year_published' => $this->faker->year(),
            'type_id' => Type::inRandomOrder()->value('id') ?? 1,
            'unit_id' => Unit::inRandomOrder()->value('id') ?? 1,
            'language' => 'Indonesia',
            'email' => $this->faker->unique()->safeEmail(),
            'keywords' => implode(', ', $this->faker->words(4)),
            'abstract' => $this->faker->paragraph(4),
            'file_path' => 'documents/' . $this->faker->slug() . '.pdf',
            'upload_date' => now(),
            'license_id' => License::inRandomOrder()->value('id') ?? 1,
            'access_right' => $this->faker->randomElement(['public', 'private']),
        ];
    }
}
