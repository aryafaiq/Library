<?php
include('./config/database.php');
$id = $_GET['id'];
if (!$id) {
    return header("Location: ./index.php");
}
$book = mysqli_query($DB_CONN, "SELECT books.*, GROUP_CONCAT(i.file SEPARATOR ',') AS images
    FROM books
    LEFT JOIN images i ON (books.id = i.book_id)
    WHERE books.id = $id
    ")->fetch_assoc();

// var_dump($book);

if (isset($_POST['title']) && isset($_POST['year']) && isset($_POST['description'])) {
    $title = $_POST['title'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    $update = mysqli_query($DB_CONN, "UPDATE books SET title='$title', year='$year', description='$description' WHERE id='$id'");

    if ($update) {
        header('Location: ./index.php');
    } else {
        var_dump(mysqli_error($DB_CONN));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>LibraryApp</title>
</head>

<body>
    <div class="container my-5">
        <div class="row">
            <div class="col">
                <?php
                $images = explode(',', $book['images']);
                foreach ($images as $image) {
                    if (isset($image) && !empty($image)) {
                ?>
                        <div>
                            <form action="./delete_single_image.php" method="get">
                                <img src="./upload/image/<?= $image ?>" class="img-thumbnail me-2 mb-2" alt="..." style="object-fit: contain;max-width: 125px; width: 100%; max-height: 125px;height: 100%">
                                <input type="hidden" value="<?= $image ?>" name="name">
                                <input type="hidden" value="<?= $id ?>" name="id">
                                <button type="submit" class="btn btn-danger">x</button>
                            </form>
                        </div>
                <?php }
                } ?>
                <button data-bs-toggle="modal" data-bs-target="#addImages" class="img-thumbnail d-flex justify-content-center align-items-center" style="object-fit: contain;max-width: 125px; width: 100%; max-height: 125px;height: 100%;">
                    <h1>
                        +
                    </h1>
                </button>
            </div>
            <div class="col-md-8">
                <form method="POST">
                    <div class="form-floating mb-3">
                        <input value="<?= $book['title'] ?>" type="text" class="form-control" id="inputTitle" placeholder="Title" name="title">
                        <label for="inputTitle">Title</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input value="<?= $book['year'] ?>" type="number" class="form-control" id="inputYear" placeholder="Year" name="year">
                        <label for="inputYear">Year</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input value="<?= $book['description'] ?>" type="text" class="form-control" id="inputDesc" placeholder="Description" name="description">
                        <label for="inputDesc">Description</label>
                    </div>
                    <button class="btn btn-success" type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addImages" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./upload_single_image.php" method="post" id="addImagesForm" enctype="multipart/form-data">
                        <input type="hidden" value="<?= $book['id'] ?>" name="id">
                        <input type="file" class="form-control" id="inputGroupFile01" multiple name="file[]">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button onclick="document.getElementById('addImagesForm').submit()" type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>