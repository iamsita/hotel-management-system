<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::all();

        return view('admin.foods.index', [
            'foods' => $foods,
        ]);
    }

    public function create()
    {
        return view('admin.foods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:breakfast,lunch,dinner,snacks,beverages',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['available'] = $request->has('available');

        Food::create($validated);

        return redirect()->route('admin.foods.index')->with('success', 'Food item added.');
    }

    public function edit(Food $food)
    {
        return view('admin.foods.edit', [
            'food' => $food,
        ]);
    }

    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:breakfast,lunch,dinner,snacks,beverages',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['available'] = $request->has('available');

        $food->update($validated);

        return redirect()->route('admin.foods.index')->with('success', 'Food item updated.');
    }

    public function destroy(Food $food)
    {
        $food->delete();

        return redirect()->route('admin.foods.index')->with('success', 'Food item deleted.');
    }
}
