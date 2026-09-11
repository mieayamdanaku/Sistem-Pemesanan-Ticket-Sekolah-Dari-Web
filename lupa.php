<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - E-Tiket</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #2563EB; /* Warna biru senada dengan background login */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .card-lupa {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .icon-lock {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }
        .btn-back {
            background: #2563EB;
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            width: 100%;
            display: inline-block;
            text-decoration: none;
            margin-top: 20px;
            transition: .3s;
        }
        .btn-back:hover {
            background: #1D4ED8;
            color: white;
        }
    </style>
</head>
<body>

    <div class="card-lupa">
        <div class="icon-lock">🔐</div>
        <h4 class="fw-bold mb-3">Lupa Password?</h4>
        <p class="text-muted mb-4" style="line-height: 1.6;">
            Demi keamanan akun Anda, jika ingin merubah atau mereset password, silakan temui <strong>Admin</strong> atau <strong>Guru</strong> yang bertugas.
        </p>
        <!-- Tombol ini bisa diatur untuk menutup tab saat diklik -->
        <button onclick="window.close();" class="btn-back border-0">Tutup Halaman Ini</button>
    </div>

</body>
</html>