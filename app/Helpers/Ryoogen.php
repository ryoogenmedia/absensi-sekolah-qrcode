<?php

/**
 *  Ryoogen Helper
 *  This file contains helper functions for the Ryoogen application.
 *  It includes functions for generating URLs, handling errors, and formatting data.
 *  *  @package Ryoogen
 *  *  @version 1.0
 */

use App\Models\WhatsappConfig;
use Illuminate\Support\Facades\Cache;

/**
 * Mengecek apakah user sedang online
 * @param int $angka bilangan yang akan diubah menjadi teks
 * @return string teks yang merepresentasikan bilangan dalam bahasa Indonesia
 */
if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas'
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            return terbilang($angka / 10) . ' Puluh ' . terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'Seratus ' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return terbilang($angka / 100) . ' Ratus ' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'Seribu ' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang($angka / 1000) . ' Ribu ' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang($angka / 1000000) . ' Juta ' . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return terbilang($angka / 1000000000) . ' Miliar ' . terbilang($angka % 1000000000);
        } else {
            return terbilang($angka / 1000000000000) . ' Triliun ' . terbilang($angka % 1000000000000);
        }
    }
}

/**
 * Mengecek apakah user sedang online
 *
 * @param string $id id dari user
 * @return boolean
 */
if (!function_exists('is_online')) {
    function is_online($id)
    {
        return Cache::has('user-is-online-' . $id) ? true : false;
    }
}

/**
 * Mengambil nilai whatsapp broadcast
 *
 * @return array (url, port, phone_number)
 */
if (!function_exists('base_whatsapp')) {
    function base_whatsapp()
    {
        $whatsappConfig = WhatsappConfig::select('url', 'port', 'phone_number')->first();

        return [
            "url" => $whatsappConfig->whatsapp_url ?? null,
            "port" => $whatsappConfig->whatsapp_port ?? null,
            "phone_number" => $whatsappConfig->phone_number ?? null,
        ];
    }
}

/**
 *  copy example fake photo to storage/student-photos
 * @param string sex for determine example photo
 * @return string new path photo
 */
if (!function_exists('example_photo')) {
    function example_photo($sex, $loopIndex = null)
    {
        // Path ke file example
        $exampleLatarMerahCewek = public_path('static/ryoogen/example/latar-merah-cewek.jpg');
        $exampleLatarMerahCowok = public_path('static/ryoogen/example/latar-merah-cowok.jpeg');

        // Pastikan direktori storage/student-photos ada
        $storagePath = storage_path('app/public/student-photos');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        // Tentukan file foto berdasarkan jenis kelamin
        if ($sex === 'laki-laki') {
            $sourcePhoto = $exampleLatarMerahCowok;
            $extension = '.jpeg';
        } else {
            $sourcePhoto = $exampleLatarMerahCewek;
            $extension = '.jpg';
        }

        // Penamaan file yang benar
        $indexPart = $loopIndex !== null ? $loopIndex . '_' : '';
        $photoFileName = 'student_' . $indexPart . time() . $extension;
        $destinationRelativePath = 'student-photos/' . $photoFileName;
        $destinationFullPath = $storagePath . '/' . $photoFileName;

        // Copy file ke storage/student-photos jika sumber ada
        if (file_exists($sourcePhoto)) {
            copy($sourcePhoto, $destinationFullPath);
            // Return path relatif ke storage/app/public agar bisa diakses via storage link
            return 'student-photos/' . $photoFileName;
        }

        return null;
    }
}


/**
 * Mengubah nomor ponsel menjadi awalan 62
 *
 * @param string $nomorPonsel
 * @return string Nomor ponsel dengan awalan 62
 */
if (!function_exists('format_number_indonesia')) {
    function format_number_indonesia(string $nomorPonsel): string
    {
        // Hapus spasi, tanda +, tanda strip, atau titik
        $nomor = preg_replace('/[^0-9]/', '', $nomorPonsel);

        // Jika sudah diawali 62, kembalikan langsung
        if (strpos($nomor, '62') === 0) {
            return $nomor;
        }

        // Jika diawali 0, ganti jadi 62
        if (strpos($nomor, '0') === 0) {
            return '62' . substr($nomor, 1);
        }

        // Jika tidak diawali 62 atau 0, kembalikan apa adanya
        return $nomor;
    }
}

/**
 * Filter user showing
 *
 * @param object $query query builder
 * @return object $data hasil query
 */
if (!function_exists('secret_user')) {
    function secret_user($query)
    {
        if (auth()->user()->role != 'developer') {
            if (in_array(auth()->user()->email, config('const.secret_email'))) {
                $query = $query->whereNotIn('email', config('const.secret_email'));
            }
        } else {
            return $query;
        }

        return $query;
    }
}
