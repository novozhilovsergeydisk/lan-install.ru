<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?= $data['title'] ?? 'Профиль пользователя' ?></h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($data['success']) ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['errors']['general'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($data['errors']['general']) ?></div>
                    <?php endif; ?>
                    
                    <form action="/profile" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя</label>
                            <input type="text" class="form-control <?= !empty($data['errors']['name']) ? 'is-invalid' : '' ?>" 
                                   id="name" name="name" value="<?= htmlspecialchars($data['user']['name'] ?? '') ?>" required>
                            <?php if (!empty($data['errors']['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($data['errors']['name']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= !empty($data['errors']['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" value="<?= htmlspecialchars($data['user']['email'] ?? '') ?>" required>
                            <?php if (!empty($data['errors']['email'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($data['errors']['email']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            <a href="/profile/password" class="btn btn-outline-secondary">Сменить пароль</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
