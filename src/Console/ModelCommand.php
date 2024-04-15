<?php

declare(strict_types=1);

namespace Yard\Models\Console;

use Roots\Acorn\Console\Commands\Command;
use Yard\Models\Facades\Model;

class ModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My custom Acorn command.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info(
            Model::getQuote()
        );
    }
}
