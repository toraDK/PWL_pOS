@empty($kategori)
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
            <a href="{{ url('/kategori') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/kategori/' . $kategori->kategori_id . '/update_ajax') }}" method="POST" id="formedit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>kategori kode</label>
                    <input value="{{ $kategori->kategori_kode }}" type="text" name="kategori_kode" id="kategori_kode" class="form-control" required>
                    <small id="error-kategori_kode" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>kategori nama</label>
                    <input value="{{ $kategori->kategori_nama }}" type="text" name="kategori_nama" id="kategori_nama" class="form-control" required>
                    <small id="error-kategori_nama" class="error-text text-danger"></small>
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
                kategori_kode: { required: true, minlength: 3, maxlength: 20 },
                kategori_nama: { required: true, minlength: 3, maxlength: 100 }
            },
            messages: {
                kategori_kode: { required: "kategori kode wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 20 karakter." },
                kategori_nama: { required: "kategori nama wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." }
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
                                    dataKategori.ajax.reload();
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
