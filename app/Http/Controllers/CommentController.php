<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Obtener todos los comentarios de una playlist
     */
    public function getComments($playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);
        
        return response()->json(
            $playlist->comments()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * Crear un nuevo comentario
     */
    public function store(Request $request, $playlistId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $playlist = Playlist::findOrFail($playlistId);

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'playlist_id' => $playlistId
        ]);

        return response()->json(
            $comment->load('user'),
            201
        );
    }

    /**
     * Eliminar un comentario
     */
    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        
        // Verificar que el usuario sea el propietario del comentario
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para eliminar este comentario'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentario eliminado']);
    }
}
