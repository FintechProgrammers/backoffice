<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Bonus;
use App\Models\User;
use App\Models\UserInfo;
use App\Notifications\NewAccountCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    const ERROR_RESPONSE = 'Unable to complete your request the moment.';

    function index()
    {
        return view('admin.users.index');
    }

    function filter(Request $request)
    {
        $dateFrom = ($request->filled('date_from')) ? Carbon::parse($request->date_from)->startOfDay() : null;

        $dateTo = ($request->filled('date_to')) ? Carbon::parse($request->date_to)->endOfDay() : null;

        $search = $request->filled('search') ? $request->search : null;
        $status = $request->filled('status')  ? $request->status : null;
        $accountType = $request->filled('account_type') ? $request->account_type : null;

        $query = User::withTrashed();

        $query = $query
            ->when(!empty($search), fn($query) => $query->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%")->orWhere('username', 'LIKE', "%{$search}%"))
            ->when(!empty($status), fn($query) => $query->where('status', $status))
            ->when(!empty($accountType), fn($query) => $accountType == 'ambassador' ? $query->where('is_ambassador', true) : $query->where('is_ambassador', false))
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($status) && !empty($dateFrom) && !empty($dateTo), fn($query) => $query->where('status', $status)->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($accountType) && !empty($dateFrom) && !empty($dateTo), fn($query) => $accountType == 'ambassador' ? $query->where('is_ambassador', true)->whereBetween('created_at', [$dateFrom, $dateTo]) : $query->where('is_ambassador', false)->whereBetween('created_at', [$dateFrom, $dateTo]));

        $data['users'] = $query->latest()->paginate(50);

        return view('admin.users._table', $data);
    }

    function create()
    {
        return view('admin.users._user-form');
    }

    function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
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
                'password' => $password
            ]);

            $mailData = [
                'username' => $request->username,
                'name' => $request->first_name,
                'password' => $password
            ];

            $user->notify(new NewAccountCreated($mailData));

            DB::commit();

            return response()->json(['success' => true, 'message' => 'User created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function show(User $user)
    {
        $data['user'] = $user;

        return view('admin.users.show', $data);
    }

    function update(UpdateUserRequest $request, User $user)
    {
        try {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            $user->userProfile->update([
                'country_code'  => $request->country,
                'address'       => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'date_of_birth' => $request->date_of_birth,
                'zip_code'      => $request->zip_code,
            ]);

            return $this->sendResponse([], "User profile updated successfully.");
        } catch (\Exception $e) {
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function suspend(User $user)
    {
        $user->update([
            'status' => 'suspended',
        ]);

        return response()->json(['success' => true, 'message' => 'Suspended successfully.']);
    }

    function activate(User $user)
    {
        $user->update([
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Activated successfully.']);
    }

    function makeAmbassador(User $user)
    {
        $user->update([
            'is_ambassador' => true,
        ]);

        Bonus::create([
            'user_id' => $user->id,
            'amount' => 0
        ]);

        return response()->json(['success' => true, 'message' => 'Ambassador Activated successfully.']);
    }

    function usernameForm(User $user)
    {
        $data['user'] = $user;

        return view('admin.users._username_form', $data);
    }

    function changeUsername(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user->update([
            'username' => $request->username
        ]);

        return $this->sendResponse([], "Username updated successfully");
    }

    function destroy(User $user)
    {

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
}