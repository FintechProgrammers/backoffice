<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, GeneratesUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $primaryKey = 'id';
    // public $incrementing = false;

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
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function getProfilePictureAttribute(): string
    {
        return !empty($this->profile_image) ? $this->profile_image : url('/') . '/assets/images/avatar.svg';
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return !empty($this->first_name) ? \Illuminate\Support\Str::title($this->first_name . ' ' . $this->last_name) : 'Unavailable';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->referral_code = static::generateReferralCode();
        });

        static::created(function ($user) {
            UserInfo::create(['user_id' => $user->id]);
            Wallet::create(['user_id' => $user->id, 'amount' => 0]);
        });
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

        return ($filledFields / $totalFields) * 100;
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
        return $this->hasMany(Withdrawal::class, 'user_id', 'id')->latest();
    }

    function sales()
    {
        return $this->hasMany(Sale::class, 'provider_id', 'id');
    }

    function getTotalWithdrawalsAmountAttribute()
    {
        return number_format($this->withdrawals()->where('status', 'completed')->sum('amount'), 2, '.', ',');
    }

    function getTotalPendingWithdrawalsAmountAttribute()
    {
        return number_format($this->withdrawals()->where('status', 'pending')->sum('amount'), 2, '.', ',');
    }

    function getTotalSalesAttribute()
    {
        $total = $this->sales()->sum('amount');

        return number_format($total, 2, '.', ',');
    }

    function getTotalSalesBvAttribute()
    {
        $total = $this->sales()->sum('bv_amount');

        return number_format($total, 2, '.', ',');
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
        return $this->hasOne(UserSubscription::class, 'user_id', 'id')
            ->whereHas('service', function ($query) {
                $query->where('ambassadorship', false);
            })->first();
    }

    function ambassadorship()
    {
        return $this->hasOne(UserSubscription::class, 'user_id', 'id')
            ->whereHas('service', function ($query) {
                $query->where('ambassadorship', true);
            })->first();
    }

    function rank()
    {
        return $this->hasOne(Rank::class, 'id', 'rank_id');
    }

    public function highestRank()
    {
        return $this->hasOne(RankHistory::class)
            ->selectRaw('MAX(ranks.creteria) AS highest_criteria')
            ->join('ranks', function ($join) {
                $join->on('rank_histories.rank_id', '=', 'ranks.id');
            });
    }

    function bonuses()
    {
        return $this->hasMany(BonusHistory::class, 'user_id', 'id')->latest();
    }

    function kyc()
    {
        return $this->hasOne(UserKyc::class, 'user_id');
    }

    // Scope to filter ambassadors
    public function scopeAmbassadors($query)
    {
        return $query->where('is_ambassador', true);
    }

    // All commission transactions (both direct and indirect)
    public function commissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'user_id');
    }

    // Direct invitees (children)
    public function directInvitees()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // Indirect invitees (grandchildren)
    public function indirectInvitees()
    {
        return $this->hasManyThrough(User::class, User::class, 'parent_id', 'parent_id', 'id', 'id');
    }

    // Direct sales
    public function directSales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    // Indirect sales
    public function indirectSales()
    {
        return $this->hasManyThrough(Sale::class, User::class, 'parent_id', 'user_id', 'id', 'id');
    }

    // Direct commission transactions
    public function directCommissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'user_id')->where('level', '0');
    }

    // Indirect commission transactions
    public function indirectCommissionTransactions()
    {
        return $this->hasManyThrough(CommissionTransaction::class, User::class, 'parent_id', 'user_id', 'id', 'id')
            ->where('level', '!=', '0');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function getDescendants()
    {
        $descendants = collect();
        $this->getAllDescendants($this, $descendants);
        return $descendants;
    }

    private function getAllDescendants($user, &$descendants)
    {
        foreach ($user->children as $child) {
            $descendants->push($child);
            $this->getAllDescendants($child, $descendants);
        }
    }

    public function getDescendantSales()
    {
        // Fetch all descendants
        $descendants = $this->getDescendants();

        // Get IDs of all descendants
        $descendantIds = $descendants->pluck('id')->toArray();

        // Fetch sales records of all descendants
        $sales = Sale::whereIn('user_id', $descendantIds)->latest();

        return $sales;
    }

    public function getTotalSalesByDescendants()
    {
        $descendants = $this->getDescendants();
        $descendantIds = $descendants->pluck('id')->toArray();

        $totalSales = Sale::whereIn('user_id', $descendantIds)->sum('amount');

        return $totalSales;
    }

    public function getAmbassadorDescendants()
    {
        $descendants = $this->getDescendants();
        $ambassadors = $descendants->filter(function ($user) {
            return $user->is_ambassador;
        });

        return $ambassadors;
    }

    public function getCustomerDescendants()
    {
        $descendants = $this->getDescendants();
        $ambassadors = $descendants->filter(function ($user) {
            return !$user->is_ambassador;
        });

        return $ambassadors;
    }

    public function getTotalBVByDescendants()
    {
        $descendants = $this->getDescendants();
        $descendantIds = $descendants->pluck('id')->toArray();

        $totalSales = Sale::whereIn('user_id', $descendantIds)->sum('bv_amount');

        return $totalSales;
    }

    public function getDirectReferralSales()
    {
        // Get direct referrals
        $directReferrals = $this->children()->pluck('id')->toArray();

        // Calculate total sales made by direct referrals
        $sales = Sale::whereIn('user_id', $directReferrals);

        return $sales;
    }

    public function getIndirectReferralSales()
    {
        // Get direct referrals
        $directReferrals = $this->children()->with('allChildren')->get();
        $indirectReferrals = collect();

        // Collect all indirect referrals
        foreach ($directReferrals as $directReferral) {
            $this->getAllDescendants($directReferral, $indirectReferrals);
        }

        // Get IDs of indirect referrals
        $indirectReferralIds = $indirectReferrals->pluck('id')->toArray();

        // Calculate total sales made by indirect referrals
        $sales = Sale::whereIn('user_id', $indirectReferralIds);

        return $sales;
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
            'password' => 'hashed',
        ];
    }
}