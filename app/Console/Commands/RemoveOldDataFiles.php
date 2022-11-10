<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveOldDataFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'KamarDS:removeOldDataFiles {days=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove data files older than {days}.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $days = $this->argument('days');

        collect(Storage::files('data'))
        ->filter(function($file) use ($days, $now){
            $modified = Storage::lastModified($file);
            return Carbon::createFromTimestamp($modified)->addDays($days)->lt($now);
        })
        ->each(function($file){
            Storage::delete($file);
        });


        return 0;
    }
}
