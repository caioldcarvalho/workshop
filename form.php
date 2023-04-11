<?php

namespace Caio\Workshop;


require 'vendor/autoload.php';

require 'src/functions.php';


isset($_FILES) ? processAllFiles($_FILES) : 'roi';

?>

<form action="form.php" method="post" enctype="multipart/form-data">
    <label for="file">Insert file</label>
    <input type="file" name="file" id="file">
    <button type="submit">Send</button>
</form>


<!-- End of File -->
