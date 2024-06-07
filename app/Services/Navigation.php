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
                'name'  => 'Settings',
                'route' => 'admin.settings.index',
                'icon'  => 'las la-cogs',
                'hasPermission' => true
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
                'name'  => 'packages',
                'route' => 'package.index',
                'icon'  => 'bx bx-briefcase',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Tickets',
                'route' => 'tickets.index',
                'icon'  => 'las la-headset',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Withdrawals',
                'route' => 'tickets.index',
                'icon'  => 'bx bx-wallet-alt',
                'hasPermission' => auth()->user()->is_ambassador ?? false
            ],
            (object) [
                'name'  => 'Sales',
                'route' => 'tickets.index',
                'icon'  => 'bx bx-bar-chart-alt-2',
                'hasPermission' => auth()->user()->is_ambassador ?? false
            ],
            (object) [
                'name'  => 'Subscriptions',
                'route' => 'tickets.index',
                'icon'  => 'bx bx-cart-alt',
                'hasPermission' => true
            ],
            (object) [
                'name'  => 'Rank History',
                'route' => 'tickets.index',
                'icon'  => 'bx bx-medal',
                'hasPermission' => auth()->user()->is_ambassador ?? false
            ],
        ];
    }
}
