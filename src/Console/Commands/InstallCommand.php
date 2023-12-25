<?php

declare(strict_types=1);

namespace Juling\Generator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generator tool init';

    public function handle(): void
    {
        $fs = new Filesystem();
        $root = dirname(__DIR__, 3);

        $fs->ensureDirectoryExists(app_path('Foundation/Exceptions'));
        $fs->copyDirectory($root.'/stubs/app/Exceptions', app_path('Foundation/Exceptions'));

        $fs->ensureDirectoryExists(storage_path('app/ts/services'));
        $fs->ensureDirectoryExists(storage_path('app/ts/types'));
    }
}