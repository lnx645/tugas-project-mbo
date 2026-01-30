<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuizSiswaHistory;
use App\Models\QuizSiswaJawaban;
use App\Service\KelasServiceImpl;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

class QuizEvaluasiController extends Controller
{
    public function periksa(Request $request, string $id)
    {
        $jawaban = QuizSiswaHistory::with([
            'jawaban_tersimpan',
            'jawaban_tersimpan.soal',
            'jawaban_tersimpan.jawaban'
        ])->find($id);
        return inertia("guru/quiz/evaluasi/periksa", [
            'jawaban' => $jawaban
        ]);
    }
    public function simpanHasilPeriksa(Request $request,  $id)
    {
        $jawaban = QuizSiswaJawaban::with([
            'history'
        ])->find($id);
        // 1. Update status benar/salah pada jawaban yang sedang diperiksa
        $jawaban->update([
            'is_correct' => $request->is_correct
        ]);

        // 2. Ambil history pengerjaan siswa ini
        $history = $jawaban->history;

        // 3. Ambil semua jawaban siswa untuk kuis ini (relasi jawaban_tersimpan)
        $semuaJawaban = $history->jawaban_tersimpan()->with('soal')->get();

        // 4. Hitung Total Bobot Soal yang dijawab BENAR
        $totalBobotBenar = $semuaJawaban->where('is_correct', true)->sum(function ($j) {
            return $j->soal->bobot ?? 0;
        });

        // 5. Hitung Total Seluruh Bobot Soal pada Kuis ini
        $totalBobotSeluruhnya = $semuaJawaban->sum(function ($j) {
            return $j->soal->bobot ?? 0;
        });

        // 6. Hitung skor akhir (hindari pembagian dengan nol)
        $scoreResult = $totalBobotSeluruhnya > 0
            ? ($totalBobotBenar / $totalBobotSeluruhnya) * 100
            : 0;

        // 7. Update tabel history pengerjaan
        $history->update([
            'score_result' => $scoreResult
        ]);

        return back()->with('message', 'Status dan skor berhasil diperbarui');
    }
    public function index(Request $request, KelasServiceImpl $kelasService)
    {
        $kelas = $kelasService->getKelasByGuru($request->role_id);
        $data = QuizSiswaHistory::with(['siswa.siswa' => function (HasOne  $query) use ($kelas) {
            $query->whereIn('kelas_id', $kelas->pluck('id_kelas'));
        }, 'jadwalQuiz.bankSoalGroup'])->whereNotNull('end_date')->get();
        return inertia("guru/quiz/evaluasi/index", [
            'kelas' => $kelas,
            'history' => $data,
        ]);
    }
}
