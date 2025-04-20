@empty($stok)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan.
            </div>
            <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/stok/' . $stok->stok_id . '/update_ajax') }}" method="POST" id="formedit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="user_name">Nama User</label>
                    <input type="text" id="user_name" class="form-control" value="{{ $user->nama }}" readonly>
                </div>
                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <input type="text" id="supplier" class="form-control" value="{{ $supplier->supplier_nama }}" readonly>
                </div>
                <div class="form-group">
                    <label for="barang">barang</label>
                    <input type="text" id="barang" class="form-control" value="{{ $barang->barang_nama }}" readonly>
                </div>
                <div class="form-group">
                    <label for="stok_tanggal">stok tanggal</label>
                    <input type="date" id="stok_tanggal" class="form-control" value="{{ $stok->stok_tanggal_formatted }}" readonly>
                </div>
                <div class="form-group">
                    <label for="stok_jumlah">stok jumlah</label>
                    <input type="number" id="stok_jumlah" name="stok_jumlah" class="form-control" value="{{ $stok->stok_jumlah }}">
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#formedit").validate({
            rules: {
                stok_jumlah: {
                    required: true,
                    number: true,
                    min: {{ $stok->stok_jumlah }} // Pastikan nilai baru lebih besar dari nilai lama
                }
            },
            messages: {
                stok_jumlah: {
                    required: "Jumlah stok wajib diisi.",
                    number: "Harus berupa angka.",
                    min: "Jumlah stok hanya boleh ditambah, tidak boleh dikurangi (minimal {{ $stok->stok_jumlah }})."
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#modal-master').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    dataStok.ajax.reload();
                                }
                            });
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan dalam mengupdate data. Silakan coba lagi.'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endempty