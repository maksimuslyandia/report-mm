
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const navbar = document.getElementById('navbar');

        // Restore sidebar state from localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            content.classList.add('collapsed');
            navbar.classList.add('collapsed');
        }

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
            navbar.classList.toggle('collapsed');

            // Save current state
            const currentlyCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', currentlyCollapsed);
        });
    });

</script>

<!-- Bootstrap JS -->
<script src="{{asset('bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js') }}"></script>

