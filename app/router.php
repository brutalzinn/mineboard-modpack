<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'regenerate':
            $id = $_POST['id'];
            $modpack = getModPackById($id);
            $folderName = sanitizeName($modpack["name"]);
            $extractDir = UPLOAD_DIR . $folderName . '/';
            if (is_dir($extractDir)) {
                $filesList = dirToArray($extractDir);
                generateManifest(dir: $extractDir, name: $name, gameVersion: $gameVersion, loader: $loader, loaderVersion: $loaderVersion, id: $id);
                generateFiles($extractDir, $filesList);
                echo "Package regenerated successfully!";
            } else {
                echo "Directory does not exist.";
            }
            break;

        case 'upload':
            $id = guidv4();
            $name = $_POST['name'] ?? 'default';
            $folderName = sanitizeName($name);
            $zipFile = $_FILES['zip_file']['tmp_name'];
            $gameVersion = $_POST['game_version'];
            $loader = $_POST['mod_loader'];
            $loaderVersion = $_POST['loader_version'];
            $extractDir = UPLOAD_DIR . $folderName . '/';
            if (!is_dir($extractDir)) {
                mkdir($extractDir, 0777, true);
            } else {
                deleteDirectory($extractDir);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo($extractDir);
                $zip->close();

                $filesList = dirToArray($extractDir);
                $modpackInfo = [
                    "id" => $id,
                    "name" => $name,
                    "game_version" => $gameVersion,
                    "loader" => $loader,
                    "loader_version" => $loaderVersion,
                    "files" => HOST . "/" . UPLOAD_DIR . $folderName . "/manifest.json"
                ];

                // Save the JSON object to a file
                file_put_contents($extractDir . 'files.json', json_encode($filesList, JSON_PRETTY_PRINT));
                file_put_contents($extractDir . 'manifest.json', json_encode($modpackInfo, JSON_PRETTY_PRINT));

                echo 'Zip file extracted and JSON file created successfully!';
            } else {
                echo 'Failed to extract zip file!';
            }



            break;

        case 'delete':
            // Handle deletion of a modpack
            if (is_dir($extractDir)) {
                deleteDirectory($extractDir);
                echo "Modpack deleted successfully!";
            } else {
                echo "Directory does not exist.";
            }
            break;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    switch ($_GET['action']) {
        case "list":
            $folders = scanFolder(UPLOAD_DIR);
            $allContents = [];
            foreach ($folders as $folder) {
                $manifestFilePath = UPLOAD_DIR . "$folder/manifest.json";
                $fileListPath = HOST . "/" . UPLOAD_DIR . "$folder/files.json";
                if (file_exists($manifestFilePath)) {
                    $fileContents = file_get_contents($manifestFilePath);
                    $data = json_decode($fileContents, true);
                    $data['files'] = $fileListPath;
                    array_push($allContents, $data);
                }
            }
            echo json_encode($allContents);
            exit;
        case "fetch":
            $id = $_GET['id'];
            $data = getModPackById($id);
            echo json_encode($data);
            exit;
    }
}
