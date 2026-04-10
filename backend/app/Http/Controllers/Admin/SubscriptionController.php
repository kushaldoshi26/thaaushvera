<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $subs = Subscription::orderBy('id', 'desc')->get();
        return response()->json(['data' => $subs]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $v->errors()], 422);
        }

        $sub = Subscription::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration_months' => $request->duration_months,
            'description' => $request->description,
            'active' => $request->has('active') ? (bool)$request->active : true,
        ]);

        return response()->json($sub, 201);
    }

    public function show($id)
    {
        $this->authorizeAdmin();
        $sub = Subscription::find($id);
        if (!$sub) return response()->json(['message' => 'Not found'], 404);
        return response()->json($sub);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $sub = Subscription::find($id);
        if (!$sub) return response()->json(['message' => 'Not found'], 404);

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $v->errors()], 422);
        }

        $sub->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_months' => $request->duration_months,
            'description' => $request->description,
            'active' => $request->has('active') ? (bool)$request->active : $sub->active,
        ]);

        return response()->json($sub);
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $sub = Subscription::find($id);
        if (!$sub) return response()->json(['message' => 'Not found'], 404);
        $sub->delete();
        return response()->json(['message' => 'Deleted']);
    }

    protected function authorizeAdmin()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role ?? '', ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }
    }
}
