<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JadwalQuiz;
use App\Models\QuizBankSoal;
use App\Models\QuizSiswaHistory;
use App\Models\QuizSiswaJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use function Symfony\Component\Clock\now;

class QuizController extends Controller
{
    public function hasilDetail(Request $request, string $history_id)
    {
        $user_id = $request->user()->id;
        $historyDetail = QuizSiswaHistory::with([
            'jawaban_tersimpan',
            'jadwalQuiz',
            'jawaban_tersimpan.soal',
            'jawaban_tersimpan.jawaban'
        ])->whereSiswaId($user_id)->where('id', $history_id)->first();
        return inertia('siswa/quiz/hasil-quiz', [
            'history' => $historyDetail,
        ]);
    }
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $histories = QuizSiswaHistory::with([
            'jawaban_tersimpan',
            'jadwalQuiz',
        ])->whereSiswaId($user_id)->get();
        return inertia('siswa/quiz/index', [
            'histories' => $histories,
        ]);
    }
    public function start(Request $request, string $id)
    {
        $jadwal = JadwalQuiz::with(['bankSoalGroup'])->find($id);
        $create = QuizSiswaHistory::create([
            'jadwal_quiz_id' => $id,
            'siswa_id' => $request->user()->id,
            'start_date' => Carbon::now(),
            'remaining_time' => $jadwal->durasi,
        ]);
        return to_route('kerjakanQuiz', [
            'id' => $id,
            'history_id' => $create->id,
        ]);
    }
    public function selesai(Request $request, string $id, $history_id)
    {
        $history = QuizSiswaHistory::with(['jadwalQuiz', 'jawaban_tersimpan'])->find($history_id);
        if ($history->siswa_id !== $request->user()->id) {
            return to_route('home');
        }
        $jawaban = $history->jawaban_tersimpan;
        [$benar] = $jawaban->partition(fn($item) => $item->is_correct == 1);
        $totalSoal = $history->jadwalQuiz->total_soal;
        $jumlahBenar = $benar->count();

        $nilai = ($totalSoal > 0) ? ($jumlahBenar / $totalSoal) * 100 : 0;
        $history->update([
            'score_result' => $nilai,
            'end_date' => Carbon::now(),
        ]);
        return to_route('siwaQuizDetail', [
            'id' => $id,
        ]);
    }
    public function autoSaveJawaban(Request $request, string $jadwal, string $history)
    {
        $jawaban = $request->jawaban;
        $history = QuizSiswaHistory::find($history);
        if ($jawaban) {
            $history->update([
                'remaining_time' => $request->remaining_time
            ]);
            foreach ($jawaban as $num => $val) {
                $soal = QuizBankSoal::with('kunci_jawaban')->find($num);
                if ($soal) {
                    if ($soal->tipe == 'pilihan_ganda' || $soal->tipe == 'benar_salah') {
                        $isCorrect = $soal->kunci_jawaban->id == $val ? true : false;
                    } else {
                        $isCorrect = false;
                    }
                    QuizSiswaJawaban::updateOrCreate([
                        'quiz_siswa_history_id' => $history->id,
                        'siswa_id'       => $request->user()->id,
                        'quiz_bank_soal_id' => $num,
                    ], [
                        'quiz_jawaban_soal_id' => is_numeric($val) ? $val : null,
                        'jawaban_texts'         => !is_numeric($val) ? $val : null,
                        'is_correct' => $isCorrect
                    ]);
                }
            }
        }
    }
    public function kerjakan(Request $request, string $id, string $history_id)
    {
        $soals = QuizSiswaHistory::with([
            'jadwalQuiz' => function ($query) {
                $query->select('id', 'judul', 'durasi', 'quiz_category_id');
            },
            'jadwalQuiz.bankSoalGroup' => function ($query) {
                $query->select('id', 'nama');
            },
            'jawaban_tersimpan' => function ($query) {
                $query->select('id', 'quiz_siswa_history_id', 'siswa_id', 'quiz_bank_soal_id', 'quiz_jawaban_soal_id', 'jawaban_texts');
            },
            'jadwalQuiz.bankSoalGroup.bankSoals' => function ($query) {
                $query->select('id', 'quiz_category_id', 'pertanyaan', 'tipe', 'lampiran_gambar');
            },
            'jadwalQuiz.bankSoalGroup.bankSoals.jawaban' => function ($query) {
                $query->select('id', 'quiz_bank_soal_id', 'teks_jawaban');
            }
        ])
            ->select('id', 'jadwal_quiz_id', 'start_date', 'remaining_time') // Data history pengerjaan
            ->find($history_id);
        //
        return inertia("siswa/quiz/kerjakan", [
            'soals' => $soals,
        ]);
    }
    public function detail(Request $request, string $id)
    {
        $user = $request->user()->id;
        $siswa =  QuizSiswaHistory::where([
            'siswa_id' => $user,
            'jadwal_quiz_id' => $id,
        ])->first();
        $quiz = JadwalQuiz::with(['bankSoalGroup'])->find($id);
        return inertia('siswa/quiz/detail', [
            'pengerjaan' => $siswa,
            'quiz' => $quiz,
        ]);
    }
}
