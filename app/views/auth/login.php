<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4 mt-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Вход в систему</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php foreach ($fieldErrors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/login" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" 
                                id="email" 
                                name="email" 
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                                required
                            >
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input 
                                type="password" 
                                class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" 
                                id="password" 
                                name="password" 
                                required
                            >
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
