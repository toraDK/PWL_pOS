<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object)[
            'title' => 'Daftar penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        $user = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select(
            't_penjualan.*',
            'm_user.nama as user_nama'
        )
        ->leftJoin('m_user', 'm_user.user_id', '=', 't_penjualan.user_id');

        // Filter berdasarkan user_id (jika ada)
        if ($request->user_id) {
            $penjualans->where('t_penjualan.user_id', $request->user_id);
        }

        // Pencarian global
        if ($search = $request->input('search.value')) {
            $penjualans->where(function ($query) use ($search) {
                $query->where('t_penjualan.penjualan_id', 'LIKE', "%{$search}%")
                    ->orWhere('t_penjualan.pembeli', 'LIKE', "%{$search}%")
                    ->orWhere('t_penjualan.penjualan_kode', 'LIKE', "%{$search}%")
                    ->orWhere('t_penjualan.penjualan_tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('m_user.nama', 'LIKE', "%{$search}%"); // Ubah ke m_user.nama
            });
        }

        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->editColumn('penjualan_tanggal', function ($penjualan) {
                return $penjualan->penjualan_tanggal ? $penjualan->penjualan_tanggal->format('Y-m-d') : '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<a href="' . url('/penjualan/' . $penjualan->penjualan_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id .'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id .'/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}