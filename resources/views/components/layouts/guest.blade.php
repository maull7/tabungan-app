<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tabungan') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="flex min-h-full items-center justify-center p-6">
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-slate-900">{{ config('app.name', 'Tabungan') }}</h1>
            <p class="text-sm text-slate-500">Masuk untuk mengelola tabungan Anda.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-700">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </div>
</body>
</html>
