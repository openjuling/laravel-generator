<?php

declare(strict_types=1);

namespace Juling\Generator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:dao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate repository objects';

    /**
     * 忽略表
     */
    private array $ignoreTable = [
        'failed_jobs',
        'jobs',
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $database = env('DB_DATABASE');
        $tables = DB::select('show tables;');
        $tables = array_column($tables, 'Tables_in_'.$database);

        foreach ($tables as $table) {
            if (in_array($table, $this->ignoreTable)) {
                continue;
            }

            $className = Str::studly(Str::singular($table));

            $this->repositoryTpl($className);
        }
    }

    private function repositoryTpl(string $name): void
    {
        $content = file_get_contents(__DIR__.'/stubs/repository.stub');
        $content = str_replace([
            '{$name}',
        ], [
            $name,
        ], $content);

        file_put_contents(app_path('Repositories/'.$name.'Repository.php'), $content);
    }
}
