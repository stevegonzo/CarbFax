
function query_db($dbconnect,$db_name,$regex)
{
    $query = "(SELECT foodId, name FROM '$db_name' WHERE name LIKE \"$regex\" GROUP BY LENGTH(name) LIMIT 5)";
    $result = mysql_query($query, $dbconnect);
    
    return $result;
}

function search_db($query_string,$db_connect,$db_name)
{
    $query_string = strtolower($query_string);
    $split_string = explode(' ', $query_string);
    $regex = join('%',$split_string);
    $regex = "%$regex%";
    
    return query_db($db_connect,$db_name,$regex);
}

function query_each_perm($perms,$db_name,$dbconnect)
{
    foreach($perms as $perm)
    {
        print_r($perm);
        echo join('%',$perm);
    }
}

function pc_permute($items, $perms = array(),$db_connect,$db_name)
{
    if(empty($items))
    {
        query_each_perm($perms,$db_connect,$db_name);
    }
    else
    {
        for ($i = count($items) - 1; $i >= 0; --$i)
        {
            $newitems = $items;
            $newperms = $perms;
            list($foo) = array_splice($newitems, $i, 1);
            array_unshift($newperms, $foo);
            pc_permute($newitems, $newperms,$db_connect,$db_name);
        }
    }
}
