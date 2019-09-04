<?php
    session_start();
    $username = $_SESSION['username'];
    $name = $_SESSION['name'];
    if(isset($_POST['update'])){
        
         // Database Connection
        $dbconnect = mysql_connect("localhost", "root", "carbfax411");
        if(!$dbconnect){
            die('Cannot connect: ' . mysql_error());
        }

        $db_selected = mysql_select_db("411_project_db", $dbconnect);

        if(!$db_selected){
            die('Cant use database: ' . mysql_error());
        }

        // add food item by ID
        if($_POST['addItemID'] != '' ){
          $newItemID = $_POST['addItemID'];
          $quantity = $_POST['quantity'];

          $query = "INSERT INTO ate (username, foodID, quantity) VALUES ('$username', '$newItemID', '$quantity') ON DUPLICATE KEY UPDATE quantity = quantity + $quantity;";

          $result = mysql_query($query, $dbconnect);

          if(!$result){
            die("Invalid Query: " . mysql_error());
          }
        }
        // Add product by UPC
        elseif($_POST['productUPC'] != ''){
          $upc = $_POST['productUPC'];
          $quantity = $_POST['quantity'];

          $query1 = "SELECT foodID FROM products WHERE upc = '$upc'";

          $result = mysql_query($query1, $dbconnect);

          if(!$result){
            die("Invalid Query: " . mysql_error());
          }
          $row = mysql_fetch_assoc($result);
          $newItemID = $row['foodID'];

          $query2 = "INSERT INTO ate (username, foodID, quantity) VALUES ('$username', '$newItemID', '$quantity') ON DUPLICATE KEY UPDATE quantity = quantity + $quantity;";

          $result2 = mysql_query($query2, $dbconnect);

          if(!$result2){
            die("Invalid Query: " . mysql_error());
          }

        }

        // Close Database Connection
        mysql_free_result($result);
        mysql_free_result($result2);
        mysql_close($dbconnect);
    }

    if(isset($_POST['remove'])){
      // Database Connection
      $dbconnect = mysql_connect("localhost", "root", "carbfax411");
      if(!$dbconnect){
          die('Cannot connect: ' . mysql_error());
      }

      $db_selected = mysql_select_db("411_project_db", $dbconnect);

      if(!$db_selected){
          die('Cant use database: ' . mysql_error());
      }

      // Remove Item
      $foodID = $_POST['removeIDVal'];
      $date = $_POST['removeDateVal'];
      $quan = $_POST['removeQuanVal'];

      $query = "DELETE FROM ate WHERE username = '$username' and foodID = '$foodID' and date LIKE \"$date%\" ";

      $result = mysql_query($query, $dbconnect);

      if(!$result){
        die("Invalid Query: " . mysql_error());
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
    <title>Nutrient Intake</title>
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
        height: 400px;
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
                        <h1 class="display-4 font-italic">Your Nutrient Intake</h1>
                    </div>
                </div>

                <main role="main" class="container">
                    <div class="row mb-2">
                      <div class="col-md-5">
                        <div class="jumbotron">
                            <h4 class="display-4">This Week's Totals</h4>
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
                                // TODO: ADD QUERY TO GET NUTRIENT AGGREGATION
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
                                $calories = $row['total_calories'];
                                $carbs = ceil($row['total_carbohydrates']);
                                $sugars = ceil($row['total_sugars']);
                                $fiber = ceil($row['total_fiber']);
                                $protein = ceil($row['total_protein']);
                                $fat = ceil($row['total_fat']);
                                $sodium = ceil($row['total_sodium']);
                                $cholesterol = ceil($row['total_cholesterol']);
                                $vitaminA = ceil($row['total_vitaminA']);
                                $vitaminB6 = ceil($row['total_vitaminB6']);
                                $vitaminB12 = ceil($row['total_vitaminB12']);
                                $vitaminC = ceil($row['total_vitaminC']);
                                $vitaminD = ceil($row['total_vitaminD']);
                                $vitaminE = ceil($row['total_vitaminE']);
                                $niacin = ceil($row['total_niacin']);
                                $thiamin = ceil($row['total_thiamin']);
                                $calcium = ceil($row['total_calcium']);
                                $iron = ceil($row['total_iron']);
                                $magnesium = ceil($row['total_magnesium']);
                                $phosphorus = ceil($row['total_phosphorus']);
                                $potassium = ceil($row['total_potassium']);
                                $riboflavin = ceil($row['total_riboflavin']);
                                $zinc = ceil($row['total_zinc']);

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
                      <!--Live search start-->
                      <h3 class="h3 mb-3 font-weight-normal">Search Item IDs</h3>
                      <input type="radio" name="search_option" id="_product" value="product" checked="checked"/> Product<br>
                      <input type="radio" name="search_option" id="_recipe" value="recipe" /> Recipe<br>
                      <input type="text" id="food_search" placeholder="Enter Item Name">
                      <div id="display_item_container">
                        <div id="food_suggestion"></div>
                      </div>
                      <!--Live search end-->
                        <form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <h3 class="h3 mb-3 font-weight-normal">Add An Item to Your Food Log</h3>
                            <label for="inputFoodItem">Add Item by ID</label>
                            <input type="number" id="inputFoodItem" class="form-control" name="addItemID" placeholder="Enter Item ID, Use Search Above">
                            <label for="inputProductUPC">Add Item by UPC</label>
                            <input type="number" id="inputProductUPC" class="form-control" name="productUPC" placeholder="Product UPC">
                            <label for="inputItemQuantity">Enter Quantity</label>
                            <input type="number" id="inputItemQuantity" class="form-control form-control-sm" name="quantity" placeholder="Quantity" required>
                            <button name="update" class="btn btn-sm btn-primary btn-block" type="submit">Add Item</button>
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
                      </aside>

                    </div><!-- /.row -->
                    <div class="row mb-2">
                      <div class="col-md-6">
                        <div class="jumbotron">
                          <h3 class="h3 mb-3 font-weight-normal">Your Weekly Log</h3>





                          <!-- food item table start -->
                          <table class="table table-hover table-dark">
                            <thead>
                              <tr>
                                <th scope="col">Item Name</th>
                                <th scope="col">Item ID</th>
                                <th scope="col">Date</th>
                                <th scope="col">Quantity</th>
                              </tr>
                            </thead>
                            <tbody id="weekly_log_content">
                            <?php include 'weekly_log.php';?>
                            </tbody>
                          </table>
                          <!-- food item table end -->

                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="jumbotron">
                          <form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <h3 class="h3 mb-3 font-weight-normal">Remove An Item</h3>
                            <label for="removeItemId">Which Item Would You Like to Remove?</label>
                            <input type="number" id="removeItemID" class="form-control form-control-sm" name="removeIDVal" placeholder="Item ID" required>
                            <label for="removeDate">From Which Date?</label>
                            <input type="date" id="removeDate" class="form-control form-control-sm" name="removeDateVal" placeholder="Date" required>
                            <button name="remove" class="btn btn-sm btn-primary btn-block" type="submit">Remove Item
                            </button>
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
        <!-- live food search -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="food_search.js"></script>
        <!-- live food search -->
        <script src="update_weekly_log.js"></script>
    </body>
</html>
