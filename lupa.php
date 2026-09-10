<?php
session_start();
require 'koneksi.php';
/** @var mysqli $conn */

if (empty($_SESSION['csrf_lupa'])) {
	$_SESSION['csrf_lupa'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
$tipe = $_POST['tipe'] ?? 'siswa';

if (isset($_POST['reset_password'])) {
	if (!hash_equals($_SESSION['csrf_lupa'], $_POST['csrf'] ?? '')) {
		$error = 'Permintaan tidak valid. Silakan coba lagi.';
	} else {
		$identitas = trim($_POST['identitas'] ?? '');
		$nama = trim($_POST['nama'] ?? '');
		$password_baru = $_POST['password_baru'] ?? '';
		$konfirmasi = $_POST['konfirmasi_password'] ?? '';

		$akun = [
			'siswa' => ['tabel' => 'siswa', 'kolom' => 'nisn', 'label' => 'NISN'],
			'admin' => ['tabel' => 'admin', 'kolom' => 'username', 'label' => 'Username'],
			'petugas' => ['tabel' => 'petugas', 'kolom' => 'username', 'label' => 'Username'],
		];

		if (!isset($akun[$tipe])) {
			$error = 'Tipe akun tidak valid.';
		} elseif ($identitas === '' || $nama === '') {
			$error = 'Identitas akun dan nama wajib diisi.';
		} elseif (strlen($password_baru) < 6) {
			$error = 'Password baru minimal 6 karakter.';
		} elseif ($password_baru !== $konfirmasi) {
			$error = 'Konfirmasi password tidak sama.';
		} else {
			$config = $akun[$tipe];
			$sql = "SELECT id FROM {$config['tabel']} WHERE {$config['kolom']} = ? AND nama = ? LIMIT 1";
			$stmt = mysqli_prepare($conn, $sql);
			mysqli_stmt_bind_param($stmt, 'ss', $identitas, $nama);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$data = mysqli_fetch_assoc($result);

			if (!$data) {
				$error = 'Data akun tidak ditemukan. Periksa kembali identitas dan nama Anda.';
			} else {
				$hash = password_hash($password_baru, PASSWORD_DEFAULT);
				$update = mysqli_prepare($conn, "UPDATE {$config['tabel']} SET password = ? WHERE id = ?");
				mysqli_stmt_bind_param($update, 'si', $hash, $data['id']);

				if (mysqli_stmt_execute($update)) {
					$success = 'Password berhasil diubah. Silakan login dengan password baru.';
					$_POST = [];
				} else {
					$error = 'Password gagal diubah. Silakan coba lagi.';
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Lupa Password - E-Tiket</title>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body { background: linear-gradient(135deg, #2563EB, #1E40AF); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; padding: 20px; }
		.login-card { background: white; padding: 40px; border-radius: 24px; width: 440px; max-width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
		.btn-login { background: #2563EB; color: white; font-weight: 600; border-radius: 12px; padding: 12px; width: 100%; border: none; }
	</style>
</head>
<body>
<div class="login-card">
	<h3 class="fw-bold text-center mb-2">Lupa Password</h3>
	<p class="text-muted text-center small mb-4">Verifikasi data akun untuk membuat password baru.</p>
	<?php if ($error): ?><div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div><?php endif; ?>
	<?php if ($success): ?><div class="alert alert-success py-2 small"><?= htmlspecialchars($success) ?></div><?php endif; ?>
	<form method="POST" autocomplete="off">
		<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_lupa']) ?>">
		<div class="mb-3">
			<label class="form-label text-muted small fw-bold">Tipe Akun</label>
			<select name="tipe" class="form-select" required>
				<option value="siswa" <?= $tipe === 'siswa' ? 'selected' : '' ?>>Siswa</option>
				<option value="admin" <?= $tipe === 'admin' ? 'selected' : '' ?>>Admin</option>
				<option value="petugas" <?= $tipe === 'petugas' ? 'selected' : '' ?>>Petugas</option>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label text-muted small fw-bold">Username / NISN</label>
			<input type="text" name="identitas" class="form-control" placeholder="Masukkan username atau NISN" value="<?= htmlspecialchars($_POST['identitas'] ?? '') ?>" required>
		</div>
		<div class="mb-3">
			<label class="form-label text-muted small fw-bold">Nama Terdaftar</label>
			<input type="text" name="nama" class="form-control" placeholder="Masukkan nama sesuai akun" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
		</div>
		<div class="mb-3">
			<label class="form-label text-muted small fw-bold">Password Baru</label>
			<input type="password" name="password_baru" class="form-control" minlength="6" required>
		</div>
		<div class="mb-4">
			<label class="form-label text-muted small fw-bold">Konfirmasi Password Baru</label>
			<input type="password" name="konfirmasi_password" class="form-control" minlength="6" required>
		</div>
		<button type="submit" name="reset_password" class="btn-login">Ubah Password</button>
	</form>
	<div class="text-center mt-3"><a href="login.php" class="small text-decoration-none">Kembali ke Login</a></div>
</div>
</body>
</html>
