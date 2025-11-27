<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class CetakPdfController extends Controller
{
    public function card(Request $request)
    {
        $cardId = $request->card_id;
        $kelas  = $request->kelas;

        // Ambil data student
        if ($kelas) {
            $students = Student::where('class_room_id', $kelas)->get();
        } elseif ($cardId) {
            $student = Student::where('nis', $cardId)->first();

            // Jika tidak ditemukan
            if (!$student) {
                abort(404, 'Data siswa tidak ditemukan.');
            }

            // Samakan format menjadi collection
            $students = collect([$student]);
        } else {
            $students = Student::all();
        }

        // Generate PDF
        $pdf = \PDF::loadView('pdf.print-card', [
            'students' => $students,
            'card_id'  => $cardId,
            'kelas'    => $kelas,
        ])->setPaper('a3', 'portrait');

        // Tentukan nama file
        if ($cardId && isset($student)) {
            $safeName = preg_replace('/[^A-Za-z0-9\-]/', '_', $student->full_name);
            $fileName = "cetak-kartu-siswa-{$safeName}-{$student->nis}";
        } elseif ($kelas) {
            $fileName = "cetak-kartu-kelas-{$kelas}";
        } else {
            $fileName = "cetak-semua-kartu-siswa";
        }

        return $pdf->stream($fileName . '.pdf');
    }
}
