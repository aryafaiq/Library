<?php
session_start();
include('./config/database.php');

if (!isset($_SESSION['isLogin'])) {
    header('Location: ./landing.php');
}

$books = mysqli_query($DB_CONN, "SELECT books.*, GROUP_CONCAT(i.file SEPARATOR ',') AS images
    FROM books
    LEFT JOIN images i ON (books.id = i.book_id)
    GROUP BY books.id;");

// return;
$error_insert = "";

// Jika ada variabel title, year dan description
if (isset($_POST["title"]) && isset($_POST["year"]) && isset($_POST["description"])) {
    try {
        if (!empty($_POST["title"]) && !empty($_POST["year"]) && !empty($_POST["description"])) {
            $title = htmlspecialchars(trim($_POST["title"]));
            $year = htmlspecialchars(trim($_POST["year"]));
            $desc = htmlspecialchars(trim($_POST["description"]));
            $insert_book = mysqli_query($DB_CONN, "INSERT INTO books (title, description, year) VALUES ('$title', '$desc', '$year')");
        } else {
            throw (new Exception("Input tidak valid"));
        }
        if (isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"][0])) {
            $images = [];
            for ($i = 0; $i < count($_FILES["file"]["name"]); $i++) {
                $images[$i]['name'] = time() . "_" . $_FILES["file"]["name"][$i];
                $images[$i]['tmp_name'] = $_FILES["file"]["tmp_name"][$i];
                $images[$i]['book_id'] = $DB_CONN->insert_id;
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
        }
        header("Location: ./index.php");
    } catch (Exception $e) {
        echo "Error : " . $e->getMessage();
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
        <a href="landing.php?function=logout" class="mb-5">logout! </a>, <span><?= $_SESSION['name'] ?></span>
        <div class="d-flex justify-content-between">
            <h3>
                List of books
                <p class="text-danger"><?= $error_insert ?></p>
            </h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewBookModal">+ Add New Book</button>
        </div>
        <hr />
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Judul</th>
                    <th scope="col">Thumbnail</th>
                    <th scope="col">Tahun Terbit</th>
                    <th scope="col">Genre</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($books as $index => $book) {
                ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $book['title'] ?></td>
                        <td>
                            <?php
                            $images = explode(',', $book["images"]);
                            // var_dump($images);
                            foreach ($images as $image) {
                                if (!empty($image)) {
                            ?>
                                    <img style="height: 100px; object-fit: contain" src="./upload/image/<?= $image ?>">
                            <?php
                                }
                            }
                            ?>
                        </td>
                        <td><?= $book['year'] ?></td>
                        <td></td>
                        <td>
                            <button class="btn btn-primary" onclick="window.location='./update_book.php?id=<?= $book['id'] ?>'">Edit</button>
                            <button class="btn btn-danger" onclick="deleteBook(<?= $book['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <!-- Add New Book Modal -->
    <div class="modal fade" id="addNewBookModal" tabindex="-1" aria-labelledby="addNewBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddNewBook" method="POST" enctype="multipart/form-data">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="inputTitle" placeholder="Title" name="title" required>
                            <label for="inputTitle">Title</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="inputYear" placeholder="Year" name="year" required>
                            <label for="inputYear">Year</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="inputDesc" placeholder="Description" name="description" required>
                            <label for="inputDesc">Description</label>
                        </div>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="inputGroupFile01" multiple name="file[]">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('formAddNewBook').submit()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteBook(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = './delete_book.php?id=' + id
                }
            })
        }
    </script>
</body>

</html>