<?php

namespace App\Services;


class Navigation
{
    public static function adminNavigation()
    {

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
                'hasPermission' => true,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Administrators',
                        'route' => 'admin.admins.index',
                        'hasPermission' => true
                    ],
                    (object) [
                        'name'  => 'All Users',
                        'route' => 'admin.users.index',
                        'hasPermission' => true
                    ],
                ]
            ],
            (object) [
                'name'  => 'Products',
                'route' => 'admin.product.index',
                'icon'  => 'bx bx-box',
                'hasPermission' => true
            ],
            (object) [
                'name'      => 'Business Plan',
                'icon'      => 'bx bx-briefcase',
                'routes'    => ['admin.package.index'],
                'hasPermission' => true,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Packages',
                        'route' => 'admin.package.index',
                        'hasPermission' => true
                    ],
                    (object) [
                        'name'  => 'Ranks',
                        'route' => 'admin.rank.index',
                        'hasPermission' => true
                    ],
                    (object) [
                        'name'  => 'Commission Plan',
                        'route' => 'admin.commission.index',
                        'hasPermission' => true
                    ]
                ]
            ],
            (object) [
                'name'  => 'Subscriptions',
                'route' => 'admin.subscriptions.index',
                'icon'  => 'bx bx-cart-alt',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Sales',
                'route' => 'admin.sales.index',
                'icon'  => 'bx bx-bar-chart-alt-2',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Withdrawals',
                'route' => 'admin.withdrawals.index',
                'icon'  => 'las la-wallet',
                'hasPermission' => true
            ],
            (object) [
                'name'      => 'Supports',
                'icon'      => 'las la-headset',
                'routes'    => ['admin.support.subjects.index', 'admin.support.tickets.index'],
                'hasPermission' => true,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'Subjects',
                        'route' => 'admin.support.subjects.index',
                        'hasPermission' => true
                    ],
                    (object) [
                        'name'  => 'Tickets',
                        'route' => 'admin.support.tickets.index',
                        'hasPermission' => true
                    ],
                ]
            ],
            (object) [
                'name'  => 'Role Management',
                'route' => 'admin.roles.index',
                'icon'  => 'las la-bezier-curve',
                'hasPermission' => true
            ],
            (object) [
                'name'      => 'Settings',
                'icon'      => 'las la-cogs',
                'routes'    => ['admin.settings.index','admin.banner.index'],
                'hasPermission' => true,
                'subMenu'   => (object) [
                    (object) [
                        'name'  => 'System Settings',
                        'route' => 'admin.settings.index',
                        'hasPermission' => true
                    ],
                    (object) [
                        'name'  => 'Banners',
                        'route' => 'admin.banner.index',
                        'hasPermission' => true
                    ],
                ]
            ],
        ];
    }

    public static function clientNavigation()
    {
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
                        'hasPermission' => auth()->user()->is_ambassador ?? false
                    ],
                    (object) [
                        'name'  => 'Rank History',
                        'route' => 'report.ranks',
                        'hasPermission' => auth()->user()->is_ambassador ?? false
                    ],
                    (object) [
                        'name'  => 'Bonus History',
                        'route' => 'report.bonuses',
                        'hasPermission' => auth()->user()->is_ambassador ?? false
                    ],
                    (object) [
                        'name'  => 'Package History',
                        'route' => 'report.packages',
                        'hasPermission' => auth()->user()->is_ambassador ?? false
                    ]
                ]
            ],
            (object) [
                'name'  => 'Withdrawals',
                'route' => 'withdrawal.index',
                'icon'  => 'bx bx-wallet-alt',
                'hasPermission' => auth()->user()->is_ambassador ?? false
            ],
            (object) [
                'name'  => 'Subscriptions',
                'route' => 'subscription.index',
                'icon'  => 'bx bx-cart-alt',
                'hasPermission' => true
            ],
            // (object) [
            //     'name'  => 'Delta Streaming',
            //     'route' => 'subscription.index',
            //     'icon'  => 'bx bxs-video',
            //     'hasPermission' => true
            // ],
            (object) [
                'name'  => 'Delta Academy',
                'route' => 'academy.index',
                'icon'  => 'bx bxs-graduation',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Tickets',
                'route' => 'tickets.index',
                'icon'  => 'las la-headset',
                'hasPermission' => true
            ]
        ];
    }
}
