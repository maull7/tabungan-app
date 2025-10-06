<?php

use App\Models\User;

it('membuat tabungan saat pengguna pertama kali mengakses dashboard', function () {
    $user = User::factory()->create();

    expect($user->savingsAccount)->toBeNull();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $user->refresh();
    expect($user->savingsAccount)->not()->toBeNull();
    expect($user->savingsAccount->balance)->toBe('0.00');
});

it('menyimpan setoran dan memperbarui saldo', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/transactions', [
        'amount' => 150000,
        'note' => 'Setoran awal',
    ]);

    $response->assertRedirect();

    $account = $user->fresh()->savingsAccount;
    expect($account->balance)->toBe('150000.00');
    expect($account->transactions)->toHaveCount(1);
    expect($account->transactions->first()->running_balance)->toBe('150000.00');
});

it('menolak penarikan ketika saldo tidak mencukupi', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post('/transactions', [
        'amount' => 50000,
    ]);

    $response = $this->actingAs($user)->post('/transactions/withdraw', [
        'amount' => 100000,
    ]);

    $response->assertSessionHasErrors('amount');
});

it('mencegah akses transaksi milik pengguna lain', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post('/transactions', [
        'amount' => 75000,
    ]);

    $transaction = $user->fresh()->savingsAccount->transactions->first();

    $this->actingAs($other)
        ->get(route('transactions.show', $transaction))
        ->assertForbidden();
});

it('endpoint struk mengembalikan respon valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/transactions', [
        'amount' => 125000,
    ]);

    $transaction = $user->fresh()->savingsAccount->transactions->first();

    $response = $this->actingAs($user)->get(route('transactions.receipt', $transaction));

    $response->assertOk();
    $contentType = $response->headers->get('content-type');
    expect($contentType)->not()->toBeNull();
    expect($contentType)->toMatch('/pdf|text\\/html/');
});
