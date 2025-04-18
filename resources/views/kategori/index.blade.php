@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('/kategori/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file excel"></i> Export kategori</a>
            <a href="{{ url('/kategori/export_pdf') }}" target="_blank" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file pdf"></i> Export kategori</a>
            <button onclick="modalAction('{{ url('/kategori/import') }}')" class="btn btn-sm btn-info mt-1">Import kategori</button>
            <button onclick="modalAction('{{ url('kategori/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
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
        <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori">
        <thead>
            <tr>
                <th>kategori_ID</th>
                <th>kategori kode</th>
                <th>Nama kategori</th>
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

        var dataKategori;
        $(document).ready(function() {
            dataKategori = $("#table_kategori").DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('kategori/list') }}",
                    "dataType": "json",
                    "type": "POST"
                },
                columns: [
                    { data: 'kategori_id', name: 'kategori_id' },
                    {data: 'kategori_kode', name: 'kategori_kode'},
                    {data: 'kategori_nama', name: 'kategori_nama'},
                    {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush