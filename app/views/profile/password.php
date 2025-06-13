<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?= $data['title'] ?? 'Смена пароля' ?></h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($data['success']) ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['errors']['general'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($data['errors']['general']) ?></div>
                    <?php endif; ?>
                    
                    <form action="/profile/password" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Текущий пароль</label>
                            <input type="password" class="form-control <?= !empty($data['errors']['current_password']) ? 'is-invalid' : '' ?>" 
                                   id="current_password" name="current_password" required>
                            <?php if (!empty($data['errors']['current_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($data['errors']['current_password']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Новый пароль</label>
                            <input type="password" class="form-control <?= !empty($data['errors']['new_password']) ? 'is-invalid' : '' ?>" 
                                   id="new_password" name="new_password" required minlength="6">
                            <div class="form-text">Пароль должен содержать не менее 6 символов</div>
                            <?php if (!empty($data['errors']['new_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($data['errors']['new_password']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Подтвердите новый пароль</label>
                            <input type="password" class="form-control <?= !empty($data['errors']['new_password_confirmation']) ? 'is-invalid' : '' ?>" 
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                            <?php if (!empty($data['errors']['new_password_confirmation'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($data['errors']['new_password_confirmation']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Изменить пароль</button>
                            <a href="/profile" class="btn btn-outline-secondary">Назад к профилю</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
