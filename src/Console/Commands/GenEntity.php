<?php

declare(strict_types=1);

namespace Juling\Generator\Console\Commands;

use Juling\Generator\Support\SchemaTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenEntity extends Command
{
    use SchemaTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:entity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate entity objects';

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
            $columns = $this->getTableColumns($database, $table);

            $this->entityTpl($className, $columns);
        }
    }

    private function entityTpl($name, $columns): void
    {
        $fields = "\n";
        foreach ($columns as $column) {
            if ($column['Field'] == 'default') {
                $column['Field'] = 'isDefault';
            }
            $fields .= "    #[OA\\Property(property: '{$column['Field']}', description: '{$column['Comment']}', type: '{$column['SwaggerType']}')]\n";
            $fields .= '    protected '.$column['BaseType'].' $'.Str::camel($column['Field']).";\n\n";
        }

        foreach ($columns as $column) {
            $fields .= $this->getSet(Str::camel($column['Field']), $column['BaseType'], $column['Comment'])."\n\n";
        }

        $fields = rtrim($fields, "\n");

        $content = file_get_contents(__DIR__.'/stubs/entity.stub');
        $content = str_replace([
            '{$name}',
            '{$fields}',
        ], [
            $name,
            $fields,
        ], $content);

        file_put_contents(app_path('Models/Entity/'.$name.'Entity.php'), $content);
    }
}
