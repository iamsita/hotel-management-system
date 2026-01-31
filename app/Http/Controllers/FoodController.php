<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::paginate(15);

        return view('staff.food.index', compact('foods'));
    }

    public function create()
    {
        return view('staff.food.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'available' => 'nullable|boolean',
        ]);

        $validated['available'] = $request->has('available') ? 1 : 0;

        Food::create($validated);

        return redirect()->route('staff.food.index')->with('success', 'Food item created!');
    }

    public function edit(Food $food)
    {
        return view('staff.food.edit', compact('food'));
    }

    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'available' => 'nullable|boolean',
        ]);

        $validated['available'] = $request->has('available') ? 1 : 0;

        $food->update($validated);

        return redirect()->route('staff.food.index')->with('success', 'Food item updated!');
    }

    public function destroy(Food $food)
    {
        $food->delete();

        return redirect()->route('staff.food.index')->with('success', 'Food item deleted!');
    }
}
