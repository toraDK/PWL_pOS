@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('level/create') }}">Tambah</a>
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
@endsection

@push('css')
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            var dataUser = $("#table_level").DataTable({
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