@extends('layout.app')
@section('content')

    <div class="flex h-screen items-center justify-center bg-gray-300">
        <div class="w-full max-w-md bg-gray-50 p-8 rounded-xl shadow-md">
            <div class="mb-6 text-center">
                <h1 class="text-3xl font-bold text-slate-800">Masuk Akun</h1>
                <p class="text-slate-500">Silahkan masukkan email dan password Anda</p>
            </div>

            <form action="{{ route('login.proses')}}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-lg border @error('email') b-red-500 @else border-slate-300 @enderror focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-colors bg-white text-slate-900">
                    
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 rounded-lg border @error('password') b-red-500 @else b-slate-300 @enderror focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-colors bg-white text-slate-900">
                    
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember" class="ml-2 block text-sm text-slate-600">Ingat Saya</label>
                    </div>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">Lupa Password?</a>
                </div>
                
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors font-medium text-lg">
                    Masuk
                </button>
            </form>

            @if (session('success'))
                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection