<?php
    session_start();
    $username = $_SESSION['username'];
    $name = $_SESSION['name'];
    $command = escapeshellcmd("python recommendations.py '$username' ");
    $output = shell_exec($command);
    $recipe_IDS = explode(",", $output);
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
    <!-- weekly_log css -->  
    <!-- Latest compiled and minified CSS -->
    <!--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    -->
    <title>Recipe Recommendations</title>
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

      .carousel {
        height: 400px;
      }

      #weekly_log_container, #display_item_container {
        height: 100px; 
        width: 350px; 
        overflow: auto;
        background-color: white;
        margin-top: 15px;
        margin-bottom: 30px;
        border: solid;
        border-radius: 5px;
      }
      .carousel-item.active,
      .carousel-item-next,
      .carousel-item-prev {
      display:block;
    }

    </style>
  </head>
    <body>
            <div class="container">
                <header class="blog-header py-3">
                    <div class="row flex-nowrap justify-content-between align-items-center">
                        <div class="col-4 pt-1">
                            <a href="profile.php" class="text-primary">Hello, <?php echo $name;?>!</a>
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
                        <h1 class="display-4 font-italic">Your Recommended Recipes</h1>
                    </div>
                </div>

                <main role="main" class="container">
                    <div class="row mb-2">
                      <div class="col-md-10">
                        <div class="jumbotron">
                            
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                              <ol class="carousel-indicators">
                                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                              </ol>
                              <div class="carousel-inner">
                                <div class="carousel-item active text-center p-4">
                                  <?php
                                     // Database Connection
                                     $dbconnect = mysql_connect("localhost", "root", "carbfax411");
                                    if(!$dbconnect){
                                      die('Cannot connect: ' . mysql_error());
                                    }

                                    $db_selected = mysql_select_db("411_project_db", $dbconnect);

                                    if(!$db_selected){
                                      die('Cant use database: ' . mysql_error());
                                    }

                                    $query = "SELECT name, descriptions FROM recipes WHERE foodID = '$recipe_IDS[0]'";
                                    $result = mysql_query($query, $dbconnect);
                                    if(!$result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    $row = mysql_fetch_assoc($result);
                                    echo "<h4>" . $row['name'] ."</h4>";
                                    echo "<p>" . $row['descriptions'] ."</p>";

                                    $query = "SELECT products.name AS name, contains.quantity AS quantity, contains.measurement_std AS measurement FROM contains, products
                                     WHERE contains.recipe_foodID = '$recipe_IDS[0]' AND contains.product_foodID = products.foodID";
                                    $ingredient_result = mysql_query($query, $dbconnect);
                                    if(!$ingredient_result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    while ($row = mysql_fetch_assoc($ingredient_result)){
                                      echo $row['quantity'] . " " . $row['measurement'] . " " . $row['name'] . "<br>";
                                    }
                                    // Close Database Connection
                                    mysql_free_result($result);
                                    mysql_free_result($ingredient_result);
                                    mysql_close($dbconnect);
                                  ?>
                                </div>
                                <div class="carousel-item text-center p-4">
                                  <?php
                                     // Database Connection
                                     $dbconnect = mysql_connect("localhost", "root", "carbfax411");
                                    if(!$dbconnect){
                                      die('Cannot connect: ' . mysql_error());
                                    }

                                    $db_selected = mysql_select_db("411_project_db", $dbconnect);

                                    if(!$db_selected){
                                      die('Cant use database: ' . mysql_error());
                                    }
                                    $query = "SELECT name, descriptions FROM recipes WHERE foodID = '$recipe_IDS[1]'";
                                    $result = mysql_query($query, $dbconnect);
                                    if(!$result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    $row = mysql_fetch_assoc($result);
                                    echo "<h4>" . $row['name'] ."</h4>";
                                    echo "<p>" . $row['descriptions'] ."</p>";
                                  
                                    $query = "SELECT products.name AS name, contains.quantity AS quantity, contains.measurement_std AS measurement FROM contains, products
                                     WHERE contains.recipe_foodID = '$recipe_IDS[1]' AND contains.product_foodID = products.foodID";
                                    $ingredient_result = mysql_query($query, $dbconnect);
                                    if(!$ingredient_result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    while ($row = mysql_fetch_assoc($ingredient_result)){
                                      echo $row['quantity'] . " " . $row['measurement'] . " " . $row['name'] . "<br>";
                                    }
                                    // Close Database Connection
                                    mysql_free_result($result);
                                    mysql_free_result($ingredient_result);
                                    mysql_close($dbconnect);
                                  ?>
                                  
                                  
                                </div>
                                <div class="carousel-item text-center p-4">
                                  <?php
                                     // Database Connection
                                     $dbconnect = mysql_connect("localhost", "root", "carbfax411");
                                    if(!$dbconnect){
                                      die('Cannot connect: ' . mysql_error());
                                    }

                                    $db_selected = mysql_select_db("411_project_db", $dbconnect);

                                    if(!$db_selected){
                                      die('Cant use database: ' . mysql_error());
                                    }
                                    $query = "SELECT name, descriptions FROM recipes WHERE foodID = '$recipe_IDS[2]'";
                                    $result = mysql_query($query, $dbconnect);
                                    if(!$result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    $row = mysql_fetch_assoc($result);
                                    echo "<h4>" . $row['name'] ."</h4>";
                                    echo "<p>" . $row['descriptions'] ."</p>";
                                    
                                    $query = "SELECT products.name AS name, contains.quantity AS quantity, contains.measurement_std AS measurement FROM contains, products
                                     WHERE contains.recipe_foodID = '$recipe_IDS[2]' AND contains.product_foodID = products.foodID";
                                    $ingredient_result = mysql_query($query, $dbconnect);
                                    if(!$ingredient_result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    while ($row = mysql_fetch_assoc($ingredient_result)){
                                      echo $row['quantity'] . " " . $row['measurement'] . " " . $row['name'] . "<br>";
                                    }
                                    // Close Database Connection
                                    mysql_free_result($result);
                                    mysql_free_result($ingredient_result);
                                    mysql_close($dbconnect);
                                  ?>

                                </div>
                                <div class="carousel-item text-center p-4">
                                  <?php
                                     // Database Connection
                                     $dbconnect = mysql_connect("localhost", "root", "carbfax411");
                                    if(!$dbconnect){
                                      die('Cannot connect: ' . mysql_error());
                                    }

                                    $db_selected = mysql_select_db("411_project_db", $dbconnect);

                                    if(!$db_selected){
                                      die('Cant use database: ' . mysql_error());
                                    }
                                    $query = "SELECT name, descriptions FROM recipes WHERE foodID = '$recipe_IDS[3]'";
                                    $result = mysql_query($query, $dbconnect);
                                    if(!$result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    $row = mysql_fetch_assoc($result);
                                    echo "<h4>" . $row['name'] ."</h4>";
                                    echo "<p>" . $row['descriptions'] ."</p>";
                                   
                                    $query = "SELECT products.name AS name, contains.quantity AS quantity, contains.measurement_std AS measurement FROM contains, products
                                     WHERE contains.recipe_foodID = '$recipe_IDS[3]' AND contains.product_foodID = products.foodID";
                                    $ingredient_result = mysql_query($query, $dbconnect);
                                    if(!$ingredient_result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    while ($row = mysql_fetch_assoc($ingredient_result)){
                                      echo $row['quantity'] . " " . $row['measurement'] . " " . $row['name'] . "<br>";
                                    }
                                    // Close Database Connection
                                    mysql_free_result($result);
                                    mysql_free_result($ingredient_result);
                                    mysql_close($dbconnect);
                                  ?>

                                </div>
                                <div class="carousel-item text-center p-4">
                                  <?php
                                     // Database Connection
                                     $dbconnect = mysql_connect("localhost", "root", "carbfax411");
                                    if(!$dbconnect){
                                      die('Cannot connect: ' . mysql_error());
                                    }

                                    $db_selected = mysql_select_db("411_project_db", $dbconnect);

                                    if(!$db_selected){
                                      die('Cant use database: ' . mysql_error());
                                    }
                                    $query = "SELECT name, descriptions FROM recipes WHERE foodID = '$recipe_IDS[4]'";
                                    $result = mysql_query($query, $dbconnect);
                                    if(!$result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    $row = mysql_fetch_assoc($result);
                                    echo "<h4>" . $row['name'] ."</h4>";
                                    echo "<p>" . $row['descriptions'] ."</p>";
                                    
                                    $query = "SELECT products.name AS name, contains.quantity AS quantity, contains.measurement_std AS measurement FROM contains, products
                                     WHERE contains.recipe_foodID = '$recipe_IDS[4]' AND contains.product_foodID = products.foodID";
                                    $ingredient_result = mysql_query($query, $dbconnect);
                                    if(!$ingredient_result){
                                      die("Invalid Query: " . mysql_error());
                                    }
                                    while ($row = mysql_fetch_assoc($ingredient_result)){
                                      echo $row['quantity'] . " " . $row['measurement'] . " " . $row['name'] . "<br>";
                                    }
                                    // Close Database Connection
                                    mysql_free_result($result);
                                    mysql_free_result($ingredient_result);
                                    mysql_close($dbconnect);
                                  ?>
                                
                                </div>
                              </div>
                              <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                              </a>
                              <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                                </a>
                            </div>
                            
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
                      </aside>

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
        </div>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>
