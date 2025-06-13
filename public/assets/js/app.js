// Common JavaScript for all pages

// Theme toggle functionality
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
    
    // If theme toggle elements don't exist, exit
    if (!themeToggle || !sunIcon || !moonIcon) return;
    
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme');
    
    // Check for saved theme preference or use system preference
    if (currentTheme === 'dark' || (!currentTheme && prefersDarkScheme.matches)) {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        sunIcon.classList.remove('d-none');
        moonIcon.classList.add('d-none');
    } else {
        document.documentElement.setAttribute('data-bs-theme', 'light');
        sunIcon.classList.add('d-none');
        moonIcon.classList.remove('d-none');
    }
    
    // Toggle theme on icon click
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
            sunIcon.classList.add('d-none');
            moonIcon.classList.remove('d-none');
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            sunIcon.classList.remove('d-none');
            moonIcon.classList.add('d-none');
        }
    });
}

// Datepicker initialization
function initDatepicker() {
    const $datepicker = $('#datepicker');
    const $dateInput = $('#dateInput');
    const $selectedDate = $('#selectedDate');
    
    // If datepicker elements don't exist, exit
    if ($datepicker.length === 0 || $dateInput.length === 0 || $selectedDate.length === 0) return;
    
    // Initialize datepicker
    $datepicker.datepicker({
        format: 'dd.mm.yyyy',
        language: 'ru',
        autoclose: true,
        todayHighlight: true,
        container: '#datepicker'
    });

    // Sync datepicker with input field
    $datepicker.on('changeDate', function(e) {
        $dateInput.val(e.format('dd.mm.yyyy'));
        $selectedDate.text(e.format('dd.mm.yyyy'));
    });

    // Initialize input field datepicker
    $dateInput.datepicker({
        format: 'dd.mm.yyyy',
        language: 'ru',
        autoclose: true,
        todayHighlight: true,
        container: '#datepicker'
    });

    // Set today's date on load
    const today = new Date();
    const formattedDate = today.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    $datepicker.datepicker('update', today);
    $dateInput.val(formattedDate);
    $selectedDate.text(formattedDate);
}

// Initialize all components when DOM is ready
$(document).ready(function() {
    initThemeToggle();
    initDatepicker();
});
