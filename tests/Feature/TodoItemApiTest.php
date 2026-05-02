<?php
namespace Tests\Feature;

use App\Models\TodoItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_todo_items(): void
    {
        TodoItem::create(['name' => 'Item 1', 'description' => 'Desc 1']);
        TodoItem::create(['name' => 'Item 2', 'description' => 'Desc 2']);

        $response = $this->getJson('/api/todo-items');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_can_create_a_todo_item(): void
    {
        $response = $this->postJson('/api/todo-items', [
            'name'        => 'New Item',
            'description' => 'New Description',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name'        => 'New Item',
                'description' => 'New Description',
            ]);

        $this->assertDatabaseHas('todo_items', [
            'name'        => 'New Item',
            'description' => 'New Description',
        ]);
    }

    public function test_validates_name_is_required_when_creating(): void
    {
        $response = $this->postJson('/api/todo-items', [
            'description' => 'Missing name',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_validates_description_is_required_when_creating(): void
    {
        $response = $this->postJson('/api/todo-items', [
            'name' => 'Missing description',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    public function test_validates_name_max_length(): void
    {
        $longName = str_repeat('a', 256);

        $response = $this->postJson('/api/todo-items', [
            'name' => $longName,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_can_show_a_single_todo_item(): void
    {
        $todoItem = TodoItem::create([
            'name'        => 'Test Item',
            'description' => 'Test Description',
        ]);

        $response = $this->getJson("/api/todo-items/{$todoItem->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id'          => $todoItem->id,
                'name'        => 'Test Item',
                'description' => 'Test Description',
            ]);
    }

    public function test_returns_404_for_non_existent_item(): void
    {
        $response = $this->getJson('/api/todo-items/999');

        $response->assertStatus(404);
    }

    public function test_can_update_a_todo_item(): void
    {
        $todoItem = TodoItem::create([
            'name'        => 'Original',
            'description' => 'Original Desc',
        ]);

        $response = $this->putJson("/api/todo-items/{$todoItem->id}", [
            'name'        => 'Updated',
            'description' => 'Updated Desc',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'        => 'Updated',
                'description' => 'Updated Desc',
            ]);

        $this->assertDatabaseHas('todo_items', [
            'id'          => $todoItem->id,
            'name'        => 'Updated',
            'description' => 'Updated Desc',
        ]);
    }

    public function test_validates_name_is_required_when_updating(): void
    {
        $todoItem = TodoItem::create(['name' => 'Test', 'description' => 'Test description']);

        $response = $this->putJson("/api/todo-items/{$todoItem->id}", [
            'name' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_validates_description_is_required_when_updating(): void
    {
        $todoItem = TodoItem::create(['name' => 'Test', 'description' => 'Test description']);

        $response = $this->putJson("/api/todo-items/{$todoItem->id}", [
            'description' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    public function test_can_delete_a_todo_item(): void
    {
        $todoItem = TodoItem::create([
            'name'        => 'To Delete',
            'description' => 'Will be deleted',
        ]);

        $response = $this->deleteJson("/api/todo-items/{$todoItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('todo_items', [
            'id' => $todoItem->id,
        ]);
    }

}
