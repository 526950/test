<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:core="my" xmlns:func="http://exslt.org/functions" xmlns:str="http://exslt.org/strings" xmlns:php="http://php.net/xsl" extension-element-prefixes="func str php">
	<xsl:variable name="lowCase">
		абвгдеёжзийклмнопрстуфхцчшщыъьэюяabcdefghijklmnopqrstuvwxyz
	</xsl:variable>
	<xsl:variable name="upCase">
		АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЪЬЭЮЯABCDEFGHIJKLMNOPQRSTUVWXYZ
	</xsl:variable>

	<func:function name="core:strtoupper" as="xsl:string">
		<xsl:param name="str" as="xsl:string" />
		<func:result select="translate($str, $lowCase, $upCase)" />
	</func:function>

	<func:function name="core:strtolower" as="xsl:string">
		<xsl:param name="str" as="xsl:string" />
		<func:result select="translate($str, $upCase, $lowCase)" />
	</func:function>

	<func:function name="core:str_replace" as="xsl:string">
		<xsl:param name="text" />
		<xsl:param name="replace" />
		<xsl:param name="by" />
		<func:result>
			<xsl:choose>
				<xsl:when test="contains($text, $replace)">
					<xsl:value-of select="substring-before($text,$replace)" />
					<xsl:value-of select="$by" />
					<xsl:call-template name="str_replace">
						<xsl:with-param name="text" select="substring-after($text,$replace)" />
						<xsl:with-param name="replace" select="$replace" />
						<xsl:with-param name="by" select="$by" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$text" />
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>

	<func:function name="core:nl2br" as="xsl:string">
		<xsl:param name="string" />
		<func:result>
			<xsl:value-of select="core:str_replace(php:function('nl2br', concat($string, '')),'&lt;br /&gt;', '&lt;br&gt;')" disable-output-escaping="yes" />
		</func:result>
	</func:function>

	<func:function name="core:crop" as="xsl:string">
		<xsl:param name="str" />
		<xsl:param name="num" select="500" />
		<xsl:param name="end" select="'...'" />
		<xsl:param name="delim" select="' '" />
		<func:result>
			<xsl:choose>
				<xsl:when test="string-length($str) &gt; $num">
					<xsl:variable name="txt" select="substring($str, 1, $num)" />
					<xsl:for-each select="str:tokenize($txt, $delim)">
						<xsl:if test="position() &lt; last()">
							<xsl:value-of select="concat(., $delim )" />
						</xsl:if>
					</xsl:for-each>
					<xsl:value-of select="$end" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$str" />
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>

	<func:function name="core:end" as="xsl:string">
		<xsl:param name="number" select="0" />
		<xsl:param name="words" />
		<!-- Строка вариантов склонения (3 штуки), разделённых запятой. например "франшиза,франшизы,франшиз" -->
		<xsl:variable name="word1" select="substring-before($words,',')" />
		<xsl:variable name="word2" select="substring-before(substring-after($words,','),',')" />
		<xsl:variable name="word3" select="substring-after(substring-after($words,','),',')" />
		<func:result>
			<xsl:choose>
				<xsl:when test="(($number mod 100) &gt;= 5) and (($number mod 100) &lt;= 20)">
					<xsl:value-of select="$word3" />
				</xsl:when>
				<xsl:when test="$number mod 10 = 1">
					<xsl:value-of select="$word1" />
				</xsl:when>
				<xsl:when test="(($number mod 10) &gt;= 2) and (($number mod 10) &lt;= 4)">
					<xsl:value-of select="$word2" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$word3" />
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>

	<func:function name="core:date" as="xsl:string">
		<xsl:param name="time" select="''" />
		<xsl:param name="format" select="'d.m.Y'" />
		<func:result select="php:function('date_xsl', concat($time, ''), concat($format, ''))" />
	</func:function>

	<func:function name="core:translit" as="xsl:string">
		<xsl:param name="str" select="''" />
		<func:result select="php:function('translitUrl', concat($str,''))" />
	</func:function>

	<func:function name="core:translitRu" as="xsl:string">
		<xsl:param name="str" select="''" />
		<func:result select="php:function('translitUrlRu', concat($str,''))" />
	</func:function>

	<func:function name="core:exec" as="xsl:string">
		<xsl:param name="function" select="''" />
		<func:result select="php:function($function)" />
	</func:function>

	<func:function name="core:translitUrl" as="xsl:string">
		<xsl:param name="id" />
		<xsl:param name="alias" />
		<xsl:param name="query" />
		<xsl:param name="get">
			<xsl:choose>
				<xsl:when test="$get='/1/'">
					<xsl:value-of select="concat('/',//requests/get/path,'/')" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$get" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:param>
		<func:result>
			<xsl:choose>
				<xsl:when test="$alias!=''">
					<xsl:value-of select="concat($get, $id, '-', $alias, $query)" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="concat($get, '?ITEM=', $id)" />
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>

	<func:function name="core:bitmask" as="xsl:string">
		<xsl:param name="num1" />
		<xsl:param name="num2" />
		<func:result select="($num1 mod ($num2*2))-($num1 mod ($num2))" />
	</func:function>

	<func:function name="core:is_login">
		<func:result>
			<xsl:choose>
				<xsl:when test="//mod_users/sub_login/user/id">
					<xsl:value-of select="//mod_users/sub_login/user/id" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="0" />
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>

	<func:function name="core:format-number" as="xsl:string">
		<xsl:param name="number" />
		<xsl:param name="format" select="'# ###.00'" />
		<xsl:param name="divider" select="'space'" />
		<func:result>
			<xsl:choose>
				<xsl:when test="$number!=''">
					<xsl:value-of select="format-number($number, $format, $divider)" />
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>
</xsl:stylesheet>