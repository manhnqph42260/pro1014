                </div> <!-- End #mainContent -->
            </div> <!-- End .guide-content -->
        </div> <!-- End .row -->
    </div> <!-- End .container-fluid -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>

    <script>
    // Initialize flatpickr with Vietnamese locale
    flatpickr.localize(flatpickr.l10ns.vi);
    
    // Initialize DataTables
    $(document).ready(function() {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            pageLength: 25,
            responsive: true
        });
    });
    
    // Offline mode detection
    function updateOnlineStatus() {
        if (!navigator.onLine) {
            showOfflineAlert();
        }
    }
    
    function showOfflineAlert() {
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-0 w-100 rounded-0';
        alert.style.zIndex = 1060;
        alert.innerHTML = `
            <i class="bi bi-wifi-off me-2"></i>
            <strong>Mất kết nối Internet</strong> - Ứng dụng đang chạy ở chế độ offline. 
            Dữ liệu sẽ được lưu cục bộ và tự động đồng bộ khi có kết nối.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.prepend(alert);
    }
    
    // Check online status
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();
    
    // Auto-save draft data
    setInterval(function() {
        if (typeof saveDraftData === 'function') {
            saveDraftData();
        }
    }, 60000); // Auto-save every minute
    
    // Back to top button
    const backToTopButton = document.createElement('button');
    backToTopButton.className = 'btn btn-primary rounded-circle position-fixed bottom-3 end-3';
    backToTopButton.style.width = '50px';
    backToTopButton.style.height = '50px';
    backToTopButton.style.zIndex = 1040;
    backToTopButton.innerHTML = '<i class="bi bi-arrow-up"></i>';
    backToTopButton.onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
    document.body.appendChild(backToTopButton);
    
    // Show/hide back to top button
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    </script>
    
    <footer class="bg-light border-top mt-4 py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-c-circle"></i> <?php echo date('Y'); ?> Tour Management System
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        HDV: <?php echo htmlspecialchars($guide_name); ?> | 
                        Phiên bản: 1.0.0 | 
                        <span id="lastSync"></span>
                    </small>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>