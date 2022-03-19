<?php
include('./config/database.php');
if ($_GET['id']) {
    try {
        $id = $_GET['id'];
        foreach (mysqli_query($DB_CONN, "SELECT * FROM images WHERE book_id = $id") as $image) {
            unlink("./upload/image/" . $image['file']);
        }
        if (
            mysqli_query($DB_CONN, "DELETE FROM images WHERE book_id='$id'")
            &&
            mysqli_query($DB_CONN, "DELETE FROM books WHERE id='$id'")
        ) {
            header("Location: ./index.php");
        } else {
            throw new Error("Error when deleting image(s)!");
        }
    } catch (Exception $e) {
        echo "Error : " . $e->getMessage();
    }
} else {
    header("Location: ./index.php");
}
