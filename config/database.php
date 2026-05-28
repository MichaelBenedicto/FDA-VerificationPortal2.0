<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        // config/database.php (inside 'connections' => [ ... ])
'fdaeservices' => [
    'driver' => 'mysql',
    'host' => env('DB_FDA1_HOST', '192.168.3.183'),
    'port' => env('DB_FDA1_PORT', '3306'),
    'database' => env('DB_FDA1_DATABASE', 'forge'),
    'username' => env('DB_FDA1_USERNAME', 'forge'),
    'password' => env('DB_FDA1_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'fdafoodproducts' => [
    'driver' => 'mysql',
    'host' => env('DB_foodproducts_HOST', '192.168.3.183'),
    'port' => env('DB_foodproducts_PORT', '3306'),
    'database' => env('DB_foodproducts_DATABASE', 'forge'),
    'username' => env('DB_foodproducts_USERNAME', 'forge'),
    'password' => env('DB_foodproducts_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'cdrr' => [
    'driver' => 'mysql',
    'host' => env('DB_cdrrcpr_HOST', '192.168.3.183'),
    'port' => env('DB_cdrrcpr_PORT', '3306'),
    'database' => env('DB_cdrrcpr_DATABASE', 'forge'),
    'username' => env('DB_cdrrcpr_USERNAME', 'forge'),
    'password' => env('DB_cdrrcpr_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],


'lto_healthrelateddevice' => [
    'driver' => 'mysql',
    'host' => env('DB_ltohealthrelateddevice_HOST', '192.168.3.183'),
    'port' => env('DB_ltohealthrelateddevice_PORT', '3306'),
    'database' => env('DB_ltohealthrelateddevice_DATABASE', 'forge'),
    'username' => env('DB_ltohealthrelateddevice_USERNAME', 'forge'),
    'password' => env('DB_ltohealthrelateddevice_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'lto_drugs' => [
    'driver' => 'mysql',
    'host' => env('DB_ltodrugs_HOST', '192.168.3.183'),
    'port' => env('DB_ltodrugs_PORT', '3306'),
    'database' => env('DB_ltodrugs_DATABASE', 'forge'),
    'username' => env('DB_ltodrugs_USERNAME', 'forge'),
    'password' => env('DB_ltodrugs_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'lto_food' => [
    'driver' => 'mysql',
    'host' => env('DB_ltofood_HOST', '192.168.3.183'),
    'port' => env('DB_ltofood_PORT', '3306'),
    'database' => env('DB_ltofood_DATABASE', 'forge'),
    'username' => env('DB_ltofood_USERNAME', 'forge'),
    'password' => env('DB_ltofood_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'lto_huhs' => [
    'driver' => 'mysql',
    'host' => env('DB_ltohuhs_HOST', '127.0.0.1'),
    'port' => env('DB_ltohuhs_PORT', '3306'),
    'database' => env('DB_ltohuhs_DATABASE', 'forge'),
    'username' => env('DB_ltohuhs_USERNAME', 'forge'),
    'password' => env('DB_ltohuhs_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'lto_medicaldevice' => [
    'driver' => 'mysql',
    'host' => env('DB_ltodevice_HOST', '192.168.3.183'),
    'port' => env('DB_ltodevice_PORT', '3306'),
    'database' => env('DB_ltodevice_DATABASE', 'forge'),
    'username' => env('DB_ltodevice_USERNAME', 'forge'),
    'password' => env('DB_ltodevice_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'lto_pco' => [
    'driver' => 'mysql',
    'host' => env('DB_ltopco_HOST', '192.168.3.183'),
    'port' => env('DB_ltopco_PORT', '3306'),
    'database' => env('DB_ltopco_DATABASE', 'forge'),
    'username' => env('DB_ltopco_USERNAME', 'forge'),
    'password' => env('DB_ltopco_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
'lto_cosmetics' => [
    'driver' => 'mysql',
    'host' => env('DB_ltocosmetics_HOST', '192.168.3.183'),
    'port' => env('DB_ltocosmetics_PORT', '3306'),
    'database' => env('DB_ltocosmetics_DATABASE', 'forge'),
    'username' => env('DB_ltocosmetics_USERNAME', 'forge'),
    'password' => env('DB_ltocosmetics_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
'lto_hup' => [
    'driver' => 'mysql',
    'host' => env('DB_ltohup_HOST', '192.168.3.183'),
    'port' => env('DB_ltohup_PORT', '3306'),
    'database' => env('DB_ltohup_DATABASE', 'forge'),
    'username' => env('DB_ltohup_USERNAME', 'forge'),
    'password' => env('DB_ltohup_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
'lto_tcca' => [
    'driver' => 'mysql',
    'host' => env('DB_ltotcca_HOST', '192.168.3.183'),
    'port' => env('DB_ltotcca_PORT', '3306'),
    'database' => env('DB_ltotcca_DATABASE', 'forge'),
    'username' => env('DB_ltotcca_USERNAME', 'forge'),
    'password' => env('DB_ltotcca_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => false,
    'engine' => null,
    'options'   => [
        PDO::ATTR_TIMEOUT => 60, // Increase timeout to 60s
    ],
],

'cpr_cdrrhr' => [
    'driver' => 'mysql',
    'host' => env('DB_cprcdrrhr_HOST', '192.168.3.183'),
    'port' => env('DB_cprcdrrhr_PORT', '3306'),
    'database' => env('DB_cprcdrrhr_DATABASE', 'forge'),
    'username' => env('DB_cprcdrrhr_USERNAME', 'forge'),
    'password' => env('DB_cprcdrrhr_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'csl' => [
    'driver' => 'mysql',
    'host' => env('DB_csl_HOST', '192.168.3.183'),
    'port' => env('DB_csl_PORT', '3306'),
    'database' => env('DB_csl_DATABASE', 'forge'),
    'username' => env('DB_csl_USERNAME', 'forge'),
    'password' => env('DB_csl_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'cdrr_new' => [
    'driver' => 'mysql',
    'host' => env('DB_cdrr_HOST', '127.0.0.1'),
    'port' => env('DB_cdrr_PORT', '3306'),
    'database' => env('DB_cdrr_DATABASE', 'forge'),
    'username' => env('DB_cdrr_USERNAME', 'forge'),
    'password' => env('DB_cdrr_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'PMT' => [
    'driver' => 'mysql',
    'host' => env('DB_pmt_HOST', '192.168.3.183'),
    'port' => env('DB_pmt_PORT', '3306'),
    'database' => env('DB_pmt_DATABASE', 'forge'),
    'username' => env('DB_pmt_USERNAME', 'forge'),
    'password' => env('DB_pmt_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'ccrr' => [
    'driver' => 'mysql',
    'host' => env('DB_ccrr_HOST', '192.168.3.183'),
    'port' => env('DB_ccrr_PORT', '3306'),
    'database' => env('DB_ccrr_DATABASE', 'forge'),
    'username' => env('DB_ccrr_USERNAME', 'forge'),
    'password' => env('DB_ccrr_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'ccrr_old' => [
    'driver' => 'mysql',
    'host' => env('DB_ccrr_old_HOST', '127.0.0.1'),
    'port' => env('DB_ccrr_old_PORT', '3306'),
    'database' => env('DB_ccrr_old_DATABASE', 'forge'),
    'username' => env('DB_ccrr_old_USERNAME', 'forge'),
    'password' => env('DB_ccrr_old_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'eportalverif2' => [
    'driver' => 'mysql',
    'host' => env('DB_eportalverif2_HOST', '127.0.0.1'),
    'port' => env('DB_eportalverif2_PORT', '3306'),
    'database' => env('DB_eportalverif2_DATABASE', 'forge'),
    'username' => env('DB_eportalverif2_USERNAME', 'forge'),
    'password' => env('DB_eportalverif2_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'fdaEMP' => [
    'driver' => 'mysql',
    'host' => env('DB_fdaEMP_HOST', '127.0.0.1'),
    'port' => env('DB_fdaEMP_PORT', '3306'),
    'database' => env('DB_fdaEMP_DATABASE', 'forge'),
    'username' => env('DB_fdaEMP_USERNAME', 'forge'),
    'password' => env('DB_fdaEMP_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'dbAdmin' => [
    'driver' => 'mysql',
    'host' => env('DB_dbAdmin_HOST', '127.0.0.1'),
    'port' => env('DB_dbAdmin_PORT', '3306'),
    'database' => env('DB_dbAdmin_DATABASE', 'forge'),
    'username' => env('DB_dbAdmin_USERNAME', 'forge'),
    'password' => env('DB_dbAdmin_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],


'fdawebsite' => [
    'driver' => 'mysql',
    'host' => env('DB_fdawebsite_HOST', '192.168.3.183'),
    'port' => env('DB_fdawebsite_PORT', '3306'),
    'database' => env('DB_fdawebsite_DATABASE', 'forge'),
    'username' => env('DB_fdawebsite_USERNAME', 'forge'),
    'password' => env('DB_fdawebsite_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],

'spp' => [
    'driver' => 'mysql',
    'host' => env('DB_SPP_HOST', '127.0.0.1'),
    'port' => env('DB_SPP_PORT', '3306'),
    'database' => env('DB_SPP_DATABASE', 'forge'),
    'username' => env('DB_SPP_USERNAME', 'forge'),
    'password' => env('DB_SPP_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],


        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '192.168.3.183'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '192.168.3.183'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '192.168.3.183'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '192.168.3.183'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '192.168.3.183'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
