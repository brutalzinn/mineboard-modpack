<?php
include 'app/functions.php';
include 'app/config.php';
include 'app/router.php';

$token = $_GET['token'] ?? null;
if ($token !== KEY) {
    http_response_code(403);
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Minecraft Modpack Upload</title>
    <link href="public/css/styles.css" rel="stylesheet">
    <script src="public/js/scripts.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>

<body>
    <h1>Minecraft Modpack Manager</h1>
    <button type="button" style="width: 300px;" onclick="showForm()">Create</button>

    <div id="modpacks-list">
        <h2>Modpacks List</h2>
    </div>

    <div id="form" class="hidden">
        <h2 id="form-title"></h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" id="editAction" value="upload">
            <input type="hidden" name="id" id="id">
            <div class="progress-bar">
                <div class="progress-bar-fill"></div>
            </div>
            <div class="status"></div>

            <div>
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" placeholder="Boberto modpack"><br><br>
            </div>
            <div>
                <label for="game_version">Minecraft Version:</label>
                <input type="text" name="game_version" id="game_version" required>
            </div>
            <div>
                <label for="mod_loader">Mod Loader:</label>
                <select name="mod_loader" id="mod_loader" required>
                    <option value="forge">Forge</option>
                    <option value="fabric">Fabric</option>
                </select>
            </div>
            <div>
                <label for="loader_version">Loader Version:</label>
                <input type="text" name="loader_version" id="loader_version" required>
            </div>
            <div>
                <label for="zip_file">Upload Zip File:</label>
                <input type="file" name="zip_file" id="zip_file">
            </div>
            <button type="submit" onclick="setAction('upload')">Upload</button>
            <button type="submit" id="regenerateBtn" class="hidden" onclick="setAction('regenerate')">Regenerate</button>
            <button type="button" onclick="hideForm()">Cancel</button>
        </form>
    </div>
</body>

</html>