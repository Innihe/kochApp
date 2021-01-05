<html>
<?php
  include "../private/crawl.php";
  echo 'Hello World 2<br>';
?>
<form method="post" action="justtesting.php">
    <input type="text" name="ingredients">
    <input type="submit" value="click" name="query">
</form>

<!--

  $searchParamArray = array("kartoffel", "karotte");
  searchUnixKochbuch($searchParamArray);
?> !-->
</html>
