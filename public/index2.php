<html>
<?php
  include "../private/crawl.php";
  echo 'Hello World 2<br>';

  $searchParamArray = array("karotte", "kürbis", "kartoffel");
  searchUnixKochbuch($searchParamArray);
?>
</html>
