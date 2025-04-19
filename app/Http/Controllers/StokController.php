<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Daftar stok',
            'list' => ['Home', 'stok']
        ];

        $page = (object)[
            'title' => 'Daftar stok yang terdaftar dalam sistem'
        ];

        $activeMenu = 'stok';

        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::select(
            't_stok.*',
            'm_supplier.supplier_nama as supplier_nama',
            'm_barang.barang_nama as barang_nama',
            'm_user.nama as user_nama'
        )
        ->leftJoin('m_supplier', 'm_supplier.supplier_id', '=', 't_stok.supplier_id')
        ->leftJoin('m_barang', 'm_barang.barang_id', '=', 't_stok.barang_id')
        ->leftJoin('m_user', 'm_user.user_id', '=', 't_stok.user_id');

        // Filter berdasarkan input spesifik (jika ada)
        if ($request->supplier_id) {
            $stoks->where('t_stok.supplier_id', $request->supplier_id);
        }
        if ($request->barang_id) {
            $stoks->where('t_stok.barang_id', $request->barang_id);
        }
        if ($request->user_id) {
            $stoks->where('t_stok.user_id', $request->user_id);
        }

        // Pencarian global dari kolom searchable
        if ($search = $request->input('search.value')) {
            $stoks->where(function ($query) use ($search) {
                $query->where('t_stok.stok_id', 'LIKE', "%{$search}%")
                        ->orWhere('m_supplier.supplier_nama', 'LIKE', "%{$search}%")
                        ->orWhere('m_barang.barang_nama', 'LIKE', "%{$search}%")
                        ->orWhere('m_user.nama', 'LIKE', "%{$search}%")
                        ->orWhere('t_stok.stok_tanggal', 'LIKE', "%{$search}%")
                        ->orWhere('t_stok.stok_jumlah', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($stoks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<a href="' . url('/stok/' . $stok->stok_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id .'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id .'/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
