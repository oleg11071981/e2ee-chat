<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Добавление поля отображаемого имени в таблицу users
 *
 * @noinspection PhpUnused
 */
class AddDisplayNameToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'display_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'username',
                'comment' => 'Отображаемое имя в чате'
            ]
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'display_name');
    }
}