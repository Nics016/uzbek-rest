<?php
$newsId = request('newsId', 0, 'int');
$sql    = new Sql(Config::val('dsn'));
$newsPage = $sql->queryOne("select * from Content where Mech = 'news'");
if ($newsId) {
    $news = array();
    $news = $sql->queryRow("select * from Content where Id = '$newsId'");
    $news['date']    = String::dbDate('d.m.Y', $news['DateT']);
    $news['comment'] = $news['Comment'];
    $news['content'] = $news['Content'];
} else {
    $result = $sql->query("select * from Content where Parent = '$newsPage' Order by DateT Desc");
    $i = 0;
    $allNews = array();
    while ($row = $result->fetchRow()) {
        $date = String::dbDate('d.m.Y', $row["DateT"]);
        $content = strip_tags($row["Content"]);
        $pos = strpos(substr($content, 150, strlen($content) - 150), ".");
        if ($pos === false) {
            $posit = 150;
        } else {
            $posit = $pos + 150;
        }
        $allNews[$i]['date']    = String::dbDate('d.m.Y', $row["DateT"]);
        $allNews[$i]['comment'] = $row["Comment"];
        $allNews[$i]['content'] = substr($content, 0, $posit);
        $allNews[$i]['link']    = href(array('pageId' => $newsPage, 'newsId' => $row['Id']));
        $i++;
    }
}
?>
<!-- отображение новостей -->
<?php if ($newsId): ?>
<div class="newsItem">
   <p class="date"><?php echo $news['date']; ?></p>
   <h1><?php echo $news['comment']; ?></h1>
   <p class="content"><?php echo $news['content']; ?></p>
</div>
<?php else: ?>
<div class="newsList">
  <?php foreach ($allNews as $news): ?>
    <div class="newsItem">
       <p class="date"><?php echo $news['date']; ?></p>
       <h2><a href="<?php echo $news['link']; ?>"><?php echo $news['comment']; ?></a></h2>
       <p class="content"><?php echo $news['content']; ?></p>
    </div>

  <?php endforeach; ?>
</div>
<?php endif; ?>