<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['swal'])) { ?>
<script>
    Swal.fire({
        icon: <?= json_encode($_SESSION['swal']['icon']); ?>,
        title: <?= json_encode($_SESSION['swal']['title']); ?>,
        text: <?= json_encode($_SESSION['swal']['text']); ?>,
        confirmButtonColor: '#198754'
    });
</script>
<?php unset($_SESSION['swal']); } ?>

</body>
</html>