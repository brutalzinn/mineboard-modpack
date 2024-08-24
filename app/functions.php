
<?php

function getModPackById($id)
{
    $folders = scanFolder(UPLOAD_DIR);
    foreach ($folders as $folder) {
        $manifestFilePath = UPLOAD_DIR . "$folder/manifest.json";
        if (file_exists($manifestFilePath)) {
            $fileContents = file_get_contents($manifestFilePath);
            $data = json_decode($fileContents, true);
            if ($data['id'] == $id) {
                return $data;
            }
        }
    }
    return [];
}

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return;
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? deleteDirectory("$dir/$file") : unlink("$dir/$file");
    }
    rmdir($dir);
}

function generateManifest($dir, $gameVersion, $name, $loader, $loaderVersion, $id)
{
    $manifest = [
        'id' => $id,
        'name' => $name,
        'gameVersion' => $gameVersion,
        'loader' => $loader,
        'loaderVersion' => $loaderVersion,
        'files' => HOST . "/" . "$dir/files.json"
    ];
    file_put_contents($dir . 'manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
}


function generateFiles($dir, $filesList)
{
    file_put_contents($dir . 'files.json', json_encode($filesList, JSON_PRETTY_PRINT));
}

function sanitizeName($name)
{
    $normalize =  preg_replace('/[^a-zA-Z0-9-_]/', '_', $name);
    $result = strtolower($normalize);
    return $result;
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
        if (in_array($value, IGNORE_FILES)) continue;
        $filePath = $dir . "/" . $value;
        $hash = hash_file('sha1', $filePath);
        $size = filesize($filePath);
        $path = str_replace("$dir/", "", $filePath);

        $url_req = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $url = HOST . "$url_req$dir$path";
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
function guidv4($data = null)
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
