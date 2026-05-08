<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdatePlaylistCoversSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Playlist::whereNull('cover')->whereNotNull('mood')->each(function ($playlist) {
            $playlist->update(['cover' => 'auto:' . $playlist->mood]);
        });
    }
}
