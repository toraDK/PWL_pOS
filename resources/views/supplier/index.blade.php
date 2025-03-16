@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('supplier/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('supplier/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
        <thead>
            <tr>
                <th>ID</th>
                <th>kode supplier</th>
                <th>nama supplier</th>
                <th>Alamat Supplier</th>
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

        var dataSupplier;
        $(document).ready(function() {
            dataSupplier = $("#table_supplier").DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('supplier/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.kategori_id = $('#kategori_id').val();
                    }
                },
                columns: [
                    {data: 'supplier_id', name: 'supplier_id', orderable: true, searchable: true},
                    {data: 'supplier_kode', name: 'supplier_kode', orderable: true, searchable: true},
                    {data: 'supplier_nama', name: 'supplier_nama', orderable: true, searchable: true},
                    {data: 'supplier_alamat', name: 'supplier_alamat', orderable: true, searchable: true},
                    {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush