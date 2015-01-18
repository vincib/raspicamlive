<?php
require_once("header.php");
require_once("common.php");

?>
            <div class="row">
              <div class="col-md-12" role="main" id="storagedir">


        <h1>Storage content</h1>
<?php
  $records=array();
  $d=opendir(STORAGEPATH);
while (($c=readdir($d))!==false) {
  if (preg_match("#^rec_#",$c) && is_file(STORAGEPATH."/".$c."/meta.json")) {
    $records[$c]=@json_decode(file_get_contents(STORAGEPATH."/".$c."/meta.json"),true);
  }
}
closedir($d);

if (!count($records)) {
  echo "<h3>No record in storage as of now</h3>";
} else {
?>
  <table class="table">
<tr><th></th><th>Record Name</th><th>Start Time</th><th>End Time</th><th>Duration</th><th>Title</th></tr>
<?php
$tz=date_default_timezone_get();
  $now=time();
    foreach($records as $name=>$record) {
      echo "<tr>";
      echo "<td>";
      if ($record["end_time"]!=0) {
	echo "<a href=\"#\"><img src=\"assets/save.png\" alt=\"save\" title=\"save\"/></a>";
	echo "&nbsp; &nbsp;";
	echo "<a href=\"#\"><img src=\"assets/delete.png\" alt=\"delete\" title=\"delete\"/></a>";
      }
      echo "</td>";
      echo "<td>".$name."</td>";
      date_default_timezone_set($tz);
      echo "<td>".date("Y-m-d H:i",$record["start_time"])."</td>";
      if ($record["end_time"]==0) {
	echo "<td>-- still recording --</td>";
      } else {
	echo "<td>".date("Y-m-d H:i",$record["end_time"])."</td>";
      }
      date_default_timezone_set("UTC");
      if ($record["end_time"]==0) {
	echo "<td>".date("H:i:s",($now-$record["start_time"]))."</td>";
      } else {
	echo "<td>".date("H:i:s",($record["end_time"]-$record["start_time"]))."</td>";
      }
      echo "<td>".htmlentities($record["title"])."</td>";
      echo "</tr>";
    }
?>
  </table>
    <?php } ?>

              </div><!-- col12 -->
</div>

<?php
require_once("footer.php"); 
?>
