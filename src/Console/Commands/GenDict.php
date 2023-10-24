<?php

declare(strict_types=1);

namespace Juling\Generator\Console\Commands;

use Juling\Generator\Support\SchemaTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenDict extends Command
{
    use SchemaTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:dict';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate database dict';

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
    public function handle()
    {
        $database = env('DB_DATABASE');
        $tables = DB::select('show tables;');
        $tables = array_column($tables, 'Tables_in_'.$database);

        $content = "# 数据字典\n\n";
        foreach ($tables as $table) {
            if (in_array($table, $this->ignoreTable)) {
                continue;
            }

            $tableInfo = DB::select("SELECT `TABLE_COMMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table';");
            $content .= "### {$tableInfo[0]->TABLE_COMMENT}(`$table`)\n";

            $columns = $this->getTableColumns($database, $table);
            $content .= $this->getContent($columns);
        }

        file_put_contents(base_path('docs/dict/README.md'), $content);
    }

    public function getContent($columns): string
    {
        $content = "| 列名 | 数据类型 | 索引 | 是否为空 | 描述 |\n";
        $content .= "| ------- | --------- | --------- | --------- | -------------- |\n";
        foreach ($columns as $column) {
            $isNull = $column['Null'] === 'NO' ? '否' : '是';
            $content .= "| {$column['Field']} | {$column['Type']} | {$column['Key']} | $isNull | {$column['Comment']} |\n";
        }
        $content .= "\n";

        return $content;
    }
}
