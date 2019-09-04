<?php
    // Database Connection
    $dbconnect = mysql_connect("localhost", "root", "carbfax411");
    if ( !$dbconnect ) {
        die('Cannot connect: ' . mysql_error());
    }

    $db_selected = mysql_select_db("411_project_db", $dbconnect);
    if ( !$db_selected ) {
        die('Cant use database: ' . mysql_error());
    }

    //get parameters
    $username = $_GET['username'];
    $foodID = $_GET['id'];
    $date = $_GET['date'];
    $add = intval($_GET['add']);
    $update_query = "UPDATE ate
                        SET quantity = quantity + ?, date = date
                        WHERE username = ? AND
                        DATEDIFF(date, ?) = 0 AND
                        foodID = ?";
    $delete_query = "DELETE FROM ate
                        WHERE username = ? AND
                        DATEDIFF(date, ?) = 0 AND
                        foodID = ?
                        AND quantity = 0";
    $ate_products_query = "SELECT products.name AS name, ate.foodID AS ID, ate.date AS date, ate.quantity AS quantity
                            FROM ate, products
                            WHERE username = ? AND ate.foodID = products.foodID";
    $ate_recipes_query = "SELECT recipes.name AS name, ate.foodID AS ID, ate.date AS date, ate.quantity AS quantity
                            FROM ate, recipes
                            WHERE username = ? AND ate.foodID = recipes.foodID";


    // update quantity and delete if quantity = 0
    // date: yyyy-mm-dd
    try {
        $DB = new PDO('mysql:host=localhost; dbname=411_project_db','root', 'carbfax411');
        $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $DB->beginTransaction();

        $stmt = $DB->prepare($update_query);
        $stmt->execute([$add, $username, $date, $foodID]);

        $stmt = $DB->prepare($delete_query);
        $stmt->execute([$username, $date, $foodID]);

        $stmt = $DB->prepare($ate_products_query);
        $stmt->execute([$username]);
        while ($row = $stmt->fetch()) {
            $food_id = $row['ID'];
            $date = $row['date'];
            $row_id = $username . "&" . $food_id . "&" . $date;
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['ID'] . "</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" .
            "<button name='remove' class='btn btn-sm btn-primary btn-block weekly_log_plus_button' type='submit' id=$row_id>+</button>"
            . $row['quantity']
            . "<button name='remove' class='btn btn-sm btn-primary btn-block weekly_log_minus_button' type='submit' id=$row_id>-</button>"
            . "</td>";
            echo "</tr>";
        }

        $stmt = $DB->prepare($ate_recipes_query);
        $stmt->execute([$username]);
        while ($row = $stmt->fetch()) {
            $food_id = $row['ID'];
            $date = $row['date'];
            $row_id = $username . "&" . $food_id . "&" . $date;
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['ID'] . "</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" .
            "<button name='remove' class='btn btn-sm btn-primary btn-block weekly_log_plus_button' type='submit' id=$row_id>+</button>"
            . $row['quantity']
            . "<button name='remove' class='btn btn-sm btn-primary btn-block weekly_log_minus_button' type='submit' id=$row_id>-</button>"
            . "</td>";
            echo "</tr>";
        }

        $DB->commit();
    } catch(PDOException $e) {
        echo "TEST_CATCH";
        $this->pdo->rollback();
        die("Invalid Query In Transaction");
    }


    // $update_query = "UPDATE ate
    //                  SET quantity = quantity + $add, date = date
    //                  WHERE username = '$username' AND
    //                  SUBSTRING(date, 1, 4) = SUBSTRING('$date', 1, 4) AND
    //                  SUBSTRING(date, 6, 2) = SUBSTRING('$date', 6, 2) AND
    //                  SUBSTRING(date, 9, 2) = SUBSTRING('$date', 9, 2) AND
    //                  foodID = '$foodID'
    //                 ";
    // $update_result = mysql_query($update_query, $dbconnect);
    //
    // if ( !$update_result ) {
    //     die('Invalid Query: ' . mysql_error());
    // }
    //
    // // delete the item with the quantity 0
    // $delete_query = "DELETE FROM ate
    //                  WHERE username = '$username' AND
    //                  SUBSTRING(date, 1, 4) = SUBSTRING('$date', 1, 4) AND
    //                  SUBSTRING(date, 6, 2) = SUBSTRING('$date', 6, 2) AND
    //                  SUBSTRING(date, 9, 2) = SUBSTRING('$date', 9, 2) AND
    //                  foodID = '$foodID' AND
    //                  quantity = 0
    //                 ";
    //
    // $delete_result = mysql_query($delete_query, $dbconnect);
    // if ( !$delete_result ) {
    //     die('Invalid Query: ' . mysql_error());
    // }


    // Query to Get Eaten Items
    // Copied code from weekley_log.php

    // $queryAte = "SELECT products.name AS name, ate.foodID AS ID, ate.date AS date, ate.quantity AS quantity FROM ate, products WHERE username = '$username' and ate.foodID = products.foodID";
    // $ateResult = mysql_query($queryAte, $dbconnect);
    //
    // if ( !$ateResult ) {
    //     die('Invalid Query: ' . mysql_error());
    // }

    // while ( $row = mysql_fetch_assoc($ateResult) ) {
    //     $food_id = $row['ID'];
    //     $date = $row['date'];
    //     $row_id = $username . "&" . $food_id . "&" . $date;
    //     echo "<tr>";
    //     echo "<td>" . $row['name'] . "</td>";
    //     echo "<td>" . $row['ID'] . "</td>";
    //     echo "<td>" . $row['date'] . "</td>";
    //     echo "<td>" .
    //     "<button name='remove' class='btn btn-sm btn-primary btn-block weekly_log_plus_button' type='submit' id=$row_id>+</button>"
    //     . $row['quantity']
    //     . "<button name='remove' class='btn btn-sm btn-primary btn-block weekly_log_minus_button' type='submit' id=$row_id>-</button>"
    //     . "</td>";
    //     echo "</tr>";
    // }

    // Close Database Connection
    mysql_free_result($update_result);
    mysql_free_result($ateResult);
    mysql_close($dbconnect);
?>
