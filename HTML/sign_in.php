<?php
  session_start();
  $msg = '';
  $msgClass = '';
  $successful = FALSE;
  // Database Connection
  $dbconnect = mysql_connect("localhost", "root", "carbfax411");
  if(!$dbconnect){
      die('Cannot connect: ' . mysql_error());
  }
  
  $db_selected = mysql_select_db("411_project_db", $dbconnect);

  if(!$db_selected){
      die('Cant use database: ' . mysql_error());
  }
  // Check for submit
  if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT username, name FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysql_query($query, $dbconnect);

    if(!$result){
      die('Invalid Query: ' . mysql_error());
    }
    //----- Why doesn't the function work?
/*
    function update_ate_records($username, $db_connect) {
	$valid_time = 7 * 24 * 60;
        $query = "DELETE FROM ate 
		  WHERE username = '$username' AND
		  	TIMESTAMPDIFF(MINUTE, date, CURRENT_TIMESTAMP()) > $valid_hour";
	$result = mysql_query($query, $dbconnect);
	if (!$result) {
       	    die("Invalid Query: " . mysql_error());
     	}
    }
*/
  
    if (mysql_num_rows($result) != 1) {
        $msg = 'Invalid Login Details';
        $msgClass = 'alert alert-danger';
    }
    else {
        $row = mysql_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        $_SESSION['name'] = $row['name'];
        $successful = TRUE;
        $msg = 'Login Successful. Welcome, ' . $_SESSION['name'];
        $msgClass = 'alert alert-success';

   	// "Trigger Event": Delete outdated records
	$valid_time = 7 * 24 * 60;
        $query = "DELETE FROM ate 
		  WHERE username = '$username' AND
		  	TIMESTAMPDIFF(MINUTE, date, CURRENT_TIMESTAMP()) > $valid_time";
	$result = mysql_query($query, $dbconnect);
	if (!$result) {
       	    die("Invalid Query: " . mysql_error());
     	}
    }
  }
  // Close Database Connection
  mysql_free_result($result);
  mysql_close($dbconnect);
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="cover.css">
    <link rel="stlyesheet" href="signin.css">
    <link rel="stylesheet" href="style.css">
    <title>Show Me the Carb Fax Sign-In</title>
    <style>
      .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      }
      
      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
        font-size: 3.5rem;
        }
      }
    </style>
  </head>
  <body class="text-center">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
             <div class="inner">
                <h3 class="masthead-brand">Show Me the Carb Fax</h3>
                <nav class="nav nav-masthead justify-content-center">
                  <a class="nav-link" href="index.html">Home</a>
                  <a class="nav-link active" href="sign_in.php">Sign In</a>
                  <a class="nav-link" href="sign_up.php">Sign Up</a>
                  <a class="nav-link" href="contact.html">Contact</a>
                </nav>
            </div>
        </header>
    
        <main role="main" class="inner cover">
          <?php if($msg != ''): ?>
            <div class = "<?php echo $msgClass; ?>"><?php echo $msg; ?></div>
          <?php endif; ?>
          <?php if($successful): ?>
            <a class="btn btn-sm btn-success" href="profile.php" role="button">Go to Profile</a>
          <?php endif; ?>
            <form class="form-signin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
                <label for="inputUsername" class="sr-only">Username</label>
                <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Username" required autofocus>
                <label for="inputPassword" class="sr-only" >Password</label>
                <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" checked="checked" value="remember-me" name="remember"> Remember me
                    </label>
                </div>
                <button name="submit" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
            <h4 class="h4 mb-3 font-weight-normal text-muted"> Not a registered user? <a href="sign_up.php" class="text-primary">Sign Up</a> </h4>
        </main>
    
        <footer class="mastfoot mt-auto">
          <div class="inner">
            <p>Copyright &copy; 2019 Team RSMS CS411 Spring 2019 UIUC</p>
            <p>Food Data Courtesy of the <a href="https://ndb.nal.usda.gov/ndb">USDA Food Composition Databases</a></p>
            <p>Recipe Data Courtesy of <a href="https://www.kaggle.com/hugodarwood/epirecipes">HugoDarwood's Epicurious Recipe Collection</a></p>
          </div>
        </footer>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>



