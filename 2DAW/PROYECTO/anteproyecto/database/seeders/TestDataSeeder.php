<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Post;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            $this->command->error('Usuario test@example.com no encontrado.');
            return;
        }

        // Limpiar datos previos para evitar duplicados en re-seeds
        Transaction::where('user_id', $user->id)->delete();
        Asset::where('user_id', $user->id)->delete();
        Portfolio::where('user_id', $user->id)->delete();
        Post::where('user_id', $user->id)->delete();
        Ticket::where('user_id', $user->id)->delete();

        // 1. Crear Carteras
        $corePortfolio = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Cartera Core (Stocks & ETFs)',
            'description' => 'Inversión a largo plazo en activos tradicionales.',
        ]);

        $cryptoPortfolio = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Criptos & Alternativos',
            'description' => 'Activos de alta volatilidad y metales preciosos.',
        ]);

        // 2. Definir Activos
        $assetsData = [
            ['portfolio' => $corePortfolio, 'name' => 'Apple Inc.', 'ticker' => 'AAPL', 'type' => 'stock', 'color' => '#000000'],
            ['portfolio' => $corePortfolio, 'name' => 'Vanguard S&P 500', 'ticker' => 'VUSA', 'type' => 'etf', 'color' => '#10b981'],
            ['portfolio' => $cryptoPortfolio, 'name' => 'Bitcoin', 'ticker' => 'BTC', 'type' => 'crypto', 'color' => '#f7931a'],
            ['portfolio' => $cryptoPortfolio, 'name' => 'Gold Spot', 'ticker' => 'GOLD', 'type' => 'other', 'color' => '#fbbf24'],
        ];

        $assets = [];
        foreach ($assetsData as $data) {
            $assets[$data['ticker']] = Asset::create([
                'user_id' => $user->id,
                'portfolio_id' => $data['portfolio']->id,
                'name' => $data['name'],
                'ticker' => $data['ticker'],
                'type' => $data['type'],
                'color' => $data['color'],
                'quantity' => 0,
                'avg_buy_price' => 0,
                'current_price' => 100, // Placeholder
            ]);
        }

        // 3. Obtener Categorías
        $catNomina = Category::where('name', 'Nómina')->first()?->id;
        $catVivienda = Category::where('name', 'Hipoteca/Alquiler')->first()?->id;
        $catComida = Category::where('name', 'Supermercado')->first()?->id;
        $catOcio = Category::where('name', 'Suscripciones (Netflix, Spotify...)')->first()?->id;

        // 4. Bucle de 60 meses
        $startDate = Carbon::now()->subMonths(60)->startOfMonth();
        
        for ($i = 0; $i < 60; $i++) {
            $currentDate = $startDate->copy()->addMonths($i);
            
            // --- INGRESOS ---
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'income',
                'amount' => 3200 + rand(-200, 500),
                'date' => $currentDate->copy()->addDays(1),
                'category_id' => $catNomina,
                'description' => 'Sueldo Mensual Software Engineer',
            ]);

            // --- GASTOS ---
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 1100,
                'date' => $currentDate->copy()->addDays(2),
                'category_id' => $catVivienda,
                'description' => 'Alquiler Piso Centro',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 450 + rand(-50, 150),
                'date' => $currentDate->copy()->addDays(15),
                'category_id' => $catComida,
                'description' => 'Compra mensual Mercadona',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 60 + rand(0, 40),
                'date' => $currentDate->copy()->addDays(20),
                'category_id' => $catOcio,
                'description' => 'Suscripciones y ocio digital',
            ]);

            // --- INVERSIONES (DCA) ---
            // Repartir 400€ al mes en 4 activos
            $dcaAmountPerAsset = 100;
            foreach ($assets as $ticker => $asset) {
                // Simular un precio que sube ligeramente en el tiempo
                $basePrice = ($ticker == 'BTC') ? 20000 : 100;
                $price = $basePrice + ($i * ($basePrice * 0.02)) + rand(-10, 10);
                $qty = $dcaAmountPerAsset / $price;

                Transaction::create([
                    'user_id' => $user->id,
                    'portfolio_id' => $asset->portfolio_id,
                    'asset_id' => $asset->id,
                    'type' => 'buy',
                    'amount' => $dcaAmountPerAsset,
                    'quantity' => $qty,
                    'price_per_unit' => $price,
                    'date' => $currentDate->copy()->addDays(5),
                    'description' => "Compra mensual DCA $ticker",
                ]);

                // Actualizar Asset
                $asset->quantity += $qty;
                $asset->avg_buy_price = (($asset->avg_buy_price * ($asset->quantity - $qty)) + ($price * $qty)) / $asset->quantity;
                $asset->save();
            }
        }

        // 5. Actividad Social
        Post::create([
            'user_id' => $user->id,
            'content' => 'Llevo 5 años haciendo DCA en BTC y no podría estar más contento con los resultados. La clave es la paciencia. #Bitcoin #DCA',
            'created_at' => Carbon::now()->subDays(10),
        ]);

        Post::create([
            'user_id' => $user->id,
            'content' => '¿Qué opináis de diversificar un 5% en Oro físico en el contexto actual? He añadido algo a mi cartera alternativa.',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // 6. Tickets de Soporte
        $ticket1 = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Problema con sincronización bancaria',
            'status' => 'closed',
            'priority' => 'medium',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $user->id,
            'message' => 'No puedo conectar mi cuenta de Revolut, me da un error de timeout.',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket1->id,
            'user_id' => 1, // Admin
            'message' => '¡Hola! Hemos revisado el log y parece que fue una caída puntual de la API de Revolut. Por favor, inténtalo de nuevo.',
        ]);

        $ticket2 = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Sugerencia: Gráficos de dividendos',
            'status' => 'open',
            'priority' => 'low',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $user->id,
            'message' => 'Me gustaría ver una proyección de dividendos anuales en el dashboard.',
        ]);

        $this->command->info('Ecosistema de datos generado para test@example.com (60 meses de historial).');
    }
}
