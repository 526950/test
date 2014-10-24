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
		<link href="/themes/admin/css/bootstrap.min.css" rel="stylesheet" />
		<link href="/themes/admin/css/dashboard.css" rel="stylesheet" />
		<link href="/themes/admin/css/add.css" rel="stylesheet" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<link href="/themes/admin/css/bootstrap-editable.css" rel="stylesheet" />
		<script src="/themes/admin/js/bootstrap.min.js"></script>

		<script src="/themes/admin/js/jquery.sortable.js"></script>
		<script src="/themes/admin/js/jquery.nestable.js"></script>
		<link href="/themes/admin/css/nestable.css" rel="stylesheet" />
		
		<script src="/themes/admin/js/jquery.ui.widget.js"/>
		<script src="/themes/admin/js/jquery.fileupload.js"></script>
		<link href="/themes/admin/css/jquery.fileupload.css" rel="stylesheet" />
		
		<script src="/themes/admin/js/custom.js"></script>

		<script src="/themes/admin/js/bootstrap-editable.min.js"></script>


	</xsl:template>
</xsl:stylesheet>