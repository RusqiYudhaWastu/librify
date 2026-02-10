<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $depts = ['PPLG', 'Broadcast', 'Animasi', 'Teknik Otomotif', 'Pengelasan'];

        foreach ($depts as $name) {
            Department::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
        }
    }
}