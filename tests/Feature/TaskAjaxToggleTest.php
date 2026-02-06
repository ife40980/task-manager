<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskAjaxToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_toggle_changes_status()
    {
        $task = Task::create([
            'title' => 'Ajax toggle',
            'description' => null,
            'status' => 'pending',
        ]);

    $response = $this->patchJson(route('tasks.toggle', $task));

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);

        // toggle back
    $response = $this->patchJson(route('tasks.toggle', $task));
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }
}
