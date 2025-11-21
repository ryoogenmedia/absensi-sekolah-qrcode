<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class CetakPdfController extends Controller
{
    public function card(Request $request)
    {
        $student = null;
        $cardId = $request->card_id ?? null;

        if ($cardId) {
            $student = Student::where('nis', $cardId)->first();
        } else {
            $student = Student::all();
        }

        $pdf = \PDF::loadView('pdf.print-card', [
            'student' => $student,
            'card_id' => $cardId,
        ])->setPaper('a4', 'portrait');

        if ($cardId) {
            $fileName = "cetak-kartu-siswa-{$student->full_name}-{$student->nis}";
        } else {
            $fileName = "cetak-semua-kartu-siswa";
        }

        return $pdf->stream($fileName);
    }
}
