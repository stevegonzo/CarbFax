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
        $new_recipe_id = $_GET["new_recipe_id"];

        $recipe_insert_query = "INSERT INTO recipes (`foodID`, `name`, `calories`, `total_carbs`, `sugar`, `protein`, `total_fat`, `sodium`, `cholesterol`, `descriptions`) 
        VALUES ('$new_recipe_id', '$recipe_name', 0, 0, 0, 0, 0, 0, 0, '$recipe_description')";
        $result = mysql_query($recipe_insert_query, $dbconnect);
        if (!$result) {
            die("Invalid Query: " . mysql_error());
        }
        echo "recipes updated";

        mysql_free_result($result);
        mysql_close($dbconnect);
    }
?>
