@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('/level/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file excel"></i> Export level</a>
            <a href="{{ url('/level/export_pdf') }}" target="_blank" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file pdf"></i> Export level</a>
            <button onclick="modalAction('{{ url('/level/import') }}')" class="btn btn-sm btn-info mt-1">Import level</button>
            <button onclick="modalAction('{{ url('level/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
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
        <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
        <thead>
            <tr>
                <th>Level_ID</th>
                <th>Level kode</th>
                <th>Nama Level</th>
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

        var dataLevel;
        $(document).ready(function() {
            dataLevel = $("#table_level").DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('level/list') }}",
                    "dataType": "json",
                    "type": "POST"
                },
                columns: [
                    { data: 'level_id', name: 'level_id' },
                    {data: 'level_kode', name: 'level_kode'},
                    {data: 'level_nama', name: 'level_nama'},
                    {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush