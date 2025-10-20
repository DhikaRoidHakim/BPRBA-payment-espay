@extends('layouts.app')

@section('title', 'Activities')
@section('page-title', 'Activities Log')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">search_activity</span>
                Activities Log
            </h3>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="vaTable" class="min-w-full text-sm text-gray-700 border border-gray-100 rounded-lg">
                <thead>
                    <tr class="bg-gray-50 text-left uppercase text-xs font-semibold tracking-wider text-gray-500">
                        <th class="py-3 px-4">Nama Log</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4">Nama Pengguna</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activities as $activity)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-2 px-4 font-medium text-gray-800">{{ $activity->log_name }}</td>
                            <td class="py-2 px-4 text-gray-800 font-medium">{{ $activity->description }}</td>
                            <td class="py-2 px-4">{{ $activity->causer->name }}</td>
                            <td class="py-2 px-4">{{ $activity->causer->role }}</td>
                            <td class="py-2 px-4">{{ $activity->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $('#vaTable').DataTable({
                    responsive: true,
                    order: [
                        [5, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                        emptyTable: '<center>Belum ada log yang terdaftar.</center>',
                        zeroRecords: '<center>Tidak ditemukan data yang cocok.</center>',
                        paginate: {
                            first: '<<',
                            last: '>>',
                            next: '>',
                            previous: '<'
                        }
                    }
                });
            }, 200);
        });
    </script>
@endsection
