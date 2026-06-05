<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('engquiries')) {
            Schema::create('engquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone');
                $table->string('subject')->nullable();
                $table->text('message');
                $table->timestamps();
            });

            return;
        }

        $columns = collect(DB::select('SHOW COLUMNS FROM `engquiries`'))->keyBy('Field');

        if (! $columns->has('id')) {
            Schema::table('engquiries', function (Blueprint $table) {
                $table->id()->first();
            });
        } else {
            $indexes = collect(DB::select('SHOW INDEX FROM `engquiries`'));
            $hasIdIndex = $indexes->contains(fn ($index) => $index->Column_name === 'id');
            $hasPrimary = $indexes->contains(fn ($index) => $index->Key_name === 'PRIMARY');

            if (! $hasIdIndex && ! $hasPrimary) {
                DB::statement('ALTER TABLE `engquiries` ADD PRIMARY KEY (`id`)');
            } elseif (! $hasIdIndex) {
                DB::statement('ALTER TABLE `engquiries` ADD INDEX `engquiries_id_index` (`id`)');
            }

            DB::statement('ALTER TABLE `engquiries` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }

        Schema::table('engquiries', function (Blueprint $table) use ($columns) {
            if (! $columns->has('name')) {
                $table->string('name')->after('id');
            }

            if (! $columns->has('email')) {
                $table->string('email')->after('name');
            }

            if (! $columns->has('phone')) {
                $table->string('phone')->after('email');
            }

            if (! $columns->has('subject')) {
                $table->string('subject')->nullable()->after('phone');
            }

            if (! $columns->has('message')) {
                $table->text('message')->after('subject');
            }

            if (! $columns->has('created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (! $columns->has('updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Keep enquiry data and the repaired primary key intact.
    }
};
