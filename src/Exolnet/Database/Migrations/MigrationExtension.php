<?php

namespace Exolnet\Database\Migrations;

use DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Schema;

trait MigrationExtension
{
    /**
     * @param string $table
     * @param array $columns
     */
    public function renameColumns($table, array $columns)
    {
        if ($this->isSQLite()) {
            foreach ($columns as $before => $after) {
                Schema::table($table, function (Blueprint $table) use ($before, $after) {
                    $table->renameColumn($before, $after);
                });
            }
        } else {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                foreach ($columns as $before => $after) {
                    $table->renameColumn($before, $after);
                }
            });
        }

    }

    /**
     * @return bool
     */
    private function isSQLite()
    {
        return DB::connection() instanceof SQLiteConnection;
    }
}