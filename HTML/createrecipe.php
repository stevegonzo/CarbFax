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

        // Close Database Connection
        mysql_free_result($result);
        mysql_close($dbconnect);
    }
?>

<!-- ***** -->

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
    <!-- recipe creation form CSS -->
    <link rel="stylesheet" href="create_recipe.css">
    <title>Create a Recipe</title>
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

                <div class="jumbotron p-4 p-md-5 text-white rounded bg-dark" >
                    <div class="col-md-6 px-0">
                        <h1 class="display-4 font-italic">Create a Recipe</h1>
                    </div>

                </div>

                <main role="main" class="container">
                    <div class="row mb-2">
                      <div class="col-md-10">
                        <div class="jumbotron" style="width: 1100px;">
<!-- form begin -->
<div id="recipe_form_container">
  <div id="recipe_info_box">
    <div class="flex_dummy">
      <p>Recipe Name</p>
    </div>
    <div class="flex_dummy">
      <input type="text" id="recipe_name_input" placeholder="Enter the Recipe Name">
    </div>
    <div class="flex_dummy">
      <p>Recipe Description</p>
    </div>
    <div class="flex_dummy">
      <textarea style="height:200px;" id="recipe_description_input" placeholder="Enter the Recipe Description"></textarea>
    </div>
    <hr>
    <div class="flex_dummy">
      <input type="radio" name="search_option" id="_product" value="product" checked="checked"/> Product<br>    
      <input type="radio" name="search_option" id="_recipe" value="recipe" /> Recipe<br>
    </div>
    <div class="flex_dummy">
      <input type="text" id="food_search" placeholder="Enter Item Name">
    </div>
    <div id="display_item_container">
      <div id="food_suggestion"></div>
    </div>       
    <hr>     
    <div class="flex_dummy">
      <p>Item selected from the food suggestion box above: </p>
    </div>
    <div id="item_selected_box">
      <div id="item_selected_text"></div>
    </div>
    <div class="flex_dummy">
      <p>Quantity</p>
    </div>
    <div class="flex_dummy">
      <input type="text" id="quantity_input" placeholder="Enter the Quantity">
    </div>
    <div class="flex_dummy">
      <input type="radio" name="quantity_unit" id="_std_unit" value="std_unit" checked="checked"/> std_unit   
      <input type="radio" name="quantity_unit" id="_volume" value="volumn" /> volume
      <input type="radio" name="quantity_unit" id="_weight" value="weight" /> weight
    </div>
    <hr>
    <div class="flex_dummy">
      <button name="add_item_button" class="btn btn-sm btn-primary btn-block" id="_add_item_button" type="submit">Add Item</button> 
    </div>
  </div>
  <div id="added_item_box">
    <table class="table table-hover table-dark">
      <thead>
        <tr>
          <th scope="col">Item Name</th>
          <th scope="col">Item ID</th>
          <th scope="col">Quantity Unit</th>
          <th scope="col">Quantity</th>
          <th scope="col">Delete Item</th>
        </tr>
      </thead>
      <tbody id="items_added_content">
      </tbody>
    </table>
    <div id="submit_recipe_btn_wrapper">
      <button name="submit_recipe" class="btn btn-sm btn-primary btn-block" id="submit_recipe_btn" type="submit">Submit Recipe</button> 
    </div>
    <div id="recipe_added_msg">
    </div>
  </div>
</div>
<!-- form end -->
                        </div>
                      </div>
<!--
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
-->
                      <div></div>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="create_recipe.js"></script>
    </body>
</html>
