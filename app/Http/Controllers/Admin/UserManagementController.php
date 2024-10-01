<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivatePlanRequest;
use App\Http\Requests\ImportUserRequest;
use App\Http\Requests\SetupAmbassadorRequest;
use App\Http\Requests\UpdateMembershipRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Bonus;
use App\Models\Country;
use App\Models\NexioUser;
use App\Models\Sale;
use App\Models\Service;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserSubscription;
use App\Notifications\MigratedUserNotification;
use App\Notifications\NewAccountCreated;
use App\Services\Authentication;
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

        // $query = User::withTrashed();
        $query = User::query();

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
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $data['user'] = $user;


        // $sales = Sale::where('parent_id', $user->id)->whereYear('created_at', $currentYear)
        //     ->whereMonth('created_at', $currentMonth)->sum('amount');

        // dd($sales);

        return view('admin.users.show', $data);
    }

    function filterReferrals(Request $request, User $user)
    {
        $data['referrals'] = User::where('parent_id', $user->id)->latest()->paginate(10);

        return view('admin.users._referrals-table', $data);
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

    function ambassadorForm(User $user)
    {
        $data['user'] = $user;
        $data['plan'] = Service::where('is_published', true)->where('ambassadorship', true)->first();

        return view('admin.users._ambassador-form', $data);
    }

    function makeAmbassador(SetupAmbassadorRequest $request, User $user)
    {
        $validated = (object) $request->validated();

        try {

            $service = Service::whereUuid($validated->package)->first();

            if ($request->duration_unit === "infinite") {
                $duration = 0;
            } else {
                $duration =  $validated->duration === 0 ? 0 : getDurationInDays($validated->duration, $request->duration_unit);
            }

            UserSubscription::create(
                [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'reference'  => generateReference(),
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays($duration),
                    'is_active' => true
                ]
            );

            $user->update([
                'is_ambassador' => true,
            ]);

            Bonus::create([
                'user_id' => $user->id,
                'amount' => 0
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ambassador Activated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
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

    function emailForm(User $user)
    {
        $data['user'] = $user;

        return view('admin.users._email-form', $data);
    }

    function changeEmail(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user->update([
            'email' => $request->email
        ]);

        return $this->sendResponse([], "Email updated successfully");
    }

    function importView()
    {
        $data['packages'] = Service::where('is_published', true)->where('ambassadorship', false)->get();
        $data['countries'] = Country::get();
        $data['users'] = User::get();

        return view('admin.users.import-user', $data);
    }

    function importStore(ImportUserRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $authentication = new Authentication();

            $response = $authentication->getUser($request->username);
            $data = $response['data'];

            if (empty($data) || isset($data['user exist'])) {
                return back()->with('error', "User does not exist on the previous database kindly check the username");
            }

            $newUserId = $data['uid'];

            $startDate = ($request->filled('start_date')) ? Carbon::parse($validated['start_date'])->startOfDay() : null;

            $endDate = ($request->filled('end_date')) ? Carbon::parse($validated['end_date'])->endOfDay() : null;

            $package = null;

            if ($request->filled('package')) {
                // check package
                $package = Service::whereUuid($validated['package'])->first();
            }

            // get referral
            $referral = User::whereUuid($validated['sponsor'])->first();

            if (!$referral) {
                sendToLog("parent uuid not found {$validated['referral_id']} on payment");
                return $this->sendError(serviceDownMessage(), [], 404);
            }

            $password = \Illuminate\Support\Str::random(5);

            // create user account
            $user = User::create([
                'id' => $newUserId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($password),
                'parent_id'  => !empty($referral) ? $referral->id : null
            ]);

            UserInfo::where('user_id', $user->id)->update([
                'country_code' => $request->country,
            ]);

            if (!empty($package)) {
                UserSubscription::create([
                    'user_id' => $user->id,
                    'service_id' => $package->id,
                    'reference'  => generateReference(),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => true
                ]);
            }

            $mailData = [
                'username' => $request->username,
                'name' => $request->first_name,
                'password' => $password
            ];

            $user->notify(new MigratedUserNotification($mailData));

            DB::commit();

            return back()->with('success', 'Account created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            sendToLog($e);

            return back()->with('error', serviceDownMessage());
        }
    }

    function destroy(User $user)
    {

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    function activatePlanForm(User $user)
    {
        $data['user'] = $user;

        // Get the IDs of services that the user is already subscribed to
        $subscribedServiceIds = $user->subscriptions()->pluck('service_id');

        // Retrieve services that are published, not ambassadorship, and not in the user's subscriptions
        $data['packages'] = Service::where('is_published', true)
            ->where('ambassadorship', false)
            ->whereNotIn('id', $subscribedServiceIds)
            ->get();

        return view('admin.users._activate-plan', $data);
    }

    function activatePlan(ActivatePlanRequest $request, User $user)
    {
        $validated = (object) $request->validated();

        try {

            $service = Service::whereUuid($validated->package)->first();

            if ($request->duration_unit === "infinite") {
                $duration = 0;
            } else {
                $duration =  $validated->duration === 0 ? 0 : getDurationInDays($validated->duration, $request->duration_unit);
            }

            UserSubscription::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'reference'  => generateReference(),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($duration),
                'is_active' => true
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Subscription Activated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function commissionForm(User $user)
    {
        $data['user'] = $user;
        $data['sales'] = Sale::get();

        return view('admin.users.commission-form', $data);
    }

    function commissionStore(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
    }

    function setupNexio(User $user)
    {
        $data['user'] = $user;

        return view('admin.users.nexio-setup-form', $data);
    }

    function nexioSetUp(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nexio_id' => 'required',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $nexioService = new \App\Services\NexioService();

        $response = $nexioService->getReceipientById($request->nexio_id);

        if (!$response['success']) {
            return $this->sendError("Recipient not found on nexio", []);
        }

        $response = $response['data'];

        NexioUser::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'recipient_id' => $response['recipientId'],
            'provider_id' => $response['providerId'],
            'provider_recipient_ref' => $response['providerRecipientRef'],
            'payout_account_id' => $response['payoutAccountId'],
            'recipient_ref' => $response['recipientRef'],
            'response' => json_encode($response)
        ]);

        return $this->sendResponse([], "NEXIO ID updated successfully");
    }

    function membershipDetails(UserSubscription $subscription)
    {
        $data['subscription'] = $subscription;

        $data['user'] = $subscription->user;

        return view('admin.users._update-plan-form', $data);
    }

    function updateMembership(UpdateMembershipRequest $request, UserSubscription $subscription)
    {
        $validated = (object) $request->validated();

        DB::beginTransaction();

        try {
            if ($request->duration_unit === "infinite") {
                $duration = 0;
            } else {
                $duration =  $validated->duration === 0 ? 0 : getDurationInDays($validated->duration, $request->duration_unit);
            }

            $currentEndDate = Carbon::parse($subscription->end_date);

            $newDate = $currentEndDate->addDays($duration);

            $subscription->update([
                'end_date' => $newDate,
                'is_active' => true
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Subscription Updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }
}
