<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResignationForm;

class DeleteExpiredResignationForms extends Command
{
    protected $signature = 'resignation-forms:cleanup';
    protected $description = 'Elimina las cartas de renuncia creadas hace más de 10 minutos';

    public function handle()
    {
        $deleted = ResignationForm::where('created_at', '<=', now()->subMinutes(10))->delete();

        $this->info("Cartas de renuncia eliminadas: {$deleted}");
    }
}   