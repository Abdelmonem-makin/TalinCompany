<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $purchases = DB::table('purchases')->whereNull('reference_id')->get();
        foreach ($purchases as $purchase) {
            $nextId = $purchase->id;
            $purchaseNumber = 'PUR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            DB::table('purchases')->where('id', $purchase->id)->update(['reference_id' => $purchaseNumber]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally reverse, but since it's adding data, maybe not needed
    }
};
