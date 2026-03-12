<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('accounts', 'balance')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->decimal('balance', 16, 2)->nullable()->default(0)->after('type');
            });
            // try copy from total if exists
            if (Schema::hasColumn('accounts', 'total')) {
                DB::statement('UPDATE accounts SET balance = total');
            }
        }

    }

    public function down()
    {
        if (Schema::hasColumn('accounts', 'balance')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('balance');
            });
        }
    
    }
};