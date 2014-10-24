<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="item" mode="option-section">
		<xsl:variable name="id" select="id" />
		<li>
			<div class="input-group">
				<span class="input-group-addon">
					<input id="id2_{id}" type="checkbox" name="id2[{id}]" value="{id}">
						<xsl:if test="../section-link/item[id2=$id]">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</span>
				<input class="form-control input-sm" type="text" name="params[{id}]" data-id="{id}" value="{../section-link/item[id2=$id]/params}" />
			</div>
			<label for="id2_{id}">
				<xsl:value-of select="name" />
			</label>
			<xsl:if test="../item[id_parent=current()/id]">
				<ul class="">
					<xsl:apply-templates select="../item[id_parent=current()/id]" mode="option-section" />
				</ul>
			</xsl:if>
		</li>
	</xsl:template>

	<xsl:template match="select-modules" mode="admin">
		<select id="tpl-select-modules" style="display:none;">
			<option value="NULL">Выберите</option>
			<xsl:for-each select="*">
				<xsl:variable name="name" select="name()" />
				<xsl:if test="not(deny-for-select='true')">
					<option value="{$name}">
						<xsl:choose>
							<xsl:when test="title!=''">
								<xsl:value-of select="title" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$name" />
							</xsl:otherwise>
						</xsl:choose>
					</option>
					<xsl:for-each select="submodules/*">
						<xsl:if test="not(deny-for-select='true')">
							<option value="{$name}_{name()}">
								<xsl:choose>
									<xsl:when test="title!=''">
										&#160;
										<xsl:value-of select="concat('- ',title)" />
									</xsl:when>
									<xsl:otherwise>
										&#160;
										<xsl:value-of select="concat('- ',name())" />
									</xsl:otherwise>
								</xsl:choose>
							</option>
						</xsl:if>
					</xsl:for-each>
				</xsl:if>
			</xsl:for-each>
		</select>
	</xsl:template>

	<xsl:template match="select-menu" mode="admin">
		<div id="tpl-select-menu" style="display:none;">
			<xsl:for-each select="*">
				<div class="col-md-6">

					<label>
						<input type="checkbox" data-name="{name()}" value="" />
						&#160;
						<xsl:value-of select="." />
					</label>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>


	<xsl:template match="list_admin" mode="admin">
		<div id="sections-output" />
		<div id="sections">
			<ol>
				<li>
					<a href="#" class="add-section" data-pk="NULL" data-type="multi" data-url="/admin/?ADMIN&amp;cmd=Add">
						<i class="glyphicon glyphicon-plus"></i>
					</a>
					<div class="dd" id="nestable">
						<ol class=" dd-list">
							<xsl:apply-templates select="item[id_parent='']" mode="sections" />
						</ol>
					</div>
				</li>
			</ol>
			<ul id="tpl-option-section" style="display:none;">
				<xsl:apply-templates select="item[id_parent='']" mode="option-section" />
			</ul>
			<div id="tpl-section-link" style="display:none;">
				<div class="btn-group btn-group-sm" data-toggle="buttons">
					<label class="btn btn-primary active">
						<input type="radio" name="options" id="link-self" data-link="self" />
						Только в своем разделе
					</label>
					<label class="btn btn-primary">
						<input type="radio" name="options" id="link-all" data-link="all" />
						Везде
					</label>
				</div>
			</div>
		</div>
		<script src="/core/modules/admin/_admin.js"></script>
	</xsl:template>


	<xsl:template match="item" mode="sections">
		<li class="dd-item" data-id="{id}" data-alias="{alias}">
			<div class="dd-handle" />
			<div class="dropdown dd-content">
				<a class="dropdown-toggle dashed" id="dropdownMenu{id}" data-toggle="dropdown">
					<xsl:if test="active=0">
						<xsl:attribute name="class">dropdown-toggle dashed text-muted</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="name" />
				</a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu{id}">
					<li class="dropdown-header">
						<xsl:value-of select="name" />
					</li>
					<li class="divider"></li>
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="" class="add-section" data-type="multi" data-pk="{id}" data-url="/admin/?ADMIN&amp;cmd=Add" data-link="self">
							<i class="glyphicon glyphicon-plus"></i>
							Добавить
						</a>
					</li>
					<li role="presentation">
						<xsl:variable name="module">
							<xsl:choose>
								<xsl:when test="submodule=''">
									<xsl:value-of select="module" />
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="concat(module,'_',submodule)" />
								</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<a role="menuitem" tabindex="-1" href="" class="edit-section" data-type="multi" data-pk="{id}" data-url="/admin/?ADMIN&amp;cmd=Add" data-link="{link}" data-params="E" data-name="{name}" data-alias="{alias}" data-module="{$module}" data-param="{params}" data-active="{active}">
							<i class="glyphicon glyphicon-edit"></i>
							Редактировать
						</a>
					</li>
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="" class="option-section" data-type="multi2" data-pk="{id}" data-url="/admin/?ADMIN&amp;cmd=Save&amp;option">
							<i class="glyphicon glyphicon-cog"></i>
							Настройки
						</a>
					</li>
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="/admin/?ADMIN&amp;DEL={id}">
							<i class="glyphicon glyphicon-trash"></i>
							Удалить
						</a>
					</li>
				</ul>
			</div>
			<xsl:if test="../item[id_parent=current()/id]">
				<ol class="dd-list">
					<xsl:apply-templates select="../item[id_parent=current()/id]" mode="sections" />
				</ol>
			</xsl:if>
		</li>
	</xsl:template>

</xsl:stylesheet>