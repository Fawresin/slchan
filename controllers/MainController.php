<?php
declare(strict_types=1);

class MainController extends BaseController {
    public $title = 'Main';

    public $threads;
    public $threadCount;
    public $currentPage;

    protected static function getView(): string {
        return 'index.php';
    }

    public function run() {
        $page = array_get_item('p', $_GET);
        if ($page !== null) {
            $page_int = (int)$page;
            if ($page_int > 0) {
                --$page_int;
            }

            $page = abs($page_int);
        }
        else {
            $page = 0;
        }

        $offset = THREADS_PER_PAGE * $page;
        $post_model = new PostModel();
        $parents = $post_model->getParents(THREADS_PER_PAGE, $offset);
        $parent_ids = array();
        $threads = array();
        $threads_in_order = array();
        foreach ($parents as $post) {
            $parent_ids[] = $post['id'];
            $threads[$post['id']] = array(
                'parent' => $post,
                'children' => array(),
                'post_count' => 0
            );
            $threads_in_order[] = &$threads[$post['id']];
        }

        $children = $post_model->getChildren($parent_ids, POSTS_PER_PREVIEW);
        $i = count($children);
        while (--$i > -1) {
            $post = $children[$i];
            $threads[$post['parent_id']]['children'][] = $post;
        }

        $child_counts = $post_model->getChildCount($parent_ids);
        foreach ($child_counts as $c) {
            $threads[$c['parent_id']]['post_count'] = $c['total'];
        }

        $this->threads = $threads_in_order;
        $this->threadCount = (int)$post_model->getThreadCount();
        $this->currentPage = $page;
        $this->showView();
    }
}
