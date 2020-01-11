<?php
declare(strict_types=1);

function join_paths(string ...$segments): string {
    $path_arr = array();
    for ($i = 0, $count = count($segments); $i < $count; ++$i) {
        $seg = trim($segments[$i]);
        if ($seg !== '') {
            $path_arr[] = (substr($seg, strlen($seg) - 1, 1) === DIRECTORY_SEPARATOR ? substr($seg, 0, -1) : $seg);
        }
    }

    return join(DIRECTORY_SEPARATOR, $path_arr);
}

require_once(join_paths('..', 'config.php'));

spl_autoload_register(function ($classname) {
    $length = strlen($classname);
    $filename = '';
    if (strpos($classname, 'Model') === ($length - 5)) {
        $filename = join_paths(DIR_MODELS, $classname . '.php');
    }
    else if (strpos($classname, 'Controller') === ($length - 10)) {
        $filename = join_paths(DIR_CONTROLLERS, $classname . '.php');
    }
    else {
        $filename = join_paths(DIR_ROOT, $classname . '.php');
    }

    if ($filename !== '' && file_exists($filename)) {
        include($filename);
    }
    else {
        throw new Exception('Unable to load ' . $classname);
    }
});

RuntimeStats::startTimer();

date_default_timezone_set(DEFAULT_TIMEZONE);
error_reporting(ERROR_REPORTING);

function array_get_item($key, array $arr) {
    if (array_key_exists($key, $arr))
        return $arr[$key];

    return null;
}

function process_post_message($message): string {
    if (!is_string($message))
        return '';

    $processed = htmlentities($message);
    $processed = str_replace("\n", "<br>", $processed);
    return $processed;
}

$action = array_get_item('action', $_GET);
$controller = null;
switch ($action) {
    case 'post':
        $controller = new PostController();
        break;
    case 'thread':
        $controller = new ThreadController();
        break;
    case 'testban':
        $controller = new TestBanController();
        break;
    case 'delete':
        $controller = new DeletePostController();
        break;
    default:
        $controller = new MainController();
        break;
}

$controller->run();
