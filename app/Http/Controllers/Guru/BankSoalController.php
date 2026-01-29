<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuizBankSoal;
use App\Models\QuizCategory;
use App\Models\QuizJawabanSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankSoalController extends Controller
{
    public function simpanEditBankSoal(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:pilihan_ganda,isian_singkat,essay,benar_salah',
            'lampiran_gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pilihan' => 'required_if:tipe,pilihan_ganda,benar_salah|array',
            'jawaban_benar' => 'required_if:tipe,pilihan_ganda,benar_salah',
        ]);

        $soal = QuizBankSoal::where('user_id', $request->user()->id)->findOrFail($id);

        return DB::transaction(function () use ($request, $soal) {

            if ($request->hasFile('lampiran_gambar')) {
                if ($soal->lampiran_gambar) {
                    Storage::disk('public')->delete($soal->lampiran_gambar);
                }
                $soal->lampiran_gambar = $request->file('lampiran_gambar')->store('bank-soal', 'public');
            }

            $soal->update([
                'pertanyaan' => $request->pertanyaan,
                'tipe' => $request->tipe,
                'lampiran_gambar' => $soal->lampiran_gambar,
            ]);

            if (in_array($request->tipe, ['pilihan_ganda', 'benar_salah'])) {
                $soal->jawaban()->delete();

                foreach ($request->pilihan as $pilih) {
                    QuizJawabanSoal::create([
                        'quiz_bank_soal_id' => $soal->id,
                        'teks_jawaban'      => $pilih['teks'],
                        'apakah_benar'      => $pilih['label'] === $request->jawaban_benar,
                    ]);
                }
            } else {
                $soal->jawaban()->delete();
            }

            return redirect()->back()->with('success', 'Soal berhasil diperbarui!');
        });
    }
    public function editBankSoal(Request $request, $id)
    {
        $bank_soal = QuizBankSoal::where(['user_id' => $request->user()->id, 'id' => $id])->with([
            'jawaban',
            'kunci_jawaban',
        ])->first();

        return inertia('guru/quiz/bank-soal/edit-bank-soal', [
            'soal' => $bank_soal,
        ]);
    }
    public function tambahSoalBaru(Request $request, string $id)
    {
        return inertia('guru/quiz/bank-soal/tambah-baru', [
            'category_id' => $id,
        ]);
    }
    public function deleteSoal(Request $request, string $id)
    {
        $soal = QuizBankSoal::where(['id' => $id, 'user_id' => $request->user()->id])->with(['jawaban'])->first();
        if ($soal) {
            $soal->delete();
            $soal->jawaban()->delete();
        }
    }
    public function simpanBankSoal(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'quiz_category_id' => 'required',
            'pertanyaan' => 'required',
            'tipe' => 'required|in:pilihan_ganda,isian_singkat,essay,benar_salah',
            'lampiran_gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bobot' => "required",
            'pilihan' => 'required_if:tipe,pilihan_ganda,benar_salah|array',
            'jawaban_benar' => 'required_if:tipe,pilihan_ganda,benar_salah',
        ]);

        // Definisikan di sini supaya aman diakses di catch
        $pathGambar = null;

        try {
            return DB::transaction(function () use ($request, &$pathGambar) {
                // 2. Upload Gambar
                if ($request->hasFile('lampiran_gambar')) {
                    $pathGambar = $request->file('lampiran_gambar')->store('bank-soal', 'public');
                }

                // 3. Simpan Soal
                $soal = QuizBankSoal::create([
                    'quiz_category_id' => $request->quiz_category_id,
                    'pertanyaan' => $request->pertanyaan,
                    'tipe' => $request->tipe,
                    'user_id' => $request->user()->id,
                    'bobot' => $request->bobot,
                    'lampiran_gambar' => $pathGambar,
                ]);

                // 4. Simpan Jawaban
                if (in_array($request->tipe, ['pilihan_ganda', 'benar_salah'])) {
                    foreach ($request->pilihan as $pilih) {
                        QuizJawabanSoal::create([
                            'quiz_bank_soal_id' => $soal->id,
                            'teks_jawaban'      => $pilih['teks'],
                            'apakah_benar'      => $pilih['label'] === $request->jawaban_benar,
                        ]);
                    }
                }

                return redirect()->back()->with('success', 'Soal berhasil disimpan!');
            });
        } catch (\Exception $e) {
            // Sekarang $pathGambar pasti terdefinisi (null atau string path)
            if ($pathGambar) {
                Storage::disk('public')->delete($pathGambar);
            }

            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan soal: ' . $e->getMessage()]);
        }
    }
    public function editQuizCategory(Request $request, string $id)
    {
        $category = QuizCategory::where(['user_id' => $request->user()->id, 'id' => $id])->first();
        $category->update([
            'nama' => $request->nama,
        ]);
    }
    public function bankSoal(Request $request, string $id)
    {
        $soals = QuizBankSoal::where([
            'user_id' => $request->user()->id,
            'quiz_category_id' => $id,
        ])->with([
            'jawaban',
            'kunci_jawaban'
        ])->get();
        return inertia('guru/quiz/bank-soal/bank-soal', [
            'soals' => $soals,
            'category_id' => $id,
        ]);
    }
    public function index(Request $request)
    {
        $category = QuizCategory::where('user_id', $request->user()->id)->withCount('bankSoals')->get();
        return inertia('guru/quiz/bank-soal/index', [
            'category' => $category,
        ]);
    }
    public function addNewCategory(Request $request)
    {
        $validated = $request->validate([
            'nama' => "required|string"
        ]);
        QuizCategory::create([
            'nama' => $validated['nama'],
            'user_id' => $request->user()->id,
        ]);
    }
}
