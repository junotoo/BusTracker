<?php
  $sql = "SELECT * FROM user_locations WHERE timestamp >= NOW() - INTERVAL 60 SECOND";
  $locations = my_query($sql, $conn);
?>