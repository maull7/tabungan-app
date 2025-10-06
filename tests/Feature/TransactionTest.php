<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_savings_account_when_user_accesses_dashboard(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->savingsAccount);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $user->refresh();

        $this->assertNotNull($user->savingsAccount);
        $this->assertSame('0.00', $user->savingsAccount->balance);
    }

    public function test_it_stores_deposit_and_updates_balance(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/transactions', [
            'amount' => 150000,
            'note' => 'Setoran awal',
        ]);

        $response->assertRedirect();

        $account = $user->fresh()->savingsAccount;

        $this->assertSame('150000.00', $account->balance);
        $this->assertCount(1, $account->transactions);
        $this->assertSame('150000.00', $account->transactions->first()->running_balance);
    }

    public function test_it_rejects_withdrawal_when_balance_is_insufficient(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/transactions', [
            'amount' => 50000,
        ]);

        $response = $this->actingAs($user)->post('/transactions/withdraw', [
            'amount' => 100000,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_it_blocks_access_to_transactions_owned_by_other_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->post('/transactions', [
            'amount' => 75000,
        ]);

        $transaction = $user->fresh()->savingsAccount->transactions->first();

        $this->actingAs($other)
            ->get(route('transactions.show', $transaction))
            ->assertForbidden();
    }

    public function test_receipt_endpoint_returns_valid_response(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/transactions', [
            'amount' => 125000,
        ]);

        $transaction = $user->fresh()->savingsAccount->transactions->first();

        $response = $this->actingAs($user)->get(route('transactions.receipt', $transaction));

        $response->assertOk();
        $contentType = $response->headers->get('content-type');

        $this->assertNotNull($contentType);
        $this->assertMatchesRegularExpression('/pdf|text\\/html/', $contentType);
    }
}
