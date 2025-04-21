<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
                    ->orWhereRaw('DATE(t_stok.stok_tanggal) LIKE ?', ["%{$search}%"])
                    ->orWhere('t_stok.stok_jumlah', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($stoks)
            ->addIndexColumn()
            ->editColumn('stok_tanggal', function ($stok) {
                return $stok->stok_tanggal ? $stok->stok_tanggal->format('Y-m-d') : '-';
            })
            ->addColumn('aksi', function ($stok) {
                $btn = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id .'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax(){
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = auth()->user();

        $tanggalHariIni = now()->format('Y-m-d');

        return view('stok.create_ajax', compact('supplier', 'barang', 'user', 'tanggalHariIni'));
    }
    
    public function store_ajax(Request $request)
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|exists:m_supplier,supplier_id',
                'barang_id' => 'required|exists:m_barang,barang_id',
                'user_id' => 'required|exists:m_user,user_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|numeric|min:1',
            ]);

            // Ambil tanggal dari form dan gabungkan dengan waktu saat ini
            $tanggal = $request->input('stok_tanggal'); // Misalnya: '2025-04-20'
            $tanggalDenganWaktu = Carbon::parse($tanggal)->setTime(now()->hour, now()->minute, now()->second);
            
            // Ganti nilai stok_tanggal dengan tanggal dan waktu
            $validated['stok_tanggal'] = $tanggalDenganWaktu;

            StokModel::create($validated);

            return response()->json([
                'status' => true,  // Sesuaikan dengan struktur respons yang diharapkan frontend
                'message' => 'Data stok berhasil disimpan!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit_ajax(string $id){
        $stok = StokModel::find($id);
        
        $supplier = SupplierModel::find($stok->supplier_id);
        $barang = BarangModel::find($stok->barang_id);
        $user = UserModel::find($stok->user_id);
    
        return view('stok.edit_ajax', ['stok' => $stok, 'supplier' => $supplier, 'barang' => $barang, 'user' => $user]);
    }

    public function update_ajax(Request $request, string $id)
    {
        try {
            $stok = StokModel::find($id);

            if (!$stok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan.'
                ], 404);
            }

            // Validasi input
            $validated = $request->validate([ 'stok_jumlah' => ['required', 'numeric', 'min:' . $stok->stok_jumlah ]]);

            // Update hanya kolom stok_jumlah
            $stok->update([
                'stok_jumlah' => $validated['stok_jumlah'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil diperbarui!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show_ajax(string $id)
    {
        $stok = StokModel::with('supplier', 'user', 'barang')->find($id);

        return view('stok.show_ajax', compact('stok'));
    }

    public function import(){
        return view('stok.import');
    }

    public function import_ajax(Request $request){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');

            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

            $data = $sheet->toArray(null, false, true, true); // ambil data excel

            $insert = [];

            if (count($data) > 1) {     // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $insert[] = [
                            'supplier_id' => $value['A'],
                            'barang_id' => $value['B'],
                            'user_id' => $value['C'],
                            'stok_tanggal' => $value['D'],
                            'stok_jumlah' => $value['E'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    StokModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        // Ambil data stok yang akan di-export
        $stok = StokModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with('supplier', 'barang', 'user')
            ->orderBy('stok_id')
            ->get();

        // Load library Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Stok ID');
        $sheet->setCellValue('C1', 'Supplier');
        $sheet->setCellValue('D1', 'Barang');
        $sheet->setCellValue('E1', 'User');
        $sheet->setCellValue('F1', 'Stok Tanggal');
        $sheet->setCellValue('G1', 'Stok Jumlah');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true); // Bold header

        // Isi data
        $no = 1; // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke-2
        foreach ($stok as $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->stok_id);
            $sheet->setCellValue('C' . $baris, $value->supplier->supplier_nama ?? 'Tidak Ditemukan');
            $sheet->setCellValue('D' . $baris, $value->barang->barang_nama ?? 'Tidak Ditemukan');
            $sheet->setCellValue('E' . $baris, $value->user->nama ?? 'Tidak Ditemukan');
            $sheet->setCellValue('F' . $baris, $value->stok_tanggal ? $value->stok_tanggal->format('Y-m-d') : '-');
            $sheet->setCellValue('G' . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }

        // Set auto size untuk kolom
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set title sheet
        $sheet->setTitle('Data Stok');

        // Buat writer dan set header untuk download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stok = StokModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with('Supplier', 'Barang', 'User')
            ->orderBy('stok_id')
            ->get();

        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);

        $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true jika ada gambar dari URL

        $pdf->render();

        return $pdf->stream('Data_Stok_' . date('Y-m-d_His') . '.pdf');
    }
}
