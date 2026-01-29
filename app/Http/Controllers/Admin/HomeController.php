<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;

class HomeController extends Controller
{
    public function index()
    {
        $statistic = [
            'siswa' => Siswa::where("status",'=','aktif')->count(),
            'guru' => Guru::where("status",'=','aktif')->count(),
            'kelas' => Kelas::count(),
        ];
        return inertia('admin/index',['statistic'=> $statistic]);
    }
}
