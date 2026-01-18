<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Menampilkan daftar task dengan sorting
public function index(Request $request)
{
    $sortBy = $request->query('sort_by', 'deadline'); // default kolom
    $sortDir = $request->query('sort_dir', 'asc');    // default arah

    // Validasi input agar hanya nilai yang diizinkan
    if (!in_array($sortDir, ['asc', 'desc'])) {
        $sortDir = 'asc';
    }

    if (!in_array($sortBy, ['deadline', 'title', 'status', 'created_at', 'updated_at'])) {
        $sortBy = 'deadline';
    }

    // Ambil task milik user, urutkan dulu berdasarkan status selesai
    $tasks = Task::where('id', auth()->id())
                ->orderBy('completed', 'asc')       // prioritas utama: belum selesai ke atas
                ->orderBy($sortBy, $sortDir)        // lalu urutkan sesuai pilihan user
                ->get();

    return view('tasks.index', compact('tasks', 'sortBy', 'sortDir'));
}


    // Menyimpan task baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'deadline' => 'required|date',
            'description' => 'nullable|string', // Tambahkan validasi untuk description
        ]);

        Task::create([
            'title' => $request->title,
            'deadline' => $request->deadline,
            'description' => $request->description, // Simpan description
            'id' => auth()->id(),
        ]);

        return redirect('/tasks');
    }

    // Toggle status "completed" (dipanggil dari checkbox)
   public function toggle(Request $request, $id)
{
    try {
        $task = Task::findOrFail($id);
        
        // Update status completed
        // Gunakan method boolean() dari Request yang lebih robust untuk menangani true/false, 1/0, "true"/"false", "on"
        $task->completed = $request->boolean('completed');
        $task->save();
        
        // Return JSON response for AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status task berhasil diperbarui',
                'task' => [
                    'id_task' => $task->id_task,
                    'completed' => $task->completed
                ]
            ]);
        }
        
        // Fallback for regular form submission
        return redirect()->back()->with('success', 'Status task berhasil diperbarui');
        
    } catch (\Exception $e) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status task'
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Gagal memperbarui status task');
    }}

    // Menghapus task
 public function destroy($id)
    {
        try {

            $task = Task::findOrFail($id);
            $task->delete();
            return response()->json([
            'message' => 'Task deleted successfully',
            'deleted_id' => $id
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update title dan deadline
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            
            // Validasi input
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'deadline' => 'required|date|after_or_equal:today',
                'completed' => 'nullable|boolean'
            ], [
                'title.required' => 'Judul task wajib diisi',
                'title.max' => 'Judul task maksimal 255 karakter',
                'description.max' => 'Deskripsi maksimal 1000 karakter',
                'deadline.required' => 'Deadline wajib diisi',
                'deadline.date' => 'Format deadline tidak valid',
                'deadline.after_or_equal' => 'Deadline tidak boleh kurang dari hari ini'
            ]);
            
            // Update task
            $task->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'deadline' => $validatedData['deadline'],
                'completed' => $request->has('completed') ? 1 : 0
            ]);
            
            // Jika request AJAX, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task berhasil diperbarui',
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'deadline' => $task->deadline,
                        'completed' => $task->completed,
                        'created_at' => $task->created_at,
                        'updated_at' => $task->updated_at->format('Y-m-d H:i:s')
                    ]
                ]);
            }
            
            // Jika bukan AJAX, redirect dengan pesan sukses
            return redirect()->route('tasks.index')->with('success', 'Task berhasil diperbarui');
            
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task tidak ditemukan'
                ], 404);
            }
            
            return redirect()->route('tasks.index')->with('error', 'Task tidak ditemukan');
            
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui task');
        }
    }
    // Menampilkan detail task
public function show($id)
    {
        try {
            $task = Task::findOrFail($id);
            
            // Jika request AJAX, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'deadline' => $task->deadline,
                        'completed' => $task->completed,
                        'created_at' => $task->created_at,
                        'updated_at' => $task->updated_at
                    ]
                ]);
            }
            
            // Jika bukan AJAX, redirect atau return view
            return redirect()->route('tasks.index');
            
        } catch (ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task tidak ditemukan'
                ], 404);
            }
            
            return redirect()->route('tasks.index')->with('error', 'Task tidak ditemukan');
        }
    }


    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $deadlineFormatted = $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '';

        return view('your_view_name', compact('task', 'deadlineFormatted'));
    }
}