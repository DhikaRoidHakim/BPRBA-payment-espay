@extends('layouts.app')

@section('title', 'Edit Virtual Account')
@section('page-title', 'Edit Virtual Account')

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl mx-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbarui Virtual Account</h3>

        <form action="{{ route('va.update', $va->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Order ID</label>
                    <input type="text" value="{{ $va->order_id }}"
                        class="mt-1 w-full border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 1 (Nomor Hp)</label>
                    <input name="remark1" type="text" value="{{ $va->remark1 }}" class="mt-1 w-full rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 2 (Nama)</label>
                    <input name="remark2" type="text" value="{{ $va->remark2 }}" class="mt-1 w-full rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 3 (Alamat)</label>
                    <input name="remark3" type="text" value="{{ $va->remark3 }}" class="mt-1 w-full rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expired Date</label>
                    <input name="va_expired" type="number" value="{{ $va->va_expired }}" class="mt-1 w-full rounded-lg">
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('va.index') }}"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
    {{-- <script>
        alert('Dalam Masa pengembangan');
        window.location.href = "{{ route('va.index') }}";
    </script> --}}
@endsection
