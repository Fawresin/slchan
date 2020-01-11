<?php

class DeletePostController extends BaseController {
    public static function getView(): string {
        return '';
    }

    public function run() {
        $post_id = array_get_item('id', $_POST);
        $post_password = array_get_item('password', $_POST);
        try {
            $post_model = new PostModel();
            $post = $post_model->getById($post_id);
            if ($post === false) {
                throw new Exception('Post not found.');
            }

            if (!password_verify($post_password, $post['password'])) {
                throw new Exception('Wrong password for this post.');
            }

            $post_model->hideById($post_id);
            $file_model = new FileModel();
            if ($post['file_id'] !== null)
                $file_model->hideById($post['file_id']);

            $this->redirect('/');
        }
        catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
