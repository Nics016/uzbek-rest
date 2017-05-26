<?php

$query   = request("query"); // Поисковый запрос
$page    = request("page", 0); // Номер страницы

if (false !== $query) {
    require_once 'HTTP/Request.php';
    $req  = new HTTP_Request("http://xmlsearch.yandex.ru/xmlsearch");
    $req->setMethod(HTTP_REQUEST_METHOD_GET);
    if (strstr($query, "host=") === FALSE ) {
        $full_query = $query.' << host="' . SEARCH_SITE_URL . '"';
    } else {
        $full_query = stripslashes($query);
    }
    $req->addQueryString('query', htmlspecialchars($full_query));
    $req->addQueryString('page', $page);
    $req->addQueryString("maxpassages", 2);
    $req->setHttpVer(HTTP_REQUEST_HTTP_VER_1_0);
    $req->sendRequest(); // Посылаем GET запрос к Яндексу
    $xml_data = $req->getResponseBody();  // Получаем XML ответ от Яндекса
    $xml = new DomDocument();
    $xsl = new DomDocument();
    $xml->loadXML($xml_data);
    $xsl->loadXML(file_get_contents(FUNC_DIR . "core/files/search.xsl"));
    $xsltproc = new XsltProcessor();
    $xsltproc->importStyleSheet($xsl);
    echo $xsltproc->transformToXML($xml);

} else {
    echo '
    <form>
    <label for="query">Поиск</label>
    <input type="text" name="query">
    <input type="hidden" name="state" value="yaxmlsearch">
    <input type="submit" value="Поиск">
    </form>';
}
