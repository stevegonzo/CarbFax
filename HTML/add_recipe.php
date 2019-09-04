<?php
    if ( isset($_GET['recipe_name']) && !empty($_GET['recipe_name']) ) {
        // Database Connection
        $dbconnect = mysql_connect("localhost", "root", "carbfax411");
        if(!$dbconnect){
            die('Cannot connect: ' . mysql_error());
        }

        $db_selected = mysql_select_db("411_project_db", $dbconnect);

        if (!$db_selected) {
          die('Cant use database: ' . mysql_error());
        }

        //Get variables
        $recipe_name = $_GET['recipe_name'];
        $recipe_description = $_GET["recipe_description"];
        $item_name = $_GET["item_name"];
        $item_id = $_GET["item_id"];
        $quantity_unit = $_GET["quantity_unit"];
        $quantity = $_GET["quantity"];
        $new_recipe_id = $_GET["new_recipe_id"];

        // first time $new_recipe_id is created;
        // there can be only one insertion into the recipe table
        if ($new_recipe_id == "undefined") {
            $get_recipe_id_query = "SELECT MAX(foodID) FROM recipes";
            $result = mysql_query($get_recipe_id_query, $dbconnect);
            if (!$result) {
                die("Invalid Query: " . mysql_error());
            }
            $new_recipe_id = mysql_fetch_array($result)[0] + 1;

            $recipe_insert_query = "INSERT INTO recipes (`foodID`, `name`, `calories`, `total_carbs`, `sugar`, `protein`, `total_fat`, `sodium`, `cholesterol`, `directions`) 
            VALUES ('$new_recipe_id', '$recipe_name', 0, 0, 0, 0, 0, 0, 0, '$recipe_description')";
            $result = mysql_query($recipe_insert_query, $dbconnect);
            if (!$result) {
                die("Invalid Query: " . mysql_error());
            }
            # for subsequent contains insertions
            echo $new_recipe_id;
        }
        else {
            $new_recipe_id = (int) $new_recipe_id;
            echo "45 php";
        }
        
        
        $contains_insert_query = "INSERT INTO contains (`recipe_foodID`,`product_foodID`,`quantity`, `measurement_std`, `volume`, `weight`) 
        VALUES ('$new_recipe_id', '$item_id', '$quantity', '', -1, -1)";
        $result = mysql_query($contains_insert_query, $dbconnect);
        if (!$result) {
            die("Invalid Query: " . mysql_error());
        }

        mysql_free_result($result);
        mysql_close($dbconnect);
    }
?>
