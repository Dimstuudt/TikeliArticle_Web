<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PurgeSoftDeletedRecords extends Command
{
    /**
     * Nama dan signature command.
     *
     * Contoh: php artisan records:purge --models=Article,User --days=30
     */
    protected $signature = 'records:purge
                            {--models= : List model dipisahkan koma, contoh: Article,User atau App\\Models\\Article}
                            {--days=30 : Berapa hari setelah soft delete akan dihapus permanen}
                            {--dry-run : Simulasi saja tanpa hapus data}';

    /**
     * Deskripsi command.
     */
    protected $description = 'Hapus permanen record soft delete yang sudah melewati batas hari tertentu.';

    /**
     * Jalankan command.
     */
    public function handle()
    {
        $inputModels = explode(',', $this->option('models'));
        $defaultDays = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        foreach ($inputModels as $model) {
            $model = trim($model);

            // ✅ Cek namespace model
            if (! class_exists($model)) {
                if (class_exists("App\\Models\\{$model}")) {
                    $model = "App\\Models\\{$model}";
                } elseif (class_exists("App\\{$model}")) {
                    $model = "App\\{$model}";
                } else {
                    $this->error("❌ Model {$model} tidak ditemukan.");
                    continue;
                }
            }

            $days = $defaultDays;
            $expired = Carbon::now()->subDays($days);

            $query = $model::onlyTrashed()->where('deleted_at', '<', $expired);
            $count = $query->count();

            if ($dryRun) {
                $this->info("🔍 DRY-RUN: {$count} records akan dihapus permanen dari {$model} (lebih dari {$days} hari).");
            } else {
                $query->forceDelete();
                $this->info("✅ {$count} records dihapus permanen dari {$model} (lebih dari {$days} hari).");
            }
        }

        return Command::SUCCESS;
    }
}
