<?php

namespace App\Http\Controllers;

use App\Models\RequestModel;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requestType'   => 'required|string',
            'date'          => 'required|date',
            'time'          => 'required',
            'description'   => 'nullable|string',
            'images.*'      => 'nullable|image|max:2048'
        ]);

        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('uploads/requests', 'public');
            }
        }

        $imageUrls = !empty($paths) ? implode(',', $paths) : null;
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated user'], 401);
        }
        $req = RequestModel::create([
            'request_type' => $validated['requestType'],
            'user_id'      => $userId,
            'driver_id'    => null,
            'car_id'       => null,
            'type'         => $request->input('type', 'other'),
            'description'  => $validated['description'] ?? '',
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'image_url'    => $imageUrls,
            'status'       => 'pending'
        ]);


        return response()->json([
            'message' => 'Request created successfully',
            'data' => $req
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $req = RequestModel::find($id);
        if (!$req) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if ($req->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'request_type'   => 'sometimes|string',
            'date'           => 'sometimes|date',
            'time'           => 'sometimes',
            'description'    => 'sometimes|string',
            'driver_status'  => 'sometimes|in:pending,accepted,done',
            'approval_date'  => 'sometimes|date',
            'approval_time'  => 'sometimes|date_format:Y-m-d H:i:s',
            'images.*'       => 'sometimes|image|max:2048'
        ]);

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('uploads/requests', 'public');
            }
            $validated['image_url'] = implode(',', $paths);
        }

        $req->update([
            'request_type'   => $validated['request_type'] ?? $req->request_type,
            'date'           => $validated['date'] ?? $req->date,
            'time'           => $validated['time'] ?? $req->time,
            'description'    => $validated['description'] ?? $req->description,
            'driver_status'  => $validated['driver_status'] ?? $req->driver_status,
            'approval_date'  => $validated['approval_date'] ?? $req->approval_date,
            'approval_time'  => $validated['approval_time'] ?? $req->approval_time,
            'image_url'      => $validated['image_url'] ?? $req->image_url,
        ]);

        return response()->json([
            'message' => 'Request updated successfully!',
            'data'    => $req
        ]);
    }


    public function destroy($id)
    {
        $request = RequestModel::find($id);

        if (!$request) {
            return response()->json([
                'message'   => 'Request not found'
            ], 404);
        }

        if ($request->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->delete();
        return response()->json(['message' => 'Request deleted successfully']);
    }
}
