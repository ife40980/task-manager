<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_validation_and_success()
    {
        // Validation: title required
        $this->post('/tasks', [
            'title' => '',
            'description' => 'desc',
        ])->assertSessionHasErrors('title');

        // Successful create
        $response = $this->post('/tasks', [
            'title' => 'Test task',
            'description' => 'A description',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test task',
            'description' => 'A description',
            'status' => 'pending',
        ]);
    }

    public function test_edit_and_update_task()
    {
        $task = Task::create([
            'title' => 'Original',
            'description' => 'orig',
            'status' => 'pending',
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'Updated',
            'description' => 'updated desc',
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated',
            'status' => 'completed',
        ]);
    }

    public function test_toggle_status()
    {
        $task = Task::create([
            'title' => 'Toggle me',
            'description' => null,
            'status' => 'pending',
        ]);

        // Toggle to completed via update
        $response = $this->put(route('tasks.update', $task), [
            'title' => $task->title,
            'description' => $task->description,
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_delete_task()
    {
        $task = Task::create([
            'title' => 'To be deleted',
            'description' => 'delete me',
            'status' => 'pending',
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
