<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SVG XML Tag Stripper</title>
    <meta name="description" content="A tool to sanitise SVG files" />
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>

    <?php

    // Function to remove the <?xml tag from SVG content
    function removeXmlTag($svgContent) {

        $pattern1 = "/<\?xml.*?\?>/";
        $pattern2 = "#<script(.*?)>(.*?)</script>#is";

        return preg_replace(array($pattern1,$pattern2, ), '', $svgContent);

    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["svgFiles"])) {
        $uploadDir = 'uploads/';
        $modifiedFiles = array(); // To store the paths of modified SVG files

        // Process each uploaded file
        foreach ($_FILES["svgFiles"]["tmp_name"] as $key => $tmp_name) {
            $uploadFile = $uploadDir . basename($_FILES['svgFiles']['name'][$key]);

            // Check if the file is an SVG
            $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
            if ($fileType === 'svg') {
                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES['svgFiles']['tmp_name'][$key], $uploadFile)) {
                    // Read the content of the uploaded SVG file
                    $svgContent = file_get_contents($uploadFile);

                    // Remove the <?xml tag from the SVG content
                    $modifiedSvgContent = removeXmlTag($svgContent);

                    // Save the modified SVG content to a new file
                    $modifiedFilePath = 'downloads/' . basename($_FILES['svgFiles']['name'][$key]);
                    file_put_contents($modifiedFilePath, $modifiedSvgContent);

                    // Store the path of the modified SVG file
                    $modifiedFiles[] = $modifiedFilePath;
                } else {
                    echo '<p>Sorry, there was an error uploading ' . $_FILES['svgFiles']['name'][$key] . '.</p>';
                }
            } else {
                echo '<p>Invalid file type. Please upload an SVG file.</p>';
            }
        }
    }
    ?>

    <div class="form-container">
        <?php // Display a thank-you message with a single button to download all modified SVGs
        if (!empty($modifiedFiles)) {
            echo '<div class="download-link">';
            echo '<a href="/" class="close"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a>';
            echo '<div class="download-link-content">';
            echo '<h1>Thank you for uploading your SVG files!</h1>';
            echo '<p><a href="#" id="downloadAll">Download All Modified SVGs</a></p>';
            echo '<script>
                document.getElementById("downloadAll").addEventListener("click", function() {
                    window.location.href = "download-all.php?files=' . implode(',', $modifiedFiles) . '";
                });
            </script>';
            echo '</div>';
            echo '</div>';
        }
        ?>
        <div class="form-before-content">
            <h3>Upload SVG files</h3>
        </div>
        <!-- HTML form for multiple file uploads -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label>
                <span>Upload your SVG files and watch the magic happen! This tool removes the XML tag which can cause issues and strips out any script tags.</span>
                <input type="file" name="svgFiles[]" accept=".svg" multiple required>
            </label>
            <button type="submit">Upload</button>
        </form>
    </div>

<footer>Created by <a href="https://ogdigital.co.uk/" target="_blank">Owen Gaudion</a></footer>

</body>
</html>
