<?php include(join_paths(DIR_VIEWS, 'post-form.php')); ?>
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
            <span class="post-subject"><?php echo $parent['subject'] ?></span>
            <span class="post-name"><?php echo $parent['name'] ?></span>
            <a href="/thread/<?php echo $parent['id'] ?>">No. <?php echo $parent['id'] ?></a>
            <span class="post-date" data-time="<?php echo $parent['created'] ?>"></span>
        </p>
        <div class="post-body">
            <?php echo process_post_message($parent['message']) ?>
        </div>
    </div>
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
                <span class="post-subject"><?php echo $child['subject'] ?></span>
                <span class="post-name"><?php echo $child['name'] ?></span>
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
