    <div class="container-fluid g-0">
        <div class="row g-0" style="min-height: 100vh;">
            <!-- Left Sidebar with Calendar -->
            <div class="col-auto sidebar p-3">
                <h4 class="mb-4">Календарь</h4>
                <div id="datepicker"></div>
                <div class="mt-3 d-none">
                    <div class="form-group">
                        <label for="dateInput" class="form-label">Выберите дату:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="dateInput" placeholder="дд.мм.гггг">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="container-fluid position-relative" style="min-height: 100vh;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="mb-0">Добро пожаловать</h1>
                        <div class="theme-toggle position-fixed" id="themeToggle" style="top: 1rem; right: 1rem; z-index: 1050;">
                            <i class="bi bi-sun theme-icon" id="sunIcon"></i>
                            <i class="bi bi-moon-stars-fill theme-icon d-none" id="moonIcon"></i>
                        </div>
                    </div>
                    
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="mainTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab">Заявки</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="teams-tab" data-bs-toggle="tab" data-bs-target="#teams" type="button" role="tab">Бригады</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" role="tab">Адреса</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Пользователи</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">Отчеты</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="mainTabsContent">
                        <div class="tab-pane fade show active" id="requests" role="tabpanel">
                            <h4>Заявки</h4>
                            <p>Здесь будет отображаться список заявок. Вы можете создавать, просматривать и управлять заявками на выполнение работ. Используйте фильтры для поиска нужных заявок по статусу, дате или другим параметрам.</p>
                            <p>Для создания новой заявки нажмите кнопку "Добавить заявку" в правом верхнем углу таблицы. Вы сможете указать все необходимые детали, включая описание работ, адрес и приоритет.</p>
                        </div>
                        <div class="tab-pane fade" id="teams" role="tabpanel">
                            <h4>Бригады</h4>
                            <p>В этом разделе отображается информация о бригадах. Вы можете просматривать состав бригад, их загрузку и текущие задачи. Для каждой бригады доступна контактная информация ответственного лица.</p>
                            <p>Используйте этот раздел для назначения заявок на бригады и контроля за выполнением работ. Вы можете фильтровать бригады по специализации или текущему статусу.</p>
                        </div>
                        <div class="tab-pane fade" id="addresses" role="tabpanel">
                            <h4>Адреса</h4>
                            <p>Справочник адресов позволяет вести учет всех объектов, с которыми вы работаете. Для каждого адреса хранится полная контактная информация, история обращений и выполненных работ.</p>
                            <p>Добавляйте новые адреса вручную или импортируйте их из файла. Система автоматически проверяет дубликаты и предлагает объединить похожие записи.</p>
                        </div>
                        <div class="tab-pane fade" id="users" role="tabpanel">
                            <h4>Пользователи</h4>
                            <p>Управление пользователями системы. В этом разделе вы можете создавать новые учетные записи, назначать роли и права доступа. Для каждого пользователя можно настроить уведомления и персональные настройки.</p>
                            <p>Используйте фильтры для поиска пользователей по отделам, ролям или статусу активности. Вы можете экспортировать список пользователей в различных форматах.</p>
                        </div>
                        <div class="tab-pane fade" id="reports" role="tabpanel">
                            <h4>Отчеты</h4>
                            <p>В этом разделе доступны различные отчеты по деятельности компании. Вы можете анализировать загруженность бригад, финансовые показатели и эффективность работы.</p>
                            <p>Настраивайте автоматическую отправку отчетов на электронную почту в удобное для вас время. Доступны шаблоны отчетов для различных подразделений компании.</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Выберите дату в календаре</h5>
                            <p class="card-text">Используйте календарь слева для выбора даты.</p>
                            <p>Выбранная дата: <span id="selectedDate" class="fw-bold">не выбрана</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


