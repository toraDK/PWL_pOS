@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('/stok/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file excel"></i> Export excel</a>
            <a href="{{ url('/stok/export_pdf') }}" target="_blank" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file pdf"></i> Export pdf</a>
            <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-sm btn-info mt-1">Import stok</button>
            <button onclick="modalAction('{{ url('stok/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row">
        </div>
        <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
        <thead>
            <tr>
                <th>Stok_ID</th>
                <th>supplier</th>
                <th>Barang</th>
                <th>User</th>
                <th>Stok tanggal</th>
                <th>jumlah stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        </table>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" databackdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = ''){
            $('#myModal').load(url,function(){
                $('#myModal').modal('show');
            });
        }

        var dataStok;
        $(document).ready(function() {
            dataStok = $("#table_stok").DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('stok/list') }}",
                    "dataType": "json",
                    "type": "POST"
                },
                columns: [
                    { data: 'stok_id', name: 't_stok.stok_id', orderable: true, searchable: true },
                    { data: 'supplier_nama', name: 'm_supplier.supplier_nama', orderable: true, searchable: true },
                    { data: 'barang_nama', name: 'm_barang.barang_nama', orderable: true, searchable: true },
                    { data: 'user_nama', name: 'm_user.nama', orderable: true, searchable: true },
                    { data: 'stok_tanggal', name: 't_stok.stok_tanggal', orderable: true, searchable: true },
                    { data: 'stok_jumlah', name: 't_stok.stok_jumlah', orderable: true, searchable: true },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush