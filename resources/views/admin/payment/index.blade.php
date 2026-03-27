@extends('admin.layout')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/simple-datatables/style.css') }}">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-striped" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Reference Code</th>
                    <th>Name</th>
                    <th>Credit</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th width="20%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topups as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->reference_code }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->amount_credits }}</td>
                    <td>{{ $row->price_total }}</td>
                    <td>
                        @if($row->status == 0)
                        <span class="badge bg-warning">Pending / Verification</span>
                        @elseif($row->status == 1)
                        <span class="badge bg-success">Approved / Success</span>
                        @else
                        <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('adminTopupDetail', $row->reference_code) }}"><span class="btn btn-sm btn-outline-primary">Detail</span></a>
                    </td>
                </tr>
                @endforeach
            <tbody>
        </table>
    </div>
</div>
@endsection
@section('js')
<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<script>
    let table1 = document.querySelector('#table1');
    let dataTable = new simpleDatatables.DataTable(table1);
</script>
@endsection