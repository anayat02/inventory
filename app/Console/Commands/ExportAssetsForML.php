<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportAssetsForML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-assets-for-m-l';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = \DB::table('in_product_lists')->get();

        $data = [];

        foreach ($products as $p) {
            $data[] = [
                'usage_years' => \Carbon\Carbon::parse($p->created_at)->diffInYears(now()),
                'scan_count' => $p->scan_count,
                'type' => $p->type,
                'write_off' => $p->write_off,
            ];
        }

        file_put_contents(
            storage_path('app/ml/assets.json'),
            json_encode($data)
        );

        $this->info('Data exported');
    }

}
