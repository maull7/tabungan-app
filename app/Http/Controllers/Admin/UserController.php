<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateSavingsAccountForUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(
        protected CreateSavingsAccountForUser $createSavingsAccountForUser
    ) {
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $isAdmin = Arr::pull($data, 'is_admin', false);

        /** @var User $user */
        $user = User::create(array_merge($data, ['is_admin' => (bool) $isAdmin]));

        $this->createSavingsAccountForUser->handle($user);

        return back()->with('status', 'Pengguna baru berhasil ditambahkan.');
    }
}
