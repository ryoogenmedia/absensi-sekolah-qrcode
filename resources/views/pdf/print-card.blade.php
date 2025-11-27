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

        /* Wrapper seluruh kartu */
        .card-wrapper {
            width: 510px;
            margin: 30px auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
            /* spacing CARD 1 & CARD 2 */
        }

        /* Container kartu */
        .card-container {
            width: 480px;
            height: 300px;
            position: relative;
            margin: 0 auto;
        }

        /* Background PNG */
        .card-image {
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: cover;
            border-radius: 16px;
            z-index: 1;
        }

        /* FOTO SISWA */
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

        /* INFORMASI SISWA DI CARD 1 */
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

        .student-info .label {
            font-weight: normal;
            color: #ffeeee;
        }

        /* CARD 2 - SYARAT */
        .syarat-text {
            position: absolute;
            top: 110px;
            left: 45px;
            width: 380px;
            font-size: 14px;
            color: white;
            line-height: 1.4;
            z-index: 3;
        }

        /* QR DI CARD 2 */
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

        .card-page {
            page-break-inside: avoid;
            page-break-after: always;
            margin-bottom: 40px;
        }

        .card-wrapper {
            width: 510px;
            margin: 0 auto;
        }
    </style>

</head>

<body>
    @if ($students->count() < 2)
        <div class="card-wrapper">

            {{-- CARD 1 --}}
            <div class="card-container">
                <img src="{{ public_path('static/ryoogen/illustration/card-presensi-1.png') }}" class="card-image">

                {{-- Foto --}}
                @if (isset($student->photo))
                    <div class="student-photo"
                        style="background-image:url('{{ public_path('storage/' . $student->photo) }}')"></div>
                @endif

                {{-- Informasi Siswa --}}
                <div class="student-info">
                    <span class="label"></span> {{ $student->full_name ?? '-' }} <br>
                    <span class="label"></span> {{ $student->nis ?? '-' }} <br>
                    <span class="label"></span> {{ $student->class_room->name_class ?? '-' }}
                </div>

                <div class="student-qrcode">
                    {!! DNS2D::getBarcodeHTML("$student->nis", 'QRCODE', 2, 2) !!}
                </div>
            </div>

            {{-- CARD 2 --}}
            <div class="card-container" style="margin-top: 10px">
                <img src="{{ public_path('static/ryoogen/illustration/card-presensi-2.png') }}" class="card-image">
            </div>
        </div>
    @else
        @foreach ($students as $siswa)
            <div class="card-page"> <!-- Tambahkan wrapper anti terpotong -->

                <div class="card-wrapper">

                    {{-- CARD 1 --}}
                    <div class="card-container">
                        <img src="{{ public_path('static/ryoogen/illustration/card-presensi-1.png') }}"
                            class="card-image">

                        {{-- Foto --}}
                        @if ($siswa->photo)
                            <div class="student-photo"
                                style="background-image:url('{{ public_path('storage/' . $siswa->photo) }}')"></div>
                        @endif

                        {{-- Informasi --}}
                        <div class="student-info">
                            {{ $siswa->full_name }} <br>
                            {{ $siswa->nis }} <br>
                            {{ $siswa->class_room->name_class ?? '-' }}
                        </div>

                        <div class="student-qrcode">
                            {!! DNS2D::getBarcodeHTML("$siswa->nis", 'QRCODE', 2, 2) !!}
                        </div>
                    </div>

                    {{-- CARD 2 --}}
                    <div class="card-container" style="margin-top: 10px">
                        <img src="{{ public_path('static/ryoogen/illustration/card-presensi-2.png') }}"
                            class="card-image">
                    </div>

                </div>

            </div>
        @endforeach

    @endif
</body>

</html>
