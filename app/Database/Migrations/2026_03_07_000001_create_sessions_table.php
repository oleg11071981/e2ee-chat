<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Миграция для создания таблицы сессий в базе данных
 *
 * Создаёт таблицу ci_sessions для хранения сессий пользователей.
 * Вызывается через CLI командой `php spark migrate`
 *
 * @noinspection PhpUnused
 */
class CreateSessionsTable extends Migration
{
    /**
     * Создание таблицы сессий
     *
     * @return void
     */
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => false,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false,
            ],
            'timestamp' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => 0,
            ],
            'data' => [
                'type' => 'BLOB',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('ci_sessions');
    }

    /**
     * Удаление таблицы сессий
     *
     * @return void
     */
    public function down(): void
    {
        $this->forge->dropTable('ci_sessions');
    }
}