<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invoices', 'user_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('invoices', 'invoice_number')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('invoice_number')->nullable()->unique()->after(Schema::hasColumn('invoices', 'user_id') ? 'user_id' : 'id');
            });
        }

        if (! Schema::hasColumn('invoices', 'client_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('client_name')->nullable()->after('currency');
            });
        }

        if (! Schema::hasColumn('invoices', 'client_email')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('client_email')->nullable()->after('client_name');
            });
        }

        if (! Schema::hasColumn('invoices', 'client_phone')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('client_phone')->nullable()->after('client_email');
            });
        }

        if (! Schema::hasColumn('invoices', 'client_address')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->text('client_address')->nullable()->after('client_phone');
            });
        }

        if (! Schema::hasColumn('invoices', 'notes')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('client_address');
            });
        }

        if (! Schema::hasColumn('invoices', 'paid_at')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->timestamp('paid_at')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('invoice_items', 'quantity')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('quantity', 10, 2)->default(1)->after('description');
            });
        }

        if (! Schema::hasColumn('invoice_items', 'unit_price')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('unit_price', 15, 2)->nullable()->after('quantity');
            });
        }

        DB::table('invoices')
            ->whereNull('invoice_number')
            ->orderBy('id')
            ->chunkById(100, function ($invoices) {
                foreach ($invoices as $invoice) {
                    DB::table('invoices')
                        ->where('id', $invoice->id)
                        ->update(['invoice_number' => 'INV-' . str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT)]);
                }
            });

        if (Schema::hasColumn('invoice_items', 'unit_price')) {
            DB::table('invoice_items')
                ->whereNull('unit_price')
                ->update(['unit_price' => DB::raw('amount')]);
        }
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            foreach (['unit_price', 'quantity'] as $column) {
                if (Schema::hasColumn('invoice_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'user_id')) {
                $table->dropForeign(['user_id']);
            }

            foreach (['paid_at', 'notes', 'client_address', 'client_phone', 'client_email', 'client_name', 'invoice_number', 'user_id'] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
