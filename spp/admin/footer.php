<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['swal'])) { ?>
<script>
    Swal.fire({
        icon: <?= json_encode($_SESSION['swal']['icon']); ?>,
        title: <?= json_encode($_SESSION['swal']['title']); ?>,
        text: <?= json_encode($_SESSION['swal']['text']); ?>,
        confirmButtonColor: '#0d6efd'
    });
</script>
<?php unset($_SESSION['swal']); } ?>

<script>
    document.querySelectorAll('.btn-hapus').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            let url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Yakin ingin menghapus data?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>

</body>
</html>