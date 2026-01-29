<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManagementKelasController extends Controller
{
    public function removeStudent(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,nis', // atau nis tergantung primary key
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id); // atau find by nis
        $siswa->update(['kelas_id' => null]);
        return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }
    /**
     * Handle the incoming request.
     */
    public function storeSiswa(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_id' => 'required|exists:siswas,nis',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $siswa->update([
            'kelas_id' => $request->kelas_id
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }
    public function index(Request $request)
    {
        $query = Kelas::query()->with(['siswa.user', 'matpel']);

        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->tingkat && $request->tingkat !== 'Semua') {
            $query->where('tingkat', $request->tingkat);
        }

        $kelas = $query->withCount(['siswa', 'matpel'])
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
        $availableStudents = Siswa::query()->whereNull('kelas_id')->with('user')
            ->get();
        return Inertia::render('admin/kelas-management/index', [
            'kelas' => $kelas,
            'available_students' => $availableStudents,
            'filters' => [
                'search' => $request->search,
                'tingkat' => $request->tingkat,
            ],
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'tingkat' => 'required|in:10,11,12',
        ]);

        Kelas::create([
            'nama' => $request->nama,
            'tingkat' => $request->tingkat
        ]);

        return back()->with('success', 'Kelas berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:50',
            'tingkat' => 'required|in:10,11,12',
        ]);

        $kelas->update([
            'nama' => $request->nama,
            'tingkat' => $request->tingkat
        ]);

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // LOGIKA UTAMA: Cek Relasi
        // Menggunakan exists() lebih ringan daripada count()
        $hasSiswa = $kelas->siswa()->exists();
        $hasMatpel = $kelas->matpel()->exists();

        if ($hasSiswa || $hasMatpel) {
            return back()->withErrors([
                'message' => 'Gagal menghapus! Kelas ini masih memiliki Siswa atau Mata Pelajaran aktif.'
            ]);
        }

        $kelas->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
