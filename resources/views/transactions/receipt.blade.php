<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Transaksi {{ $transaction->receipt_number }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f7fafc; color: #1f2937; margin: 0; padding: 0; }
        .receipt { max-width: 560px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 24px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .logo { font-size: 20px; font-weight: 700; color: #1d4ed8; }
        .badge { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }
        .badge.deposit { background: #dcfce7; color: #15803d; }
        .badge.withdrawal { background: #fef3c7; color: #b45309; }
        dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        dt { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        dd { font-size: 16px; font-weight: 600; color: #1f2937; margin: 4px 0 0; }
        .note { margin-top: 20px; padding: 16px; border-radius: 16px; background: #f8fafc; font-size: 14px; color: #334155; }
        .footer { margin-top: 24px; display: flex; justify-content: space-between; align-items: flex-end; }
        .qr { display: grid; grid-template-columns: repeat(10, 6px); grid-auto-rows: 6px; gap: 1px; padding: 6px; background: #000; border-radius: 8px; }
        .qr span { background: #fff; border-radius: 1px; }
        .qr span.filled { background: #000; }
        @media print {
            body { background: #fff; }
            .receipt { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>
@php
    $hash = md5($transaction->receipt_number);
    $bits = [];
    for ($i = 0; $i < 100; $i++) {
        $bits[] = intval($hash[$i % strlen($hash)], 16) % 2 === 0;
    }
@endphp
<div class="receipt">
    <div class="header">
        <div>
            <div class="logo">{{ config('app.name', 'Tabungan') }}</div>
            <div style="font-size:12px; color:#64748b;">Struk Transaksi</div>
        </div>
        <div class="badge {{ $transaction->type }}">
            {{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}
        </div>
    </div>
    <div style="margin-bottom: 24px;">
        <div style="font-size:28px; font-weight:700; color:#111827;">{{ 'Rp '.number_format($transaction->amount, 2, ',', '.') }}</div>
        <div style="font-size:14px; color:#64748b;">Saldo setelah transaksi: {{ 'Rp '.number_format($transaction->running_balance, 2, ',', '.') }}</div>
    </div>
    <dl>
        <div>
            <dt>Nomor Struk</dt>
            <dd>{{ $transaction->receipt_number }}</dd>
        </div>
        <div>
            <dt>Tanggal</dt>
            <dd>{{ $transaction->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</dd>
        </div>
        <div>
            <dt>Nama Nasabah</dt>
            <dd>{{ $user->name }}</dd>
        </div>
        <div>
            <dt>Email</dt>
            <dd>{{ $user->email }}</dd>
        </div>
        <div>
            <dt>No. Rekening</dt>
            <dd>{{ $account->account_number }}</dd>
        </div>
        <div>
            <dt>Jenis Transaksi</dt>
            <dd>{{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}</dd>
        </div>
        @if ($transaction->type === 'deposit')
            <div>
                <dt>Status Pembayaran</dt>
                <dd>
                    @if ($transaction->payment_status === \App\Models\Transaction::STATUS_COMPLETED)
                        Lunas
                    @elseif ($transaction->payment_status === \App\Models\Transaction::STATUS_PENDING)
                        Menunggu Pembayaran
                    @else
                        Gagal
                    @endif
                </dd>
            </div>
            @if ($transaction->payment_reference)
                <div>
                    <dt>Referensi Pembayaran</dt>
                    <dd>{{ $transaction->payment_reference }}</dd>
                </div>
            @endif
        @endif
    </dl>

    @if ($transaction->note)
        <div class="note">
            <strong>Catatan:</strong>
            <div>{{ $transaction->note }}</div>
        </div>
    @endif

    <div class="footer">
        <div>
            <div style="font-size:12px; color:#94a3b8;">Dicetak pada {{ now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</div>
            <div style="font-size:12px; color:#64748b;">Terima kasih telah menggunakan layanan kami.</div>
        </div>
        <div class="qr">
            @foreach ($bits as $bit)
                <span class="{{ $bit ? 'filled' : '' }}"></span>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
