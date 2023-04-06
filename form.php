<?php

// $file = isset($_FILES)

var_dump($_FILES);

?>

<form action="form.php" method="post" enctype="multipart/form-data">
    <label for="file">Insert file</label>
    <input type="file" name="file" id="file">
    <button type="submit">Send</button>
</form>