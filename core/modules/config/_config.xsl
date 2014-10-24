<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="mod_config">
		<xsl:apply-templates select="defines" mode="config" />
	</xsl:template>

	<xsl:template match="defines" mode="config">
		<div class="well">
			<h3 class="lead text-uppercase text-right text-danger">Настройки</h3>
			<form method="post" class="form-horizontal">
				<xsl:for-each select="child::*">
					<div class="row">
						<div class="form-group col-md-12">

							<div class="col-md-4">
								<label class="lead control-label" style="font-size:18px;">
									<xsl:value-of select="name()" />
								</label>
								<br />
								<span class="text-primary">
									<xsl:value-of select="item[@id=1]" />
								</span>
							</div>
							<div>
								<xsl:attribute name="class">
								<xsl:choose>
									<xsl:when test="number(item[@id=0]) = item[@id=0] and string-length(item[@id=0])&lt;4">
										col-md-1
									</xsl:when>
									<xsl:when test="number(item[@id=0]) = item[@id=0] and string-length(item[@id=0])&lt;13">
										col-md-2
									</xsl:when>
									<xsl:when test="number(item[@id=0]) = item[@id=0] and string-length(item[@id=0])&gt;13">
										col-md-3
									</xsl:when>
									<xsl:when test="string-length(item[@id=0])&lt;33">
										col-md-4
									</xsl:when>
									<xsl:otherwise>
										col-md-8
									</xsl:otherwise>
								</xsl:choose>
								</xsl:attribute>
								<input type="hidden" name="DEF_HID[{name()}]" value="{item[@id=1]}" class="form-control" />
								<input type="text" name="DEF[{name()}]" value="{item[@id=0]}" class="form-control" />
							</div>
						</div>




					</div>
				</xsl:for-each>
				<div>
					<xsl:call-template name="saveButton">
						<xsl:with-param name="noBut" select="1" />
						<xsl:with-param name="saveText" select="'Сохранить настройки'" />
					</xsl:call-template>
				</div>
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>