<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />
	<xsl:template match="mod_counters" mode="counters">
		<xsl:apply-templates mode="counters" />
	</xsl:template>

	<xsl:template match="add|edit" mode="counters">
		<h2 class="sub-header">Счетчики</h2>
		<div class="well">
			<form method="post">
				<div class="form-group">
					<label>Название</label>
					<input name="title" value="{title}" class="form-control" type="text" required="" />
				</div>
				<div class="form-group">
					<label>Код счетчика</label>
					<textarea name="text" rows="5" class="form-control" required="">
						<xsl:value-of select="text" />
					</textarea>
				</div>
				<xsl:call-template name="saveButton">
					<xsl:with-param name="active" select="active" />
				</xsl:call-template>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="list_admin" mode="counters">
		<h2 class="sub-header">Счетчики</h2>
		<a href="/counters/?ADMIN&amp;ADD" class="btn btn-success btn-sm">
			<i class="glyphicon glyphicon-plus"></i>
			Добавить
		</a>
		<br />
		<br />
		<div class="row">
			<ul id="tree" data-url="/counters/">
				<xsl:for-each select="item">
					<li class="sort clearfix" id="{id}">
						<span class="col-md-7">
							<xsl:value-of select="title" />
							<div class="pull-right">
								<xsl:call-template name="controlButton">
									<xsl:with-param name="active" select="active" />
								</xsl:call-template>
							</div>
						</span>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="item" mode="counters">
		<xsl:if test="//DEBUG!=1">
			<xsl:choose>
				<xsl:when test="contains(title, ':index')">
					<xsl:value-of select="text" disable-output-escaping="yes" />
				</xsl:when>
				<xsl:otherwise>
					<noindex>
						<xsl:value-of select="text" disable-output-escaping="yes" />
					</noindex>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:template>
	<xsl:template match="*" mode="counters" />
</xsl:stylesheet>