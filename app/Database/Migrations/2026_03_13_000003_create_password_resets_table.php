<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Создание таблицы для восстановления пароля
 *
 * @noinspection PhpUnused
 */
class CreatePasswordResetsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('token');
        $this->forge->addKey('expires_at');
        $this->forge->createTable('password_resets');
    }

    public function down(): void
    {
        $this->forge->dropTable('password_resets');
    }
}