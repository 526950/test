<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:core="my" exclude-result-prefixes="core">
	<xsl:template name="body">
		<xsl:apply-templates select="//mod_admin_menu" mode="brief" />

		<div class="container-fluid">
			<div class="row">
	<!-- 			<div class="col-sm-3 col-md-2 sidebar">
					<xsl:call-template name="sidebar" />
				</div> -->
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
					<xsl:call-template name="content" />
				</div>

			</div>
		</div>



	</xsl:template>

</xsl:stylesheet>