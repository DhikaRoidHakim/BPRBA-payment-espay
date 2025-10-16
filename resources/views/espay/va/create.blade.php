@extends('layouts.app')

@section('title', 'Create Virtual Account')
@section('page-title', 'Buat Virtual Account')

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl mx-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Form Pembuatan VA</h3>

        <form action="{{ route('va.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Order ID</label>
                    <input type="text" name="order_id" value="{{ old('order_id') }}"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                    <input type="text" name="amount" value="{{ old('amount') }}"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Comm Code</label>
                    <input type="text" name="comm_code" value="SGWBPRB"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bank Code</label>
                    <input type="text" name="bank_code" value="013"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 1 (Phone)</label>
                    <input type="text" name="remark1" value="{{ old('remark1') }}"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 2 (Name)</label>
                    <input type="text" name="remark2" value="{{ old('remark2') }}"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 3 (Email)</label>
                    <input type="email" name="remark3" value="{{ old('remark3') }}"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remark 4 (Reference)</label>
                    <input type="text" name="remark4" value="BPRBANGUNA17393660081AB4W"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">VA Expired (menit)</label>
                    <input type="number" name="va_expired" value="60"
                        class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('va.index') }}"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Submit</button>
            </div>
        </form>
    </div>
@endsection
