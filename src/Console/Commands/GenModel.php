<?php

declare(strict_types=1);

namespace Juling\Generator\Console\Commands;

use Juling\Generator\Support\SchemaTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenModel extends Command
{
    use SchemaTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate model objects';

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
        $tables = DB::select('show tables');
        $tables = array_column($tables, 'Tables_in_'.$database);

        foreach ($tables as $table) {
            if (in_array($table, $this->ignoreTable)) {
                continue;
            }

            $className = Str::studly(Str::singular($table));
            $columns = $this->getTableColumns($database, $table);

            $this->modelTpl($table, $className, $columns);
        }
    }

    private function modelTpl($tableName, $className, $columns): void
    {
        $softDelete = false;

        $fieldStr = '';
        foreach ($columns as $column) {
            $fieldStr .= str_pad(' ', 8)."'{$column['Field']}',\n";
            if ($column['Field'] === 'deleted_at') {
                $softDelete = true;
            }
        }
        $fieldStr = rtrim($fieldStr, "\n");

        $useSoftDelete = '';
        if ($softDelete) {
            $useSoftDelete = "    use SoftDeletes;\n";
        }

        $content = file_get_contents(__DIR__.'/stubs/model.stub');
        $content = str_replace([
            '{$name}',
            '$tableName',
            '$useSoftDelete',
            '$fieldStr',
        ], [
            $className,
            $tableName,
            $useSoftDelete,
            $fieldStr,
        ], $content);

        file_put_contents(app_path('Models/'.$className.'.php'), $content);
    }
}
