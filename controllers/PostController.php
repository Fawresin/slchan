<?php

class PostController extends BaseController {
    protected static function getView(): string {
        return '';
    }

    public function run() {
        $name = array_get_item('name', $_POST);
        $subject = array_get_item('subject', $_POST);
        $message = array_get_item('message', $_POST);
        $password = array_get_item('password', $_POST);
        if ($name !== null)
            $name = substr($name, 0, 50);
        if ($subject !== null)
            $subject = substr($subject, 0, 100);
        if ($message === null || empty(trim($message)))
            $this->error('Message cannot be empty.');
        $message = substr($message, 0, 3000);
        if ($password !== null)
            $password = password_hash($password, PASSWORD_DEFAULT);
        else
            $password = password_hash('supersecretadminpassword', PASSWORD_DEFAULT);

        try {
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $ban_model = new BanModel();
            $ban = $ban_model->getByIpAddress($ip_address);
            if ($ban !== false) {
                // TODO: Dislpay banned posts
                $time_left = 'Never';
                if ($ban['expires'] !== null) {
                    $time_left = ((int)$ban['expires'] - time()) . ' seconds';
                }
                $this->error('You are banned: ' . $ban['reason'] . '<br>' . 'Expires: ' . $time_left);
            }

            $post_model = new PostModel();
            $last_post = $post_model->getLastPostByIpAddress($ip_address);
            if ($last_post) {
                $time_left = (POST_COOLDOWN - (time() - (int)$last_post['created']));
                if ($time_left > 0)
                    $this->error('You can not post for another ' . $time_left . ' seconds.');
            }

            $thread_id = array_get_item('thread_id', $_POST);

            $post = $post_model->insert($name, $subject, time(), time(), $message, null, $ip_address, $password, $thread_id, false);

            if ($thread_id !== null) {
                $this->redirect("/thread/$thread_id");
            }
        }
        catch (Exception $e) {
            $this->error($e->getMessage());
        }

        $this->redirect('/');
    }
}
