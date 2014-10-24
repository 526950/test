<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:variable name="get">
		<xsl:choose>
			<xsl:when test="//requests/get/path='' or not(//requests/get/path)">
				<xsl:value-of select="'/'" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="concat('/', //requests/get/path, '/')" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:template match="pages" mode="pages">
		<xsl:if test="count_pages>1">
			<div class="text-center">
				<ul class="pagination">
					<!-- <xsl:if test="block_prev">
						<li>
							<a href="{substring(get,1, string-length(get)-1)}">первая</a>
						</li>
					</xsl:if> -->
					<xsl:if test="prev">
						<xsl:choose>
							<xsl:when test="prev=1">
								<li>
									<a href="{substring(get,1, string-length(get)-1)}" id="prev"></a>
								</li>
							</xsl:when>
							<xsl:otherwise>
								<li>
									<a href="{get}PAGE={prev}" id="prev"></a>
								</li>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:if test="block_prev">
						<li class="disabled">
							<a>…</a>
						</li>
					</xsl:if>
					<xsl:for-each select="sheets/item">
						<xsl:choose>
							<xsl:when test=".=../current_page">
								<li class="active">
									<a>
										<xsl:value-of select="." />
									</a>
								</li>
							</xsl:when>
							<xsl:otherwise>
								<li>
									<xsl:choose>
										<xsl:when test=".=1">
											<a href="{substring(../get,1, string-length(../get)-1)}">
												<xsl:value-of select="." />
											</a>
										</xsl:when>
										<xsl:otherwise>
											<a href="{../get}PAGE={.}">
												<xsl:value-of select="." />
											</a>
										</xsl:otherwise>
									</xsl:choose>


								</li>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<xsl:if test="block_next">
						<li class="disabled">
							<a>…</a>
						</li>
					</xsl:if>
					<xsl:if test="next">
						<li>
							<a href="{get}PAGE={next}" id="next"></a>
						</li>
					</xsl:if>
					<!-- <xsl:if test="block_next and count_pages!=next">
						<li>
							<a href="{get}PAGE={count_pages}">последняя</a>
						</li>
					</xsl:if> -->
				</ul>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template name="controlButton">
		<xsl:param name="active" />
		<div class="btn-group">
			<xsl:choose>
				<xsl:when test="$active=1">
					<a href="{$get}?cmd=Active&amp;id={id}" class="btn btn-sm btn-default publishAjax glyphicon glyphicon-eye-open"></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="{$get}?ADMIN&amp;cmd=Active&amp;id={id}" class="btn btn-sm btn-default publishAjax glyphicon glyphicon-eye-close"></a>
				</xsl:otherwise>
			</xsl:choose>
			<a href="{$get}?ADMIN&amp;EDIT={id}" class="btn btn-sm btn-warning edit glyphicon glyphicon-cog" title="редактировать"></a>
			<a href="{$get}?ADMIN&amp;DEL={id}" class="btn btn-sm btn-danger delete_ajax glyphicon glyphicon-trash" title="удалить"></a>
		</div>
	</xsl:template>

	<xsl:template name="saveButton">
		<xsl:param name="active" />
		<xsl:param name="noBut" />
		<xsl:param name="saveText" select="'Сохранить'" />
		<xsl:param name="cancelText" select="'Отменить'" />
		<div class="saveButtons">
			<a class="btn btn-default btn-sm">
				<xsl:attribute name="href">
				<xsl:choose>
					<xsl:when test="//requests/server/HTTP_REFERER">
							<xsl:value-of select="//requests/server/HTTP_REFERER" />
					</xsl:when>
					<xsl:otherwise>
					<xsl:value-of select="concat($get,'?ADMIN')" />
					</xsl:otherwise>
				</xsl:choose>
				</xsl:attribute>
				<xsl:value-of select="$cancelText" />
			</a>
			&#160;
			<input type="submit" name="save" class="btn btn-success btn-sm" value="{$saveText}" />
			&#160;
			<xsl:if test="$noBut!=1">
				<xsl:choose>
					<xsl:when test="$active=1">
						<input type="submit" name="unsavePublic" class="btn btn-warning  btn-sm" value="Сохранить и скрыть" />
					</xsl:when>
					<xsl:otherwise>
						<input type="submit" name="savePublic" class="btn btn-success  btn-sm" value="Сохранить и отобразить" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</div>
	</xsl:template>
</xsl:stylesheet>