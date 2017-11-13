<?php

namespace Exolnet\Database\Migrations;

trait BlocksRollback
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        die('['. self::class .'] Migrations cannot be rolled back before this point.'. PHP_EOL);
    }
}
