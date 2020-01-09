<form method="POST" action="/post">
    <?php if (isset($this->thread_id)): ?>
        <input type="hidden" name="thread_id" value="<?php echo $this->thread_id ?>">
    <?php endif ?>
    <table id="post-form-table">
        <tbody>
            <tr>
                <td><label for="txt-name">Name:</label></td>
                <td><input id="txt-name" type="text" name="name" size="40" maxlength="50" autocomplete="off"></td>
            </tr>
            <tr>
                <td><label for="txt-subject">Subject:</label></td>
                <td><input id="txt-subject" type="text" name="subject" size="40" maxlength="100" autocomplete="off"></td>
            </tr>
            <tr>
                <td><label for="txt-message">Message:</label></td>
                <td><textarea id="txt-message" name="message" rows="5" cols="50"></textarea></td>
            </tr>
            <tr>
                <td><label for="upload">File:</label></td>
                <td><input id="upload" type="file" name="file"></td>
            </tr>
            <tr>
                <td><label for="txt-password">Password:</label></td>
                <td><input id="txt-password" type="password" name="password" size="25"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Submit</button>
                    <button type="reset">Clear</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>
