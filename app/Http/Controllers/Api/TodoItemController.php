<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoItemController extends Controller
{

protected $rules = [
        'name'        => 'required|string|max:255',
        'description' => 'required|string',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(TodoItem::latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules);

        $todoItem = DB::transaction(function () use ($validated) {
            return TodoItem::create($validated);
        });

        return response()->json($todoItem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoItem $todoItem)
    {
        return response()->json($todoItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TodoItem $todoItem)
    {
        $validated = $request->validate($this->rules);

        $todoItem = DB::transaction(function () use ($todoItem, $validated) {
            $todoItem->update($validated);
            return $todoItem->fresh();
        });

        return response()->json($todoItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoItem $todoItem)
    {
        $todoItem->delete();

        return response()->json(null, 204);
    }
}
