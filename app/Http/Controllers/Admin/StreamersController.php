<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Category;
use App\Models\ChatGroup;
use App\Models\Streamer;
use App\Models\StreamerCategory;
use App\Models\User;
use App\Notifications\StreamerNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StreamersController extends Controller
{
    const ERROR_RESPONSE = 'Unable to complete your request the moment.';

    function index()
    {
        return view('admin.streamer.index');
    }

    function filter(Request $request)
    {
        $dateFrom = ($request->filled('date_from')) ? Carbon::parse($request->date_from)->startOfDay() : null;

        $dateTo = ($request->filled('date_to')) ? Carbon::parse($request->date_to)->endOfDay() : null;

        $search = $request->filled('search') ? $request->search : null;
        $status = $request->filled('status')  ? $request->status : null;

        // $query = User::withTrashed();
        $query = User::query()->where('is_streamer', true);

        $query = $query
            ->when(!empty($search), fn($query) => $query->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%")->orWhere('username', 'LIKE', "%{$search}%")->orWhere('username', 'LIKE', "%{$search}%"))
            ->when(!empty($status), fn($query) => $query->where('status', $status))
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($status) && !empty($dateFrom) && !empty($dateTo), fn($query) => $query->where('status', $status)->whereBetween('created_at', [$dateFrom, $dateTo]));

        $data['streamers'] = $query->latest()->paginate(50);

        return view('admin.streamer._table', $data);
    }

    function create()
    {
        $data['categories'] = Category::get();

        return view('admin.streamer.create', $data);
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => ['required', 'unique:users,username'],
            'email' => 'required|email|unique:users,email',
            'categories' => 'required',
            'categories.*' => 'required|exists:categories,id',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            $password = \Illuminate\Support\Str::random(5);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'first_name'  => $request->first_name,
                'last_name' => $request->last_name,
                'password' => $password,
                'is_streamer' => true
            ]);

            ChatGroup::create([
                'streamer_id' => $user->id
            ]);

            // assign categories to educator
            foreach ($request->categories as $category) {
                StreamerCategory::create([
                    'streamer_id' => $user->id,
                    'category_id' => $category
                ]);
            }

            $mailData = [
                'email' => $request->email,
                'name' => $request->first_name,
                'password' => $password
            ];

            $user->notify(new StreamerNotification($mailData));

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Streamer created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function edit(Streamer $streamer)
    {
        $data['streamer'] = $streamer;
        $data['categories'] = Category::get();
        $data['streamerCategories'] = StreamerCategory::where('streamer_id', $streamer->id)->pluck('category_id')->toArray();

        return view('admin.streamer.edit', $data);
    }


    function show(Streamer $streamer)
    {
        $data['streamer'] = $streamer;

        return view('admin.streamer.show', $data);
    }

    function update(Request $request, Streamer $streamer)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'categories' => 'required',
            'categories.*' => 'required|exists:categories,id',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $streamer->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            // assign categories to educator
            foreach ($request->categories as $category) {
                StreamerCategory::where('streamer_id', $streamer->id)->delete();

                StreamerCategory::create([
                    'streamer_id' => $streamer->id,
                    'category_id' => $category
                ]);
            }

            return $this->sendResponse([], "Streamer profile updated successfully.");
        } catch (\Exception $e) {
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function suspend(Streamer $streamer)
    {
        $streamer->update([
            'status' => 'suspended',
        ]);

        return response()->json(['success' => true, 'message' => 'Suspended successfully.']);
    }

    function activate(Streamer $streamer)
    {
        $streamer->update([
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Activated successfully.']);
    }

    function emailForm(Streamer $streamer)
    {
        $data['streamer'] = $streamer;

        return view('admin.steamer._email-form', $data);
    }

    function changeEmail(Request $request, Streamer $streamer)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $streamer->update([
            'email' => $request->email
        ]);

        return $this->sendResponse([], "Email updated successfully");
    }

    function destroy(Streamer $streamer)
    {

        $streamer->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
}
