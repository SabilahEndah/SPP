<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Data Siswa";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* TAMBAH DATA */
if (isset($_POST['tambah'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_tlp = mysqli_real_escape_string($koneksi, $_POST['no_tlp']);
    $id_spp = mysqli_real_escape_string($koneksi, $_POST['id_spp']);

    try {
        mysqli_query($koneksi, "
            INSERT INTO tb_siswa 
            (nisn, nis, nama, id_kelas, alamat, no_tlp, id_spp)
            VALUES
            ('$nisn', '$nis', '$nama', '$id_kelas', '$alamat', '$no_tlp', '$id_spp')
        ");

        setAlert('success', 'Berhasil', 'Data siswa berhasil ditambahkan', 'data_siswa.php');
    } catch (Exception $e) {
        setAlert('error', 'Gagal', 'Data siswa gagal ditambahkan: ' . $e->getMessage(), 'data_siswa.php');
    }
}

/* UPDATE DATA */
if (isset($_POST['update'])) {
    $nisn_lama = mysqli_real_escape_string($koneksi, $_POST['nisn_lama']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_tlp = mysqli_real_escape_string($koneksi, $_POST['no_tlp']);
    $id_spp = mysqli_real_escape_string($koneksi, $_POST['id_spp']);

    try {
        mysqli_query($koneksi, "
            UPDATE tb_siswa SET
                nisn = '$nisn',
                nis = '$nis',
                nama = '$nama',
                id_kelas = '$id_kelas',
                alamat = '$alamat',
                no_tlp = '$no_tlp',
                id_spp = '$id_spp'
            WHERE nisn = '$nisn_lama'
        ");

        setAlert('success', 'Berhasil', 'Data siswa berhasil diubah', 'data_siswa.php');
    } catch (Exception $e) {
        setAlert('error', 'Gagal', 'Data siswa gagal diubah: ' . $e->getMessage(), 'data_siswa.php');
    }
}

/* HAPUS DATA */
if (isset($_GET['hapus'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    try {
        mysqli_query($koneksi, "
            DELETE FROM tb_siswa 
            WHERE nisn = '$nisn'
        ");

        setAlert('success', 'Berhasil', 'Data siswa berhasil dihapus', 'data_siswa.php');
    } catch (Exception $e) {
        setAlert('error', 'Gagal', 'Data siswa tidak bisa dihapus karena masih memiliki data pembayaran', 'data_siswa.php');
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Siswa</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                Tambah Siswa
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Keahlian</th>
                        <th>No Telepon</th>
                        <th>Alamat</th>
                        <th>SPP</th>
                        <th width="170">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    $query = mysqli_query($koneksi, "
                        SELECT 
                            s.*,
                            k.nama_kelas AS nama_kelas_tampil,
                            k.komp_keahlian,
                            spp.tahun,
                            spp.nominal
                        FROM tb_siswa s
                        LEFT JOIN tb_kelas k 
                            ON s.id_kelas = k.id_kelas
                        LEFT JOIN tb_spp spp 
                            ON s.id_spp = spp.id_spp
                        ORDER BY s.nama ASC
                    ");

                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= e($row['nisn']); ?></td>
                            <td><?= e($row['nis']); ?></td>
                            <td><?= e($row['nama']); ?></td>
                            <td><?= e($row['nama_kelas_tampil']); ?></td>
                            <td><?= e($row['komp_keahlian']); ?></td>
                            <td><?= e($row['no_tlp']); ?></td>
                            <td><?= e($row['alamat']); ?></td>
                            <td><?= e($row['tahun']); ?> - <?= rupiah($row['nominal']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= e($row['nisn']); ?>">
                                    Edit
                                </button>

                                <a href="#"
                                   class="btn btn-danger btn-sm btn-hapus"
                                   data-url="data_siswa.php?hapus=<?= e($row['nisn']); ?>">
                                    Hapus
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit<?= e($row['nisn']); ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Siswa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="nisn_lama" value="<?= e($row['nisn']); ?>">

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">NISN</label>
                                                    <input type="text" name="nisn" class="form-control" value="<?= e($row['nisn']); ?>" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">NIS</label>
                                                    <input type="text" name="nis" class="form-control" value="<?= e($row['nis']); ?>" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Siswa</label>
                                                    <input type="text" name="nama" class="form-control" value="<?= e($row['nama']); ?>" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">No Telepon</label>
                                                    <input type="text" name="no_tlp" class="form-control" value="<?= e($row['no_tlp']); ?>">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kelas</label>
                                                    <select name="id_kelas" class="form-select" required>
                                                        <option value="">-- Pilih Kelas --</option>
                                                        <?php
                                                        $qKelas = mysqli_query($koneksi, "SELECT * FROM tb_kelas ORDER BY nama_kelas ASC");
                                                        while ($kelas = mysqli_fetch_assoc($qKelas)) {
                                                        ?>
                                                            <option value="<?= e($kelas['id_kelas']); ?>" <?= $row['id_kelas'] == $kelas['id_kelas'] ? 'selected' : ''; ?>>
                                                                <?= e($kelas['nama_kelas']); ?> - <?= e($kelas['komp_keahlian']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">SPP</label>
                                                    <select name="id_spp" class="form-select" required>
                                                        <option value="">-- Pilih SPP --</option>
                                                        <?php
                                                        $qSpp = mysqli_query($koneksi, "SELECT * FROM tb_spp ORDER BY tahun DESC");
                                                        while ($spp = mysqli_fetch_assoc($qSpp)) {
                                                        ?>
                                                            <option value="<?= e($spp['id_spp']); ?>" <?= $row['id_spp'] == $spp['id_spp'] ? 'selected' : ''; ?>>
                                                                <?= e($spp['tahun']); ?> - <?= rupiah($spp['nominal']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <textarea name="alamat" class="form-control" rows="3"><?= e($row['alamat']); ?></textarea>
                                                </div>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Telepon</label>
                            <input type="text" name="no_tlp" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="id_kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php
                                $qKelas = mysqli_query($koneksi, "SELECT * FROM tb_kelas ORDER BY nama_kelas ASC");
                                while ($kelas = mysqli_fetch_assoc($qKelas)) {
                                ?>
                                    <option value="<?= e($kelas['id_kelas']); ?>">
                                        <?= e($kelas['nama_kelas']); ?> - <?= e($kelas['komp_keahlian']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">SPP</label>
                            <select name="id_spp" class="form-select" required>
                                <option value="">-- Pilih SPP --</option>
                                <?php
                                $qSpp = mysqli_query($koneksi, "SELECT * FROM tb_spp ORDER BY tahun DESC");
                                while ($spp = mysqli_fetch_assoc($qSpp)) {
                                ?>
                                    <option value="<?= e($spp['id_spp']); ?>">
                                        <?= e($spp['tahun']); ?> - <?= rupiah($spp['nominal']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="alert alert-info small">
                        Jika ingin siswa bisa login, tambahkan akun di menu <b>Data Petugas</b> dengan level <b>siswa</b> dan username sama dengan NISN.
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