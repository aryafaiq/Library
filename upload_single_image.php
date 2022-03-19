<?php
require('./config/database.php');
if (isset($_POST['id']) && isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"][0])) {
    try {
        $images = [];
        for ($i = 0; $i < count($_FILES["file"]["name"]); $i++) {
            $images[$i]['name'] = time() . "_" . $_FILES["file"]["name"][$i];
            $images[$i]['tmp_name'] = $_FILES["file"]["tmp_name"][$i];
            $images[$i]['book_id'] = $_POST['id'];
        }

        foreach ($images as $image) {
            $dir_target = "./upload/image/" . $image['name'];
            move_uploaded_file($image['tmp_name'], $dir_target);
        }

        $image_map = (array_map(function ($image) {
            $book_id = $image['book_id'];
            $image_name = $image['name'];
            return "('$image_name', $book_id)";
        }, $images));

        $query_image = implode(',', $image_map);
        $insert_image = mysqli_query($DB_CONN, "INSERT INTO images (file, book_id) VALUES $query_image");
        header('Location: ./update_book.php?id=' . $_POST['id']);
    } catch (Exception $e) {
        echo "Error : " . $e->getMessage();
    }
}
