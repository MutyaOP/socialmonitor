<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $items = User::query()
            ->when($request->q, fn($q, $s) => $q->where(
                fn($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
            ))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('items'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'operator'])]]);

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['role' => 'Өз рөліңізді өзгертуге болмайды.']);
        }

        $user->update($data);

        return back()->with('success', 'Рөл жаңартылды.');
    }
}
