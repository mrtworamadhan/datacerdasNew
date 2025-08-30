<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'dataCerdas',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Data</b>CERDAS',
    'logo_img' => 'images/logo/logo only purple.png',
    'logo_img_class' => 'brand-image elevation-2',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xl',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'images\logo\logo only trp.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images\logo\logo only purple.png',
            'alt' => 'Sebentar ...',
            'effect' => 'animation__shake',
            'width' => 200,
            'height' => 200,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-teal',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => false,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary bg-white elevation-4 text-white',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => false,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'header' => 'SUPER ADMIN', 
            'context' => 'superadmin',
            'can' => 'view superadmin menu',
        ],
        [
            'text' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
            'can' => 'view superadmin menu',
            'context' => 'superadmin',
        ],
        [
            'text' => 'Manajemen Desa',
            'url' => 'desas',
            'icon' => 'fas fa-fw fa-building',
            'can' => 'view superadmin menu',

        ],
        [
            'text' => 'Manajemen Pengguna',
            'url' => 'admin/users',
            'icon' => 'fas fa-fw fa-users-cog',
            'can' => 'view superadmin menu',
            'context' => 'superadmin',
        ],
        [
            'text' => 'Pengaturan Perusahaan', // Tambahkan menu ini
            'url' => 'company-settings',      // Rute baru
            'icon' => 'fas fa-fw fa-cogs',    // Icon yang sesuai
            'can' => 'view superadmin menu',
            'context' => 'superadmin',
        ],
        

        // --- GRUP MENU ADMIN DESA ---
        [
            'header' => 'MENU UTAMA',
            'context' => 'tenant', // "Tanda Pengenal" untuk filter
        ],
        [
            'text' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
            'context' => 'tenant',
        ],
        [
            'text' => 'Profil & Kelembagaan',
            'icon' => 'fas fa-fw fa-university',
            'can' => 'kelola profil',
            'context' => 'tenant',
            'submenu' => [
                [
                    'text' => 'Profil Desa',
                    'url' => 'profil-desa',
                    'icon' => 'fas fa-fw fa-building',
                    'context' => 'tenant',
                ],
                [
                    'text' => 'Manajemen Perangkat Desa',
                    'url' => 'perangkat-desa',
                    'icon' => 'fas fa-fw fa-user-tie',
                    'context' => 'tenant',
                ],
            ],
        ],
        [
            'text' => 'Administrasi Sistem',
            'icon' => 'fas fa-fw fa-cogs',
            'can' => 'kelola profil',
            'context' => 'tenant',
            'submenu' => [
                [
                    'text' => 'Manajemen Pengguna Desa',
                    'url' => 'manajemen-pengguna-desa',
                    'icon' => 'fas fa-fw fa-user-friends',
                    'context' => 'tenant',
                ],
                [
                    'text' => 'Manajemen Hak Akses',
                    'route'  => 'permissions.index',
                    'icon' => 'fas fa-fw fa-key',
                    'context' => 'tenant',
                ],
                [
                    'text' => 'Profil Pengguna',
                    'url' => 'profil-pengguna',
                    'icon' => 'fas fa-fw fa-address-book',
                    'context' => 'tenant',
                ],
            ],
        ],
        [
            'header' => 'CERDAS tata Lembaga',
            'context' => 'tenant',
        ],
        [
            'text' => 'Lembaga Desa',
            'url' => 'lembaga',
            'icon' => 'fas fa-fw fa-handshake',
            'can' => 'kelola kegiatan',
            'context' => 'tenant',
        ],
        [
            'text' => 'Kelompok Desa',
            'url' => 'kelompok',
            'icon' => 'fas fa-fw fa-people-group',
            'can' => 'kelola kegiatan',
            'context' => 'tenant',
        ],
        [
            'text' => 'Kegiatan & LPJ',
            'url' => 'kegiatans',
            'icon' => 'fas fa-fw fa-file-pen',
            'can' => 'kelola kegiatan',
            'context' => 'tenant',
        ],

        [
            'header' => 'Aset CERDAS',
            'context' => 'tenant',
        ],
        [
            'text' => 'Manajemen Aset',
            'icon' => 'fas fa-fw fa-cubes',
            'can' => 'kelola aset',
            'context' => 'tenant',
            'submenu' => [
                [
                    'text' => 'Daftar Aset',
                    'route'  => 'asets.index',
                    'can' => 'kelola aset',
                    'context' => 'tenant',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Tambah Aset Baru',
                    'route'  => 'asets.create',
                    'can' => 'kelola aset',
                    'context' => 'tenant',
                    'icon' => 'fas fa-fw fa-plus-circle',
                ],
            ],
        ],
        [
            'header' => 'CERDAS tata FASUM',
            'context' => 'tenant',
        ],
        [
            'text' => 'Data Fasum',
            'url' => 'fasum',
            'icon' => 'fas fa-fw fa-hospital',
            'can' => 'kelola fasum',
            'context' => 'tenant',
        ],

        [
            'header' => 'CERDAS tata Warga',
            'context' => 'tenant',
        ],
        [
            'text' => 'Data Wilayah',
            'url' => 'wilayah',
            'icon' => 'fas fa-fw fa-map-marker-alt',
            'can' => 'kelola warga',
            'context' => 'tenant',
        ],
        [
            'text' => 'Data Kependudukan',
            'icon' => 'fas fa-fw fa-users',
            'can' => 'kelola warga',
            'context' => 'tenant',
            'submenu' => [
                [
                    'text' => 'Data Kartu Keluarga',
                    'url' => 'kartu-keluarga',
                    'icon' => 'fas fa-fw fa-id-card',
                    'context' => 'tenant',
                ],
                [
                    'text' => 'Daftar Semua Warga',
                    'url' => 'warga',
                    'icon' => 'fas fa-fw fa-user-friends',
                    'context' => 'tenant',
                ],
                [
                    'text' => 'Import Warga',
                    'route'  => 'warga.import.form',
                    'can' => 'kelola aset',
                    'context' => 'tenant',
                    'icon' => 'fas fa-fw fa-list',
                ]
            ],
        ],
        [
            'text' => 'Kategori Bantuan',
            'url' => 'kategori-bantuan',
            'icon' => 'fas fa-fw fa-tags',
            'can' => 'kelola bantuan',
            'context' => 'tenant',
        ],

        [
            'header' => 'CERDAS Pelayanan',
            'context' => 'tenant',
        ],
        [
            'text' => 'Buat Pengajuan Surat',
            'url' => 'pengajuan-surat/create',
            'icon' => 'fas fa-fw fa-pen-square',
            'can' => 'kelola surat',
            'context' => 'tenant',
        ],
        [
            'text' => 'Daftar Pengajuan',
            'url' => 'pengajuan-surat',
            'icon' => 'fas fa-fw fa-inbox',
            'can' => 'kelola surat',
            'context' => 'tenant',
        ],
        [
            'text' => 'Pengaturan Surat',
            'icon' => 'fas fa-fw fa-cogs',
            'can' => 'kelola surat',
            'context' => 'tenant',
            'submenu' => [
                [
                    'text' => 'Setting Kop & TTD',
                    'url' => 'pengaturan-surat',
                    'icon' => 'fas fa-fw fa-signature',
                    'context' => 'tenant',
                ],
                [
                    'text' => 'Template Surat',
                    'url' => 'jenis-surat',
                    'icon' => 'fas fa-fw fa-file-alt',
                    'context' => 'tenant',
                ],
            ],
        ],
        [
            'header' => 'CERDAS tata POSYANDU', 
            'can' => 'kelola kesehatan',
            'context' => 'tenant',
        ], // Nanti kita buat Gate-nya
        [
            'text' => 'Data Posyandu',
            'url' => 'posyandu',
            'icon' => 'fas fa-fw fa-user-nurse',
            'can' => 'kelola kesehatan',
            'context' => 'tenant',
        ],
        [
            'text' => 'Data Kesehatan Anak',
            'url' => 'kesehatan-anak',
            'icon' => 'fas fa-fw fa-child',
            'can' => 'kelola kesehatan',
            'context' => 'tenant',
        ],

        // --- GRUP MENU PENGATURAN AKUN ---
        ['header' => 'PENGATURAN AKUN'],
        [
            'text' => 'Profile',
            'url' => 'profile', // Mengarah ke route profile bawaan Laravel
            'icon' => 'fas fa-fw fa-user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        \App\Filters\ContextFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'FontAwesome' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
                ],
            ],
        ],
        // Ganti seluruh blok 'Select2' di config/adminlte.php dengan ini:
        'Select2' => [
            'active' => true, // <-- PENTING: Ubah menjadi true
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    // Memuat JS Select2 versi modern
                    'location' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    // Memuat CSS Select2 versi modern
                    'location' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    // PENTING: Memuat CSS TEMA untuk Bootstrap 5
                    'location' => 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Summernote' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/summernote/dist/summernote-bs5.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/summernote/dist/summernote-bs5.min.js',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
