<?php
include_once '../../config.php';
include_once SITE_DIR . '/core/functions/loader.php';
$sql = new Sql(Config::getInstance()->prop('dsn'));
if ($_GET['click']) {
   $sql->update('BannersToPages', array('Clicks' => 'unesc|Clicks + 1'), array('Id' => $_GET['btpid']));
   $sql->insert('BannerTime', array('BannerId' => $_GET['bannerid'], 'Type' => 'click', 'PageId' => $_GET['pageid']));
   exit();
}
if (isset($_GET['id'])) {
    $row = $sql->queryRow(sprintf("select * from BannersToPages where Id = '%d'", $_GET['id']));
    if ($searchbot == 0) {
        $clickCode  = '<html><head>';
        $clickCode .= sprintf('<script type="text/javascript"><!--
                var saveScript = \'<script type="text/javascript" src="%sengine/hcore/click.php?click=1&pageid=%d&bannerid=%d&btpid=%d"></script>\';
                document.write(saveScript);
                //--></script>', SITE_URL, $row['pageid'], $row['bannerid'], $_GET['id']);
        $clickCode .= sprintf('<script type="text/javascript">document.location = "%s"</script>', $sql->queryOne(sprintf("SELECT Url FROM Banners WHERE Id = '%d'", $row['bannerid'])));
        $clickCode .= '</head></html>';
        echo $clickCode;
    }
}
?>