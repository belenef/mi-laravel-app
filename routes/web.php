<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---

use App\Models\Playlist;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// --- Public Routes ---

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('index');
})->name('index');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();
        return redirect()->route('home');
    }
    return back()->withErrors(['email' => 'Las credenciales no coinciden.']);
})->name('login.post');

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('register');
})->name('register');

Route::post('/register', function (Request $request) {
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    Auth::login($user);
    return redirect()->route('home');
})->name('register.post');

// --- Protected Routes (Real Auth) ---

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $playlists = Playlist::with('user')->latest()->get();
        return view('home', compact('playlists'));
    })->name('home');

    Route::get('/tienda', function () {
        return view('tienda');
    })->name('tienda');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/player', function () {
        $playlists = Playlist::with('user')->where('user_id', Auth::id())->get();
        return view('player', compact('playlists'));
    })->name('player');

    Route::get('/grupos', function () {
        return view('grupos');
    })->name('grupos');

    Route::post('/grupos', function (Request $request) {
        if ($request->group_id) {
            $group = Group::findOrFail($request->group_id);
            if ($group->user_id != Auth::id())
                abort(403);
            $group->update($request->only('name', 'category', 'description'));
            return back()->with('success', 'Comunidad actualizada.');
        }

        Group::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'members_count' => 1,
            'color' => 'linear-gradient(135deg, #6f42c1, #a370f7)',
            'icon' => 'fa-users'
        ]);
        return back()->with('success', 'Comunidad creada correctamente.');
    })->name('groups.store');

    Route::delete('/grupos/{group}', function (Group $group) {
        if ($group->user_id != Auth::id())
            abort(403);
        $group->delete();
        return response()->json(['status' => 'deleted']);
    })->name('groups.destroy');

    Route::get('/playlists/create', function () {
        return view('create_playlist');
    })->name('playlists.create');

    Route::post('/playlists', function (Request $request) {
        $data = $request->only(['title', 'description', 'mood', 'is_collaborative', 'songs']);
        $data['user_id'] = Auth::id();

        $uploadedFile = $request->file('cover');

        if ($uploadedFile && $uploadedFile->isValid()) {
            $path = $uploadedFile->store('covers', 'public');
            $data['cover'] = $path;
        } elseif ($request->mood) {
            $data['cover'] = "auto:{$request->mood}";
        }

        Playlist::create($data);
        return back()->with('success', 'Playlist creada correctamente.');
    })->name('playlists.store');

    Route::get('/playlists/{playlist}', function (\App\Models\Playlist $playlist) {
        $playlist->load('user');
        return view('playlist_detail', compact('playlist'));
    })->name('playlists.show');

    Route::get('/playlists/{playlist}/edit', function (\App\Models\Playlist $playlist) {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }
        return view('playlist_edit', compact('playlist'));
    })->name('playlists.edit');

    // 🔥 FIX SOLO AQUÍ (IMPORTANTE)
    Route::put('/playlists/{playlist}', function (Request $request, \App\Models\Playlist $playlist) {

        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->only(['title', 'description', 'mood', 'is_collaborative', 'songs']);

        $uploadedFile = $request->file('cover');

        if ($uploadedFile && $uploadedFile->isValid()) {

            // borrar anterior SOLO si no era auto
            if ($playlist->cover && !str_starts_with($playlist->cover, 'auto:')) {
                $oldPath = storage_path('app/public/' . $playlist->cover);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $data['cover'] = $uploadedFile->store('covers', 'public');
        }

        // ❌ IMPORTANTE: NO tocamos cover si no hay nueva imagen

        $playlist->update($data);

        return redirect()->route('playlists.show', $playlist)
            ->with('success', 'Playlist actualizada correctamente.');
    })->name('playlists.update');

    Route::delete('/playlists/{playlist}', function (\App\Models\Playlist $playlist) {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }
        $playlist->delete();
        return redirect()->route('home')->with('success', 'Playlist eliminada correctamente.');
    })->name('playlists.destroy');

    Route::post('/follow', function (Request $request) {
        $followedId = $request->followed_id;
        $follower = Auth::user();

        $isFollowing = $follower->following()->where('followed_id', $followedId)->exists();

        if ($isFollowing) {
            $follower->following()->detach($followedId);
            return response()->json(['status' => 'unfollowed']);
        } else {
            $follower->following()->attach($followedId);
            return response()->json(['status' => 'followed']);
        }
    })->name('follow');

    Route::post('/comments', function (Request $request) {
        $comment = \App\Models\Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'playlist_id' => $request->playlist_id
        ]);
        return response()->json(['status' => 'success', 'comment' => $comment, 'user_name' => Auth::user()->name]);
    })->name('comments.store');

    Route::post('/grupos/{group}/join', function (Group $group) {
        $user = Auth::user();
        if ($user->groups()->where('group_id', $group->id)->exists()) {
            $user->groups()->detach($group->id);
            $group->decrement('members_count');
            return response()->json(['status' => 'left']);
        } else {
            $user->groups()->attach($group->id);
            $group->increment('members_count');
            return response()->json(['status' => 'joined']);
        }
    })->name('groups.join');

    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/test/upload', function (Request $request) {
        $info = [
            'has_file' => $request->hasFile('avatar'),
            'storage_path' => storage_path('app/public'),
            'can_write' => @is_writable(storage_path('app/public')),
            'avatars_dir' => storage_path('app/public/avatars'),
            'avatars_exists' => is_dir(storage_path('app/public/avatars')),
            'avatars_writable' => @is_writable(storage_path('app/public/avatars')),
        ];
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $info['file_name'] = $file->getClientOriginalName();
            $info['file_mime'] = $file->getMimeType();
            $info['file_size'] = $file->getSize();
            $info['file_valid'] = $file->isValid();
            
            // Intentar guardar directamente
            try {
                $filename = time() . '_test_' . $file->getClientOriginalName();
                $path = $file->storeAs('avatars', $filename, 'public');
                $info['store_result'] = $path;
                $info['full_path'] = storage_path('app/public/avatars/' . $filename);
                $info['file_exists_after_store'] = file_exists($info['full_path']);
            } catch (\Exception $e) {
                $info['store_error'] = $e->getMessage();
            }
        }
        return response()->json($info);
    })->name('test.upload');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('index');
    })->name('logout');

    // ===== RUTAS API PARA COMENTARIOS Y LIKES =====
    // Comentarios
    Route::get('/api/playlists/{playlistId}/comments', [CommentController::class, 'getComments']);
    Route::post('/api/playlists/{playlistId}/comments', [CommentController::class, 'store']);
    Route::delete('/api/comments/{commentId}', [CommentController::class, 'destroy']);

    // Likes
    Route::get('/api/playlists/{playlistId}/likes/count', [LikeController::class, 'getCount']);
    Route::post('/api/playlists/{playlistId}/likes', [LikeController::class, 'store']);
    Route::delete('/api/playlists/{playlistId}/likes', [LikeController::class, 'destroy']);
});