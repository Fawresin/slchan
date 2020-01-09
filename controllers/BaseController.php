<?php
declare(strict_types=1);

abstract class BaseController {
    abstract public function run();

    abstract protected static function getView(): string;

    protected function showView() {
        include(join_paths(DIR_VIEWS, 'header.php'));
        include(join_paths(DIR_VIEWS, $this::getView()));
        include(join_paths(DIR_VIEWS, 'footer.php'));
    }

    protected function redirect(string $location) {
        header("Location: $location");
        exit();
    }

    protected function error(string $message) {
        die($message);
    }
}
