<?php
namespace Tests\Unit;

use App\Models\TodoItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_todo_item(): void
    {
        $todoItem = TodoItem::create([
            'name'        => 'Test Item',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('todo_items', [
            'name'        => 'Test Item',
            'description' => 'Test Description',
        ]);
        $this->assertNotNull($todoItem->id);
    }

    public function test_has_fillable_attributes(): void
    {
        $todoItem = new TodoItem();
        $fillable = $todoItem->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
    }

    public function test_can_update_a_todo_item(): void
    {
        $todoItem = TodoItem::create([
            'name'        => 'Original Name',
            'description' => 'Original Description',
        ]);

        $todoItem->update([
            'name'        => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        $this->assertEquals('Updated Name', $todoItem->fresh()->name);
        $this->assertEquals('Updated Description', $todoItem->fresh()->description);
    }

    public function test_can_delete_a_todo_item(): void
    {
        $todoItem = TodoItem::create([
            'name'        => 'To Be Deleted',
            'description' => 'Will be deleted',
        ]);

        $id = $todoItem->id;
        $todoItem->delete();

        $this->assertNull(TodoItem::find($id));
    }

    public function test_returns_items_in_descending_order(): void
    {
        $first  = TodoItem::create(['name' => 'First Item', 'description' => 'Desc 1']);
        $second = TodoItem::create(['name' => 'Second Item', 'description' => 'Desc 2']);
        $third  = TodoItem::create(['name' => 'Third Item', 'description' => 'Desc 3']);

        $items = TodoItem::latest('id')->get();

        $this->assertEquals($third->id, $items->first()->id);
        $this->assertEquals($first->id, $items->last()->id);
    }

    public function test_database_rejects_null_description(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        TodoItem::create([
            'name' => 'Test Item',
        ]);
    }

    public function test_database_rejects_null_name(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        TodoItem::create([
            'description' => 'Test Item',
        ]);
    }
}
