<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // DDL is auto-committed by MySQL/TiDB. If a deploy stops midway,
        // the table can exist even though this migration was not recorded.
        if (! Schema::hasTable('shops')) {
            Schema::create('shops', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('users', 'shop_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->after('role')->constrained('shops')->nullOnDelete();
            });
        }

        foreach (['products', 'categories', 'customers', 'orders', 'order_items', 'settings'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'shop_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                });
            }
        }

        $shopName = DB::table('settings')->where('key', 'shop_name')->value('value') ?: 'Default Shop';
        $shopId = DB::table('shops')->orderBy('id')->value('id');

        if ($shopId === null) {
            $shopId = DB::table('shops')->insertGetId([
                'name' => $shopName,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->where('role', '!=', 'admin')->update(['shop_id' => $shopId]);
        DB::table('shops')->where('id', $shopId)->whereNull('owner_id')->update([
            'owner_id' => DB::table('users')->where('role', 'owner')->value('id'),
        ]);

        foreach (['products', 'categories', 'customers', 'orders', 'order_items', 'settings'] as $tableName) {
            DB::table($tableName)->whereNull('shop_id')->update(['shop_id' => $shopId]);
        }

        if (Schema::hasColumn('settings', 'key')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_key_unique');
                $table->unique(['shop_id', 'key']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_shop_id_key_unique');
            $table->dropColumn('shop_id');
            $table->unique('key');
        });

        foreach (['products', 'categories', 'customers', 'orders', 'order_items'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['shop_id']);
                $table->dropColumn('shop_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::dropIfExists('shops');
    }
};
