<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class GroceryListController extends Controller
{
    public function index()
    {
        $branches = Branch::active()->orderBy('name')->get();
        return view('grocery-list.index', compact('branches'));
    }
}
