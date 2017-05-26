<?php
function statePath($state) {
    $path0 = SITE_DIR . "engine/core/$state.php";
    $path1 = FUNC_DIR . "state/$state.php";
    if (file_exists($path0)) {
        return $path0;
    } elseif (file_exists($path1)) {
        return $path1;
    } else {
        return false;
    }
}

if (empty($state)) {
    if (! empty($pageId)) {
        $row = $sql->select('Content', array('Id' => $pageId), Sql::ONE);
        if ($subm == "explore") {
            print("<ul>");
            BuildSubmExpm($pageId);
            print("</ul>");
        } else {
            if (! empty($row["Mech"])) {
                if (statePath($row["Mech"])) {
                    include_once(statePath($row["Mech"]));
                }
            } elseif (! empty($row['Mirror'])) {
                PagePost($row['Mirror']);
            } else {
                PagePost($pageId);
            }
        }
    }
} else {
    if (statePath($state)) {
        include_once(statePath($state));
    } else {
        header("HTTP/1.1 404 Not Found");
//        $message = "ERR: Обращение к несуществующему состоянию (STATE) $state " . SITE_URL;
//        adminEmail(dumpServerVars(), $message, SITE_URL);
    }
}
?>
