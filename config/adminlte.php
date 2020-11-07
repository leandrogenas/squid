<?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Title
        |--------------------------------------------------------------------------
        |
        | Here you can change the default title of your admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
        |
        */

        'title' => 'SyncWeb',
        'title_prefix' => 'SW',
        'title_postfix' => '',

        /*
        |--------------------------------------------------------------------------
        | Favicon
        |--------------------------------------------------------------------------
        |
        | Here you can activate the favicon.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#62-favicon
        |
        */

        'use_ico_only' => false,
        'use_full_favicon' => false,

        /*
        |--------------------------------------------------------------------------
        | Logo
        |--------------------------------------------------------------------------
        |
        | Here you can change the logo of your admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#63-logo
        |
        */

        'logo' => '<b>Sync</b>WEB',
        'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
        'logo_img_class' => 'brand-image img-circle elevation-3',
        'logo_img_xl' => null,
        'logo_img_xl_class' => 'brand-image-xs',
        'logo_img_alt' => 'AdminLTE',

        /*
        |--------------------------------------------------------------------------
        | User Menu
        |--------------------------------------------------------------------------
        |
        | Here you can activate and change the user menu.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#64-user-menu
        |
        */

        'usermenu_enabled' => true,
        'usermenu_header' => true,
        'usermenu_header_class' => 'bg-primary',
        'usermenu_image' => true,
        'usermenu_desc' => true,

        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        |
        | Here we change the layout of your admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#65-layout
        |
        */

        'layout_topnav' => null,
        'layout_boxed' => null,
        'layout_fixed_sidebar' => null,
        'layout_fixed_navbar' => null,
        'layout_fixed_footer' => null,

        /*
        |--------------------------------------------------------------------------
        | Extra Classes
        |--------------------------------------------------------------------------
        |
        | Here you can change the look and behavior of the admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#66-classes
        |
        */

        'classes_body' => '',
        'classes_brand' => '',
        'classes_brand_text' => '',
        'classes_content_header' => '',
        'classes_content' => '',
        'classes_sidebar' => 'sidebar-dark-primary elevation-4',
        'classes_sidebar_nav' => 'nav-flat nav-compact',
        'classes_topnav' => 'navbar-white navbar-light',
        'classes_topnav_nav' => 'navbar-expand-md',
        'classes_topnav_container' => 'container',

        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        |
        | Here we can modify the sidebar of the admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#67-sidebar
        |
        */

        'sidebar_mini' => true,
        'sidebar_collapse' => false,
        'sidebar_collapse_auto_size' => false,
        'sidebar_collapse_remember' => false,
        'sidebar_collapse_remember_no_transition' => true,
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
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#68-control-sidebar-right-sidebar
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
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#69-urls
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

        /*
        |--------------------------------------------------------------------------
        | Laravel Mix
        |--------------------------------------------------------------------------
        |
        | Here we can enable the Laravel Mix option for the admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#610-laravel-mix
        |
        */

        'enabled_laravel_mix' => false,

        /*
        |--------------------------------------------------------------------------
        | Menu Items
        |--------------------------------------------------------------------------
        |
        | Here we can modify the sidebar/top navigation of the admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#611-menu
        |
        */

        'menu' => [
            [
                'header' => 'Filmes',
                'group-permission' => "filmes"
            ],
            [
                "text" => "Filmes",
                "icon" => "fas fa-film",
                'group-permission' => "filmes",
                "submenu" => [
                    [
                        'text' => 'Publicar Filme',
                        'route' => 'filmes.create',
                        'can'=>\App\Enums\PermissoesTipo::VER_PUBLICAR_FILME
                    ],
                    [
                        'text' => 'Trocar Capa Filmes',
                        'route' => 'filmes.trocaimagem',
                        'can'=>\App\Enums\PermissoesTipo::VER_TROCAR_CAPA_FILMES
                    ],
                    [
                        'text' => 'Procura IMG Offline',
                        'route' => 'filme.procura.imagem',
                        'can'=>\App\Enums\PermissoesTipo::VER_PROCURA_IMG_OFFLINE
                    ],
                ]
            ],
            [
                'header' => "Séries Torrent",
                'group-permission' => "seriesTorrent"
            ],
            [
                "text" => "Séries Torrent",
                "icon" => "fas fa-video",
                'group-permission' => "seriesTorrent",
                "submenu" => [
                    [
                        "text" => "Publicar Série",
                        "route" => "series.create",
                        'can'=>\App\Enums\PermissoesTipo::VER_PUBLICAR_SERIE
                    ],
                    [
                        "text" => "Atualizar Série",
                        "route" => "serie.tela.update",
                        'can'=>\App\Enums\PermissoesTipo::VER_ATUALIZAR_SERIE
                    ]
                ]
            ],
            [
                'header' => "Séries Online",
                'group-permission' => "seriesOnline",
            ],
            [
                "text" => "Séries Online",
                "icon" => "fas fa-file-video",
                'group-permission' => "seriesOnline",
                "submenu" => [
                    [
                        "text" => "Publicar Série",
                        "route" => "season.index",
                        'can'=>\App\Enums\PermissoesTipo::VER_PUBLICA_SERIE_ONLINE
                    ]
                ]
            ],
            [
                'header' => "Jogos",
                'group-permission' => "jogos",
            ],
            [
                "text" => "Jogos",
                "icon" => "fas fa-gamepad",
                'group-permission' => "jogos",
                "submenu" => [
                    [
                        "text" => "Publicar Jogos",
                        "route" => "jogos.index",
                        'can'=>\App\Enums\PermissoesTipo::VER_PUBLICAR_JOGOS
                    ]
                ]
            ],
            [
                'header' => "Animes",
                'group-permission' => "animes",
            ],
            [
                "text" => "Animes",
                "icon" => "fas fa-tv",
                'group-permission' => "animes",
                "submenu" => [
                    [
                        "text" => "Fazer Postagem",
                        "route" => "anime.tela.posta",
                        'can'=>\App\Enums\PermissoesTipo::VER_FAZER_POSTAGEM
                    ],
                    [
                        "text" => "Fazer Postagem (Orion)",
                        "route" => "animes.postagem.orion",
                        'can'=>\App\Enums\PermissoesTipo::VER_FAZER_POSTAGEM_ORION
                    ],
                    [
                        "text" => "Atualizar Postagem",
                        "route" => "anime.pagina.atualiza",
                        'can'=>\App\Enums\PermissoesTipo::VER_ATUALIZAR_POSTAGEM
                    ],
                    [
                        "text" => "Verificar Postagem Orion",
                        "route" => "animes.animesrion.telaverifica",
                        'can'=>\App\Enums\PermissoesTipo::VER_VERIFICAR_POSTAGEM_ORION
                    ]
                ]
            ],
            [
                'header' => "INDEX PROJECT",
                'group-permission' => "feed"
            ],
            [
                'text' => "Ultimos Links",
                'icon' => 'fas fa-robot',
                "route" => "feed.ultimoslinks",
                'can'=>\App\Enums\PermissoesTipo::VER_FEEDS
            ],
//            ['header' => "IA"],
//            [
//                'text' => "IA (AutoPost)",
//                'icon' => 'fas fa-robot',
//                "submenu" => [
//                    [
//                        "text" => "Configurações",
//                        "route"=>"ia.config"
//                    ],
//                    [
//                        "text" => "Postagens Feitas"
//                    ]
//                ]
//            ],
            [
                'header' => 'Administração',
                'can'=>\App\Enums\PermissoesTipo::ADMINISTRAR_SISTEMA
            ],
            [
                'text' => "Usuarios",
                'target' => '_blank',
                'icon' => 'fas fa-users',
                'can'=>\App\Enums\PermissoesTipo::ADMINISTRAR_SISTEMA,
                "submenu"=>[
                    [
                        "text"=>"Cadastrar",
                        "route" => 'usuarios.create',
                    ],
                    [
                        "text"=>"Lista",
                        "route" => 'usuarios.lista',
                    ]
                ]
            ],
            [
                'text' => "Logs",
                "url" => 'log-viewer',
                'target' => '_blank',
                'icon' => 'fas fa-bug',
                'can'=>\App\Enums\PermissoesTipo::ADMINISTRAR_SISTEMA
            ],
            [
                'text' => "Limpar Cache",
                "route" => 'limpar.cache',
                'target' => '_blank',
                'icon' => 'fas fa-recycle',
                'can'=>\App\Enums\PermissoesTipo::ADMINISTRAR_SISTEMA
            ],
            [
                "text" => "Lista Episódios Json",
                "route" => "home.lista.episodios",
                'icon' => "fas fa-folder",
                'can'=>\App\Enums\PermissoesTipo::ADMINISTRAR_SISTEMA
            ],
            [
                "text" => "Pegar dados Curso",
                "route" => "cursos",
                'icon' => "fas fa-tv",
                'can'=>\App\Enums\PermissoesTipo::ADMINISTRAR_SISTEMA
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Menu Filters
        |--------------------------------------------------------------------------
        |
        | Here we can modify the menu filters of the admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#612-menu-filters
        |
        */

        'filters' => [
            JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
            JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
            \App\Utils\filter\FiltroMenu::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | Plugins Initialization
        |--------------------------------------------------------------------------
        |
        | Here we can modify the plugins used inside the admin panel.
        |
        | For more detailed instructions you can look here:
        | https://github.com/jeroennoten/Laravel-AdminLTE/#613-plugins
        |
        */

        'plugins' => [
            [
                'name' => 'Datatables',
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
            [
                'name' => 'Select2',
                'active' => false,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                    ],
                    [
                        'type' => 'css',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                    ],
                ],
            ],
            [
                'name' => 'Chartjs',
                'active' => true,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                    ],
                ],
            ],
            [
                'name' => 'Sweetalert2',
                'active' => false,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                    ],
                ],
            ],
            [
                'name' => 'Pace',
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
    ];
