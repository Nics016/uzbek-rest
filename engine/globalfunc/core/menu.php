<?php
$query  = "select * from Content where Parent = 0 and Active = 1 order by Sort asc;";
$result = mysql_query($query);
if ($result) {
    while ($row = mysql_fetch_array($result)) {
        if ($mainId == $row["Id"]) {
            printf("\t\t\t\t\t\t\t\t\t\t\t<tr><td class=current><a href=\"/?pageId=%d\">%s</a></td></tr>\n", $row["Id"], $row["MenuName"]);
        } else {
            printf("\t\t\t\t\t\t\t\t\t\t\t<tr><td ><a href=\"/?pageId=%d\">%s</a></td></tr>\n", $row["Id"], $row["MenuName"]);
        }
    }
} else {

}
?>

