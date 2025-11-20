@extends('layouts.app')

@section('title', 'Virtual Account Management')
@section('page-title', 'Daftar Virtual Account')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">account_balance</span>
                Virtual Account List
            </h3>

            <div class="flex items-center space-x-2">
                {{-- 🔄 Tombol Update Expired --}}
                <button id="updateAllBtn"
                    class="bg-green-600 text-white flex items-center px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <span class="material-symbols-outlined text-sm mr-1">update</span>
                    Update Expired
                </button>

                <a href="{{ route('va.create') }}"
                    class="bg-blue-600 text-white flex items-center px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <span class="material-symbols-outlined text-sm mr-1">add</span>
                    Tambah VA
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="vaTable" class="min-w-full text-sm text-gray-700 border border-gray-100 rounded-lg">
                <thead>
                    <tr class="bg-gray-50 text-left uppercase text-xs font-semibold tracking-wider text-gray-500">
                        <th class="py-3 px-4 text-center">
                            <input type="checkbox" id="selectAll" class="cursor-pointer">
                        </th>
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">VA Number</th>
                        <th class="py-3 px-4">Bank</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Expired Date</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($va as $data)
                        <tr class="border-b hover:bg-gray-50 transition">
                            {{-- ✅ Checkbox per baris --}}
                            <td class="py-2 px-4 text-center">
                                <input type="checkbox" name="selected[]" value="{{ $data->id }}"
                                    class="rowCheckbox cursor-pointer">
                            </td>

                            <td class="py-2 px-4 font-medium text-gray-800">{{ $data->order_id }}</td>
                            <td class="py-2 px-4 text-gray-800 font-medium">{{ $data->va_number ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $data->bank_code == '013' ? 'Bank Permata' : '-' }}</td>
                            <td class="py-2 px-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ $data->remark2 ?? '-' }}</span>
                                    <span class="text-xs text-gray-500">{{ $data->remark3 ?? '' }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                @php
                                    $statusColor = match ($data->status) {
                                        'ACTIVE' => 'bg-green-100 text-green-700',
                                        'EXPIRED' => 'bg-yellow-100 text-yellow-700',
                                        'FAILED' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ strtoupper($data->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 text-gray-600">
                                {{ $data->expired_date ? \Carbon\Carbon::parse($data->expired_date)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="py-2 px-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('va.edit', $data->id) }}" class="text-blue-600 hover:text-blue-800"
                                        title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form action="{{ route('va.destroy', $data->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus VA ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
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

    {{-- ✅ SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // ✅ Init DataTable
            setTimeout(() => {
                $('#vaTable').DataTable({
                    responsive: true,
                    order: [
                        [6, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                        emptyTable: '<center>Belum ada Virtual Account yang terdaftar.</center>',
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

            // ✅ Checkbox Select All
            $('#selectAll').on('change', function() {
                $('.rowCheckbox').prop('checked', this.checked);
            });

            // ✅ Klik tombol Update Expired
            $('#updateAllBtn').click(function() {
                const selected = $('.rowCheckbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selected.length === 0) {
                    Swal.fire('Perhatian', 'Pilih minimal satu VA untuk diupdate!', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Mengupdate Virtual Account...',
                    text: 'Mohon tunggu, proses sedang berjalan.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('va.massUpdate') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selected
                    },
                    success: function(res) {
                        checkBatchStatus(res.batch_id);
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memulai proses update.', 'error');
                    }
                });
            });

           
            function checkBatchStatus(batchId) {
                const interval = setInterval(() => {
                    $.get(`/batch-status/${batchId}`, function(res) {
                        if (res.finished) {
                            clearInterval(interval);
                            Swal.fire('Selesai ✅', 'Semua Virtual Account berhasil diperbarui.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }, 2000);
            }
        });
    </script>
@endsection
