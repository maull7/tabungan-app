<div class="overflow-hidden rounded-2xl border border-slate-200">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                {{ $head ?? '' }}
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            @if(isset($body))
                {{ $body }}
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
    @isset($empty)
        <div class="p-8 text-center text-slate-500">
            {{ $empty }}
        </div>
    @endisset
</div>
