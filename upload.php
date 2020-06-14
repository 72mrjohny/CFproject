<?php
require_once "connect.php";
require_once "import.php";

$dane = [];

if (isset($_POST['submit'])) {

    $regex = "/^dane_[0-9]{4}-[0-9]{2}-[0-9]{2}\\.csv$/";
    $file = $_FILES['file'];

    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    $fileExt = explode('.', $fileName);

    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('csv');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) {
                if (preg_match($regex, $fileName)) {
                    $fileDestination = 'uploads/' . $fileName;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    echo "File validation ok, saved in folder /uploads";
                    $import_object = new Import($connectionParams);
                    $import_object->import_data();
                    $import_object->process_data();
                } else {
                    echo "Wrong file's name";
                }
            } else {
                echo "File's size is too big";
            }
        } else {
            echo "Error during uploading file";
        }
    } else {
        echo "You can not upload files of this type";
    }
}
