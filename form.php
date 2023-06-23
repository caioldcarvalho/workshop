<!-- form -->

<form action="index.php" method="post" enctype="multipart/form-data">
    <h2>Create files and minis</h2>
    <label for="file">Insert file</label>
    <input type="file" name="files[]" id="file" multiple accept=".jpg, .jpeg, .png, .webp">
    <button type="submit">Send</button>
</form>

<form action="index.php" method="post" enctype="multipart/form-data">
    <h2>Create blurred version</h2>
    <label for="blur">Insert file</label>
    <input type="file" name="blur[]" id="blur" multiple accept=".jpg, .jpeg, .png, .webp">
    <button type="submit">Send</button>
</form>

<!-- End of File -->
