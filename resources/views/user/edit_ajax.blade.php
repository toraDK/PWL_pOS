@empty($user)
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
            <a href="{{ url('/user') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/user/' . $user->user_id . '/update_ajax') }}" method="POST" id="formedit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Level Pengguna</label>
                    <select name="level_id" id="level_id" class="form-control" required>
                        <option value="">- Pilih Level -</option>
                        @foreach($level as $l)
                            <option {{ ($l->level_id == $user->level_id) ? 'selected' : '' }} 
                                value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input value="{{ $user->username }}" type="text" name="username" id="username" class="form-control" required>
                    <small id="error-username" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input value="{{ $user->nama }}" type="text" name="nama" id="nama" class="form-control" required>
                    <small id="error-nama" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>photo profil</label>
                    <img id="preview-image" src="{{ asset('photos/' . $user->photo) }}" alt="Photo Profil" width="100" class="img-thumbnail mb-2">
                    <input type="file" name="photo" id="photo" class="form-control">
                    <small id="error-photo" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengubah password.</small>
                    <small id="error-password" class="error-text text-danger"></small>
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
            level_id: { required: true },
            username: { required: true, minlength: 3, maxlength: 20 },
            nama: { required: true, minlength: 3, maxlength: 100 },
            photo: { required: false },
            password: { minlength: 6, maxlength: 20 }
        },
        messages: {
            level_id: { required: "Level pengguna wajib dipilih." },
            username: { required: "Username wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 20 karakter." },
            nama: { required: "Nama wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." },
            password: { minlength: "Minimal 6 karakter.", maxlength: "Maksimal 20 karakter." }
        },
        submitHandler: function(form, event) {
            event.preventDefault(); // Mencegah submit form bawaan

            let formData = new FormData(form);
            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                contentType: false,
                processData: false,
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
                                dataUser.ajax.reload();
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
