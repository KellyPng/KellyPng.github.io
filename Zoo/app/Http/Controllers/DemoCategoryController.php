<?php

namespace App\Http\Controllers;

use App\Models\DemoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class DemoCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $demoCategories = DemoCategory::all();
    return view('tickets.index', compact('demoCategories'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'category_name' => 'required'
        ];
        $messages = [
            'category_name.required' => 'The category name field is required.'
        ];

        $validator = FacadesValidator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->route('tickets.index')->withErrors($validator)->withInput(); // Keep the old input values in the form
        }

        $category = new DemoCategory();
        $category->demoCategoryName = $request->input('category_name');
        $category->save();

        return redirect()->route('tickets.index')->with('success', 'Category Added!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = DemoCategory::query()->find((int)$id);
        $category->delete();
        return redirect()->route('tickets.index')->with('success', 'Category Removed!');
    }
}
