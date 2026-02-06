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
        if (! Schema::hasTable('tasks')) {
            return;
        }

        // Normalize existing values first
        DB::table('tasks')->where('status', 'Pending')->update(['status' => 'pending']);

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support MODIFY/ENUM. Recreate the table with a string status column and copy data.
            Schema::rename('tasks', 'tasks_old');

            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                // Use string for SQLite (enum emulation)
                $table->string('status')->default('pending');
                $table->timestamps();
            });

            // Copy data over, converting 'Pending' -> 'pending' already handled above
            DB::statement("INSERT INTO tasks (id, title, description, status, created_at, updated_at) SELECT id, title, description, status, created_at, updated_at FROM tasks_old");

            Schema::drop('tasks_old');
        } else {
            // MySQL / MariaDB: use MODIFY to change enum values
            DB::statement("ALTER TABLE `tasks` MODIFY `status` ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks')) {
            // Revert enum to the previous set (preserving values as best-effort).
            DB::statement("ALTER TABLE `tasks` MODIFY `status` ENUM('Pending','in_progress','completed') NOT NULL DEFAULT 'pending'");

            // If there are lowercase 'pending' values, convert them back to 'Pending' to match previous enum semantics.
            DB::table('tasks')->where('status', 'pending')->update(['status' => 'Pending']);
        }
    }
};
