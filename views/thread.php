<?php include(join_paths(DIR_VIEWS, 'post-form.php')); ?>
<hr>
<a href="/">Back</a>
<br>
<br>
<?php foreach ($this->thread as $post): ?>
    <div id="<?php echo $post['id'] ?>" class="post">
        <?php if ($post['file_id'] !== null): ?>
            <div class="file">
                <p class="fileinfo">
                    File:
                    <a href="/images/<?php echo $post['file_id'] . '.' . $post['file_extension'] ?>" target="_blank"><?php $post['file_id'] . '.' . $post['file_extension'] ?></a>
                    (<?php echo $post['file_width'] . ' x ' . $post['file_height'] . ' ' . $post['file_size'] . 'KB - ' . $post['file_name'] ?>)
                </p>
                <a href="/images/<?php echo $post['file_id'] . '.' . $post['file_extension'] ?>" target="_blank">
                    <img src="/images/t<?php echo $post['file_id'] . '.' . $post['file_extension'] ?>" width="<?php echo $post['file_width'] ?>" height="<?php echo $post['file_height'] ?>">
                </a>
            </div>
        <?php endif ?>
        <p class="post-head">
            <span class="post-subject"><?php echo $post['subject'] ?></span>
            <span class="post-name"><?php echo $post['name'] ?></span>
            <a href="#" data-id="<?php echo $post['id'] ?>">No. <?php echo $post['id'] ?></a>
            <span class="post-date" data-time="<?php echo $post['created'] ?>"></span>
        </p>
        <div class="post-body">
            <?php echo htmlentities($post['message']); ?>
        </div>
    </div>
    <br class="clear">
<?php endforeach ?>
