<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:import href="./core/xsl/function.xsl" />
	<xsl:import href="./core/xsl/templates.xsl" />
	<!--import modules -->
	<xsl:output cdata-section-elements="script" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" encoding="UTF-8" indent="yes" media-type="html" method="xml" standalone="no" omit-xml-declaration="yes" />
	<xsl:template match="root">
		<html>
			<head>
				<xsl:call-template name="head" />
			</head>
			<body>
				<xsl:apply-templates select="//mod_admin_menu" />
				<xsl:call-template name="body" />
			</body>
		</html>
	</xsl:template>

	<xsl:template name="content">
		<xsl:apply-templates select="//mod_message/list/content" mode="message" />
		<xsl:apply-templates select="//CURRENT" mode="CLASS" />
	</xsl:template>
	
	<xsl:template name="sidebar">
		<!-- <xsl:apply-templates select="//mod_message/list/sidebar" mode="message" />
		<xsl:apply-templates select="//CURRENT" mode="CLASS_sidebar" /> -->
		<xsl:apply-templates select="mod_admin" mode="admin_sidebar" />
	</xsl:template>
</xsl:stylesheet>