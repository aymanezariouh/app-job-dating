<?php

return [
    'app_name' => 'Framework Minimaliste',
    'app_url' => 'http://framework-minimaliste.test',
    'app_env' => 'development',
    
    'db_host' => 'localhost',
    'db_name' =>  'job_dating' ?? '',
    'db_user' => 'root',
    'db_pass' => $_ENV['DB_PASS'] ??'',
    
    'session_lifetime' => $_ENV['SESSION_LIFETIME'] ?? 3600,
    'timezone' => 'UTC'
];