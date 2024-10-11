<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Navigation
{
    public static function adminNavigation()
    {

        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Admin::find(Auth::guard('admin')->user()->id);

        return [
            (object) [
                'name'  => 'Dashboard',
                'route' => 'admin.dashboard.index',
                'icon'  => 'bx bx-home',
                'hasPermission' => true
            ],
            (object) [
                'name'      => 'Users',
                'icon'      => 'fe fe-users',
                'routes'    => ['admin.users.index'],
                'hasPermission' => $user->can('manage user') || $user->can('manage admin') ? true : false,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Administrators',
                        'route' => 'admin.admins.index',
                        'hasPermission' => $user->can('manage admin')  ? true : false,
                    ],
                    (object) [
                        'name'  => 'All Users',
                        'route' => 'admin.users.index',
                        'hasPermission' => $user->can('manage user') ? true : false
                    ],
                ]
            ],
            (object) [
                'name'  => 'Categories',
                'route' => 'admin.category.index',
                'icon'  => 'las la-stamp',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Asset',
                'route' => 'admin.assets.index',
                'icon'  => 'las la-coins',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Streamers',
                'route' => 'admin.streamers.index',
                'icon'  => 'las la-user-tie',
                'hasPermission' => true
            ],
            // (object) [
            //     'name'  => 'Products',
            //     'route' => 'admin.product.index',
            //     'icon'  => 'bx bx-box',
            //     'hasPermission' => $user->can('manage product') ? true : false,
            // ],
            (object) [
                'name'      => 'Business Plan',
                'icon'      => 'bx bx-briefcase',
                'routes'    => ['admin.package.index'],
                'hasPermission' =>
                $user->can('manage package') || $user->can('manage rank') || $user->can('manage commission plan') ? true : false,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Packages',
                        'route' => 'admin.package.index',
                        'hasPermission' => $user->can('manage package') ? true : false,
                    ],
                    (object) [
                        'name'  => 'Ranks',
                        'route' => 'admin.rank.index',
                        'hasPermission' => $user->can('manage rank') ? true : false
                    ],
                    (object) [
                        'name'  => 'Commission Plan',
                        'route' => 'admin.commission.index',
                        'hasPermission' => $user->can('manage commission plan') ? true : false
                    ]
                ]
            ],
            (object) [
                'name'  => 'Subscriptions',
                'route' => 'admin.subscriptions.index',
                'icon'  => 'bx bx-cart-alt',
                'hasPermission' => $user->can('manage subscription') ? true : false
            ],
            (object) [
                'name'  => 'Sales',
                'route' => 'admin.sales.index',
                'icon'  => 'bx bx-bar-chart-alt-2',
                'hasPermission' => $user->can('manage sales') ? true : false
            ],
            (object) [
                'name'  => 'Commissions',
                'route' => 'admin.commissions.index',
                'icon'  => 'las la-wallet',
                'hasPermission' => $user->can('manage commission') ? true : false
            ],
            (object) [
                'name'  => 'Transactions',
                'route' => 'admin.transactions.index',
                'icon'  => 'las la-wallet',
                'hasPermission' => $user->can('manage transactions') ? true : false
            ],
            // (object) [
            //     'name'  => 'Cycles',
            //     'route' => 'admin.cycle.index',
            //     'icon'  => 'las la-sync',
            //     'hasPermission' => true
            // ],
            (object) [
                'name'      => 'Supports',
                'icon'      => 'las la-headset',
                'routes'    => ['admin.support.subjects.index', 'admin.support.tickets.index'],
                'hasPermission' => $user->can('manage ticket') || $user->can('manage support subject')  ? true : false,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Subjects',
                        'route' => 'admin.support.subjects.index',
                        'hasPermission' => $user->can('manage support subject') ? true : false,
                    ],
                    (object) [
                        'name'  => 'Tickets',
                        'route' => 'admin.support.tickets.index',
                        'hasPermission' => $user->can('manage ticket') ? true : false,
                    ],
                ]
            ],
            (object) [
                'name'  => 'Kycs',
                'route' => 'admin.kyc.index',
                'icon'  => 'bx bx-file',
                'hasPermission' => $user->can('manage kyc') ? true : false,
            ],
            (object) [
                'name'  => 'Role Management',
                'route' => 'admin.roles.index',
                'icon'  => 'las la-bezier-curve',
                'hasPermission' => $user->can('manage roles') ? true : false
            ],
            (object) [
                'name'      => 'Settings',
                'icon'      => 'las la-cogs',
                'routes'    => ['admin.settings.index', 'admin.banner.index'],
                'hasPermission' => $user->can('manage settings') || $user->can('manage banner')  ? true : false,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'System Settings',
                        'route' => 'admin.settings.index',
                        'hasPermission' =>
                        $user->can('manage settings') ? true : false
                    ],
                    (object) [
                        'name'  => 'Banners',
                        'route' => 'admin.banner.index',
                        'hasPermission' => $user->can('manage banner')  ? true : false
                    ],
                    (object) [
                        'name'  => 'Providers',
                        'route' => 'admin.provider.index',
                        'hasPermission' => $user->can('manage provider') ? true : false
                    ],
                ]
            ],
        ];
    }

    public static function clientNavigation()
    {

        $user = User::find(Auth::user()->id);

        return [
            (object) [
                'name'  => 'Dashboard',
                'route' => 'dashboard',
                'icon'  => 'bx bx-home',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Packages',
                'route' => 'package.index',
                'icon'  => 'bx bx-briefcase',
                'hasPermission' => true
            ],
            (object) [
                'name'      => 'Account Report',
                'icon'      => 'bx bx-file-blank',
                'routes'    => ['admin.package.index'],
                'hasPermission' => true,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Sales History',
                        'route' => 'sales.index',
                        'hasPermission' => $user->is_ambassador ?? false
                    ],
                    // (object) [
                    //     'name'  => 'Rank History',
                    //     'route' => 'report.ranks',
                    //     'hasPermission' => $user->is_ambassador ?? false
                    // ],
                    (object) [
                        'name'  => 'Commission',
                        'route' => 'report.commissions',
                        'hasPermission' => $user->is_ambassador ?? false
                    ],
                    // (object) [
                    //     'name'  => 'Bonus History',
                    //     'route' => 'report.bonuses',
                    //     'hasPermission' => $user->is_ambassador ?? false
                    // ],
                    (object) [
                        'name'  => 'Package History',
                        'route' => 'report.packages',
                        'hasPermission' => true
                    ]
                ]
            ],
            (object) [
                'name'  => 'Wallet',
                'route' => 'wallet.index',
                'icon'  => 'bx bx-wallet-alt',
                'hasPermission' => $user->is_ambassador ?? false
            ],
            (object) [
                'name'  => 'Subscriptions',
                'route' => 'subscription.index',
                'icon'  => 'bx bx-cart-alt',
                'hasPermission' => true
            ],
            (object) [
                'name'      => 'Team',
                'icon'      => 'bx bx-sitemap',
                'routes'    => ['team.index', 'team.genealogy'],
                'hasPermission' => $user->is_ambassador ?? false,
                'subMenu'   => (object) [
                    (object) [
                        'name' => 'Add New Registration',
                        'route' => 'team.create.customer',
                        'hasPermission' => $user->is_ambassador ?? false
                    ],
                    (object) [
                        'name'  => 'My Team',
                        'route' => 'team.index',
                        'hasPermission' => $user->is_ambassador ?? false
                    ],
                    (object) [
                        'name'  => 'My Customers',
                        'route' => 'customers.index',
                        'hasPermission' => $user->is_ambassador ?? false
                    ],
                    (object) [
                        'name'  => 'Genealogy',
                        'route' => 'team.genealogy',
                        'hasPermission' => $user->is_ambassador ?? false
                    ],
                ]
            ],
            (object) [
                'name'  => 'Tickets',
                'route' => 'tickets.index',
                'icon'  => 'las la-headset',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Kyc',
                'route' => 'kyc.index',
                'icon'  => 'bx bx-file',
                'hasPermission' => $user->is_ambassador ?? false
            ],
        ];
    }
}