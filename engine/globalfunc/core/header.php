<?php
if ($ptitle != '') {
	$pageTitle = $ptitle;
} elseif ($Title != '') {
	$pageTitle = $Title;
} elseif ($pname != '') {
	$pageTitle = $pname;
} elseif ($Name != '') {
	$pageTitle = $Name;
} elseif (! empty($row['MenuName'])) {
    $pageTitle = $row['MenuName'];
} else {
    $pageTitle = '';
}
$GLOBALS['pageTitle'] = $pageTitle;

if (! empty($GLOBALS['config']['mode'])) {
    switch ($GLOBALS['config']['mode']) {
        case 'standard':
            echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                "http://www.w3.org/TR/html4/loose.dtd">';
            break;
        case 'xhtml':
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            break;
        case 'xhtmlt':
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            break;
            default:
            echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
    }
} else {
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=windows-1251">
<meta name="Description" content="<?php echo ($pmetadesc != '') ? $pmetadesc : $MetaDesc ?>">
<meta name="Keywords" content="<?php echo ($pmetakey != '') ? $pmetakey : $MetaKey ?>">
<title><?php echo $pageTitle; ?></title>
<?php 
if(file_exists("css/reset.css"))
echo '<link rel="stylesheet" type="text/css" href="/css/reset.css">';
?>
<link rel="stylesheet" type="text/css" href="/css/style.css">
<?php if ((! empty($cssLinks) && is_array($cssLinks))): ?>
    <?php foreach ($cssLinks as $link): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $link; ?>">
    <?php endforeach; ?>
<?php endif; ?>
<script type="text/javascript" src="/js/sitefunc.js"></script>
