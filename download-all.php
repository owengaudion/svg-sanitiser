<?php
if (isset($_GET['files'])) {
    $files = explode(',', $_GET['files']);
    $zipname = 'sanitised_svgs.zip';
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);

    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }

    $zip->close();

    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $zipname);
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);

    // Remove the temporary zip file
    unlink($zipname);
} else {
    echo 'Invalid request.';
}
?>
