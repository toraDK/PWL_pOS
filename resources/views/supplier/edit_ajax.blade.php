@empty($supplier)
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
            <a href="{{ url('/supplier') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/supplier/' . $supplier->supplier_id . '/update_ajax') }}" method="POST" id="formedit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>supplier kode</label>
                    <input value="{{ $supplier->supplier_kode }}" type="text" name="supplier_kode" id="supplier_kode" class="form-control" required>
                    <small id="error-supplier_kode" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>supplier nama</label>
                    <input value="{{ $supplier->supplier_nama }}" type="text" name="supplier_nama" id="supplier_nama" class="form-control" required>
                    <small id="error-supplier_nama" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>supplier alamat</label>
                    <input value="{{ $supplier->supplier_alamat }}" type="text" name="supplier_alamat" id="supplier_alamat" class="form-control" required>
                    <small id="error-supplier_alamat" class="error-text text-danger"></small>
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
                supplier_kode: { required: true, minlength: 3, maxlength: 20 },
                supplier_nama: { required: true, minlength: 3, maxlength: 100 },
                supplier_alamat: {required: true, minlength: 3, maxlength: 100},
            },
            messages: {
                supplier_kode: { required: "supplier kode wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 20 karakter." },
                supplier_nama: { required: "supplier nama wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." },
                supplier_alamat: { required: "supplier alamat wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." }
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
                                    dataSupplier.ajax.reload();
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
