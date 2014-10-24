<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />
	<xsl:variable name="col" select="9" />
	<xsl:template match="mod_article" mode="article">
		<xsl:if test="//requests/get/ADMIN">
			<script type="text/javascript" src="/engine/js/jquery.uploadify.js" />
			<script type="text/javascript" src="/engine/js/swfobject.js" />
			<script type="text/javascript" src="/engine/modules/article/article.js" />
		</xsl:if>
		<xsl:apply-templates mode="article" />
	</xsl:template>

	<xsl:template match="add|edit" mode="article">
		<script language="javascript" type="text/javascript" src="/engine/tiny_mce/tiny_mce.js" />
		<script type="text/javascript" src="/engine/js/inittiny.js" />
		<h4>
			<xsl:choose>
				<xsl:when test="name()='add'">
					Новая статья
				</xsl:when>
				<xsl:when test="name()='edit'">
					Редактирование статьи
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
			(
			<xsl:value-of select="//section/current_name" />
			)
		</h4>

		<div class="well">
			<form method="post">
				<div class="form-group">
					<label>
						Название
					</label>
					<input name="title" value="{item/title}" type="text" class="form-control input-sm" required="" />
				</div>
				<div class="row">
					<div class="span">
						<label class="checkbox inline">
							<input type="checkbox" name="title_show" value="1">
								<xsl:if test="item/title_show=1">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							Выводить заголовок с текстом
						</label>
					</div>
				</div>
				<br />
				<div class="form-group">
					<label>
						Анонс
					</label>
					<textarea name="anons" title="Small" rows="3" class="form-control">
						<xsl:value-of select="item/anons" />
					</textarea>
				</div>
				<div class="form-group">
					<div class="col-md-4 col-md-offset-8">
						<label>Автор</label>
						<input name="author" value="{item/author}" type="text" class="form-control input-sm" />
					</div>
				</div>
				<xsl:if test="//requests/get/tags">
					<div class="form-group">
						<label>Теги</label>
						<input name="tags" value="{item/tags}" class="form-control" type="text" />
					</div>
				</xsl:if>
				<div class="form-group">
					<label>Описание:</label>
					<textarea name="text" class="editor_admin form-control" rows="15">
						<xsl:value-of select="item/text" />
					</textarea>
				</div>
				<div class="row">
					<div class="span">
						<label> Фото анонса: </label>
						<!-- <xsl:call-template name="upload_photo">
							<xsl:with-param name="xpath" select="'edit/item/'" />
							<xsl:with-param name="field" select="'photo_anons'" />
							<xsl:with-param name="module" select="'article'" />
						</xsl:call-template> -->

					</div>
				</div>
				<div class="row">
					<div class="span">
						<label> Фото: </label>
						<!-- <xsl:call-template name="upload_photo_multi">
							<xsl:with-param name="xpath" select="'edit/item/'" />
							<xsl:with-param name="field" select="'photo'" />
							<xsl:with-param name="module" select="'article'" />
						</xsl:call-template> -->
					</div>
				</div>
				<br />
				<br />
				<xsl:call-template name="saveButton">
					<xsl:with-param name="active" select="item/active" />
				</xsl:call-template>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="item" mode="article">
		<xsl:if test="count(../item)!=1 or title_show=1">
			<h1>
				<xsl:value-of select="title" disable-output-escaping="yes" />
			</h1>
		</xsl:if>
		<article>
			<xsl:value-of select="text" disable-output-escaping="yes" />
		</article>
		<xsl:if test="count(photo/item)>0">
			<div class="gallery">
				<xsl:for-each select="photo/item">
					<div class="item">
						<a href="/1000*600/uploads/article/{name}" class="lb" rel="article" title="{note}">
								<img src="/163*90/uploads/article/{name}" alt="{note}" />
						</a>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>


	<xsl:template match="list" mode="article">
		<xsl:choose>
			<xsl:when test="count(item)=1 or //requests/get/all">
				<xsl:apply-templates select="item" mode="article" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="item">
						<xsl:for-each select="item">
							<div class="clearfix">
								<xsl:if test="photo_anons!=''">
									<a class="_image" style="float:left">
										<xsl:call-template name="translit">
											<xsl:with-param name="id" select="id" />
											<xsl:with-param name="alias" select="alias" />
										</xsl:call-template>
										<img src="/140*100/uploads/article/{photo_anons}" />
									</a>
								</xsl:if>
								<h4 class="list">
									<a>
										<xsl:call-template name="translit">
											<xsl:with-param name="id" select="id" />
											<xsl:with-param name="alias" select="alias" />
										</xsl:call-template>
										<xsl:value-of select="title" />
									</a>
								</h4>
								<xsl:value-of select="anons" disable-output-escaping="yes" />
							</div>
							<br />
						</xsl:for-each>
					</xsl:when>
				</xsl:choose>
				<xsl:apply-templates select="pages" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list_admin" mode="article">
		<xsl:variable name="col_" select="number($col)-3" />
		<a href="{$get}?ADMIN&amp;ADD" class="btn btn-success">
			<i class="icon-plus icon-white"></i>
			Добавить
		</a>
		<br />
		<br />
		<xsl:apply-templates select="pages" mode="pages"/>
		<ul id="tree"  data-url="/">
			<xsl:variable name="cnt" select="count(item)" />
			<xsl:for-each select="item">
				<li class="sort clearfix " id="{id}">
					<span class="span{$col_}">
						<a>
							<xsl:choose>
								<xsl:when test="alias!=''">
									<xsl:attribute name="href">
												<xsl:value-of select="concat($get, id,'-', alias)" />
											</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="href">
												<xsl:value-of select="concat($get, '?ITEM=', id)" />
											</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:value-of select="title" />
						</a>
					</span>
					<div class="pull-right">
								<xsl:call-template name="controlButton">
									<xsl:with-param name="active" select="active" />
								</xsl:call-template>
							</div>
				</li>
			</xsl:for-each>
		</ul>
		<xsl:apply-templates select="pages" mode="pages"/>
	</xsl:template>
</xsl:stylesheet>