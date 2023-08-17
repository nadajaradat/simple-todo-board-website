<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

require_once 'conn.php';
$activeUser = $_SESSION['user_id'];

function get_name(){
    
    $activeUser = $_SESSION['user_id'];
    $conn = get_connection();
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $activeUser);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $username = $row['username'];
    } else {
        $username = "User";
    }
    return $username;

}
function isAdmin(){
    
    $activeUser = $_SESSION['user_id'];
    $conn = get_connection();
    $stmt = $conn->prepare("SELECT isAdmin FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $activeUser);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $isAdmin = $row['isAdmin'];
    } else {
        $isAdmin = "NO";
    }
    return ($isAdmin == "YES");

}


function save_task($type, $task, $id, $user_id) {
    $conn = get_connection();
    if ($id) {
        $sql = "UPDATE kaban_board SET `task`=?, `user_id`=?, `edit_id`=? WHERE id=?";
        $query = $conn->prepare($sql);
        $query->execute([$task, $user_id, $_SESSION['user_id'], $id]);
        return $id;
    } else {
        $sql = "INSERT INTO kaban_board(`task`,`type`,`user_id`,`edit_id`) VALUES (?,?,?,?)";
        $query = $conn->prepare($sql);
        $query->execute([$task, $type, $user_id, $_SESSION['user_id']]);
        return $conn->lastInsertId();
    }
}

function move_task($id, $position) {
    $activeUser = $_SESSION['user_id'];
    $conn = get_connection();
    $sql = "UPDATE kaban_board SET `type`=? WHERE id=? AND user_id=?";
    $query = $conn->prepare($sql);
    $query->execute([$position, $id, $activeUser]);
}

function get_tasks($type, $user_id) {
    $results = [];
    try {
        $conn = get_connection();
        $query = $conn->prepare("SELECT * FROM kaban_board WHERE type=? AND user_id=? ORDER BY id DESC");
        $query->execute([$type, $user_id]);
        $results = $query->fetchAll();
    } catch (Exception $e) {
    }
    return $results;
}

function show_title($taskObject, $type=""){
    $baseUrl = $_SERVER["PHP_SELF"]."?shift&id=".$taskObject["id"]."&type=";
    $editUrl = $_SERVER["PHP_SELF"] . "?edit&id=".$taskObject["id"]."&type=". $type;
    $deleteUrl = $_SERVER["PHP_SELF"] . "?delete&id=".$taskObject["id"];
    $o = '<span class="board">'.$taskObject["task"].'
        <hr>
        <span>
            <a href="'.$baseUrl.'backlog">B</a> |
            <a href="'.$baseUrl.'pending">P</a> |
            <a href="'.$baseUrl.'progress">IP</a> |
            <a href="'.$baseUrl.'completed">C</a> |
        </span>
        <a href="'.$editUrl.'">Edit</a> | <a href="'.$deleteUrl.'">Delete</a>
    </span>';
    return $o;
}

$activeId = "";
$activeTask = "";

if(isset($_GET['shift'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    if($id){
        move_task($id, $type);
        header("Location: ". $_SERVER['PHP_SELF']);
        exit();
    } else {
        header("Location: ". $_SERVER['PHP_SELF']);
    }
}

function get_task($id) {
    try {
        $conn = get_connection();
        $query = $conn->prepare("SELECT * FROM kaban_board WHERE id=?");
        $query->execute([$id]);
        $results = $query->fetchAll();
        return $results[0];
    } catch (Exception $e) {
        // Handle the exception if needed
        return null;
    }
}
function get_active_value($type, $content){
    $currentType = isset($_GET['type']) ? $_GET['type'] : null;
    if($currentType == $type){
        return $content;
    }
    return "";
}

$activeId = "";
$activeTask = "";

if(isset($_GET['shift'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    if($id){
        move_task($id, $type);
        header("Location: ". $_SERVER['PHP_SELF']);
        exit();
    } else {
        header("Location: ". $_SERVER['PHP_SELF']);
    }
}

if(isset($_GET['edit'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $activeId = $id;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    if($id){
        $taskObject = get_task($id);
        $activeTask = $taskObject["task"];
    }
}

if(isset($_GET['delete'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if($id){
        try {
            $conn = get_connection();
            $query = $conn->prepare("DELETE from kaban_board WHERE id=?");
            $query->execute([$id]);
            header("Location: ". $_SERVER['PHP_SELF']);
        } catch (Exception $e) {
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save-backlog'])) {
        $backlog = isset($_POST['backlog']) ? $_POST['backlog'] : null;
        save_task('backlog', $backlog, $activeId, $activeUser);
    }

    if (isset($_POST['save-pending'])) {
        $pending = isset($_POST['pending']) ? $_POST['pending'] : null;
        save_task('pending', $pending, $activeId, $activeUser);
    }

    if (isset($_POST['save-progress'])) {
        $progress = isset($_POST['progress']) ? $_POST['progress'] : null;
        save_task('progress', $progress, $activeId, $activeUser);
    }

    if (isset($_POST['save-completed'])) {
        $completed = isset($_POST['completed']) ? $_POST['completed'] : null;
        save_task('completed', $completed, $activeId, $activeUser);
    }

    if ($activeId) {
        header("Location: " . $_SERVER['PHP_SELF']);
    }
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

    <title>USER | Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class=" navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="app.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Hi <?php echo get_name()?></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="app.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

           

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Addons
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Login Screens</span>
                    
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="login.php">Login</a>
                        <div class="collapse-divider"></div>
               
                    </div>
                </div>
            </li>
            <!-- Nav Item - Pages Collapse Menu -->
             <?php if(isAdmin($_SESSION['user_id'])){?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="users.php" data-toggle="collapse2" data-target="#collapsePages2"
                    aria-expanded="true" aria-controls="collapsePages2">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Users Management</span>
                     
                        
                       
                    

                    
                </a>
                
            </li> 
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="register.php" data-toggle="collapse2" data-target="#collapsePages2"
                    aria-expanded="true" aria-controls="collapsePages2">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Add User</span>
                    
                </a>
                
            </li> 
             <?php }?>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                   

                    <!-- Bank icon-->
                    <div class="nav-item mt-3">
                        <img src ="https://www.tnb.ps/assets/images/logo.png" class="h4 mb-4 text-center signintext top"/>
                    </div>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php echo get_name()?> </span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                               
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                            <!-- Approach -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">My Tasks</h6>
                                </div>
                                <div class="card-body">
                                    <div class ="container">
                                        <div class="row">
                                            <div class ="col-lg-3 col-md-6 card">
                                                <div class ="card-header">Backlog</div>
                                                <div class ="card-body">
                                                    <form method="post">
                                                        <input value="<?php echo get_active_value("backlog", $activeTask);?>" type="text" name="backlog" style = "width:100%;" autocomplete="off"/>
                                                        <br><!-- -->
                                                        <button type="submit" class="btn btn-primary btn-sm mt-1" name = "save-backlog">save</button>
                                                        <hr><!-- comment -->
                                                        
                                                        <?php foreach (get_tasks('backlog', $activeUser) as $task):?>
                                                            <div class="card mt-2 text-dark">
                                                            <?php echo show_title($task,'backlog');?>
                                                            </div>
                                                        <?php endforeach;?>
                                                        
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <div class ="col-lg-3 col-md-6 card">
                                                <div class ="card-header">Pending</div>
                                                <div class ="card-body">
                                                    <form method="post">
                                                        <input value="<?php echo get_active_value("pending", $activeTask);?>" type="text" name="pending" style=" width: 100%" autocomplete="off"/>
                                                        <br><!-- -->
                                                        <button type="submit" class="btn btn-primary btn-sm mt-1" name = "save-pending">save</button>
                                                        <hr><!-- comment -->
                                                        
                                                        <?php foreach (get_tasks('pending', $activeUser) as $task):?>
                                                            <div class="card mt-2 text-dark">
                                                            <?php echo show_title($task,'pending');?>
                                                            </div>
                                                        <?php endforeach;?>
                                                        
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <div class ="col-lg-3 col-md-6 card">
                                                <div class ="card-header">In Progress</div>
                                                <div class ="card-body">
                                                    <form method="post">
                                                        <input value="<?php echo get_active_value("progress", $activeTask);?>" type="text" name="progress" style="width: 100%" autocomplete="off"/>
                                                        <br><!-- -->
                                                        <button type="submit" class="btn btn-primary btn-sm mt-1" name = "save-progress">save</button>
                                                        <hr><!-- comment -->
                                                        
                                                        <?php foreach (get_tasks('progress', $activeUser) as $task):?>
                                                            <div class="card mt-2 text-dark">
                                                            <?php echo show_title($task,'progress');?>
                                                            </div>
                                                        <?php endforeach;?>
                                                        
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <div class ="col-lg-3 col-md-6 card">
                                                <div class ="card-header">Completed</div>
                                                <div class ="card-body">
                                                    <form method="post">
                                                        <input value="<?php echo get_active_value("completed", $activeTask);?>" type="text" name="completed" style = "width:100%;" autocomplete="off"/>
                                                        <br><!-- -->
                                                        <button type="submit" class="btn btn-primary btn-sm mt-1" name = "save-completed">save</button>
                                                        <hr><!-- comment -->
                                                        
                                                        <?php foreach (get_tasks('completed', $activeUser) as $task):?>
                                                            <div class="card mt-2 text-dark">
                                                            <?php echo show_title($task,'completed');?>
                                                            </div>
                                                        <?php endforeach;?>
                                                        
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>TNB 2023</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>