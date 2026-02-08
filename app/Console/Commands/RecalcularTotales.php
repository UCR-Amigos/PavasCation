<?php

namespace App\Console\Commands;

use App\Models\Culto;
use App\Services\CalculoTotalesCultoService;
use Illuminate\Console\Command;

class RecalcularTotales extends Command
{
    protected $signature = 'totales:recalcular';
    protected $description = 'Recalcula los totales de todos los cultos desde los sobres/detalles';

    public function handle(CalculoTotalesCultoService $service): int
    {
        $cultos = Culto::all();
        $bar = $this->output->createProgressBar($cultos->count());

        $this->info("Recalculando totales de {$cultos->count()} cultos...");
        $bar->start();

        foreach ($cultos as $culto) {
            $service->recalcular($culto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Totales recalculados exitosamente.');

        return Command::SUCCESS;
    }
}
