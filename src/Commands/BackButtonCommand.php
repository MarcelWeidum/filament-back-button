<?php

namespace MarcelWeidum\BackButton\Commands;

use Illuminate\Console\Command;

class BackButtonCommand extends Command
{
    public $signature = 'filament-back-button';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
