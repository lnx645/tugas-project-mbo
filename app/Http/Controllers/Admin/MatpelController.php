<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matpel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Rap2hpoutre\FastExcel\Facades\FastExcel;
use Rap2hpoutre\FastExcel\FastExcel as Rap2hpoutreFastExcel;

class MatpelController extends Controller
{
    public function index(Request $request)
    {
        $query = Matpel::query();
        if ($request->search) {
            $query->where('nama', 'like', "%{$request->search}%")
                ->orWhere('kode', 'like', "%{$request->search}%");
        }

        return inertia('admin/matpel/index', [
            'matpels' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:matpels,kode',
            'nama' => 'required|string|max:255',
            'kelompok' => 'nullable|string' // misal: Muatan Nasional, Kejuruan, dll
        ]);

        Matpel::create($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $collection = (new Rap2hpoutreFastExcel())->import($request->file('file'), function ($line) {

                return Matpel::updateOrCreate(
                    ['kode' => $line['kode']],
                    [
                        'nama'     => $line['nama'],
                        'kelompok' => $line['kelompok'] ?? null,
                    ]
                );
            });

            return back()->with('success', 'Berhasil mengimport ' . $collection->count() . ' data mata pelajaran!');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal import: Pastikan header excel adalah (kode, nama, kelompok). Error: ' . $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        $matpel = Matpel::findOrFail($id);

        $request->validate([
            'kode' => [
                'required',
                Rule::unique('matpels', 'kode')->ignore($matpel->kode, 'kode'),
            ],
            'nama' => 'required|string|max:255',
            'kelompok' => 'nullable|string'
        ]);

        $matpel->update($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        Matpel::findOrFail($id)->delete();
        return back()->with('success', 'Mata Pelajaran berhasil dihapus');
    }
}
