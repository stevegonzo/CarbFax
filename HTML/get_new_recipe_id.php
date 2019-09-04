<?php
        // Database Connection
        $dbconnect = mysql_connect("localhost", "root", "carbfax411");
        if(!$dbconnect){
            die('Cannot connect: ' . mysql_error());
        }

        $db_selected = mysql_select_db("411_project_db", $dbconnect);

        if (!$db_selected) {
          die('Cant use database: ' . mysql_error());
        }

        $get_recipe_id_query = "SELECT MAX(foodID) FROM recipes";
        $result = mysql_query($get_recipe_id_query, $dbconnect);
        if (!$result) {
            die("Invalid Query: " . mysql_error());
        }
        $new_recipe_id = mysql_fetch_array($result)[0] + 1;

        # echo the recipe id for insertions into contains table
        echo $new_recipe_id;

        mysql_free_result($result);
        mysql_close($dbconnect);
?>
