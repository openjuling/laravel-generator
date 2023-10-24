<?php

declare(strict_types=1);

namespace Juling\Generator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate service objects';

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

            // $this->inputTpl($className);
            // $this->outputTpl($className);
            $this->serviceTpl($className);
        }
    }

    private function inputTpl(string $name): void
    {
        $content = file_get_contents(__DIR__.'/stubs/input.stub');
        $content = str_replace([
            '{$name}',
        ], [
            $name,
        ], $content);

        file_put_contents(app_path('Services/Input/'.$name.'Input.php'), $content);
    }

    private function outputTpl(string $name): void
    {
        $content = file_get_contents(__DIR__.'/stubs/output.stub');
        $content = str_replace([
            '{$name}',
        ], [
            $name,
        ], $content);

        file_put_contents(app_path('Services/Output/'.$name.'Output.php'), $content);
    }

    private function serviceTpl(string $name): void
    {
        $content = file_get_contents(__DIR__.'/stubs/service.stub');
        $content = str_replace([
            '{$name}',
        ], [
            $name,
        ], $content);
        $serviceFile = app_path('Services/'.$name.'Service.php');
        if (! file_exists($serviceFile)) {
            file_put_contents($serviceFile, $content);
        }
    }
}
