<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Wine is wine</title>
</head>
<body>
  <form action="results_screen.php" method="GET">
    <br>A wine name (or part of a wine name)
    <input type="text" name="wineName" value="">
    <br>A winery name (or part of a winery name)
    <input type="text" name="wineryName" value="">

    <br> <?php
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

        print "<br>\nRegion: ";
        selectDistinct($connection, "region", "region_name", "regionName", "All");
        print "<br>\nGrape Variety: ";
        selectDistinct($connection, "grape_variety", "variety", "grapeVariety", "All");
        print "<br>\nFrom: ";
        selectDistinct($connection, "wine", "year", "wineYearLowBound", "----");
        print "\nto: ";
        selectDistinct($connection, "wine", "year", "wineYearUpBound", "----");

    ?>
    <br>Minimum wine quantity in stock:
    <input type="text" name="wineMinStock" value="">
    <br>Minimum wine quantity ordered:
    <input type="text" name="wineMinOrder" value="">
    <br>Wine minimum cost:
    <input type="text" name="wineMinCost" value="">
    <br>Wine maximum cost:
    <input type="text" name="wineMaxCost" value="">
 
    <br><input type="submit" name="submit" value="Show Wines">
  </form>
</body>
</html>






