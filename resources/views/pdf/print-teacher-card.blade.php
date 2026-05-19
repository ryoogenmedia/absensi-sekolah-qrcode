<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Akun Guru</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f3f4f6;
        }

        .card-page {
            page-break-inside: avoid;
            page-break-after: always;
            margin-bottom: 40px;
        }

        .card-wrapper {
            width: 550px;
            margin: 30px auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .card-container {
            width: 520px;
            height: 320px;
            position: relative;
            margin: 0 auto;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            color: #ffffff;
        }

        .card-header-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 12px;
            background: #f59e0b;
            z-index: 2;
        }

        .card-logo {
            position: absolute;
            top: 25px;
            left: 30px;
            z-index: 3;
        }

        .card-logo img {
            height: 35px;
        }

        .card-title-text {
            position: absolute;
            top: 28px;
            right: 35px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #f59e0b;
            z-index: 3;
            text-transform: uppercase;
        }

        .teacher-photo-placeholder {
            position: absolute;
            top: 90px;
            left: 35px;
            width: 95px;
            height: 120px;
            background-color: rgba(255, 255, 255, 0.2);
            border: 4px solid #ffffff;
            border-radius: 12px;
            z-index: 3;
            background-size: cover;
            background-position: center;
        }

        .teacher-info {
            position: absolute;
            top: 90px;
            left: 155px;
            z-index: 3;
            font-size: 12px;
            line-height: 1.8;
            max-width: 220px;
        }

        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #bfdbfe;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .teacher-qrcode {
            position: absolute;
            bottom: 35px;
            right: 35px;
            width: 75px;
            height: 75px;
            background: #ffffff;
            padding: 8px;
            border-radius: 12px;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .teacher-qrcode svg {
            width: 75px !important;
            height: 75px !important;
        }

        .card-footer-note {
            position: absolute;
            bottom: 20px;
            left: 35px;
            font-size: 8px;
            color: #bfdbfe;
            z-index: 3;
            font-style: italic;
        }

        /* BACK OF CARD */
        .card-back-container {
            width: 520px;
            height: 320px;
            position: relative;
            margin: 10px auto 0;
            background: #111827;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            color: #ffffff;
            border: 2px solid #1e3a8a;
        }

        .instructions-box {
            padding: 40px;
            z-index: 3;
        }

        .inst-title {
            font-size: 16px;
            font-weight: bold;
            color: #f59e0b;
            margin-bottom: 15px;
            border-bottom: 1px solid #374151;
            padding-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .inst-list {
            font-size: 11px;
            line-height: 1.8;
            color: #d1d5db;
            margin: 0;
            padding-left: 20px;
        }

        .inst-list li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    @foreach ($teachers as $teacher)
        <div class="card-page">
            <div class="card-wrapper">

                {{-- KARTU DEPAN --}}
                <div class="card-container">
                    <div class="card-header-accent"></div>

                    <!-- Logo -->
                    <div class="card-logo">
                        <img src="{{ public_path('static/nurhaliza/logo/DARK.png') }}" alt="School Logo">
                    </div>

                    <!-- Title -->
                    <div class="card-title-text">KARTU AKUN GURU</div>

                    <!-- Foto -->
                    @if ($teacher->photo)
                        <div class="teacher-photo-placeholder"
                            style="background-image:url('{{ public_path('storage/' . $teacher->photo) }}')"></div>
                    @else
                        <div class="teacher-photo-placeholder"
                            style="background-image:url('{{ public_path('static/nurhaliza/illustration/avatar-placeholder.png') }}')"></div>
                    @endif

                    <!-- Info -->
                    <div class="teacher-info">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">{{ $teacher->name ?? '-' }}</div>

                        <div class="info-label">NIP / NUPTK</div>
                        <div class="info-value">{{ $teacher->nip ?? $teacher->nuptk ?? '-' }}</div>

                        <div class="info-label">Mata Pelajaran</div>
                        <div class="info-value">{{ strtoupper($teacher->subject_study->name_subject ?? 'Belum Ditentukan') }}</div>

                        <div class="info-label">Email Akun</div>
                        <div class="info-value" style="color: #f59e0b;">{{ $teacher->user->email ?? '-' }}</div>
                    </div>

                    <!-- QR Code -->
                    <div class="teacher-qrcode">
                        {!! DNS2D::getBarcodeHTML($teacher->nip ?? $teacher->id, 'QRCODE', 3, 3) !!}
                    </div>

                    <!-- Footer Note -->
                    <div class="card-footer-note">
                        * Kartu ini diterbitkan secara sah oleh sistem Nurhaliza Academy.
                    </div>
                </div>

                {{-- KARTU BELAKANG --}}
                <div class="card-back-container">
                    <div class="card-header-accent" style="background: #3b82f6;"></div>
                    <div class="instructions-box">
                        <div class="inst-title">Panduan Penggunaan Akun</div>
                        <ol class="inst-list">
                            <li>Gunakan <b>Email Akun</b> yang tertera di bagian depan kartu untuk masuk ke portal sekolah.</li>
                            <li>Kata sandi default Anda adalah <b>nip</b> Anda (atau kata sandi khusus yang dibagikan admin).</li>
                            <li>Demi keamanan, harap segera mengubah kata sandi Anda setelah berhasil login pertama kali di menu Pengaturan Akun.</li>
                            <li>Kartu ini dilengkapi QR Code untuk verifikasi identitas di lingkungan sekolah.</li>
                            <li>Simpan kartu credential ini dengan baik dan jangan menyebarluaskan informasi akun Anda kepada pihak luar.</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    @endforeach
</body>

</html>
