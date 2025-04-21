@if(is_null($penjualan))
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                Data penjualan tidak ditemukan.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>
@else
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Penjualan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <!-- Informasi Penjualan -->
            <h6>Informasi Penjualan</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped table-hover table-sm"
                    style="table-layout: fixed; width: 100%;">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 70%;">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Penjualan ID</th>
                            <td>{{ $penjualan->penjualan_id }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $penjualan->user->nama ?? 'Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <th>Pembeli</th>
                            <td>{{ $penjualan->pembeli ?? 'Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <th>Penjualan Kode</th>
                            <td>{{ $penjualan->penjualan_kode ?? 'Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <th>Penjualan Tanggal</th>
                            <td>{{ $penjualan->penjualan_tanggal ? $penjualan->penjualan_tanggal->format('Y-m-d') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Detail Penjualan -->
            <h6>Detail Penjualan</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualan->details as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->barang->barang_nama ?? 'Tidak Ditemukan' }}</td>
                            <td>{{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada detail penjualan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>
@endif