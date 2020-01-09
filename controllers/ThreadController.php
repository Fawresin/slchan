<?php
declare(strict_types=1);

class ThreadController extends BaseController {
    public $title;

    public $thread;

    protected static function getView(): string {
        return 'thread.php';
    }

    public function run() {
        $id = array_get_item('id', $_GET);
        if ($id === null)
            $this->error('Thread ID not found.');

        $post_model = new PostModel();
        $thread = $post_model->getThread($id);
        if (count($thread) === 0) {
            $this->error('Thread ID not found.');
        }

        $subject = $thread[0]['subject'];
        if (empty(trim($subject))) {
            $this->title = 'Thread #' . $id;
        }
        else {
            $this->title = $subject;
        }

        $this->thread_id = $id;
        $this->thread = $thread;
        $this->showView();
    }
}
