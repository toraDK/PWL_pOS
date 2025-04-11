<form action="{{ url('/user/ajax') }}" method="POST" id="form-tambah">
@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tambah Data User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Level Pengguna</label>
            <select name="level_id" id="level_id" class="form-control" required>
                <option value="">- Pilih Level -</option>
                @foreach($level as $l)
                    <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                @endforeach
            </select>
            <small id="error-level_id" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input value="" type="text" name="username" id="username" class="form-control" required>
            <small id="error-username" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
            <label>Nama</label>
            <input value="" type="text" name="nama" id="nama" class="form-control" required>
            <small id="error-nama" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
            <label>photo profil</label>
            <input type="file" name="photo" id="photo" class="form-control">
            <small id="error-photo" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input value="" type="password" name="password" id="password" class="form-control" required>
            <small id="error-password" class="error-text form-text text-danger"></small>
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
        $("#form-tambah").submit(function (e) {
            e.preventDefault(); // Mencegah reload halaman

            var formData = new FormData(this); // Gunakan FormData untuk file upload

            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(".error-text").text(""); // Bersihkan error
                },
                success: function (response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        dataUser.ajax.reload();
                    } else {
                        $.each(response.msgField, function (prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan pada server!'
                    });
                }
            });
        });
    });
</script>