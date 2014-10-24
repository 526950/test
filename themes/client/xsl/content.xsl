<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:core="my" exclude-result-prefixes="core">
	<xsl:template name="body">

<xsl:apply-templates select="//mod_admin_menu" mode="brief" />
		<div class="container-fluid">
			<div class="row">
				<xsl:call-template name="content" />
				<xsl:apply-templates select="//mod_counters/show/item" mode="counters" />


			</div>
		</div>

		<link href="/themes/admin/css/bootstrap.min.css" rel="stylesheet" />
		<link href="/themes/admin/css/dashboard.css" rel="stylesheet" />
		<link href="/themes/admin/css/add.css" rel="stylesheet" />

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="/themes/admin/js/bootstrap.min.js"></script>
		<script src="/themes/admin/js/jquery.sortable.js"></script>
		<script src="/themes/admin/js/custom.js"></script>



	</xsl:template>

</xsl:stylesheet>