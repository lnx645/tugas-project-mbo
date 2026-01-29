<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalQuiz;
use App\Models\QuizBankSoal;
use App\Models\QuizCategory;
use App\Service\Contract\KelasServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalQuizController extends Controller
{
    public function index(Request $request)
    {
        $jadwal = JadwalQuiz::where('user_id', $request->user()->id)->with([
            'bankSoalGroup' => function ($query) {
                $query->withCount("bankSoals");
            },
            'kelas',

        ]);
        return inertia('guru/quiz/jadwal/index', [
            'jadwals' => $jadwal->get()
        ]);
    }
    public function simpanJadwal(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required'],
            'quiz_category_id' => ['required'],
            'kelas_id' => ['required'],
            'total_soal' => ['required'],
            'max_attempts' => ['required'],
            'kkm' => ['required'],
            'mulai' => ['required'],
            'selesai' => ['required'],
            'durasi' => ['required']
        ]);
        DB::transaction(function () use (&$validated) {
            $validated['user_id'] = request()->user()->id;
            return  JadwalQuiz::create($validated);
        });
    }
    public function tambah(Request $request, KelasServiceInterface $kelasService)
    {
        $kelas = $kelasService->getKelasByGuru($request->role_id);
        $bankSoals = QuizCategory::where([
            'user_id' => $request->user()->id,
        ])
            ->withCount('bankSoals')->get();
        return inertia('guru/quiz/jadwal/tambah', [
            'listKelas' => $kelas,
            'bankSoals' => $bankSoals,
        ]);
    }
}
