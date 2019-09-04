<?php
  
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
    $query = "SELECT username FROM users WHERE username = '$username'";
    $result = mysql_query($query, $dbconnect);
  
    
    if(!$result){
      die('Invalid Query: ' . mysql_error());
    }
  

    if(mysql_num_rows($result) == 0){
      $msg = 'Account Created';
      $msgClass = 'alert alert-success';
      $successful = TRUE;
    }
  
  
    else {
      $msg = 'That Username is Taken';
      $msgClass = 'alert alert-danger';
    }

    mysql_free_result($result);

  }
  
  if($successful){
    session_start();

    $_SESSION['username'] = $_POST['username'];
    $_SESSION['name'] = $_POST['name'];
    // New User Data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $cals = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];
    $fat = $_POST['fat'];
    // Update Database
    $query = "INSERT INTO users VALUES ('$username', '$password', '$name', '$email', '$age', '$height', '$weight', '$cals', '$carbs', '$fat', '$protein')";
    $result = mysql_query($query, $dbconnect);
    if(!$result){
      die('Invalid Query: ' . mysql_error);
    }

  }

  // Close Database connection
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
    <title>Show Me the Carb Fax Sign-Up</title>
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
                  <a class="nav-link" href="sign_in.php">Sign In</a>
                  <a class="nav-link active" href="sign_up.php">Sign Up</a>
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
            <form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h3 class="h3 mb-3 font-weight-normal">Please Complete This Form</h3>
                <label for="inputUsername">Username</label>
                <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Username" required autofocus>
                <label for="inputPassword">Password</label>
                <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
                <label for="inputName">Name</label>
                <input type="text" id="inputName" class="form-control" name="name" placeholder="Your Name (First, Last)" required>
                <label for="inputEmail">Email Address</label>
                <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email Address" required>
                <label for="inputAge">Age</label>
                <input type="number" id="inputAge" class="form-control form-control-sm" name="age" placeholder="Age" required>
                <label for="inputHeight">Height</label>
                <input type="number" id="inputHeight" class="form-control form-control-sm" name="height" placeholder="Height (In Inches)" required>
                <label for="inputWeight">Weight</label>
                <input type="number" id="inputWeight" class="form-control form-control-sm" name="weight" placeholder="Weight (In Pounds)" required>
                <label for="inputCalories">What is Your Daily Calorie Target?</label>
                <input type="number" id="inputCalories" class="form-control form-control-sm" name="calories" placeholder="Number of Calories" required>
                <label for="inputProtein">What is Your Daily Protein Target?</label>
                <input type="number" id="inputProtein" class="form-control form-control-sm" name="protein" placeholder="Grams of Protein" required>
                <label for="inputCarbs">What is Your Daily Carbohydrate Target?</label>
                <input type="number" id="inputCarbs" class="form-control form-control-sm" name="carbs" placeholder="Grams of Carbohydrates" required>
                <label for="inputFat">What is Your Daily Fat Target?</label>
                <input type="number" id="inputFat" class="form-control form-control-sm" name="fat" placeholder="Grams of Fat" required>
                
                <button name="submit" class="btn btn-lg btn-primary btn-block" type="submit">Create Account</button>
            </form>
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



