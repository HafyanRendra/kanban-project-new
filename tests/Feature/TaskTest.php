<?php

namespace Tests\Feature;
use App\Models\User; // Ditambahkan
use App\Models\Task; // Ditambahkan

use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth; // Ditambahkan
use Illuminate\Support\Facades\Gate; // Ditambahkan
use Illuminate\Support\Facades\Storage; // Ditambahkan
use Illuminate\Http\UploadedFile; // Ditambahkan
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_home(): void
    // {
    //     $response = $this->get(route('home'));

    //     $response->assertStatus(302);
    // }





    use RefreshDatabase;

    private $mockedUsers = [];
    private $mockedTasks = []; // Ditambahkan

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create();
        User::factory()->create();

        $user1 = User::first();
        $user2 = User::where('id', '!=', $user1->id)->first();

        array_push($this->mockedUsers, $user1, $user2);

        $this->actingAs($user1);

        // Tambahkan code di bawah ini
        $tasks = [
            [
                'name' => 'Task 1',
                'status' => Task::STATUS_NOT_STARTED,
                'user_id' => $user1->id,
            ],
            [
                'name' => 'Task 2',
                'status' => Task::STATUS_IN_PROGRESS,
                'user_id' => $user1->id,
            ],
            [
                'name' => 'Task 3',
                'status' => Task::STATUS_COMPLETED,
                'user_id' => $user1->id,
            ],
            [
                'name' => 'Task 4',
                'status' => Task::STATUS_COMPLETED,
                'user_id' => $user2->id,
            ],
        ];

        Task::insert($tasks);

        $this->mockedTasks = Task::with('user', 'files')
            ->get()
            ->toArray();
    }
    

    public function test_redirect_not_logged_in_user(): void
    {
        Auth::logout();

        $response = $this->get('/');
        $response->assertStatus(302);
    }

    public function test_home(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $response->assertViewIs('home');
        $response->assertViewHas('completed_count');
        $response->assertViewHas('uncompleted_count');

        $completed_count = $response->viewData('completed_count');
        $uncompleted_count = $response->viewData('uncompleted_count');

        $this->assertEquals(1, $completed_count);
        $this->assertEquals(2, $uncompleted_count);
    }

    public function test_index_without_permission(): void
    {
        $response = $this->get(route('tasks.index'));
    
         $response->assertStatus(200);
        $response->assertViewIs('tasks.index');

        // Data task dikirim dari controller 
        $tasks = $response->viewData('tasks')->toArray();

         // Data task pada $user1 diperoleh dari $mockedTasks 
         $expectedTasks = [
        $this->mockedTasks[0],
        $this->mockedTasks[1],
        $this->mockedTasks[2],
         ];

         // Bandingkanlah 2 (dua) data di atas tersebut
        $this->assertEquals($expectedTasks, $tasks);
    }

    public function test_index_with_right_permission(): void
    {
        Gate::shouldReceive('allows')
            ->with('viewAnyTask', Task::class)
            ->andReturn(true);
        Gate::shouldReceive('any')->andReturn(false);
        Gate::shouldReceive('check')->andReturn(false);

        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);

        $tasks = $response->viewData('tasks');

        // Data task dari semua pengguna
        $expectedTasks = $this->mockedTasks;

        $this->assertEquals($expectedTasks, $tasks->toArray());
    }

    public function test_create()
    {
         $response = $this->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.create');
        $response->assertViewHas('pageTitle');

        $pageTitle = $response->viewData('pageTitle');

    $this->assertEquals('Create Task', $pageTitle);
    }

    public function test_store_without_file()
    {
        $newTask = [
                    'name' => 'New Task',
                    'detail' => 'New Task Detail',
                    'due_date' => date('Y-m-d', time()),
                    'status' => Task::STATUS_IN_PROGRESS,
                   ];

        // Request berjenis post ke method tasks.store
        $response = $this->post(route('tasks.store'), $newTask);

         $response->assertStatus(302);
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHasNoErrors();

        // Memeriksa apakah tabel "tasks" memiliki data yang sama dengan $newTask 
         $this->assertDatabaseHas('tasks', $newTask);
    }

    public function test_store_with_file()
    {
        // Mempersiapkan penyimpanan file dalam disk "public"
        Storage::fake('public');

        $newTask = [
            'name' => 'New Task',
            'detail' => 'New Task detail',
            'due_date' => date('Y-m-d', time()),
            'status' => Task::STATUS_IN_PROGRESS,
        ];

        // Melakukan file upload ke tempat penyimpanan tersebut
        $file = UploadedFile::fake()->image('test_image.png');

        // Menyimpan data task beserta dengan file
        $response = $this->post(
            route('tasks.store'),
            array_merge($newTask, ['file' => $file])
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', $newTask);

        // Memeriksa apakah data task yang tersimpan memiliki file
        $task = Task::where('name', 'New Task')->first();
        $this->assertNotNull($task->files);

        // Memeriksa apakah file yang tersimpan dalam disc "public" dengan menggunakan file path
        $filePath = $task->files[0]->path;
        Storage::disk('public')->assertExists($filePath);
    }

    public function test_store_invalid_request()
    {
        $response = $this->post(route('tasks.store'), [
        'detail' => 'New Task',
        ]);

        $response->assertSessionHasErrors(['name', 'due_date', 'status']);
    }

    public function task_edit_task_owner()
    {
        $response = $this->get(route('tasks.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.edit');
        $response->assertViewHas('pageTitle');
    
        $pageTitle = $response->viewData('pageTitle');
    
        $this->assertEquals('Edit Task', $pageTitle);
    }

    // public function test_edit_with_right_permission()
    // {
  
    // }

    // public function test_edit_unauthorized_user()
    // {
  
    // }


}
