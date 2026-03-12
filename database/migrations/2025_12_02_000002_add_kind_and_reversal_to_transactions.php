<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('transactions', 'kind')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('kind', 50)->nullable()->after('type');
                $table->boolean('reversed')->default(false)->after('kind');
                $table->string('reversal_note')->nullable()->after('reversed');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('transactions', 'kind')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn(['kind', 'reversed', 'reversal_note']);
            });
        }
    }
};