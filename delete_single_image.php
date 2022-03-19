<?php
include('./config/database.php');
if ($_GET['name'] && $_GET['id']) {
    try {
        $id = $_GET['id'];
        $name = $_GET['name'];
        foreach (mysqli_query($DB_CONN, "SELECT * FROM images WHERE book_id = $id AND file = '$name'") as $image) {
            unlink("./upload/image/" . $image['file']);
        }
        if (
            mysqli_query($DB_CONN, "DELETE FROM images WHERE book_id = $id AND file = '$name'")
        ) {
            header("Location: ./update_book.php?id=$id");
        } else {
            throw new Error("Error when deleting image(s)!");
        }
    } catch (Exception $e) {
        echo "Error : " . $e->getMessage();
    }
} else {
    header("Location: ./update_book.php?id=$id");
}
