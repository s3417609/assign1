<?php
require "db.php";

function selectDistinct ($connection, $tableName, $attributeName, $pulldownName, $defaultValue) 
{
  $defaultWithinResultSet = FALSE;

  $distinctQuery = "SELECT DISTINCT {$attributeName} FROM
  {$tableName} ORDER BY {$attributeName}";

  if (!($resultId = @ mysql_query ($distinctQuery, $connection)))
    showerror();

  print "\n<select name=\"{$pulldownName}\">";

  while ($row = @ mysql_fetch_array($resultId))
  {
    $result = $row[$attributeName];

    if (isset($defaultvalue) && $result == $defaultValue)
      print "\n\t<option selected value=\"{$result}\">{$result}";
    else
      print "\n\t<option value=\"{$result}\">{$result}";
    print "</option>";
  }
  print "\n</select>";
}  

if (!($connection = @ mysql_connect(DB_HOST, DB_USER, DB_PW)))
{
  showerror();
}

if (!mysql_select_db(DB_NAME, $connection)) 
{
  showerror();
}

$wineName = $wineryName = $wineMinStock = $wineMinOrder = $wineMinCost = $wineMaxCost = '';

$err_year = $err_stock = $err_order = $err_min_cost = $err_max_cost = '';

if (isset($_GET['submit']))
{
  $wineName=trim($_GET['wineName']);
  $wineryName= trim($_GET['wineryName']);
  $regionName=$_GET['regionName'];
  $grapeVariety=$_GET['grapeVariety'];
  $wineYearLowBound=$_GET['wineYearLowBound'];
  $wineYearUpBound=$_GET['wineYearUpBound'];
  $wineMinStock=trim($_GET['wineMinStock']);
  $wineMinOrder=trim($_GET['wineMinOrder']);
  $wineMinCost=trim($_GET['wineMinCost']);
  $wineMaxCost=trim($_GET['wineMaxCost']);

  // Year input validation
  if($_GET['wineYearUpBound'] < $_GET['wineYearLowBound'])
  {
    $err_year="*'From' year must be inferior to 'To' year";
  }

  // Stock input validation
  if(!empty($wineMinStock))
  {
    if(is_numeric($wineMinStock))
    {
      if((int)$wineMinStock < 0)
      {
        $err_stock = "*A quantity cannot be negative";
      }
    }
    else
      $err_stock = "*Please input a numeric value.";
  }

  // Order input validation
  if(!empty($wineMinOrder))
  {
    if(is_numeric($wineMinOrder))
    {
      if((int)$wineMinOrder < 0)
      {
        $err_order = "*A quantity cannot be negative";
      }
    }
    else
      $err_order = "*Please input a numeric value.";
  }

  // Min Cost input validation
  if(!empty($wineMinCost))
  {
    if(is_numeric($wineMinCost))
    {
      if((double)$wineMinCost < 0)
      {
        $err_min_cost = "*A cost cannot be negative";
      }
    }
    else
      $err_min_cost = "*Please input a numeric value.";
  }

  // Max Cost input validation
  if(!empty($wineMaxCost))
  {
    if(is_numeric($wineMaxCost))
    {
      if((double)$wineMaxCost < 0)
      {
        $err_max_cost = "*A cost cannot be negative";
      }
    }
    else
      $err_max_cost = "*Please input a numeric value.";
  }

  // Cost range input validation
  if(!empty($wineMinCost) && !empty($wineMaxCost))
  {
    if(is_numeric($wineMinCost) && is_numeric($wineMaxCost))
    {         
      if((double)$wineMinCost>(double)$wineMaxCost)
      {
        $err_min_cost="*Maximum cost must be superior to minimum cost.";
        $err_max_cost="*Maximum cost must be superior to minimum cost.";
      }
    }
  }
  
  if(($err_year == "") && ($err_stock == "") && ($err_order == "") && ($err_min_cost == "") && ($err_max_cost == ""))
  {
    header("Location: results_screen.php?wineName=".$wineName."&wineryName=".$wineryName."&regionName=".$regionName."&grapeVariety=".$grapeVariety."&wineMinStock=".$wineMinStock."&wineMinOrder=".$wineMinOrder."&wineMinCost=".$wineMinCost."&wineMaxCost=".$wineMaxCost."&wineYearLowBound=".$wineYearLowBound."&wineYearUpBound=".$wineYearUpBound);
  }
  
}
?>


<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="mystyle.css">
<title>Wine is wine</title>
</head>
<body>

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
<br>A wine name (or part of a wine name)
  <input type="text" name="wineName" value="">
<br>A winery name (or part of a winery name)
  <input type="text" name="wineryName" value="">


  <?php  print "<br>\nRegion: ";
  selectDistinct($connection, "region", "region_name", "regionName", "All");
  print "<br>\nGrape Variety: ";
  selectDistinct($connection, "grape_variety", "variety", "grapeVariety", "");
  print "<br>\nFrom: ";
  selectDistinct($connection, "wine", "year", "wineYearLowBound", "");
  print "\nto: ";
  selectDistinct($connection, "wine", "year", "wineYearUpBound", "");
  echo "<span class='error'>".$err_year."</span>";

  ?>
  <br>Minimum wine quantity in stock:
  <input type="text" name="wineMinStock" value="">
  <span class="error"><?php echo $err_stock;?></span>

  <br>Minimum wine quantity ordered:
  <input type="text" name="wineMinOrder" value="">
  <span class="error"><?php echo $err_order;?></span>

  <br>Wine minimum cost:
  <input type="text" name="wineMinCost" value="">
  <span class="error"><?php echo $err_min_cost;?></span>
  
  <br>Wine maximum cost:
  <input type="text" name="wineMaxCost" value="">
  <span class="error"><?php echo $err_max_cost;?></span>
 
 <br><input type="submit" name="submit" value="Show Wines">
  </form>

  </body>
  </html>
