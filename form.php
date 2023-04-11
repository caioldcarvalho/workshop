<?php

namespace Caio\Workshop;


require 'vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Caio\Workshop;

require 'src/functions.php';


isset($_FILES) ? process_all_files($_FILES) : 'roi';

?>

<form action="form.php" method="post" enctype="multipart/form-data">
    <label for="file">Insert file</label>
    <input type="file" name="file" id="file">
    <button type="submit">Send</button>
</form>


<!-- End of File -->
