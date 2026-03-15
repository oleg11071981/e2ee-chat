<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Переименование таблицы сессий
 *
 * @noinspection PhpUnused
 */
class RenameSessionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->renameTable('ci_sessions', 'user_sessions');
    }

    public function down(): void
    {
        $this->forge->renameTable('user_sessions', 'ci_sessions');
    }
}