<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Exploring Wines in a Region</title>
</head>

<body bgcolor="white">
<?php

  function showerror() {
     die("Error " . mysql_errno() . " : " . mysql_error());
  }

  require 'db.php';

  function displayWinesList($connection, $query) {
    
    if (!($result = @ mysql_query ($query, $connection))) {
      showerror();
    }

    $rowsFound = @ mysql_num_rows($result);

    if ($rowsFound > 0) {
      print "Details of your wine search<br>";

      print "\n<table>\n<tr>" .
          "\n\t<th>Wine name</th>" .
          "\n\t<th>Wine variety</th>" .
          "\n\t<th>Year</th>" .
          "\n\t<th>Winery</th>" .
          "\n\t<th>Region</th>" .
          "\n\t<th>Cost in the inventory</th>" .
          "\n\t<th>Quantity on hand</th>" .
          "\n\t<th>Quantity sold</th>" . 
          "\n\t<th>Total sales</th>\n</tr>"; 

      while ($row = @ mysql_fetch_array($result)) {
        
        print "\n<tr>\n\t<td>{$row["wine_name"]}</td>" .
            "\n\t<td>{$row["variety"]}</td>" .
            "\n\t<td>{$row["year"]}</td>" .
            "\n\t<td>{$row["winery_name"]}</td>" .
            "\n\t<td>{$row["region_name"]}</td>" .
            "\n\t<td>{$row["cost"]}</td>" .
            "\n\t<td>{$row["on_hand"]}</td>" .
            "\n\t<td>{$row["qty"]}</td>".
            "\n\t<td>{$row["SUM(items.price)"]}</td>\n</tr>";

      }

      print "\n</table>";
    } 
    
    print "{$rowsFound} records found matching your criteria<br>";
  } 
  
  if (!($connection = @ mysql_connect(DB_HOST, DB_USER, DB_PW))) {
    die("Could not connect");
  }

  // get the user data
  $wineName = $_GET['wineName'];
  $wineryName = $_GET['wineryName'];

  if (!mysql_select_db(DB_NAME, $connection)) {
    showerror();
  }

  // Start a query ...
  $query = "SELECT wine_name, variety, year, winery_name, region_name, cost,on_hand, qty, SUM(items.price) 
FROM wine, grape_variety, winery, region, wine_variety, inventory, items
WHERE wine.wine_id = wine_variety.wine_id
AND wine_variety.variety_id = grape_variety.variety_id
AND winery.region_id = region.region_id
AND wine.winery_id = winery.winery_id
AND wine.wine_id = inventory.wine_id
AND wine.wine_id = items.wine_id";

  if (isset($wineName) && $wineName != "") {
    $query .= " AND wine_name = '{$wineName}'";
  }

  if (isset($wineryName) && $wineryName != "") {
    $query .= " AND winery_name = '{$wineryName}'";
  }

  $query .= " GROUP BY items.wine_id
  ORDER BY wine_name, year";

  displayWinesList($connection, $query, $wineName);
?>
</body>
</html>




