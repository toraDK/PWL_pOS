<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Input tersembunyi untuk user_id -->
                <input type="hidden" name="user_id" value="{{ $user->user_id }}">

                <div class="form-group">
                    <label for="user_name">Nama User</label>
                    <input type="text" id="user_name" class="form-control" value="{{ $user->nama }}" readonly>
                </div>

                <div class="form-group">
                    <label for="pembeli">Pembeli</label>
                    <input type="text" name="pembeli" id="pembeli" class="form-control" required>
                    <span id="error-pembeli" class="error-text invalid-feedback"></span>
                </div>

                <div class="form-group">
                    <label for="penjualan_kode">Penjualan Kode</label>
                    <input type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" value="{{ $kode }}" readonly>
                    <span id="error-penjualan_kode" class="error-text invalid-feedback"></span>
                </div>

                <!-- Tabel untuk Input Barang -->
                <div class="form-group">
                    <label>Daftar Barang</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabel-barang">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="barang-list">
                                <!-- Baris pertama akan selalu ada -->
                                <tr class="barang-row">
                                    <td>
                                        <select name="barang_id[]" class="form-control barang-id" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($barang as $item)
                                                <option value="{{ $item->barang_id }}" data-harga="{{ $item->harga_jual }}">{{ $item->barang_nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="harga[]" class="form-control harga" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control jumlah" min="1" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus-barang" disabled>Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" id="tambah-barang">Tambah Barang</button>
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
        // Fungsi untuk mengisi harga otomatis saat barang dipilih
        function updateHarga() {
            $('#barang-list').on('change', '.barang-id', function() {
                let row = $(this).closest('.barang-row');
                let harga = $(this).find('option:selected').data('harga') || 0;
                row.find('.harga').val(harga);
            });
        }
    
        // Panggil fungsi updateHarga untuk baris awal
        updateHarga();
    
        // Tambah baris baru
        $('#tambah-barang').click(function() {
            let newRow = `
                <tr class="barang-row">
                    <td>
                        <select name="barang_id[]" class="form-control barang-id" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->barang_id }}" data-harga="{{ $item->harga_jual }}">{{ $item->barang_nama }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="harga[]" class="form-control harga" readonly>
                    </td>
                    <td>
                        <input type="number" name="jumlah[]" class="form-control jumlah" min="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm btn-hapus-barang">Hapus</button>
                    </td>
                </tr>
            `;
            $('#barang-list').append(newRow);
            updateHarga(); // Panggil ulang untuk baris baru
        });
    
        // Hapus baris
        $('#barang-list').on('click', '.btn-hapus-barang', function() {
            $(this).closest('.barang-row').remove();
        });
    
        // Validasi form menggunakan jQuery Validate
        $("#form-tambah").validate({
            rules: {
                pembeli: { required: true },
                'barang_id[]': { required: true },
                'jumlah[]': { required: true, number: true, min: 1 }
            },
            messages: {
                pembeli: "Pembeli harus diisi.",
                'barang_id[]': "Pilih barang.",
                'jumlah[]': {
                    required: "Jumlah harus diisi.",
                    number: "Jumlah harus berupa angka.",
                    min: "Jumlah minimal 1."
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataPenjualan.ajax.reload(); // Reload DataTables
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
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menyimpan data. Silakan coba lagi.'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('td').append(error); // Tempatkan error di dalam <td>
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>