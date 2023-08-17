<?php
ini_set("session.cookie_lifetime","1000");

session_start(); // Start the session
$servername = "127.0.0.1";  // Change this to your database server
$username = "root";         // Change this to your database username
$password = "";             // Change this to your database password
$dbname = "hello";          // Change this to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        
        
        $stmt = $conn->prepare("SELECT id, email FROM users WHERE email=:email AND password=:password");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Store user information in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email'];
            header("Location: app.php"); // Redirect to dashboard
            exit(); // Make sure to exit after redirection
        } else {
            echo "Invalid login credentials.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<html>
    <head>
        <title>login page</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
        
        <style>
            

.row{

    margin-left: 2%;
    margin-right: 2%;
    margin-top:5%;
}
.page{

    width: 100%;
    height:auto;
    min-height:100vh;
    background-color:#ede7e6;
    background-size: 100% 100%;
    background-position: top center;
    padding:10px;
}

.signintext{
    margin-right:20%!important;
    color:black;

}

.top{
    color: #002E65;
}

.btn{
    width: 30%;
    margin-top:5%;
    margin-bottom: 5%;
    margin-left: 25%;

}

.ForgotPassBtn{
    background-color:white; 
    color:black;
    border-bottom: 1px solid #c7c5c5;
    padding-bottom: 3px;
    text-decoration: none!important;
    margin-right: 20%;
    border-top-color: none;

}
.LoginBtn{
      border-radius: 25px;
      background-color: #002E65;
      border:none;
}


form{
    padding: 0;
    margin-left: 15%;
}

.signin{
    background-color: white;
    padding: 2%;
    margin: auto;
    height: 80%;
}

.welcome{
    background-color: #002E65;
    padding: 2%;
    height: 80%;
}

.form-control{
    width: 80%;
    margin-bottom: 5%;
    margin-top: 5%;
    background-color: #ebe8e8;

}





.custom-control{
    margin-bottom: 2%;
} 

h2.HelloFriend{
    color:#fff !important;
    font-weight:200px;
    margin-top:30%;
    margin-bottom:10%;
}

.SignupText{
    color:#fff;
    margin-bottom:5%;
    font-size:16px;
    font-weight: 10px !important;
    font-family: Garamond;
}

@media only screen and (max-width: 600px) {
   row{
       margin-top: 5%;
       margin-left: 1%;
       margin-right: 1%;
    }    }
  
input, input:focus{
        border-width: 0px;
        outline:0; 
        box-shadow: none;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
}


 .social-btn .btn {
	color: #fff;
        margin: 8px 0 0 30px;
	font-size: 15px;
       width: 45px;
        height: 40px;
        line-height: 25px;
        border-radius: 50%;
	font-weight: normal;
        text-align: center;
	border: solid 0.5px grey;
	transition: all 0.4s;
    }	
	.social-btn .btn:first-child {
		margin-left: 0;
	}
	.social-btn .btn:hover {
		opacity: 0.8;
	}

	.social-btn .btn i {
		font-size: 20px;
	}

  .social-btn{
    margin-right: 20%;
    margin-bottom: 7%;
    margin-top: 5%;
  }
        </style>
    </head>
    
    <body>
        <!--For all screen-->
<div class="page">


  <!--Login & Signup in single row-->
  <div class="row">
  
    
  <!--Column for signin-->
    <div class="col-sm-8 text-center signin">
  
  
      <!-- Default form login -->
      <form method = "post">
          <img src ="https://www.tnb.ps/assets/images/logo.png" class="h4 mb-4 text-center signintext top"/>
        <!--<p class="h4 mb-4 text-center signintext top"><strong>Sign in to this Website</strong></p>-->
  
  <!--Login with Social Media Buttons
  <div class="social-btn text-center">
        <a href="#" class="btn btn-primary btn-lg" title="Facebook"><i class="fa fa-facebook"></i></a>
        <a href="#" class="btn btn-info btn-lg" title="Twitter"><i class="fa fa-twitter"></i></a>
        <a href="#" class="btn btn-danger btn-lg" title="Pinterest"><i class="fa fa-pinterest"></i></a>
      </div>
  -->
        <p class="text-center signintext">use your email account to login</p>
  
        <!-- username -->
        <input type="email" class="form-control mb-4" name ="email" placeholder="&#xf0e0; Email" style="font-family:Arial, FontAwesome">
  
        <!-- Password -->
        <input type="password" class="form-control mb-4" name ="password" placeholder="&#xf023;  Password" style="font-family:Arial, FontAwesome">
  
       
  
        <!-- Sign in button -->
        <button class="btn btn-info btn-block LoginBtn" type="submit" >SIGN IN</button>
  
  
      </form>
  
    </div>
  
  <!--Column for signin-->
    <div class="col-sm-4 welcome text-center">
      <h2 class="HelloFriend">Hello, Friend!</h2>
      <h4 class="SignupText">Welcome again!</h4>
      
      <!--<button class="btn btn-info btn-block signupbtn" type="submit">SIGN UP</button>-->
  
    </div>
  </div>
  </div>
    </body>
</html>
