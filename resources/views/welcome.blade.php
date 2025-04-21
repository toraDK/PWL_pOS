@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-tittle">Halo apa kabar!!</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            <h3>Selamat datang {{ $user->nama }}, ini adalah halaman utama aplikasi ini.</h3>

            <div class="row mb-3">
                <div class="col-sm-12 col-md-4 mb-2">
                    <div class="small-box bg-info shadow-lg rounded">
                        <div class="inner text-center">
                            <h4>{{ $ringkasan->sum('stok_ready') }}</h4>
                            <p>Total Stok Ready</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 mb-2">
                    <div class="small-box bg-warning shadow-lg rounded">
                        <div class="inner text-center">
                            <h4>{{ $ringkasan->sum('total_masuk') }}</h4>
                            <p>Total Stok Masuk</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck-loading"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 mb-2">
                    <div class="small-box bg-success shadow-lg rounded">
                        <div class="inner text-center">
                            <h4>{{ $ringkasan->sum('total_terjual') }}</h4>
                            <p>Total Barang Terjual</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>

                {{-- Tabel Detail Stok --}}
                <div class="card col-12 mt-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title m-0">Detail Stok Barang</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Total Stok Masuk</th>
                                        <th>Stok Dijual</th>
                                        <th>Stok Ready</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ringkasan as $index => $barang)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $barang->barang_nama }}</td>
                                            <td>{{ $barang->total_masuk }}</td>
                                            <td>{{ $barang->total_terjual }}</td>
                                            <td>{{ $barang->stok_ready }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data barang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th>{{ $ringkasan->sum('total_masuk') }}</th>
                                        <th>{{ $ringkasan->sum('total_terjual') }}</th>
                                        <th>{{ $ringkasan->sum('stok_ready') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection