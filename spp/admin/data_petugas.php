<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Data Petugas";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* TAMBAH DATA */
if (isset($_POST['tambah'])) {
    $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nama_petugas = mysqli_real_escape_string($koneksi, $_POST['nama_petugas']);
    $level = mysqli_real_escape_string($koneksi, $_POST['level']);

    try {
        mysqli_query($koneksi, "
            INSERT INTO tb_petugas (id_petugas, username, password, nama_petugas, level)
            VALUES ('$id_petugas', '$username', '$password', '$nama_petugas', '$level')
        ");

        setAlert('success', 'Berhasil', 'Data petugas berhasil ditambahkan', 'data_petugas.php');
    } catch (Exception $e) {
        setAlert('error', 'Gagal', 'Data petugas gagal ditambahkan: ' . $e->getMessage(), 'data_petugas.php');
    }
}

/* UPDATE DATA */
if (isset($_POST['update'])) {
    $id_petugas_lama = mysqli_real_escape_string($koneksi, $_POST['id_petugas_lama']);
    $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nama_petugas = mysqli_real_escape_string($koneksi, $_POST['nama_petugas']);
    $level = mysqli_real_escape_string($koneksi, $_POST['level']);

    try {
        if ($password == '') {
            mysqli_query($koneksi, "
                UPDATE tb_petugas SET
                    id_petugas = '$id_petugas',
                    username = '$username',
                    nama_petugas = '$nama_petugas',
                    level = '$level'
                WHERE id_petugas = '$id_petugas_lama'
            ");
        } else {
            mysqli_query($koneksi, "
                UPDATE tb_petugas SET
                    id_petugas = '$id_petugas',
                    username = '$username',
                    password = '$password',
                    nama_petugas = '$nama_petugas',
                    level = '$level'
                WHERE id_petugas = '$id_petugas_lama'
            ");
        }

        setAlert('success', 'Berhasil', 'Data petugas berhasil diubah', 'data_petugas.php');
    } catch (Exception $e) {
        setAlert('error', 'Gagal', 'Data petugas gagal diubah: ' . $e->getMessage(), 'data_petugas.php');
    }
}

/* HAPUS DATA */
if (isset($_GET['hapus'])) {
    $id_petugas = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    try {
        mysqli_query($koneksi, "
            DELETE FROM tb_petugas 
            WHERE id_petugas = '$id_petugas'
        ");

        setAlert('success', 'Berhasil', 'Data petugas berhasil dihapus', 'data_petugas.php');
    } catch (Exception $e) {
        setAlert('error', 'Gagal', 'Data petugas gagal dihapus: ' . $e->getMessage(), 'data_petugas.php');
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Petugas</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                Tambah Petugas
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>ID Petugas</th>
                        <th>Username</th>
                        <th>Nama Petugas</th>
                        <th>Level</th>
                        <th width="170">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM tb_petugas ORDER BY id_petugas ASC");

                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= e($row['id_petugas']); ?></td>
                            <td><?= e($row['username']); ?></td>
                            <td><?= e($row['nama_petugas']); ?></td>
                            <td>
                                <span class="badge bg-primary"><?= e($row['level']); ?></span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= e($row['id_petugas']); ?>">
                                    Edit
                                </button>

                                <a href="#"
                                   class="btn btn-danger btn-sm btn-hapus"
                                   data-url="data_petugas.php?hapus=<?= e($row['id_petugas']); ?>">
                                    Hapus
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit<?= e($row['id_petugas']); ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Petugas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="id_petugas_lama" value="<?= e($row['id_petugas']); ?>">

                                            <div class="mb-3">
                                                <label class="form-label">ID Petugas</label>
                                                <input type="text" name="id_petugas" class="form-control" value="<?= e($row['id_petugas']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="username" class="form-control" value="<?= e($row['username']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Password Baru</label>
                                                <input type="text" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nama Petugas</label>
                                                <input type="text" name="nama_petugas" class="form-control" value="<?= e($row['nama_petugas']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Level</label>
                                                <select name="level" class="form-select" required>
                                                    <option value="admin" <?= $row['level'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="petugas" <?= $row['level'] == 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                                                    <option value="siswa" <?= $row['level'] == 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" name="update" class="btn btn-primary">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Petugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Petugas</label>
                        <input type="text" name="id_petugas" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Petugas</label>
                        <input type="text" name="nama_petugas" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-select" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>

                    <div class="alert alert-info small">
                        Untuk akun siswa, username sebaiknya diisi dengan NISN siswa.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>