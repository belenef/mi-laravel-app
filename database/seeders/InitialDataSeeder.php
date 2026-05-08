<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Belén',
            'email' => 'belen@vibely.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'bio' => 'Amante de la música y creadora de Vibely.',
            'avatar' => 'https://i.pravatar.cc/150?u=belen'
        ]);

        $ana = \App\Models\User::create([
            'name' => 'Ana',
            'email' => 'ana@vibely.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'bio' => 'Lofi girl at heart.',
            'avatar' => 'https://i.pravatar.cc/150?u=ana'
        ]);

        // Playlists
        \App\Models\Playlist::create([
            'title' => 'Chill Vibes',
            'description' => 'Perfecta para relajarse y leer.',
            'mood' => 'Calma',
            'cover' => 'https://picsum.photos/seed/p1/400/200',
            'is_collaborative' => true,
            'user_id' => $ana->id
        ]);

        \App\Models\Playlist::create([
            'title' => 'Energía Máxima',
            'description' => 'Para empezar el día con todo.',
            'mood' => 'Felicidad',
            'cover' => 'https://picsum.photos/seed/p2/400/200',
            'is_collaborative' => false,
            'user_id' => $user->id
        ]);

        // Groups
        \App\Models\Group::create([
            'name' => 'Adictos al Lofi',
            'category' => 'Indie',
            'description' => 'Comunidad para compartir los mejores beats relajantes.',
            'members_count' => 120,
            'color' => '#6f42c1',
            'icon' => 'fa-solid fa-mug-hot',
            'user_id' => $ana->id
        ]);

        \App\Models\Group::create([
            'name' => 'Techno Berlin',
            'category' => 'Techno',
            'description' => 'Puro ritmo underground de la capital alemana.',
            'members_count' => 450,
            'color' => '#000000',
            'icon' => 'fa-solid fa-compact-disc',
            'user_id' => $user->id
        ]);
    }
}
