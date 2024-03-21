<?php
include 'inc_header.php';

if (isset($_POST["submit"])) {
    $total = count($_FILES['filesToUpload']['name']);
    $target_dir = "uploads/";

    // Loop through each file
    foreach ($_FILES["filesToUpload"]["name"] as $key => $name) {
        $target_file = $target_dir . basename($name);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists: $name<br>";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["filesToUpload"]["size"][$key] > 500000) {
            echo "Sorry, your file is too large: $name<br>";
            $uploadOk = 0;
        }

        // Allow only CSV file formats
        if ($imageFileType != "csv") {
            echo "Sorry, only CSV files are allowed: $name<br>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded: $name<br>";
        } else {
            if (move_uploaded_file($_FILES["filesToUpload"]["tmp_name"][$key], $target_file)) {
                echo '<div style="text-align: center;">';
                echo 'The file <strong>' . htmlspecialchars($name) . '</strong> has been uploaded.<br><br>';
                echo '<a href="/" class="btn btn-primary ">&lt;&lt; BACK</a>';
                echo '</div>';
                
                $newFileName = $target_dir . pathinfo($target_file, PATHINFO_FILENAME) . ".imported.csv";
                rename($target_file, $newFileName);
                // Open the file for reading
                if (($handle = fopen($newFileName, "r")) !== FALSE) {
                    $data = fgetcsv($handle, 1000, ",");
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $date = DateTime::createFromFormat('m/d/Y', $data[0]);
                        $formattedDate = $date ? $date->format('Y-m-d') : ''; // adjust date format to yy-mm-dd

                        $num = count($data);

                        $formattedDate = SQLite3::escapeString($formattedDate);
                        $description = SQLite3::escapeString($data[1]);
                        $amount = SQLite3::escapeString($data[2]);
                        $category = null;

                        if (!empty($amount)) {
                            $SQLinsert = "INSERT INTO transactions (date, description, amount)";
                            $SQLinsert .= " VALUES ";
                            $SQLinsert .= " ('$formattedDate', '$description', '$amount')";

                            $db->exec($SQLinsert);
                            $changes = $db->changes();
                        }
                    }
                    fclose($handle);
                }
            } else {
                echo "Sorry, there was an error uploading your file: $name<br>";
            }
        }
    }
}
