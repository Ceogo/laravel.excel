<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('teachers.index', compact('teachers'));
    }
    public function create()
    {
        return view('teachers.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
            'status' => $request->status,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Преподаватель успешно добавлен.');
    }
    public function destroy(User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            return redirect()->route('teachers.index')->with('error', 'Этот пользователь не является преподавателем.');
        }

        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Преподаватель успешно удален.');
    }
}
