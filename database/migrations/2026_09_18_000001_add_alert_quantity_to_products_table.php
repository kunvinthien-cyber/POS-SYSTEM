<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'alert_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('alert_quantity')->default(5)->after('stock');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'alert_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('alert_quantity');
            });
        }
    }
};
