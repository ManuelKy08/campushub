<?php
/**
 * CampusHub - Notification/Toast System
 * Fungsi: Menampilkan notifikasi toast sukses/error
 * Digunakan: Setelah login, register, submit form, dan lainnya
 */
if (!defined('IN_CAMPUSHUB')) die('Akses dilarang');

// Ambil pesan notifikasi dari session
$toast_message = $_SESSION['toast_message'] ?? null;
$toast_type = $_SESSION['toast_type'] ?? null;

// Hapus pesan setelah ditampilkan (satu pakai saja)
if ($toast_message) {
    unset($_SESSION['toast_message']);
    unset($_SESSION['toast_type']);
}
?>

<?php if ($toast_message): ?>
<div class="fixed top-4 right-4 z-50 max-w-sm">
    <div class="toast px-6 py-4 rounded-lg border-left-4 <?= $toast_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> shadow-lg animate-in fade-in-0">
        <div class="flex">
            <div class="flex-shrink-0 h-6 w-6 rounded-full flex items-center justify-center">
                <i class='bx <?= $toast_type === 'success' ? 'bx-check-circle' : 'bx-error-circle' ?> text-2xl'></i>
            </div>
            <div class="ml-4 flex-1 min-w-0">
                <p class="font-medium truncate"><?= htmlspecialchars($toast_message) ?></p>
            </div>
            <button class="bg-white rounded-full p-1 hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2" type="button" id="toast-close-btn" aria-label="Close">
                <span class="sr-only">Close</span>
                <i class='bx bx-x'></i>
            </button>
        </div>
    </div>
</div>

<script>
    // Auto dismiss toast after 5 seconds
    const toast = document.querySelector('.toast');
    if (toast) {
        setTimeout(() => {
            toast.style.display = 'none';
        }, 5000);
        
        // Close on button click
        const closeBtn = document.getElementById('toast-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.style.display = 'none';
            });
        }
    }
</script>
<?php endif; ?>