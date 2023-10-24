<?php

declare(strict_types=1);

namespace Juling\Generator\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait SchemaTrait
{
    private function getTableColumns($database, $tableName): array
    {
        $sql = "SELECT COLUMN_NAME,COLUMN_COMMENT FROM	information_schema.COLUMNS WHERE TABLE_SCHEMA = '{$database}' AND TABLE_NAME = '{$tableName}'";
        $result = DB::select($sql);

        $comments = [];
        foreach ($result as $row) {
            $comments[$row->COLUMN_NAME] = $row->COLUMN_COMMENT;
        }

        $sql = 'desc '.$tableName;
        $result = DB::select($sql);

        $columns = [];
        foreach ($result as $row) {
            $row = collect($row)->toArray();
            $row['Comment'] = $comments[$row['Field']];
            $row['BaseType'] = $this->getFieldType($row['Type']);
            $row['SwaggerType'] = $row['BaseType'] === 'int' ? 'integer' : $row['BaseType'];
            $columns[] = $row;
        }

        return $columns;
    }

    private function getFieldType($type): string
    {
        preg_match('/(\w+)\(/', $type, $m);
        $type = $m[1] ?? $type;
        $type = str_replace(' unsigned', '', $type);
        if (in_array($type, ['bit', 'int', 'bigint', 'mediumint', 'smallint', 'tinyint', 'enum'])) {
            $type = 'int';
        }
        if (in_array($type, ['varchar', 'char', 'text', 'mediumtext', 'longtext'])) {
            $type = 'string';
        }
        if (in_array($type, ['decimal', 'float'])) {
            $type = 'float';
        }
        if (in_array($type, ['date', 'datetime', 'timestamp', 'time'])) {
            $type = 'string';
        }

        return $type;
    }

    private function getSet($field, $type, $comment): string
    {
        $capitalName = Str::studly($field);

        return <<<EOF
    /**
     * 获取{$comment}
     */
    public function get{$capitalName}(): $type
    {
        return \$this->$field;
    }

    /**
     * 设置{$comment}
     */
    public function set{$capitalName}($type \${$field}): void
    {
        \$this->$field = \${$field};
    }
EOF;
    }
}
