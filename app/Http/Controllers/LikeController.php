<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Obtener el conteo de likes de una playlist
     */
    public function getCount($playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);
        $count = $playlist->likes()->count();
        $userLiked = Auth::check() ? $playlist->likes()->where('user_id', Auth::id())->exists() : false;

        return response()->json([
            'count' => $count,
            'userLiked' => $userLiked
        ]);
    }

    /**
     * Dar like a una playlist
     */
    public function store($playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);

        // Verificar si el usuario ya dio like
        $existingLike = Like::where('user_id', Auth::id())
            ->where('playlist_id', $playlistId)
            ->first();

        if ($existingLike) {
            return response()->json(['message' => 'Ya has dado like a esta playlist'], 409);
        }

        $like = Like::create([
            'user_id' => Auth::id(),
            'playlist_id' => $playlistId
        ]);

        $newCount = $playlist->likes()->count();

        return response()->json([
            'message' => 'Like agregado',
            'count' => $newCount
        ], 201);
    }

    /**
     * Quitar like de una playlist
     */
    public function destroy($playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);

        Like::where('user_id', Auth::id())
            ->where('playlist_id', $playlistId)
            ->delete();

        $newCount = $playlist->likes()->count();

        return response()->json([
            'message' => 'Like removido',
            'count' => $newCount
        ]);
    }
}
