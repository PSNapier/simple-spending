<?php

namespace App\Http\Controllers;

use App\Models\Spend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpendController extends Controller
{
    public function index()
    {
        return Spend::where('user_id', Auth::id())
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        return Spend::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);
    }

    public function update(Request $request, Spend $spend)
    {
        $this->authorize('update', $spend);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'amount' => 'numeric',
            'date' => 'date',
        ]);

        $spend->update($validated);

        return $spend;
    }

    public function destroy(Spend $spend)
    {
        $this->authorize('delete', $spend);
        $spend->delete();

        return response()->noContent();
    }
}
