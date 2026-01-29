<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupChatMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina mensajes de chat y sus archivos adjuntos que tengan más de 90 días.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = 90;
        $cutoff = Carbon::now()->subDays($days);

        $this->info("Iniciando limpieza de mensajes de chat anteriores a: " . $cutoff->toDateString());

        // Obtener mensajes con archivos para borrarlos del disco
        $messagesWithFiles = Message::where('created_at', '<', $cutoff)
            ->whereNotNull('file_path')
            ->get();

        $fileCount = 0;
        foreach ($messagesWithFiles as $message) {
            if (Storage::disk('public')->exists($message->file_path)) {
                Storage::disk('public')->delete($message->file_path);
                $fileCount++;
            }
        }

        // Borrar todos los mensajes antiguos de la base de datos
        $deletedCount = Message::where('created_at', '<', $cutoff)->delete();

        $this->info("Limpieza completada:");
        $this->info("- Mensajes eliminados: $deletedCount");
        $this->info("- Archivos eliminados del disco: $fileCount");

        return Command::SUCCESS;
    }
}
