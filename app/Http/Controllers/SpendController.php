<?php

namespace App\Http\Controllers;

use App\Models\Spend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SpendController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Spend::where('user_id', Auth::id())
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

	public function destroy(Spend $spend) {
	//     \Log::info('🔨 Entered SpendController@destroy');
	//     \Log::info('Auth ID is: ' . auth()->id());
	//     \Log::info('Spend user_id: ' . $spend->user_id);

	$this->authorize('delete', $spend);

	$spend->delete();

	return response()->noContent();
	}
}
