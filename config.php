<?php

define('DBDRIVER', 'mysql');
define('DBHOST', 'localhost');
define('DBPORT', '3307');
define('DBUSER', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'slchan');

define('ERROR_REPORTING', E_ALL);

define('DEFAULT_TIMEZONE', 'UTC');

define('THREADS_PER_PAGE', 10);
define('POSTS_PER_PREVIEW', 10);

define('POST_COOLDOWN', 30);

define('DIR_ROOT', __DIR__);
define('DIR_MODELS', join_paths(DIR_ROOT, 'models'));
define('DIR_VIEWS', join_paths(DIR_ROOT, 'views'));
define('DIR_CONTROLLERS', join_paths(DIR_ROOT, 'controllers'));
