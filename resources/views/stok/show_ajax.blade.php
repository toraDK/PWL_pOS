@if(is_null($stok))
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                Data stok tidak ditemukan.
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
            <h5 class="modal-title">Detail Stok</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped table-hover table-sm"
                    style="table-layout: fixed; width: 100%;">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 70%;">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Stok ID</th>
                            <td>{{ $stok->stok_id }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $stok->barang->barang_nama ?? 'Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Supplier</th>
                            <td>{{ $stok->supplier->supplier_nama ?? 'Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <th>Nama User</th>
                            <td>{{ $stok->user->nama ?? 'Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Stok</th>
                            <td>{{ $stok->stok_tanggal ? $stok->stok_tanggal->format('Y-m-d') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Stok</th>
                            <td>{{ $stok->stok_jumlah }}</td>
                        </tr>
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