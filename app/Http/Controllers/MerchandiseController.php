<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchandise;
use Yajra\DataTables\Facades\DataTables;

class MerchandiseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Merchandise::query())
                ->addColumn('action', function($row){
                    return '<button class="btn btn-sm btn-primary edit-btn" data-id="'.$row->id.'">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backoffice.merchendise');
    }

    public function show($id)
    {
        $merchandise = Merchandise::findOrFail($id);
        
        return response()->json($merchandise);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
        ]);

        $merchandise = Merchandise::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Merchandise created successfully',
            'data' => $merchandise
        ]);
    }

    public function update(Request $request, $id)
    {
        $merchandise = Merchandise::find($id);
        
        if (!$merchandise) {
            return response()->json([
                'success' => false,
                'message' => 'Merchandise not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
        ]);

        $merchandise->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Merchandise updated successfully',
            'data' => $merchandise
        ]);
    }

    public function destroy($id)
    {
        $merchandise = Merchandise::find($id);

        if (!$merchandise) {
            return response()->json([
                'success' => false,
                'message' => 'Merchandise not found'
            ], 404);
        }

        $merchandise->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Merchandise deleted successfully'
        ]);
    }
}
