<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Data Kelas";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ==========================
   TAMBAH DATA KELAS
========================== */
if (isset($_POST['tambah'])) {
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama_kelas']);
    $komp_keahlian = mysqli_real_escape_string($koneksi, $_POST['komp_keahlian']);

    try {
        mysqli_query($koneksi, "
            INSERT INTO tb_kelas 
            (id_kelas, nama_kelas, komp_keahlian)
            VALUES 
            ('$id_kelas', '$nama_kelas', '$komp_keahlian')
        ");

        setAlert(
            'success',
            'Berhasil',
            'Data kelas berhasil ditambahkan',
            'data_kelas.php'
        );
    } catch (Exception $e) {
        setAlert(
            'error',
            'Gagal',
            'Data kelas gagal ditambahkan: ' . $e->getMessage(),
            'data_kelas.php'
        );
    }
}

/* ==========================
   UPDATE DATA KELAS
========================== */
if (isset($_POST['update'])) {
    $id_kelas_lama = mysqli_real_escape_string($koneksi, $_POST['id_kelas_lama']);
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama_kelas']);
    $komp_keahlian = mysqli_real_escape_string($koneksi, $_POST['komp_keahlian']);

    try {
        mysqli_query($koneksi, "
            UPDATE tb_kelas SET
                id_kelas = '$id_kelas',
                nama_kelas = '$nama_kelas',
                komp_keahlian = '$komp_keahlian'
            WHERE id_kelas = '$id_kelas_lama'
        ");

        setAlert(
            'success',
            'Berhasil',
            'Data kelas berhasil diubah',
            'data_kelas.php'
        );
    } catch (Exception $e) {
        setAlert(
            'error',
            'Gagal',
            'Data kelas gagal diubah: ' . $e->getMessage(),
            'data_kelas.php'
        );
    }
}

/* ==========================
   HAPUS DATA KELAS
========================== */
if (isset($_GET['hapus'])) {
    $id_kelas = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    try {
        mysqli_query($koneksi, "
            DELETE FROM tb_kelas 
            WHERE id_kelas = '$id_kelas'
        ");

        setAlert(
            'success',
            'Berhasil',
            'Data kelas berhasil dihapus',
            'data_kelas.php'
        );
    } catch (Exception $e) {
        setAlert(
            'error',
            'Gagal',
            'Data kelas tidak bisa dihapus karena masih digunakan di data siswa',
            'data_kelas.php'
        );
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Kelas</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                Tambah Kelas
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="60">No</th>
                        <th>ID Kelas</th>
                        <th>Nama Kelas</th>
                        <th>Kompetensi Keahlian</th>
                        <th width="170">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    $query = mysqli_query($koneksi, "
                        SELECT * FROM tb_kelas 
                        ORDER BY id_kelas ASC
                    ");

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['id_kelas']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>
                                <td><?= e($row['komp_keahlian']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= e($row['id_kelas']); ?>">
                                        Edit
                                    </button>

                                    <a href="#"
                                       class="btn btn-danger btn-sm btn-hapus"
                                       data-url="data_kelas.php?hapus=<?= e($row['id_kelas']); ?>">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                            <!-- MODAL EDIT -->
                            <div class="modal fade" id="modalEdit<?= e($row['id_kelas']); ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Data Kelas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" name="id_kelas_lama" value="<?= e($row['id_kelas']); ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">ID Kelas</label>
                                                    <input 
                                                        type="text" 
                                                        name="id_kelas" 
                                                        class="form-control" 
                                                        value="<?= e($row['id_kelas']); ?>" 
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nama Kelas</label>
                                                    <input 
                                                        type="text" 
                                                        name="nama_kelas" 
                                                        class="form-control" 
                                                        value="<?= e($row['nama_kelas']); ?>" 
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Kompetensi Keahlian</label>
                                                    <input 
                                                        type="text" 
                                                        name="komp_keahlian" 
                                                        class="form-control" 
                                                        value="<?= e($row['komp_keahlian']); ?>" 
                                                        required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Batal
                                                </button>

                                                <button type="submit" name="update" class="btn btn-primary">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Data kelas belum tersedia.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Kelas</label>
                        <input 
                            type="text" 
                            name="id_kelas" 
                            class="form-control" 
                            placeholder="Contoh: 1"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input 
                            type="text" 
                            name="nama_kelas" 
                            class="form-control" 
                            placeholder="Contoh: 10A"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kompetensi Keahlian</label>
                        <input 
                            type="text" 
                            name="komp_keahlian" 
                            class="form-control" 
                            placeholder="Contoh: Rekayasa Perangkat Lunak"
                            required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" name="tambah" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>