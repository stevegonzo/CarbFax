<?php
    session_start();
    $username = $_SESSION['username'];
    $name = $_SESSION['name'];
    // Database Connection
    $dbconnect = mysql_connect("localhost", "root", "carbfax411");
    if(!$dbconnect){
        die('Cannot connect: ' . mysql_error());
    }

    $db_selected = mysql_select_db("411_project_db", $dbconnect);

    if(!$db_selected){
        die('Cant use database: ' . mysql_error());
    }

    $query = "SELECT age, height, weight FROM users WHERE username = '$username'";
    $result = mysql_query($query, $dbconnect);

    if(!$result){
      die('Invalid Query: ' . mysql_error());
    }

    $row = mysql_fetch_assoc($result);
    $age = $row['age'];
    $height_cms = $row['height'] * 2.54;
    $weight_kgs = $row['weight'] * 0.453592;
    $cals_recmnd = ceil((10 * $weight_kgs) + (6.25 * $height_cms) - (5 * $age) + 5);
    $carbs_recmnd = ceil($cals_recmnd * 0.5 / 4);
    $prot_recmnd = ceil($cals_recmnd * 0.25 / 4);
    $fat_recmnd = ceil($cals_recmnd * 0.25 / 9);

     // Close Database Connection
     mysql_free_result($result);
     mysql_close($dbconnect);

    if(isset($_POST['update'])){
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

      $query = "UPDATE users SET calorie_target = '$new_cals', carb_target = '$new_carbs', protein_target = '$new_protein',
                fat_target = '$new_fat' WHERE username = '$username'";
      
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
    <!-- weekly_log css -->  
    <!-- Latest compiled and minified CSS -->
    <!--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    -->
    <title>Nutrient Targets</title>
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
                        <h1 class="display-4 font-italic">Your Nutrient Targets</h1>
                    </div>
                </div>

                <main role="main" class="container">
                    <div class="row mb-2">
                      <div class="col-md-5">
                        <div class="jumbotron">
                            <h3 class="h3 mb-3 font-weight-normal">Your Average Daily Nutrient Intake</h3>
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

                                $query = "SELECT username, SUM(total_calories) AS total_calories, SUM(total_carbohydrates) AS total_carbohydrates,
                                          SUM(total_sugars) AS total_sugars, SUM(total_fiber) AS total_fiber, SUM(total_protein) AS total_protein, 
                                          SUM(total_fat) AS total_fat, SUM(total_sodium) AS total_sodium, SUM(total_cholesterol) AS total_cholesterol,
                                          SUM(total_vitaminA) AS total_vitaminA, SUM(total_vitaminB6) AS total_vitaminB6, SUM(total_vitaminB12) AS total_vitaminB12,
                                          SUM(total_vitaminC) AS total_vitaminC, SUM(total_vitaminD) AS total_vitaminD, SUM(total_vitaminE) AS total_vitaminE, 
                                          SUM(total_niacin) AS total_niacin, SUM(total_thiamin) AS total_thiamin, SUM(total_calcium) AS total_calcium,
                                          SUM(total_iron) AS total_iron, SUM(total_magnesium) AS total_magnesium, SUM(total_phosphorus) AS total_phosphorus,
                                          SUM(total_potassium) AS total_potassium, SUM(total_riboflavin) AS total_riboflavin, SUM(total_zinc) AS total_zinc
                                          FROM ((SELECT * FROM nutrient_sum_products_3 WHERE username = '$username') 
                                          UNION (SELECT * FROM nutrient_sum_recipes_1 WHERE username = '$username'))
                                          AS totals GROUP BY username";
                                $result = mysql_query($query, $dbconnect);
                                if (!$result){
                                  die("Invalid Query: " . mysql_error());
                                }

                                $row = mysql_fetch_assoc($result);
                                $calories = ceil($row['total_calories'] / 7);
                                $carbs = ceil($row['total_carbohydrates'] / 7);
                                $sugars = ceil($row['total_sugars'] / 7);
                                $fiber = ceil($row['total_fiber'] / 7);
                                $protein = ceil($row['total_protein'] / 7);
                                $fat = ceil($row['total_fat'] / 7);
                                $sodium = ceil($row['total_sodium'] / 7);
                                $cholesterol = ceil($row['total_cholesterol'] / 7);
                                $vitaminA = ceil($row['total_vitaminA'] / 7);
                                $vitaminB6 = ceil($row['total_vitaminB6'] / 7);
                                $vitaminB12 = ceil($row['total_vitaminB12'] / 7);
                                $vitaminC = ceil($row['total_vitaminC'] / 7);
                                $vitaminD = ceil($row['total_vitaminD'] / 7);
                                $vitaminE = ceil($row['total_vitaminE'] / 7);
                                $niacin = ceil($row['total_niacin'] / 7);
                                $thiamin = ceil($row['total_thiamin'] / 7);
                                $calcium = ceil($row['total_calcium'] / 7);
                                $iron = ceil($row['total_iron'] / 7);
                                $magnesium = ceil($row['total_magnesium'] / 7);
                                $phosphorus = ceil($row['total_phosphorus'] / 7);
                                $potassium = ceil($row['total_potassium'] / 7);
                                $riboflavin = ceil($row['total_riboflavin'] / 7);
                                $zinc = ceil($row['total_zinc'] / 7);

                                // TODO: OUTPUT RESULTS 
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Calories: " . $calories . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Protein: " . $protein . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Carbohydrate: " . $carbs . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Fat: " . $fat . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Sugars: " . $sugars . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Dietary Fiber: " . $fiber . "g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Cholesterol: " . $cholesterol . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Sodium: " . $sodium . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Calcium: " . $calcium . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Vitamin A: " . $vitaminA . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Vitamin B6: " . $vitaminB6 . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Vitamin B12: " . $vitaminB12 . "&#181g </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Vitamin C: " . $vitaminC .  "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Vitamin D: " . $vitaminD . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Vitamin E: " .  $vitaminE . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Niacin: " . $niacin . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Thiamin: " . $thiamin . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Iron: " .  $iron . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Magnesium: " . $magnesium . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Phosphorus: " . $phosphorus . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Potassium: " . $potassium . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Riboflavin: " . $riboflavin . "mg </a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Zinc: " . $zinc . "mg </a>";
                                

                                 // Close Database Connection
                                mysql_free_result($result);
                                mysql_close($dbconnect);
                            ?>
                            
                            </div>
                          </div>
                        </div>
                      <div class="col-md-5">
                      <div class="jumbotron">
                        <h3 class="h3 mb-3 font-weight-normal">Our Recommended Daily Targets</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">Calories: <?php echo "$cals_recmnd"; ?> </a>
                            <a href="#" class="list-group-item list-group-item-action">Protein: <?php echo "$prot_recmnd"; ?> g </a>
                            <a href="#" class="list-group-item list-group-item-action">Carbohydrate: <?php echo "$carbs_recmnd"; ?> g </a>
                            <a href="#" class="list-group-item list-group-item-action">Fat: <?php echo "$fat_recmnd"; ?> g </a>
                            <a href="#" class="list-group-item list-group-item-action">Sugars: Less than 35 g</a>
                            <a href="#" class="list-group-item list-group-item-action">Dietary Fiber: 29 g</a>
                            <a href="#" class="list-group-item list-group-item-action">Cholesterol: Less than 300 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Sodium: 2300 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Calcium: 1000 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Vitamin A: 800 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Vitamin B6: 1.3 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Vitamin B12: 2.4 &#181g </a>
                            <a href="#" class="list-group-item list-group-item-action">Vitamin C: 85 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Vitamin D: 600 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Vitamin E: 15 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Niacin: 15 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Thiamin: 1.2 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Iron: 13 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Magnesium: 360 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Phosphorus: 700 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Potassium: 4700 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Riboflavin: 1.2 mg</a>
                            <a href="#" class="list-group-item list-group-item-action">Zinc: 9.5 mg</a>
                        </div>
                      
                      </div>
                      </div>

                      <aside class="col-md-2 blog-sidebar">
                        <div class="p-4">
                          <h4 class="font-italic">Elsewhere</h4>
                          <ol class="list-unstyled">
                            <li><a href="https://wiki.illinois.edu//wiki/display/cs411changsp19">CS411 Homepage</a></li>
                            <li><a href="https://wiki.illinois.edu/wiki/display/CS411ChangSP19/Project+Show+Me+the+Carb+Fax">Team Project Page</a></li>
                            <li><a href="#">Github</a></li>
                          </ol>
                        </div>
                      </aside>

                    </div><!-- /.row -->
                    <div class="row mb-2">
                      <div class="col-md-6">
                        <div class="jumbotron">
                            <h3 class="h3 mb-3 font-weight-normal">Current Macro Nutrient Daily Targets</h3>
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
                                $query = "SELECT calorie_target, carb_target, fat_target, protein_target FROM users WHERE username = '$username'";
                                $result = mysql_query($query, $dbconnect);
                                if(!$result){
                                    die('Invalid Query: ' . mysql_error());
                                }
                                $row = mysql_fetch_assoc($result);
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Calorie Target:   " . $row['calorie_target'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Protein Target:   " . $row['protein_target'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Carbohydrate Target:   " . $row['carb_target'] . "</a>";
                                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\">Fat Target:   " . $row['fat_target'] . "</a>";
                                

                                 // Close Database Connection
                                mysql_free_result($result);
                                mysql_close($dbconnect);
                            ?>
                            </div>
                          </div>
                      </div>
                      <div class="col-md-6">
                        <div class="jumbotron">
                          <h3 class="h3 mb-3 font-weight-normal">Update Your Macro Nutrient Targets</h3>
                          <form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <label for="inputCalories">New Daily Calorie Target</label>
                            <input type="number" id="inputCalories" class="form-control form-control-sm" name="calories" placeholder="Number of Calories" required>
                            <label for="inputProtein">New Daily Protein Target</label>
                            <input type="number" id="inputProtein" class="form-control form-control-sm" name="protein" placeholder="Grams of Protein" required>
                            <label for="inputCarbs">New Daily Carbohydrate Target</label>
                            <input type="number" id="inputCarbs" class="form-control form-control-sm" name="carbs" placeholder="Grams of Carbohydrates" required>
                            <label for="inputFat">New Daily Fat Target</label>
                            <input type="number" id="inputFat" class="form-control form-control-sm" name="fat" placeholder="Grams of Fat" required>
                            <button name="update" class="btn btn-sm btn-primary btn-block" type="submit">Update Targets</button>
                        </form>
                          
                        </div>
                      </div>
                      
                    </div>


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
