<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\GeneratesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

// use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, GeneratesUuid, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $primaryKey = 'id';
    public $incrementing = false;

    // Define the required fields for the user model and the related userInfo model
    protected $userRequiredFields = [
        'first_name',
        'last_name',
        'email',
        'username',
        'phone_number'
    ];

    protected $userInfoRequiredFields = [
        'country_code',
        'state',
        'city',
        'address',
        'zip_code',
        'date_of_birth',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = self::generateUniqueId();
            }
            $user->referral_code = static::generateReferralCode();
        });

        static::created(function ($user) {
            UserInfo::create(['user_id' => $user->id]);
            Wallet::create(['user_id' => $user->id]);
        });
    }

    private static function generateUniqueId()
    {
        $uniqueId = rand(100000, 999999); // generate a random numeric ID between 100000 and 999999

        // check if the generated ID already exists
        while (self::whereId($uniqueId)->exists()) {
            $uniqueId = rand(100000, 999999); // generate a new ID if it already exists
        }

        return $uniqueId;
    }

    /**
     * Define the route model binding key for a given model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    function userProfile()
    {
        return $this->hasOne(UserInfo::class, 'user_id');
    }

    function sponsor()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id')->withTrashed();
    }

    public function getProfilePictureAttribute(): string
    {
        return !empty($this->profile_image) ? $this->profile_image : url('/') . '/assets/images/default-dp.png';
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return !empty($this->first_name) ? \Illuminate\Support\Str::title($this->first_name . ' ' . $this->last_name) : 'Unavailable';
    }

    public static function generateReferralCode()
    {
        do {
            $code = Str::random(10);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'user_id', 'id')->latest();
    }

    /**
     * Calculate the profile completion percentage.
     *
     * @return float The percentage of profile completion.
     */
    public function getProfileCompletionPercentageAttribute()
    {
        $filledFields = 0;

        // Check fields in User model
        foreach ($this->userRequiredFields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        // Check fields in UserInfo model
        $userInfo = $this->userProfile;
        if ($userInfo) {
            foreach ($this->userInfoRequiredFields as $field) {
                if (!empty($userInfo->$field)) {
                    $filledFields++;
                }
            }
        }

        // Total required fields
        $totalFields = count($this->userRequiredFields) + count($this->userInfoRequiredFields);
        if ($totalFields === 0) {
            return 100; // If no required fields are defined, consider profile as fully complete
        }

        $percentage = ($filledFields / $totalFields) * 100;

        return round($percentage, 2); // Round to 2 decimal places
    }

    function bonuWallet()
    {
        return $this->hasOne(Bonus::class, 'user_id', 'id');
    }

    function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    function withdrawals()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id')->where('type', 'withdrawal')->latest();
    }

    function getTotalWithdrawalsAmountAttribute()
    {
        return $this->withdrawals()->where('status', 'completed')->sum('amount');
    }

    function getTotalPendingWithdrawalsAmountAttribute()
    {
        return $this->withdrawals()->where('status', 'pending')->sum('amount');
    }


    function activities()
    {
        return $this->hasMany(UserActivities::class, 'user_id', 'id')->latest();
    }

    function subscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id', 'id');
    }

    function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'user_id', 'id')
            ->whereHas('service', function ($query) {
                $query->where('ambassadorship', false);
            });
    }

    public function getActiveSubscriptionsAttribute()
    {
        // Get the current date
        $currentDate = Carbon::now();

        // Fetch subscription IDs where end_date is greater than or equal to the current date
        $subscriptionIds = UserSubscription::where('end_date', '>=', $currentDate)
            ->where('user_id', $this->id)
            ->pluck('service_id')
            ->toArray();

        return $subscriptionIds;
    }

    /**
     * Check if the user has any active subscription.
     *
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()->active()->exists();
    }

    function ambassadorship()
    {
        return $this->hasOne(UserSubscription::class, 'user_id', 'id')
            ->whereHas('service', function ($query) {
                $query->where('ambassadorship', true);
            });
    }

    function rank()
    {
        return $this->hasOne(Rank::class, 'id', 'rank_id');
    }


    public function highestRank()
    {
        return $this->hasOne(RankHistory::class)
            ->join('ranks', 'rank_histories.rank_id', '=', 'ranks.id')
            ->where('rank_histories.user_id', $this->id) // Filter by the current user's ID
            ->select('ranks.*', 'rank_histories.*')
            ->orderBy('ranks.creteria', 'desc')
            ->limit(1);
    }


    public function nextRank()
    {
        if ($this->rank) {
            return Rank::where('creteria', '>', $this->rank->creteria)
                ->orderBy('creteria')
                ->first();
        }

        // If there is no current rank, return the rank with the lowest creteria
        return Rank::orderBy('creteria')->first();
    }

    public function getSalesForCurrentMonth()
    {
        return $this->sales()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->select('amount')
            ->sum('amount');
    }

    public function getRankProgress()
    {

        $currentCriteria = 0;

        $currentRank = $this->rank;

        if ($currentRank) {
            $currentCriteria = $currentRank->creteria;
        }

        $nextRank = $this->nextRank();

        if (!$nextRank) {
            return 0;
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // $currentSales = $this->getSalesForCurrentMonth();
        $currentSales = $this->commissionTransactions()->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
        // $currentSales = $this->total_bv_this_month;

        $progress = ($currentSales - $currentCriteria) / ($nextRank->creteria - $currentCriteria) * 100;

        return $progress > 100 ? 100 : ($progress < 0 ? 0 : round($progress, 2));
    }

    function bonuses()
    {
        return $this->hasMany(BonusHistory::class, 'user_id', 'id')->latest();
    }

    function kyc()
    {
        return $this->hasOne(UserKyc::class, 'user_id');
    }

    /**
     * Get the last enrolled users referred by this user.
     *
     * @param int $limit Number of users to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLastEnrolledUsers($limit = 6)
    {
        // Get the last enrolled users by ordering them by the creation date
        return User::where('parent_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    // Scope to filter ambassadors
    public function scopeAmbassadors($query)
    {
        return $query->where('is_ambassador', true);
    }

    // All commission transactions (both direct and indirect)
    public function commissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'user_id')->latest();
    }

    // Direct commission transactions
    public function directCommissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'user_id')->where('level', '0');
    }

    // Indirect commission transactions
    public function indirectCommissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'user_id')->where('level', '!=', '0');
    }

    function getTotalEarningsAttribute()
    {
        return $this->commissionTransactions->where('is_converted', true)->select('amount')->sum('amount');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id')->withTrashed();
    }

    public function getDescendants()
    {
        // $commissionLevels = CommissionLevels::all()->keyBy('level');
        // $maxLevel = $commissionLevels->keys()->max();

        // $currentLevel = 1;

        // // Initialize collection to store downline users
        // $downline = collect();

        // //   Base case: if the current level exceeds the max level, stop recursion
        // if ($currentLevel > $maxLevel) {
        //     return $downline;
        // }

        // // Get direct children
        // $children = $this->children;

        // // Add direct children to the downline collection
        // $downline = $downline->concat($children);

        // // Recursively fetch downline for each child
        // foreach ($children as $child) {
        //     $downline = $downline->concat($child->getDescendants($maxLevel, $currentLevel + 1));
        // }

        // return $downline;

        return User::where('parent_id', $this->id)->latest();
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // public function getDescendants()
    // {
    //     $descendants = collect();
    //     $this->getAllDescendants($this, $descendants);
    //     return $descendants;
    // }

    // private function getAllDescendants($user, &$descendants)
    // {
    //     foreach ($user->children as $child) {
    //         $descendants->push($child);
    //         $this->getAllDescendants($child, $descendants);
    //     }
    // }

    function directAmbassadors() {}

    public function getAmbassadorDescendants()
    {
        // $descendants = $this->getDescendants(2);

        // $ambassadors = $descendants->filter(function ($user) {
        //     return $user->is_ambassador;
        // });

        // return $ambassadors;

        return User::where('parent_id', $this->id)->where('is_ambassador', true)->latest();
    }

    public function getCustomerDescendants()
    {
        // $descendants = $this->getDescendants();
        // $customers = $descendants->filter(function ($user) {
        //     return !$user->is_ambassador;
        // });

        // return $customers;

        return User::where('parent_id', $this->id)->where('is_ambassador', false)->latest();
    }

    // Get descendants with any active subscription
    public function getActiveSubscriptionDescendants()
    {
        // $descendants = $this->getDescendants();
        // $activeSubscriptions = $descendants->filter(function ($user) {
        //     return $user->subscriptions()->active()->exists();
        // });

        // return $activeSubscriptions;

        return User::where('parent_id', $this->id)
            ->whereHas('subscriptions', function ($query) {
                $query->where('status', 'active'); // Adjust 'status' and 'active' as per your subscription model
            })
            ->latest()
            ->get();
    }

    // Get top sellers in the user's team
    public function getTopSellers($limit = 10)
    {
        // $descendants = $this->getDescendants();

        // // Calculate total sales for each descendant and exclude those with zero sales
        // $topSellers = $descendants->map(function ($user) {
        //     $totalSales = $user->getMonthlyTotalSales();

        //     if ($totalSales > 0) {
        //         $user->total_sales = $totalSales;
        //         return $user;
        //     }
        //     return null;
        // })->filter() // Remove null values (users with zero sales)
        //     ->sortByDesc('total_sales')
        //     ->take($limit);

        // return $topSellers;

        // Retrieve the descendants (direct children in this case)
        $descendants = User::where('parent_id', $this->id)->get();

        // Calculate total sales for each descendant and exclude those with zero sales
        $topSellers = $descendants->map(function ($user) {
            $totalSales = $user->getMonthlyTotalSales();

            if ($totalSales > 0) {
                $user->total_sales = $totalSales;
                return $user;
            }
            return null;
        })->filter() // Remove null values (users with zero sales)
            ->sortByDesc('total_sales')
            ->take($limit); // Limit the result if needed

        return $topSellers;
    }

    function purchase()
    {
        return $this->hasMany(Sale::class, 'user_id', 'id')->latest();
    }

    function totalPurchaseAttribute()
    {
        return Sale::where('user_id', $this->id)->count();
    }

    // get list of all sales
    public function sales()
    {
        // Fetch all descendants
        $descendants = $this->getDescendants();

        // Get IDs of all descendants
        $descendantIds = $descendants->pluck('id')->toArray();

        // Fetch both direct and team sales without repetition
        $sales = Sale::where(function ($query) use ($descendantIds) {
            $query->where('parent_id', $this->id);
            // ->orWhereIn('parent_id', $descendantIds);
        })->latest();

        // Sale::where('parent_id', $this->id)->orWhereIn('parent_id', $descendantIds)->latest();

        return $sales;
    }

    // get list of all sales
    public function monthlySales()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Fetch all descendants
        $descendants = $this->getDescendants();

        // Get IDs of all descendants
        $descendantIds = $descendants->pluck('id')->toArray();

        // Fetch both direct and team sales without repetition
        $sales = Sale::where(function ($query) use ($descendantIds) {
            $query->where('parent_id', $this->id);
            // ->orWhereIn('parent_id', $descendantIds);
        })->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->latest();

        return $sales;
    }

    public function getTotalBvThisMonthAttribute()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return $this->sales()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->select('bv_amount')
            ->sum('bv_amount');
    }

    // Calculate total sales for a user
    public function getTotalSales()
    {
        return $this->sales()->sum('amount');
    }

    function getTotalSalesAttribute()
    {
        $total = $this->sales()->sum('amount');

        return $total;
    }

    function getMonthlyTotalSales()
    {
        $total = 0;

        if ($this->monthlySales()->count() > 0) {
            $total = $this->monthlySales()
                ->sum('amount');
        }

        return $total;
    }

    function getMonthlyTotalSalesAttribute()
    {
        $total = 0;

        if ($this->monthlySales()->count() > 0) {
            $total = $this->monthlySales()
                ->sum('amount');
        }

        return $total;
    }

    function getTotalBvAttribute()
    {
        $total = $this->sales()->sum('bv_amount');

        return $total;
    }

    function directSales()
    {
        $directSales = Sale::where('parent_id', $this->id);

        return $directSales;
    }

    public function getTeamSale()
    {
        // Get all descendants of the current user
        $descendants = $this->getDescendants();

        // Get the IDs of the descendants
        $descendantIds = $descendants->pluck('id')->toArray();

        // Get the sales where the parent_id is in the descendant IDs
        $teamSales = Sale::whereIn('parent_id', $descendantIds);

        return $teamSales;
    }

    function getTeamVolumeAttribute()
    {
        $total = $this->getTeamSale()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('bv_amount');

        return $total;
    }

    public function getTeamCommissions()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Indirect commission transactions
        $indirectCommissions = $this->indirectCommissionTransactions()
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('amount');

        return $indirectCommissions;
    }

    function nexio()
    {
        return $this->hasOne(NexioUser::class, 'user_id');
    }

    function pmonthlySales()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return $this->hasMany(Sale::class, 'parent_id', 'id')->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'rank_updated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
