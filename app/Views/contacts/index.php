<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Мои контакты<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/contacts.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array $contacts Список контактов
 * @var int $count Количество контактов
 */
?>
    <div class="contacts-container">
        <div class="contacts-header">
            <div>
                <h1>Мои контакты</h1>
                <p class="contacts-count">Всего контактов: <?= $count ?></p>
            </div>
            <div class="header-actions">
                <a href="<?= base_url('contacts/search') ?>" class="btn btn-primary">
                    <i class="fas fa-search"></i> Найти пользователей
                </a>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>
        </div>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= session('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <p><?= session('error') ?></p>
            </div>
        <?php endif; ?>

        <?php if (empty($contacts)): ?>
            <div class="no-contacts">
                <i class="fas fa-address-book fa-3x"></i>
                <h2>У вас пока нет контактов</h2>
                <p>Найдите пользователей и добавьте их в список контактов, чтобы начать общение.</p>
            </div>
        <?php else: ?>
            <div class="contacts-grid">
                <?php foreach ($contacts as $contact): ?>
                    <div class="contact-card <?= $contact['is_active'] ? 'active' : 'inactive' ?>">
                        <div class="contact-avatar">
                            <?php
                            $name = $contact['display_name'] ?? $contact['username'];
                            $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                            ?>
                            <span class="avatar-initials"><?= $initials ?></span>
                        </div>

                        <div class="contact-info">
                            <div class="contact-name">
                                <strong><?= esc($contact['display_name'] ?? $contact['username']) ?></strong>
                                <?php if ($contact['is_active']): ?>
                                    <span class="online-indicator" title="Онлайн"></span>
                                <?php else: ?>
                                    <span class="offline-indicator" title="Офлайн"></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="contact-actions">
                            <a href="<?= base_url('chat/' . $contact['id']) ?>"
                               class="btn btn-primary btn-icon"
                               title="Написать сообщение">
                                <i class="fas fa-comment"></i>
                            </a>
                            <form action="<?= base_url('contacts/remove/' . $contact['id']) ?>"
                                  method="POST"
                                  class="inline-form"
                                  onsubmit="return confirm('Удалить контакт из списка?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-icon" title="Удалить контакт">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>