<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        \Log::info('=== INICIANDO UPDATE DE PROFILE ===');
        \Log::info('Usuario actual:', ['id' => Auth::id(), 'name' => Auth::user()->name]);
        \Log::info('Datos recibidos:', $request->only('name', 'email', 'bio'));
        \Log::info('¿Tiene archivo?', ['has_file' => $request->hasFile('avatar')]);
        
        $user = Auth::user();

        // Validar solo datos de texto
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
        ]);

        \Log::info('Validación pasada');

        // Actualizar datos básicos
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? $user->bio;

        \Log::info('Datos básicos asignados:', [
            'name' => $user->name,
            'email' => $user->email,
            'bio' => $user->bio
        ]);

        // Procesar imagen si existe
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            try {
                $file = $request->file('avatar');
                
                \Log::info('Archivo recibido:', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ]);
                
                // Crear directorio si no existe
                @mkdir(storage_path('app/public/avatars'), 0777, true);
                
                // Generar nombre único para el archivo
                $originalName = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $ext;
                
                \Log::info('Nombre de archivo generado:', ['filename' => $filename]);
                
                // Guardar con move() en lugar de store()
                $destinationPath = storage_path('app/public/avatars');
                $file->move($destinationPath, $filename);
                
                \Log::info('Archivo movido exitosamente');
                
                // Verificar que el archivo existe
                $fullPath = $destinationPath . '/' . $filename;
                if (file_exists($fullPath)) {
                    \Log::info('Archivo existe en el sistema de archivos', ['path' => $fullPath]);
                } else {
                    \Log::error('Archivo NO existe después del move', ['path' => $fullPath]);
                }
                
                // Eliminar avatar anterior
                if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                    @unlink(storage_path('app/public/' . $user->avatar));
                    \Log::info('Avatar anterior eliminado');
                }
                
                // Guardar ruta relativa en la BD
                $user->avatar = 'avatars/' . $filename;
                
                \Log::info('Avatar asignado al usuario:', ['avatar' => $user->avatar]);
                
            } catch (\Exception $e) {
                \Log::error('Error al guardar avatar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar la imagen: ' . $e->getMessage()
                ], 500);
            }
        }

        // GUARDAR EL USUARIO
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'bio' => $user->bio,
        ];
        
        if ($user->avatar) {
            $data['avatar'] = $user->avatar;
        }
        
        \Log::info('Datos a guardar:', $data);
        
        $updateResult = $user->update($data);
        
        \Log::info('Resultado del update():', [
            'result' => $updateResult,
            'user_id' => $user->id,
        ]);
        
        // Recargar desde BD para verificar
        $userFromDB = \App\Models\User::find($user->id);
        \Log::info('Usuario desde BD después de update:', [
            'id' => $userFromDB->id,
            'avatar' => $userFromDB->avatar,
            'name' => $userFromDB->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'avatar_url' => $userFromDB->avatar ? asset('storage/' . $userFromDB->avatar) : null,
            'user' => $userFromDB
        ]);
    }
}
