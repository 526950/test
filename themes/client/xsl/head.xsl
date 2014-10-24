<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:template name="head">
		<base href="{domain}" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1" />
		<xsl:choose>
			<xsl:when test="mod_meta_tags/title!=''">
				<title>
					<xsl:value-of select="mod_meta_tags/title" disable-output-escaping="yes" />
				</title>
			</xsl:when>
			<xsl:otherwise>
				<title>
					<xsl:value-of select="//section/node/current_name" disable-output-escaping="yes" />
				</title>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="mod_meta_tags/description!=''">
				<meta name="description">
					<xsl:attribute name="content">
              <xsl:value-of select="mod_meta_tags/description" disable-output-escaping="yes" />
            </xsl:attribute>
				</meta>
			</xsl:when>
			<xsl:otherwise>
				<meta name="description">
					<xsl:attribute name="content">
              <xsl:value-of select="//section/node/current_name" disable-output-escaping="yes" />
            </xsl:attribute>
				</meta>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="mod_meta_tags/keywords!=''">
				<meta name="description">
					<xsl:attribute name="content">
              <xsl:value-of select="mod_meta_tags/keywords" disable-output-escaping="yes" />
            </xsl:attribute>
				</meta>
			</xsl:when>
			<xsl:otherwise>
				<meta name="keywords" content="#KEYWORDS#" />
			</xsl:otherwise>
		</xsl:choose>

		<!-- <link rel="icon" href="/favicon.ico" type="image/x-icon" /> -->

	</xsl:template>
</xsl:stylesheet>