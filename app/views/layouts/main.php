<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'LAN Install'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --sidebar-bg: #fff;
            --card-bg: #fff;
            --card-border: rgba(0,0,0,.125);
            --calendar-border: #dee2e6;  /* Light theme border color */
        }

        [data-bs-theme="dark"] {
            --bg-color: #212529;
            --text-color: #f8f9fa;
            --sidebar-bg: #2c3034;
            --card-bg: #2c3034;
            --card-border: #495057;
            --calendar-border: #ffffff;  /* White border for dark theme */
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        body {
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            position: sticky;
            top: 0;
            min-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;  /* Prevent horizontal scroll */
            background-color: var(--bg-color);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            z-index: 1000;
            flex: 0 0 auto;
            width: 100%;
            max-width: 400px;  /* Max width as requested */
            padding: 1rem 7px;    /* Added top/bottom padding */
            border-right: 1px solid var(--card-border);
        }
        .datepicker {
            border: 1px solid var(--calendar-border);
            border-radius: 0.5rem;
            width: 100%;
            max-width: 100%;
            height: 320px;  /* Increased height */
            margin: 0;
            padding: 1rem 0.75rem;  /* More vertical padding */
            background-color: var(--card-bg);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        
        .datepicker table {
            width: 100%;
            height: 100%;
            margin: 0;
        }
        .datepicker table {
            width: 100%;
        }
        .datepicker .day {
            width: 2.5rem;
            height: 2.5rem;
            line-height: 2.5rem;
            margin: 0.1rem;
            border-radius: 50%;
        }
        .datepicker .day:hover {
            background-color: #e9ecef;
        }
        .datepicker .day.active {
            background-color: #0d6efd;
            color: white;
        }
        
        .card {
            background-color: var(--card-bg);
            border-color: var(--card-border);
        }
        
        .form-control, .form-select, .input-group-text {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--card-border);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            padding-bottom: 0; /* Remove bottom padding to prevent overlap with sticky footer */
        }
        
        .nav-tabs .nav-link {
            color: var(--text-color);
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }
        
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: var(--card-bg);
            border-color: var(--card-border) var(--card-border) var(--card-bg);
        }
        
        .theme-toggle {
            cursor: pointer;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background-color: var(--card-bg);
            transform: scale(1.1);
        }
        
        .theme-icon {
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        .tab-content {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-top: none;
            padding: 1.5rem;
            border-radius: 0 0 0.25rem 0.25rem;
        }
    </style>
</head>
<body>

    <main>
        <?php include $content; ?>
    </main>
    
    <!-- Divider -->
    <hr class="my-0 border-top border-2 border-opacity-10">
    
    <!-- Footer -->
    <footer class="bg-dark text-white sticky-bottom">
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="row g-4 d-none">
                        <!-- Temporarily hidden footer content -->
                        <div class="col-md-4 text-center text-md-start">
                            <h5>О компании</h5>
                            <p class="text-muted small">Сервис для управления заявками и бригадами. Удобный инструмент для организации работы монтажных бригад.</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Контакты</h5>
                            <ul class="list-unstyled text-muted small">
                                <li><i class="bi bi-telephone me-2"></i> +7 (XXX) XXX-XX-XX</li>
                                <li><i class="bi bi-envelope me-2"></i> info@fursa.ru</li>
                                <li><i class="bi bi-geo-alt me-2"></i> г. Москва, ул. Примерная, 123</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5>Быстрые ссылки</h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white text-decoration-none">Главная</a></li>
                                <li><a href="#" class="text-white text-decoration-none">Услуги</a></li>
                                <li><a href="#" class="text-white text-decoration-none">Тарифы</a></li>
                                <li><a href="#" class="text-white text-decoration-none">Документация</a></li>
                            </ul>
                        </div>
                    </div>
                    <hr class="my-4 bg-secondary d-none">
                    <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-center text-md-start small">
                        &copy; 2025 lan-install.online. Все права защищены.
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center text-md-end">
                        <a href="#" class="text-white text-decoration-none me-3"><i class="bi bi-telegram"></i></a>
                        <a href="#" class="text-white text-decoration-none me-3"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="text-white text-decoration-none me-3"><i class="bi bi-vk"></i></a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ru.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
