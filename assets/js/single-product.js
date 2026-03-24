document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-link');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-tab');

            // Убираем активный класс у всех вкладок и панелей
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));

            // Добавляем активный класс текущей вкладке и соответствующей панели
            this.classList.add('active');
            document.getElementById('tab-' + targetId).classList.add('active');
        });
    });
});