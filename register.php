<?php
session_start();

$servername = "127.0.0.1";  // Change this to your database server
$username = "root";         // Change this to your database username
$password = "";             // Change this to your database password
$dbname = "hello";          // Change this to your database name

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $username2 = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

      
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, admin_id) VALUES (:username2, :email, :password, :admin_id)");
        $stmt->bindParam(':username2', $username2);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':admin_id', $_SESSION['user_id']);
        $stmt->execute();

        echo "Registration successful. <a href='login.php'>Login here</a>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                                
                            <form class="user" method="post">
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" name ="username" id="username"
                                            placeholder="User Name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <input type="email" class="form-control form-control-user" name = "email" id="email"
                                        placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            name = "password" id="password" placeholder="Password">
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a href ="app.php">go back </a>
                                    <input type="submit" class = "btn btn-primary" value="Add">
                                    
                                </div>
                            </form>
                                </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>