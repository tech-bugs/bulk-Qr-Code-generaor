<?php
// Path to the upload directory
$uploadDirectory = __DIR__ . '/pages';

// Ensure the upload directory exists and has the correct permissions
if (!is_dir($uploadDirectory)) {
    // Create the directory with permissions set to 755
    if (!mkdir($uploadDirectory, 0755, true)) {
        // Return an error message if directory creation fails
        echo json_encode(['uploaded' => false, 'error' => 'Failed to create upload directory']);
        exit;
    }
} else {
    // Check if the directory has correct permissions (755)
    $permissions = fileperms($uploadDirectory);
    if (($permissions & 0777) !== 0755) {
        // Attempt to change the directory permissions to 755
        if (!chmod($uploadDirectory, 0755)) {
            // Return an error message if permission change fails
            echo json_encode(['uploaded' => false, 'error' => 'Failed to set directory permissions']);
            exit;
        }
    }
}

// Check if a file is uploaded
if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
    $fileInfo = pathinfo($_FILES['upload']['name']);
    $extension = strtolower($fileInfo['extension']);

    // Check if the file extension is allowed
    if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg') {
        // Generate a unique file name to avoid conflicts
        $fileName = uniqid() . '.' . $extension;
        $targetFile = $uploadDirectory . '/' . $fileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['upload']['tmp_name'], $targetFile)) {
            // Return the uploaded file URL
            $url = '/pages/' . $fileName;
            echo json_encode(['uploaded' => true, 'url' => $url]);
        } else {
            // Return an error message if the file upload failed
            echo json_encode(['uploaded' => false, 'error' => 'File upload failed']);
        }
    } else {
        // Return an error message if the file extension is not allowed
        echo json_encode(['uploaded' => false, 'error' => 'File format not supported']);
    }
} else {
    // Return an error message if no file is uploaded
    echo json_encode(['uploaded' => false, 'error' => 'No file uploaded']);
}
?>
