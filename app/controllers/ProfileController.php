<?php
class ProfileController extends Controller {
    private $auth;
    
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->auth = new Auth();
        
        // Ensure user is logged in for all methods in this controller
        if (!$this->auth->check()) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Show user profile
     */
    public function show() {
        $user = $this->auth->user();
        
        $data = [
            'title' => 'Мой профиль',
            'user' => $user,
            'errors' => $_SESSION['form_errors'] ?? [],
            'success' => $_SESSION['success_message'] ?? null
        ];
        
        // Clear flash messages
        unset($_SESSION['form_errors'], $_SESSION['success_message']);
        
        $this->view('profile/show', $data);
    }
    
    /**
     * Update user profile
     */
    public function update() {
        if ($this->request->getMethod() !== 'PUT') {
            $this->response->setStatusCode(405);
            $this->response->setContent('Method Not Allowed');
            return;
        }
        
        $user = $this->auth->user();
        $input = $this->request->put();
        
        // Validate input
        $errors = [];
        
        if (empty($input['name'])) {
            $errors['name'] = 'Имя обязательно для заполнения';
        }
        
        if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        }
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/profile');
            return;
        }
        
        // Update user data
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE users SET name = :name, email = :email, updated_at = NOW() WHERE id = :id",
                [
                    'name' => $input['name'],
                    'email' => $input['email'] ?? $user['email'],
                    'id' => $user['id']
                ]
            );
            
            // Update session
            $updatedUser = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $user['id']]);
            $_SESSION['user'] = $updatedUser;
            
            $_SESSION['success_message'] = 'Профиль успешно обновлен';
        } catch (Exception $e) {
            error_log('Error updating profile: ' . $e->getMessage());
            $_SESSION['form_errors']['general'] = 'Произошла ошибка при обновлении профиля';
        }
        
        $this->redirect('/profile');
    }
    
    /**
     * Show change password form
     */
    public function showPasswordForm() {
        $data = [
            'title' => 'Смена пароля',
            'errors' => $_SESSION['form_errors'] ?? []
        ];
        
        unset($_SESSION['form_errors']);
        
        $this->view('profile/password', $data);
    }
    
    /**
     * Update user password
     */
    public function updatePassword() {
        if ($this->request->getMethod() !== 'PUT') {
            $this->response->setStatusCode(405);
            $this->response->setContent('Method Not Allowed');
            return;
        }
        
        $user = $this->auth->user();
        $input = $this->request->put();
        
        // Validate input
        $errors = [];
        
        if (empty($input['current_password'])) {
            $errors['current_password'] = 'Текущий пароль обязателен';
        }
        
        if (empty($input['new_password'])) {
            $errors['new_password'] = 'Новый пароль обязателен';
        } elseif (strlen($input['new_password']) < 6) {
            $errors['new_password'] = 'Пароль должен содержать не менее 6 символов';
        }
        
        if ($input['new_password'] !== $input['new_password_confirmation']) {
            $errors['new_password_confirmation'] = 'Пароли не совпадают';
        }
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/profile/password');
            return;
        }
        
        // Verify current password
        if (!password_verify($input['current_password'], $user['password_hash'])) {
            $_SESSION['form_errors'] = ['current_password' => 'Неверный текущий пароль'];
            $this->redirect('/profile/password');
            return;
        }
        
        // Update password
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id",
                [
                    'password_hash' => password_hash($input['new_password'], PASSWORD_DEFAULT),
                    'id' => $user['id']
                ]
            );
            
            $_SESSION['success_message'] = 'Пароль успешно изменен';
            $this->redirect('/profile');
        } catch (Exception $e) {
            error_log('Error updating password: ' . $e->getMessage());
            $_SESSION['form_errors'] = ['general' => 'Произошла ошибка при смене пароля'];
            $this->redirect('/profile/password');
        }
    }
}
