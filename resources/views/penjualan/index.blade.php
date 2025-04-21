@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file excel"></i> Export excel</a>
            <a href="{{ url('/penjualan/export_pdf') }}" target="_blank" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file pdf"></i> Export pdf</a>
            <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
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
        <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
            <thead>
                <tr>
                    <th>Penjualan ID</th>
                    <th>User</th>
                    <th>Pembeli</th>
                    <th>Penjualan Kode</th>
                    <th>Penjualan Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var dataPenjualan;
        $(document).ready(function() {
            dataPenjualan = $("#table_penjualan").DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}",
                    "dataType": "json",
                    "type": "POST",
                },
                columns: [
                    { data: 'penjualan_id', name: 't_penjualan.penjualan_id', orderable: true, searchable: true },
                    { data: 'user_nama', name: 'm_user.nama', orderable: true, searchable: true },
                    { data: 'pembeli', name: 't_penjualan.pembeli', orderable: true, searchable: true },
                    { data: 'penjualan_kode', name: 't_penjualan.penjualan_kode', orderable: true, searchable: true },
                    { data: 'penjualan_tanggal', name: 't_penjualan.penjualan_tanggal', orderable: true, searchable: true },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush