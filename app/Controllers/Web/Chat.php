<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Contact;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Контроллер для веб-страниц чата
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
 */
class Chat extends BaseController
{
    protected Contact $contactModel;
    protected User $userModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
        $this->userModel = new User();
    }

    /**
     * Страница со списком контактов для чата
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function index(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');
        $contacts = $this->contactModel->getContacts($userId);

        return view('chat/index', ['contacts' => $contacts]);
    }

    /**
     * Страница диалога с конкретным контактом
     *
     * @param int $contactId
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function conversation(int $contactId): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');

        // Проверяем, что это контакт
        if (!$this->contactModel->isContact($userId, $contactId)) {
            return redirect()->to('chat')
                ->with('error', 'Этот пользователь не в вашем списке контактов');
        }

        // Получаем информацию о собеседнике
        $contact = $this->userModel->select('id, username, display_name, is_active')
            ->find($contactId);

        if (!$contact) {
            return redirect()->to('chat')
                ->with('error', 'Пользователь не найден');
        }

        return view('chat/conversation', [
            'contact' => $contact,
            'userId' => $userId
        ]);
    }
}