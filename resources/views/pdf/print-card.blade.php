<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Presensi Siswa</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
        }

        .card-page {
            page-break-inside: avoid;
            page-break-after: always;
            margin-bottom: 40px;
        }

        .card-wrapper {
            width: 510px;
            margin: 30px auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .card-container {
            width: 480px;
            height: 300px;
            position: relative;
            margin: 0 auto;
        }

        .card-image {
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: cover;
            border-radius: 16px;
            z-index: 1;
        }

        .student-photo {
            position: absolute;
            top: 45px;
            left: 55px;
            width: 80px;
            height: 105px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            border: 4px solid white;
            z-index: 3;
        }

        .student-info {
            position: absolute;
            top: 100px;
            left: 300px;
            z-index: 3;
            color: white;
            font-size: 12px;
            line-height: 3;
            font-weight: bold;
        }

        .student-qrcode {
            position: absolute;
            bottom: 37px;
            right: 25px;
            width: 40px;
            height: 40px;
            background: white;
            padding: 6px;
            border-radius: 8px;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-qrcode svg {
            width: 90px !important;
            height: 90px !important;
        }
    </style>
</head>

<body>
    {{--
        Cukup gunakan satu loop @foreach.
        Jika $students hanya berisi 1 data, loop akan berjalan 1 kali.
        Jika lebih, loop akan berjalan sesuai jumlah data.
    --}}
    @foreach ($students as $siswa)
        <div class="card-page">
            <div class="card-wrapper">

                {{-- CARD 1 (DEPAN) --}}
                <div class="card-container">
                    <img src="{{ public_path('static/ryoogen/illustration/card-presensi-1.png') }}" class="card-image">

                    {{-- Foto --}}
                    @if ($siswa->photo)
                        <div class="student-photo"
                            style="background-image:url('{{ public_path('storage/' . $siswa->photo) }}')"></div>
                    @endif

                    {{-- Informasi --}}
                    <div class="student-info">
                        {{ $siswa->full_name ?? '-' }} <br>
                        {{ $siswa->nis ?? '-' }} <br>
                        {{ $siswa->class_room->name_class ?? '-' }}
                    </div>

                    {{-- QR Code --}}
                    <div class="student-qrcode">
                        {!! DNS2D::getBarcodeHTML("$siswa->nis", 'QRCODE', 2, 2) !!}
                    </div>
                </div>

                {{-- CARD 2 (BELAKANG) --}}
                <div class="card-container" style="margin-top: 10px">
                    <img src="{{ public_path('static/ryoogen/illustration/card-presensi-2.png') }}" class="card-image">
                </div>

            </div>
        </div>
    @endforeach
</body>

</html>
