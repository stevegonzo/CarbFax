<?php
    if ( isset($_GET['name']) && !empty($_GET['name']) ) {
        // Database Connection
        $dbconnect = mysql_connect("localhost", "root", "carbfax411");
        if(!$dbconnect){
            die('Cannot connect: ' . mysql_error());
        }

        $db_selected = mysql_select_db("411_project_db", $dbconnect);

        if (!$db_selected) {
          die('Cant use database: ' . mysql_error());
        }

        function query_db($dbconnect,$db_name,$regex) {
            $query = "SELECT foodId, name FROM $db_name WHERE name LIKE \"$regex\" GROUP BY LENGTH(name)";
            $result = mysql_query($query, $dbconnect);
            if (!$result) {
                die("Invalid Query: ". mysql_error());
            }
            return $result;
        }

        function search_db($query_string,$db_connect,$db_name) {
            $query_string = strtolower($query_string);
            $split_string = explode(' ', $query_string);
            $regex = join('%',$split_string);
            $regex = "%$regex%";
            return query_db($db_connect,$db_name,$regex);
        }

        $db_name ='';
        $string = $_GET['name'];
        if ($_GET['option'] == 'product') {
            $db_name = products;
        }
        else {
            $db_name = recipes;
        }

        $searchResults = search_db($string, $dbconnect, $db_name);
        $suggestions_string = '';
        while ($row = mysql_fetch_assoc($searchResults)) {
            $name_and_id = str_replace(' ', '_', $row['name']) . "*" . str_replace(' ', '_', $row['foodId']);
            echo "<div class='food_search_item' id=$name_and_id><p>" . $row['name'] . ", " . $row['foodId'] . "</p></div>";
        }
    }
?>
