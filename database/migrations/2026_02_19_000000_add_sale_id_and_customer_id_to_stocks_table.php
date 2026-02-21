<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('stocks')) {
            return;
        }

        Schema::table('stocks', function (Blueprint $table) {
            if (! Schema::hasColumn('stocks', 'sale_id')) {
                $table->unsignedBigInteger('sale_id')->nullable()->after('reference_id');
            }
            if (! Schema::hasColumn('stocks', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('sale_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('stocks')) {
            return;
        }

        Schema::table('stocks', function (Blueprint $table) {
            if (Schema::hasColumn('stocks', 'sale_id')) {
                $table->dropColumn('sale_id');
            }
            if (Schema::hasColumn('stocks', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};
