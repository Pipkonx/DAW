<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * FileStorageService - Gestiona el almacenamiento de medios del usuario.
 * 
 * Se encarga de guardar y eliminar avatares y banners, manteniendo 
 * el sistema de archivos limpio y organizado.
 */
class FileStorageService
{
    /**
     * Almacena un avatar y devuelve la ruta pública.
     */
    public function storeAvatar(UploadedFile $file): string
    {
        $path = $file->store('avatars', 'public');
        return "/storage/{$path}";
    }

    /**
     * Almacena un banner y devuelve la ruta interna.
     */
    public function storeBanner(UploadedFile $file): string
    {
        return $file->store('banners', 'public');
    }

    /**
     * Elimina un archivo físico del almacenamiento.
     */
    public function deleteFile(?string $path, string $disk = 'public'): void
    {
        if (!$path) return;

        // Limpiar prefijo /storage/ si existe para encontrar el archivo real
        $cleanPath = str_replace('/storage/', '', $path);

        if (Storage::disk($disk)->exists($cleanPath)) {
            Storage::disk($disk)->delete($cleanPath);
        }
    }

    /**
     * Verifica si una ruta es una URL externa (ej. Google Avatar).
     */
    public function isExternal(?string $path): bool
    {
        if (!$path) return false;
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }
}
