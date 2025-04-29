<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CabinetController extends Controller
{
    public function index()
    {
        $cabinets = Cabinet::all();
        return view('cabinets.index', compact('cabinets'));
    }
    public function create()
    {
        return view('cabinets.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|string|unique:cabinets,number|max:50',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Cabinet::create([
            'number' => $request->number,
            'description' => $request->description,
            'capacity' => $request->capacity,
        ]);

        return redirect()->route('cabinets.index')->with('success', 'Кабинет успешно добавлен.');
    }
    public function destroy(Cabinet $cabinet)
    {
        $cabinet->delete();
        return redirect()->route('cabinets.index')->with('success', 'Кабинет успешно удален.');
    }
}
