<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncomeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Income::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'mods' => 'nullable|array',
        ]);

        return Income::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);
    }

    public function update(Request $request, Income $income)
    {
        $this->authorize('update', $income);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'amount' => 'numeric',
            'date' => 'date',
            'mods' => 'nullable|array',
        ]);

        $income->update($validated);

        return $income;
    }

    public function destroy(Income $income)
    {
        $this->authorize('delete', $income);
        $income->delete();
        return response()->noContent();
    }
} 