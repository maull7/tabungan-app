<x-layouts.guest>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi</label>
            <input id="password" type="password" name="password" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>
        <div class="flex items-center justify-between text-sm text-slate-600">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Ingat saya
            </label>
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:underline">Daftar akun</a>
        </div>
        <x-button type="submit" class="w-full justify-center">Masuk</x-button>
    </form>
</x-layouts.guest>
