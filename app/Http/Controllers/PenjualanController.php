<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\StokModel;
use Illuminate\Support\Facades\DB;
use App\Models\PenjualanDetailModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

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
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'details.barang')->find($id);

        return view('penjualan.show_ajax', compact('penjualan'));
    }

    public function create_ajax(){
        $barang = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')->get();
        $user = auth()->user();

        $lastPenjualan = PenjualanModel::select('penjualan_kode')
        ->orderBy('penjualan_id', 'desc')
        ->first();

        $kode = 1; // Default kode jika belum ada data
        if ($lastPenjualan) {
            $lastKode = (int) $lastPenjualan->penjualan_kode; // Konversi ke integer
            $kode = $lastKode + 1; // Tambah 1 untuk kode berikutnya
        }

        return view('penjualan.create_ajax', compact('barang', 'user', 'kode'));
    }
    

public function store_ajax(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:255',
            'penjualan_kode' => 'required|numeric|unique:t_penjualan,penjualan_kode',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga.*' => 'required|numeric|min:0',
            'jumlah.*' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors()->toArray(),
            ]);
        }

        // Gunakan transaksi untuk memastikan konsistensi
        return DB::transaction(function () use ($request) {
            // Simpan data penjualan
            $penjualan = new PenjualanModel();
            $penjualan->user_id = $request->user_id;
            $penjualan->pembeli = $request->pembeli;
            $penjualan->penjualan_kode = $request->penjualan_kode;
            $penjualan->penjualan_tanggal = now();
            $penjualan->save();

            // Ambil data barang yang dibeli
            $barang_ids = $request->barang_id;
            $hargas = $request->harga;
            $jumlahs = $request->jumlah;

            // Validasi stok sebelum menyimpan detail penjualan
            foreach ($barang_ids as $index => $barang_id) {
                $jumlah = $jumlahs[$index];

                // Ambil stok terbaru untuk barang ini
                $stok = StokModel::where('barang_id', $barang_id)
                    ->orderBy('stok_tanggal', 'desc')
                    ->first();

                if (!$stok) {
                    throw new \Exception("Stok untuk barang dengan ID {$barang_id} tidak ditemukan.");
                }

                if ($stok->stok_jumlah < $jumlah) {
                    throw new \Exception("Stok untuk barang dengan ID {$barang_id} tidak mencukupi. Stok tersedia: {$stok->stok_jumlah}, dibutuhkan: {$jumlah}.");
                }
            }

            // Simpan detail penjualan dan kurangi stok
            for ($i = 0; $i < count($barang_ids); $i++) {
                $barang_id = $barang_ids[$i];
                $jumlah = $jumlahs[$i];

                // Simpan detail penjualan
                $detail = new PenjualanDetailModel();
                $detail->penjualan_id = $penjualan->penjualan_id;
                $detail->barang_id = $barang_id;
                $detail->harga = $hargas[$i];
                $detail->jumlah = $jumlah;
                $detail->save();

                // Kurangi stok
                $stok = StokModel::where('barang_id', $barang_id)
                    ->orderBy('stok_tanggal', 'desc')
                    ->first();

                $stok->stok_jumlah -= $jumlah;
                $stok->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan.',
            ]);
        });
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
        ]);
    }
}
}