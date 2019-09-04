<?php
    session_start();
    $username = $_SESSION['username'];
    $name = $_SESSION['name'];
    if(isset($_POST['update'])){
        $new_email = $_POST['email'];
        $new_age = $_POST['age'];
        $new_height = $_POST['height'];
        $new_weight = $_POST['weight'];
        $new_cals = $_POST['calories'];
        $new_protein = $_POST['protein'];
        $new_carbs = $_POST['carbs'];
        $new_fat = $_POST['fat'];
         // Database Connection
        $dbconnect = mysql_connect("localhost", "root", "carbfax411");
        if(!$dbconnect){
            die('Cannot connect: ' . mysql_error());
        }
   
        $db_selected = mysql_select_db("411_project_db", $dbconnect);

        if(!$db_selected){
            die('Cant use database: ' . mysql_error());
        }

        $query = "UPDATE users SET email_address = '$new_email', age = '$new_age', height = '$new_height', weight = '$new_weight', 
                    calorie_target = '$new_cals', carb_target = '$new_carbs', fat_target = '$new_fat', protein_target = '$new_protein' WHERE username = '$username'";
        $result = mysql_query($query, $dbconnect);

        if(!$result){
            die('Invalid Query: ' . mysql_error());
        }
        // Close Database Connection
        mysql_free_result($result);
        mysql_close($dbconnect);
    }
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
    <link rel="stylesheet" href="blog.css">
    <title>Profile - <?php echo $name; ?></title>
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
    <body>
            <div class="container">
                    <header class="blog-header py-3">
                      <div class="row flex-nowrap justify-content-between align-items-center">
                        <div class="col-4 pt-1">
                          <span class="text-primary">Hello, <?php echo $name;?>!</span>
                        </div>
                        <div class="col-md-4 text-center">
                          <a class="blog-header-logo text-dark" href="index.html">Show Me the Carb Fax</a>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center">
                            <a class="text-primary" href="logout.php">Log Out</a>
                        </div>
                      </div>
                    </header>
                  
                    <div class="nav-scroller py-1 mb-2">
                      <nav class="nav d-flex justify-content-between">
                        <a class="p-2 text-muted" href="intake.php">Nutrient Intake</a>
                        <a class="p-2 text-muted" href="targets.php">Nutrient Targets</a>
                        <a class="p-2 text-muted" href="recipes.php">Recipes</a>
                        <a class="p-2 text-muted" href="createrecipe.php">Create a Recipe</a>
                      </nav>
                    </div>
                  
                    <div class="jumbotron p-4 p-md-5 text-white rounded bg-dark">
                      <div class="col-md-6 px-0">
                        <h1 class="display-4 font-italic">Your Profile</h1>
                      </div>
                    </div>
                  
                    <div class="row mb-2">
                      <div class="col-md-6">
                        <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                          <div class="col p-4 d-flex flex-column position-static">
                            <strong class="d-inline-block mb-2 text-primary">Macro and Micro Nutrient Profile</strong>
                            <h3 class="mb-0">View your nutrient intake!</h3>
                            <div class="mb-1 text-muted">Updated Daily</div>
                            <p class="card-text mb-auto">A history of your nutrient intake for the last 7 days.</p>
                            <a href="intake.php" class="stretched-link">View Intake</a>
                          </div>
                          <div class="col-auto d-none d-lg-block">
                            <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Intake Image"><title>Intake</title><image href="log.jpg" width="200" height="250"/></svg>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                          <div class="col p-4 d-flex flex-column position-static">
                            <strong class="d-inline-block mb-2 text-primary">Your Nutrient Targets</strong>
                            <h3 class="mb-0">View your current nutrient targets!</h3>
                            <div class="mb-1 text-muted">Macro and Micro Nutrients</div>
                            <p class="mb-auto">As determined by your profile and FDA recommendations.</p>
                            <a href="targets.php" class="stretched-link">View Targets</a>
                          </div>
                          <div class="col-auto d-none d-lg-block">
                            <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Targets Image"><title>Target</title><image href="goals.jpg" width="200" height="250"/></svg>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-2">
                            <div class="col-md-6">
                              <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                                <div class="col p-4 d-flex flex-column position-static">
                                  <strong class="d-inline-block mb-2 text-success">Recipes</strong>
                                  <h3 class="mb-0">Your recommended recipes!</h3>
                                  <div class="mb-1 text-muted">Updated Daily</div>
                                  <p class="card-text mb-auto">Delicious recipes to help you reach your targets.</p>
                                  <a href="recipes.php" class="stretched-link">View Recipes</a>
                                </div>
                                <div class="col-auto d-none d-lg-block">
                                  <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Recipe Image"><title>Recipe</title><image href="recipe.jpg" width="200" height="250"/></svg>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                                <div class="col p-4 d-flex flex-column position-static">
                                  <strong class="d-inline-block mb-2 text-success">Create Recipes</strong>
                                  <h3 class="mb-0">Create a new recipe!</h3>
                                  <div class="mb-1 text-muted">Create and Share</div>
                                  <p class="mb-auto">Add a new recipe to our database to grow our digital cookbook.</p>
                                  <a href="createrecipe.php" class="stretched-link">Create a Recipe</a>
                                </div>
                                <div class="col-auto d-none d-lg-block">
                                  <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Food Image"><title>Food</title><image href="foods.jpg" width="200" height="250"/></svg>
                                </div>
                              </div>
                            </div>
                          </div>
                  </div>
                  
                  <main role="main" class="container">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="jumbotron">
                            <h4 class="display-4">About You</h4>
                            <div class="list-group">
                            <?php
                                 // Database Connection
                                $dbconnect = mysql_connect('localhost', 'root', 'carbfax411');
                                if(!$dbconnect){
                                    die('Cannot connect: ' . mysql_error());
                                }
                                $db_selected = mysql_select_db("411_project_db", $dbconnect);
                                if(!$db_selected){
                                    die('Cant use database: ' . mysql_error());
                                }
                                $query = "SELECT * FROM users WHERE username = '$username'";
                                $result = mysql_query($query, $dbconnect);
                                if(!$result){
                                    die('Invalid Query: ' . mysql_error());
                                }
                                $row = mysql_fetch_assoc($result);
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Name:   " . $row['name'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">E-Mail Address:   " . $row['email_address'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Age:   " . $row['age'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Height:   " . $row['height'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Weight:   " . $row['weight'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Calorie Target:   " . $row['calorie_target'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Carbohydrate Target:   " . $row['carb_target'] . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Fat Target:   " . $row['fat_target'] . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Protein Target:   " . $row['protein_target'] . "g </a>";

                                 // Close Database Connection
                                mysql_free_result($result);
                                mysql_close($dbconnect);
                            ?>
                            </div>

                        </div>
                      </div><!-- /.blog-main -->
                      <div class="col-md-5">
                      <div class="jumbotron">
                        <form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <h3 class="h3 mb-3 font-weight-normal">Update Your Profile</h3>
                            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email Address" required>
                            <input type="number" id="inputAge" class="form-control form-control-sm" name="age" placeholder="Age" required>
                            <input type="number" id="inputHeight" class="form-control form-control-sm" name="height" placeholder="Height (In Inches)" required>
                            <input type="number" id="inputWeight" class="form-control form-control-sm" name="weight" placeholder="Weight (In Pounds)" required>
                            <label for="inputCalories">What is Your Daily Calorie Target?</label>
                            <input type="number" id="inputCalories" class="form-control form-control-sm" name="calories" placeholder="Number of Calories" required>
                            <label for="inputProtein">What is Your Daily Protein Target?</label>
                            <input type="number" id="inputProtein" class="form-control form-control-sm" name="protein" placeholder="Grams of Protein" required>
                            <label for="inputCarbs">What is Your Daily Carbohydrate Target?</label>
                            <input type="number" id="inputCarbs" class="form-control form-control-sm" name="carbs" placeholder="Grams of Carbohydrates" required>
                            <label for="inputFat">What is Your Daily Fat Target?</label>
                            <input type="number" id="inputFat" class="form-control form-control-sm" name="fat" placeholder="Grams of Fat" required>
                            <button name="update" class="btn btn-sm btn-primary btn-block" type="submit">Update Information</button>
                        </form>
                        </div>
                      </div>      
                  
                      <aside class="col-md-2 blog-sidebar">
                  
                        <div class="p-4">
                          <h4 class="font-italic">Elsewhere</h4>
                          <ol class="list-unstyled">
                            <li><a href="https://wiki.illinois.edu//wiki/display/cs411changsp19">CS411 Homepage</a></li>
                            <li><a href="https://wiki.illinois.edu/wiki/display/CS411ChangSP19/Project+Show+Me+the+Carb+Fax">Team Project Page</a></li>
                            <li><a href="https://github.com/mingchao-zhang/cs411-final-project">Github</a></li>
                          </ol>
                        </div>
                      </aside><!-- /.blog-sidebar -->
                  
                    </div><!-- /.row -->
                        
                  
                  </main><!-- /.container -->
                  
                  <footer class="blog-footer">
                    <p>Copyright &copy; 2019 Team RSMS CS411 Spring 2019 UIUC</p>
                    <p>Food Data Courtesy of the <a href="https://ndb.nal.usda.gov/ndb">USDA Food Composition Databases</a></p>
                    <p>Recipe Data Courtesy of <a href="https://www.kaggle.com/hugodarwood/epirecipes">HugoDarwood's Epicurious Recipe Collection</a></p>
                    <p>
                      <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Back to top</a>
                    </p>
                  </footer>   
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>