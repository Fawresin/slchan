<?php include(join_paths(DIR_VIEWS, 'post-form.php')); ?>
<?php
    $pagination = 'Pages: <ul class="pagination">';
    $total_pages = ($this->threadCount > 0) ? ceil($this->threadCount / (float)THREADS_PER_PAGE) : 1;
    for($i=0; $i<$total_pages; ++$i) {
        $page = $i + 1;
        $item = '<li>';
        if ($i === $this->currentPage) {
            $item .= $page;
        }
        else {
            $item .= '<a href="/' . $page . '">' . $page . '</a>';
        }
        $item .= '</li>';
        $pagination .= $item;
    }
    $pagination .= '</ul>';

    echo $pagination;
?>
<hr>
<?php foreach ($this->threads as $thread): ?>
    <?php $parent = $thread['parent'] ?>
    <div class="post" data-parent-id="<?php echo $parent['id'] ?>">
        <?php if ($parent['file_id'] !== null): ?>
            <div class="file">
                <p class="file-info">
                    File:
                    <a href="/images/<?php echo $parent['file_id'] . '.' . $parent['file_extension'] ?>" target="_blank"><?php $parent['file_id'] . '.' . $parent['file_extension'] ?></a>
                    (<?php echo $parent['file_width'] . ' x ' . $parent['file_height'] . ' ' . $parent['file_size'] . 'KB - ' . $parent['file_name'] ?>)
                </p>
                <a href="/images/<?php echo $parent['file_id'] . '.' . $parent['file_extension'] ?>" target="_blank">
                    <img src="/images/t<?php echo $parent['file_id'] . '.' . $parent['file_extension'] ?>" width="<?php echo $parent['file_width'] ?>" height="<?php echo $parent['file_height'] ?>">
                </a>
            </div>
        <?php endif ?>
        <p class="post-head">
            <input type="checkbox" name="delete_id" value="<?php echo $parent['id'] ?>">
            <span class="post-subject"><?php echo $parent['subject'] ?></span>
            <span class="post-name"><?php echo !empty($parent['name']) ? $parent['name'] : 'Anonymous' ?></span>
            <a href="/thread/<?php echo $parent['id'] ?>">No. <?php echo $parent['id'] ?></a>
            <span class="post-date" data-time="<?php echo $parent['created'] ?>"></span>
        </p>
        <div class="post-body">
            <?php echo process_post_message($parent['message']) ?>
        </div>
    </div>
    <?php if ($thread['post_count'] > POSTS_PER_PREVIEW): ?>
        <div class="amount-hidden">
            <?php echo ($thread['post_count'] - POSTS_PER_PREVIEW) . ' posts not shown. Click <a href="/thread/' . $parent['id'] . '">here</a> to view the entire thread.' ?>
        </div>
    <?php endif ?>
    <?php foreach ($thread['children'] as $child): ?>
        <div class="post" data-parent-id="<?php echo $parent['id']?>">
            <?php if ($child['file_id'] !== null): ?>
                <div class="file">
                    <p class="file-info">
                        File:
                        <a herf="/images/<?php echo $child['file_id'] . '.' . $child['file_extension'] ?>" target="_blank"><?php $child['file_id'] . '.' . $child['file_extension'] ?></a>
                        (<?php echo $child['file_width'] . ' x ' . $child['file_height'] . ' ' . $child['file_size'] . 'KB - ' . $child['file_name'] ?>)
                    </p>
                    <a href="/images/<?php echo $child['file_id'] . '.' . $child['file_extension'] ?>" target="_blank">
                        <img src="/images/t<?php echo $child['file_id'] . '.' . $child['file_extension'] ?>" width="<?php echo $child['file_width'] ?>" height="<?php echo $parent['file_height'] ?>">
                    </a>
                </div>
            <?php endif ?>
            <p class="post-head">
                <input type="checkbox" name="delete_id" value="<?php echo $child['id'] ?>">
                <span class="post-subject"><?php echo $child['subject'] ?></span>
                <span class="post-name"><?php echo !empty($child['name']) ? $child['name'] : 'Anonymous' ?></span>
                <a href="/thread/<?php echo $parent['id'] . '#' . $child['id'] ?>">No. <?php echo $child['id'] ?></a>
                <span class="post-date" data-time="<?php echo $child['created'] ?>"></span>
            </p>
            <div class="post-body">
                <?php echo process_post_message($child['message']) ?>
            </div>
        </div>
    <?php endforeach ?>
    <br class="clear">
    <hr>
<?php endforeach ?>
<?php echo $pagination ?>

<?php include(join_paths(DIR_VIEWS, 'delete-form.php')) ?>
