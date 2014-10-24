<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template match="error|success|notice|info|warning" mode="message">
		<xsl:if test="count(item)>0">
			<div>
				<xsl:choose>
					<xsl:when test="name()='error'">
						<xsl:attribute name="class">alert alert-danger</xsl:attribute>
						<strong>Ошибка!</strong>
					</xsl:when>
					<xsl:when test="name()='success'">
						<xsl:attribute name="class">alert alert-success</xsl:attribute>
					</xsl:when>
					<xsl:when test="name()='notice'">
						<xsl:attribute name="class">alert alert-block</xsl:attribute>
						<strong>Примечание.</strong>
					</xsl:when>
					<xsl:when test="name()='info'">
						<xsl:attribute name="class">alert alert-info</xsl:attribute>
						<strong>Информация.</strong>
					</xsl:when>
					<xsl:when test="name()='warning'">
						<xsl:attribute name="class">alert alert-warning</xsl:attribute>
						<strong>Предупреждение!</strong>
					</xsl:when>
					<xsl:otherwise>

					</xsl:otherwise>
				</xsl:choose>
				<a class="close" data-dismiss="alert">×</a>
				<br />
				<xsl:for-each select="item">
					<br />
					<xsl:value-of select="." disable-output-escaping="yes" />
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>