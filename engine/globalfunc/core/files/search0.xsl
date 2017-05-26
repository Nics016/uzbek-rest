<?xml version="1.0" encoding="windows-1251"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.1">
<xsl:output method="html" indent="yes"/>


<!-- следующие параметры будут переписаны обработчиком   -->
<xsl:param name="script" select="''"/>
<xsl:param name="images" select="'http://www.yandex.ru/i/'"/>

<!-- Определим для удобства часто используемые переменные -->
<xsl:variable name="page" select="string(/yandexsearch/request/page)"/>
<xsl:variable name="reqid" select="string(/yandexsearch/response/reqid)"/>
<xsl:variable name="query" select="string(/yandexsearch/request/query)"/>

<!-- Формирование строки с группировкой -->
<xsl:variable name="grouping">mode=<xsl:value-of select="/yandexsearch/request/groupings/groupby/@mode"/>.attr=<xsl:value-of select="/yandexsearch/request/groupings/groupby/@attr"/>.groups-on-page=<xsl:value-of select="/yandexsearch/request/groupings/groupby/@groups-on-page"/>.docs-in-group=<xsl:value-of select="/yandexsearch/request/groupings/groupby/@docs-in-group"/>.curcateg=<xsl:value-of select="/yandexsearch/request/groupings/groupby/@curcateg"/></xsl:variable>

<xsl:variable name="grouping_esc">mode%3D<xsl:value-of select="/yandexsearch/request/groupings/groupby/@mode"/>.attr%3D<xsl:value-of select="/yandexsearch/request/groupings/groupby/@attr"/>.groups-on-page%3D<xsl:value-of select="/yandexsearch/request/groupings/groupby/@groups-on-page"/>.docs-in-group%3D<xsl:value-of select="/yandexsearch/request/groupings/groupby/@docs-in-group"/>.curcateg%3D<xsl:value-of select="/yandexsearch/request/groupings/groupby/@curcateg"/></xsl:variable>

<!-- Этот элемент не должен производить какой-либо вывод -->
<xsl:template match="request" />

<xsl:template name="printoption">
    <xsl:param name="optvalue" select="''"/>
    <xsl:param name="optcurrent" select="''"/>
    <xsl:param name="optname" select="''"/>
    <xsl:choose>
        <xsl:when test="$optvalue = $optcurrent">
            <option value="{$optvalue}" selected="selected"><xsl:value-of select="$optname"/></option>
        </xsl:when>
        <xsl:otherwise>
            <option value="{$optvalue}"><xsl:value-of select="$optname"/></option>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="printcheck">
    <xsl:param name="value" select="''"/>
    <xsl:param name="current" select="''"/>
    <xsl:param name="name" select="''"/>
    <xsl:choose>
        <xsl:when test="$value = $current">
           <input type="checkbox" name="{$name}" value="{$value}" checked="checked" />
        </xsl:when>
        <xsl:otherwise>
           <input type="checkbox" name="{$name}" value="{$value}" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="printradio">
    <xsl:param name="value" select="0"/>
    <xsl:param name="current" select="0"/>
    <xsl:param name="name" select="''"/>
    <xsl:choose>
        <xsl:when test="$value = $current">
           <input type="radio" name="{$name}" value="{$value}" checked="checked" />
        </xsl:when>
        <xsl:otherwise>
           <input type="radio" name="{$name}" value="{$value}" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Здесь рисуем шапку, подвал и форму запроса -->
<xsl:template match="response">
<html>
<head>
<title>Яndex: список найденных документов</title>
<style>
.k {color:black}
hr {color: #C5C8D0}
p {margin : 0;}
</style>
</head>
<body bgcolor="ffffff" link="0000cc">
<blockquote>
<form name="search" method="get" action="{$script}">
<table border="0" cellspacing="0" cellpadding="4">

<tr bgcolor="ffcc00">
<td><b>Запрос:</b></td>
<td><input size="54" name="text" value="{$query}"/></td>
<td><table border="0" cellspacing="0" cellpadding="1">
<td><input type="submit" value="&#160;Найти&#160;"/>
</td></table></td>
</tr>

<tr bgcolor="ffcc00" valign="middle">
<td>&#160;</td>
<td style="padding:0px"><font size="2">
<xsl:call-template name="printcheck">
    <xsl:with-param name="name" select="'oldreq'"/>
    <xsl:with-param name="value" select="$reqid"/>
    <xsl:with-param name="current" select="0"/>
</xsl:call-template>
&#160;
<label for="c2">искать в&#160;найденном</label></font></td>
<td>&#160;</td>
</tr>
</table>
</form>

<hr noshade="noshade" size="1"/>

<xsl:variable name="err" select="./error"/>
<xsl:choose>
    <xsl:when test="string-length($err)&gt;0">
       <xsl:value-of select="$err"/>
    </xsl:when>
    <xsl:otherwise>
        <table border="0" cellpadding="5">
        <tr><td valign="top">
        <p style="margin-left:10px;">
        <font size="2">
        <b>Результат поиска: </b> <xsl:value-of select="found[@priority='all']"/> документов<br/>
        <b>Статистика слов: </b> <i><xsl:value-of select="wordstat"/></i>
        </font>
        </p>
        </td></tr>
        </table>
        <xsl:apply-templates select="results"/>
    </xsl:otherwise>
</xsl:choose>

</blockquote>
<hr noshade="noshade" size="1"/>
<table width="100%" border="0" cellspacing="0" cellpadding="12">
<tr valign="top">
<td align="right"><font size="2">Copyright &#169; 1997&#151;2003
<a href="http://www.yandex.ru/">Яndex</a></font></td>
</tr>
</table>
</body></html>

</xsl:template>

<xsl:template match="results">
    <xsl:apply-templates select="grouping"/>
</xsl:template>

<xsl:template name="pagelink">
    <xsl:param name="pagenum" select="0"/>
    <xsl:param name="curpage" select="0"/>
    <xsl:if test="$pagenum&gt;0">&#160;|&#160;</xsl:if>
    <xsl:choose>
        <xsl:when test="$pagenum = $curpage">
            <xsl:value-of select="$pagenum+1"/>
        </xsl:when>
        <xsl:otherwise>
            <a href="{$script}?query={$query}&amp;groupby={$grouping_esc}&amp;page={$pagenum}">
            <xsl:value-of select="$pagenum+1"/></a>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="pagelinks">
    <xsl:param name="pagemin" select="0"/>
    <xsl:param name="pagemax" select="0"/>
    <xsl:param name="pagenum" select="0"/>
    <xsl:param name="curpage" select="0"/>
    <xsl:call-template name="pagelink">
        <xsl:with-param name="pagenum" select="$pagenum"/>
        <xsl:with-param name="curpage" select="$curpage"/>
    </xsl:call-template>
    <xsl:if test="$pagenum&lt;$pagemax">
        <xsl:call-template name="pagelinks">
            <xsl:with-param name="pagenum" select="$pagenum + 1"/>
            <xsl:with-param name="pagemax" select="$pagemax"/>
            <xsl:with-param name="pagemin" select="$pagemin"/>
            <xsl:with-param name="curpage" select="$curpage"/>
        </xsl:call-template>
    </xsl:if>
</xsl:template>

<xsl:template match="grouping">
    <xsl:variable name="curpage" select="number(page)"/>
    <xsl:variable name="groupcount" select="number(found[@priority='all'])"/>
    <xsl:variable name="first" select="number(page/@first)"/>
    <xsl:variable name="last" select="number(page/@last)"/>
    <xsl:variable name="pagemax" select="ceiling($groupcount div @groups-on-page)-1"/>
    <xsl:variable name="pagemin">
        <xsl:choose>
          <xsl:when test="number($curpage)-5&gt;0"><xsl:value-of select="number($curpage)-5"/></xsl:when>
          <xsl:otherwise><xsl:value-of select="0"/></xsl:otherwise>
       </xsl:choose>
    </xsl:variable>
    <p>
    <xsl:choose>
        <xsl:when test="@docs-in-group&gt;1"><xsl:text>Группы документов </xsl:text></xsl:when>
        <xsl:otherwise><xsl:text>Документы </xsl:text></xsl:otherwise>
    </xsl:choose>
    <xsl:value-of select="$first"/> - <xsl:value-of select="$last"/> из
    <xsl:value-of select="$groupcount"/>.
    </p>
    <ol start="{$first}">
        <xsl:apply-templates select="group"/>
    </ol>
    <hr noshade="noshade" size="1"/>
    Страницы:&#160;
    <xsl:if test="$curpage&gt;0">
        <a HREF="{$script}?query={$query}&amp;groupby={$grouping_esc}&amp;page={number($curpage)-1}">&lt;&lt;&#160;Назад</a>&#160;
    </xsl:if>
    <xsl:call-template name="pagelinks">
        <xsl:with-param name="curpage" select="$curpage"/>
        <xsl:with-param name="pagemin" select="$pagemin"/>
        <xsl:with-param name="pagenum" select="$pagemin"/>
        <xsl:with-param name="pagemax">
            <xsl:choose>
              <xsl:when test="$pagemax&gt;number($curpage)+5"><xsl:value-of select="number($curpage)+5"/></xsl:when>
              <xsl:otherwise><xsl:value-of select="$pagemax"/></xsl:otherwise>
           </xsl:choose>
        </xsl:with-param>
   </xsl:call-template>
   <xsl:if test="$last&lt;$groupcount">
      &#160;|&#160;<a HREF="{$script}?query={$query}&amp;groupby={$grouping_esc}&amp;page={number($curpage)+1}">Дальше&#160;&gt;&gt;</a>
   </xsl:if>
</xsl:template>

<xsl:template match="group">
    <li><xsl:apply-templates select="doc"/></li>
</xsl:template>

<xsl:template match="doc">
    <font size="3">
    <a href="{url}"><xsl:apply-templates select="title"/></a></font>
    <br/><dl><dd>
    <xsl:apply-templates select="headline"/>
    <xsl:apply-templates select="passages"/>
    <a href="{url}"><xsl:value-of select="url"/></a><xsl:text>, </xsl:text>
    <xsl:variable name="sz" select="number(size)"/>
    <xsl:choose>
      <xsl:when test="$sz&gt;1024">
        <xsl:value-of select="ceiling($sz div 1024)"/><xsl:text> Кб</xsl:text>
      </xsl:when>
      <xsl:otherwise>
          <xsl:value-of select="$sz"/><xsl:text> б</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:variable name="tm" select="string(modtime)"/>
    <xsl:variable name="year" select="substring($tm, 1, 4)"/>
    <xsl:variable name="month" select="substring($tm, 5, 2)"/>
    <xsl:variable name="day" select="substring($tm, 7, 2)"/>
    <xsl:variable name="hour" select="substring($tm, 10, 2)"/>
    <xsl:variable name="minute" select="substring($tm, 12, 2)"/>
    <xsl:variable name="second" select="substring($tm, 14, 2)"/>
    <xsl:text>, </xsl:text>
    <xsl:value-of select="$day"/><xsl:text>-</xsl:text>
    <xsl:value-of select="$month"/><xsl:text>-</xsl:text>
    <xsl:value-of select="$year"/><xsl:text>, </xsl:text>
    <i>
    <xsl:value-of select="$hour"/><xsl:text>:</xsl:text>
    <xsl:value-of select="$minute"/><xsl:text>:</xsl:text>
    <xsl:value-of select="$second"/>
    </i>
    </dd></dl><br/>
</xsl:template>

<xsl:template match="passages">
    <xsl:apply-templates select="passage"/>
</xsl:template>

<xsl:template match="passage">
    <p> *** <xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="headline">
    <xsl:apply-templates/>
</xsl:template>

<xsl:template match="hldoc">
    <xsl:value-of select="." disable-output-escaping="yes"/>
</xsl:template>

<xsl:template match="hlword">
<b><xsl:value-of select="." disable-output-escaping="yes"/></b>
<xsl:if test="following-sibling::*"><xsl:text> </xsl:text></xsl:if>
</xsl:template>


<xsl:template match="statistics">
<A NAME="YANDEX_BOTTOM"></A>
</xsl:template>

</xsl:stylesheet>