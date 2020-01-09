<?php
declare(strict_types=1);

class TestBanController extends BaseController {
    protected static function getView(): string {
        return '';
    }

    public function run() {
        $ban_model = new BanModel();
        $ban_model->insert($_SERVER['REMOTE_ADDR'], 'Testing the ban system.', time(), time() + 120, null);
    }
}
