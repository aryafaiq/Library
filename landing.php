<?php
session_start();

if (isset($_GET['function'])) {
    if ($_GET['function'] == 'logout') {
        session_destroy();
        return header("Location: ./landing.php");
    }
}

if (isset($_SESSION['isLogin'])) {
    return header("Location: ./index.php");
}

function trim_and_htmlspecialchars($string)
{
    return htmlspecialchars(trim($string));
}

function login()
{
    include('./config/database.php');
    if (
        !isset($_POST['identity']) || empty(trim_and_htmlspecialchars($_POST['identity'])) ||
        !isset($_POST['password']) || empty(trim_and_htmlspecialchars($_POST['password']))
    ) {
        echo "Identity and Password is required!";
    }
    $identity = $_POST['identity'];
    $password = $_POST['password'];
    $user = mysqli_fetch_assoc(
        mysqli_query(
            $DB_CONN,
            "SELECT * FROM users WHERE email = '$identity' OR username = '$identity'"
        )
    );

    if (
        !password_verify($password, $user['password'])
    ) {
        echo "Password salah!<br>";
    } else {
        $_SESSION["isLogin"] = true;
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        switch ($user['role']) {
            case 'superadmin':
                return header("Location: ./dashboard/superadmin.php");;
            case 'admin':
                return header("Location: ./dashboard/admin.php");;
            default:
                return header("Location: ./dashboard/user.php");;
        }
        // return header("Location: ./index.php");
    }
}

function register()
{
    include('./config/database.php');
    if (
        !isset($_POST['name']) || empty(trim_and_htmlspecialchars($_POST['name'])) ||
        !isset($_POST['email']) || empty(trim_and_htmlspecialchars($_POST['email'])) ||
        !isset($_POST['username']) || empty(trim_and_htmlspecialchars($_POST['username'])) ||
        !isset($_POST['password']) || empty(trim_and_htmlspecialchars($_POST['password'])) ||
        !isset($_POST['confirm_password']) || empty(trim_and_htmlspecialchars($_POST['confirm_password']))
    ) {
        echo "All field was required!";
        return;
    }

    if (trim_and_htmlspecialchars($_POST['password']) != trim_and_htmlspecialchars($_POST['confirm_password'])) {
        echo "Password and confirm password must be same!";
        return;
    }

    // $name = $_POST['name'];
    // $email = $_POST['email'];
    // $username = $_POST['username'];
    // $password = $_POST['password'];

    // $query = mysqli_query($DB_CONN, "INSERT INTO users (name, email, username, password) VALUES ('$name', '$email', '$username', '$password')");

    $query = $DB_CONN->prepare("INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)");

    $query->bind_param(
        "ssss",
        $name,
        $email,
        $username,
        $password
    );

    $name = trim_and_htmlspecialchars($_POST['name']);
    $email = trim_and_htmlspecialchars($_POST['email']);
    $username = trim_and_htmlspecialchars($_POST['username']);
    $password = password_hash(trim_and_htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);

    $query->execute();

    // $password = password_hash(trim_and_htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);
    if (!empty($DB_CONN->error)) {
        echo "Error : " . $DB_CONN->error;
    } else {
        header("Location: ./index.php");
    }

    $query->close();
    $DB_CONN->close();
}

if (isset($_GET['function'])) {
    switch ($_GET['function']) {
        case 'login':
            login();
            return;
        case 'register':
            register();
            return;
        default:
            header("Location: ./landing.php");
            return;
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
    <title>Landing Page | LibraryApp</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled">Disabled</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSignIn">Sign In</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal Sign In/Up -->
    <div class="modal fade" id="modalSignIn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sign In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="signInTabs" role="tablist">
                        <li role="presentation" class="nav-item">
                            <button class="nav-link active" id="signIn-tab" data-bs-toggle="tab" data-bs-target="#signIn" type="button" role="tab" aria-controls="signIn" aria-selected="true">Sign In</button>
                        </li>
                        <li role="presentation" class="nav-item">
                            <button class="nav-link " aria-current="page" id="signUp-tab" data-bs-toggle="tab" data-bs-target="#signUp" type="button" role="tab" aria-controls="signUp" aria-selected="false">Sign Up</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="signInContent">
                        <div class="tab-pane fade show active" id="signIn" role="tabpanel" aria-labelledby="home-tab">
                            <form action="?function=login" method="post">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="floatingInput" placeholder="name@example.com" name="identity">
                                    <label for="floatingInput">Email address / Username</label>
                                </div>
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
                                    <label for="floatingPassword">Password</label>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-danger me-3">Close</button>
                                    <button class="btn btn-success" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="signUp" role="tabpanel" aria-labelledby="signUp-tab">
                            <div class="tab-pane fade show active" id="signIn" role="tabpanel" aria-labelledby="home-tab">
                                <form action="?function=register" method="post">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" id="floatingPassword" placeholder="Password" name="name" required>
                                        <label for="floatingPassword">Name</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="floatingPassword" placeholder="Password" name="username" required>
                                        <label for="floatingPassword">Username</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" required>
                                        <label for="floatingInput">Email address</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
                                        <label for="floatingPassword">Password</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="confirm_password" required>
                                        <label for="floatingPassword">Confirm Password</label>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn btn-danger me-3">Close</button>
                                        <button class="btn btn-success" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>