<?php
define("host", getURL());
define("uploadDir", "uploads/");
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['zip_file'])) {
    $zipFile = $_FILES['zip_file']['tmp_name'];
    $folderName = $_POST['folder_name'];
    $gameVersion = $_POST['game_version'];
    $loader = $_POST['mod_loader'];
    $loaderVersion = $_POST['loader_version'];

    $extractDir = uploadDir . $folderName . '/';
    if (!is_dir($extractDir)) {
        mkdir($extractDir, 0777, true);
    } else {
        deleteFilesDir($extractDir);
    }

    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($extractDir);
        $zip->close();

        $filesList = dirToArray($extractDir);
        $modpackInfo = [
            "name" => $folderName,
            "game_version" => $gameVersion,
            "loader" => $loader,
            "loader_version" => $loaderVersion,
            "files" => host . "/" . uploadDir . $folderName . "/manifest.json"
        ];

        // Save the JSON object to a file
        file_put_contents($extractDir . 'files.json', json_encode($filesList, JSON_PRETTY_PRINT));
        file_put_contents($extractDir . 'manifest.json', json_encode($modpackInfo, JSON_PRETTY_PRINT));

        echo 'Zip file extracted and JSON file created successfully!';
    } else {
        echo 'Failed to extract zip file!';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    header('Content-Type: application/json');

    $uploadsDir = 'uploads'; // Directory containing the extracted folders
    $folders = scanFolder($uploadsDir);
    $allContents = [];

    foreach ($folders as $folder) {
        $manifestFilePath = "$uploadsDir/$folder/manifest.json";
        $fileListPath = host . "/" . "$uploadsDir/$folder/files.json";

        if (file_exists($manifestFilePath)) {
            $fileContents = file_get_contents($manifestFilePath);
            $data = json_decode($fileContents, true);
            $data['files'] = $fileListPath;
            array_push($allContents, $data);
        }
    }

    echo json_encode($allContents);
    exit;
}


function scanAllDir($dir)
{
    $result = [];
    foreach (scandir($dir) as $filename) {
        if ($filename[0] === '.') continue;
        $filePath = $dir . '/' . $filename;
        if (is_dir($filePath)) {
            foreach (scanAllDir($filePath) as $childFilename) {
                $result[] = $filename . '/' . $childFilename;
            }
        } else {
            $result[] = $filename;
        }
    }
    return $result;
}

function deleteFilesDir($dir)
{
    $files = glob("$dir/*"); //get all file names
    foreach ($files as $file) {
        if (is_file($file))
            unlink($file); //delete file
    }
}
function scanFolder($dir)
{
    $result = [];
    foreach (scandir($dir) as $filename) {
        if ($filename[0] === '.') continue;
        $filePath = $dir . '/' . $filename;
        if ($filename == "php") continue;
        if (is_dir($filePath)) $result[] = $filename;
    }
    return $result;
}

function dirToArray($dir)
{
    $res = [];
    $cdir = scanAllDir($dir);
    foreach ($cdir as $key => $value) {
        $filePath = $dir . "/" . $value;
        $hash = hash_file('sha1', $filePath);
        $size = filesize($filePath);
        $path = str_replace("$dir/", "", $filePath);

        $url_req = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $url = host . "$url_req$dir$path";
        $res[] = array("url" => $url, "size" => $size, "hash" => $hash, "path" => $path);
    }
    return $res;
}

function getURL()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol . $domainName;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Minecraft Modpack Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h1 {
            color: #333;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .progress-bar {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .progress-bar-fill {
            height: 20px;
            background-color: #007BFF;
            border-radius: 4px;
            width: 0;
            transition: width 0.2s;
        }

        .status {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const progressBarFill = document.querySelector(".progress-bar-fill");
            const statusText = document.querySelector(".status");

            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                xhr.open("POST", "", true);

                xhr.upload.addEventListener("progress", function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBarFill.style.width = percentComplete + "%";
                        statusText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                    }
                });

                xhr.addEventListener("load", function() {
                    if (xhr.status == 200) {
                        statusText.textContent = "Upload and extraction complete!";
                    } else {
                        statusText.textContent = "An error occurred!";
                    }
                });

                xhr.send(formData);
            });
        });
    </script>
</head>

<body>
    <h1>Minecraft Modpack Upload</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="progress-bar">
            <div class="progress-bar-fill"></div>
        </div>
        <div>
            <label for="zip_file">Upload Zip File:</label>
            <input type="file" name="zip_file" id="zip_file" required>
        </div>
        <div>
            <label for="folder_name">Folder Name:</label>
            <input type="text" name="folder_name" id="folder_name" required>
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
                <!-- Add other loaders as needed -->
            </select>
        </div>
        <div>
            <label for="loader_version">Loader Version:</label>
            <input type="text" name="loader_version" id="loader_version" required>
        </div>
        <button type="submit">Upload and Extract</button>
    </form>
    <div class="status"></div>
</body>

</html>