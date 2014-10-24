<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />




	<xsl:template match="admin_menu" mode="brief">
		<div class="navbar navbar-inverse navbar-fixed-top adminMenu" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button class="btn pull-left pin glyphicon" />
					<a class="navbar-brand" href="#">Администрирование</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">

						<li class="dropdown">
							<xsl:if test="//requests/get/subclass='profile'">
								<xsl:attribute name="class">dropdown active</xsl:attribute>
							</xsl:if>
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="glyphicon glyphicon-user"></i>
								<xsl:value-of select="concat(' ',//mod_users/sub_login/user/login)" />
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li class="dropdown-header">
									<xsl:value-of select="//requests/session/user/login" />
								</li>
								<li class="divider"></li>
								<xsl:if test="//requests/session/user/position='superadmin'">
									<li>
										<xsl:if test="//requests/get/path='admin'">
											<xsl:attribute name="class">active</xsl:attribute>
										</xsl:if>
										<a href="/admin/?ADMIN">
											<i class="glyphicon glyphicon-list-alt"></i>
											Разделы
										</a>
									</li>
									<li>
										<xsl:if test="//requests/get/path='config'">
											<xsl:attribute name="class">active</xsl:attribute>
										</xsl:if>
										<a href="/config/?ADMIN">
											<i class="glyphicon glyphicon-cog"></i>
											Настройки
										</a>
									</li>
								</xsl:if>
								<!-- <li> <xsl:if test="//requests/get/path='finder'"> <xsl:attribute name="class">active</xsl:attribute> </xsl:if> <a href="/finder/?ADMIN"> <i class="glyphicon glyphicon-th"></i> Finder </a> </li> -->
								<li>
									<xsl:if test="//requests/get/path='counters'">
										<xsl:attribute name="class">active</xsl:attribute>
									</xsl:if>
									<a href="/counters/?ADMIN">
										<i class="glyphicon glyphicon-star-empty"></i>
										Счетчики
									</a>
								</li>
								<li>
									<xsl:if test="//requests/get/path='profile'">
										<xsl:attribute name="class">active</xsl:attribute>
									</xsl:if>
									<a href="/profile/?ADMIN">
										<i class="glyphicon glyphicon-user"></i>
										Профиль
									</a>
								</li>
								<li class="divider"></li>
								<li>
									<a href="/?logout">
										<i class="glyphicon glyphicon-off"></i>
										Выход
									</a>
								</li>
							</ul>
						</li>
					</ul>
					<!-- <form class="navbar-form navbar-right"> <input type="text" class="form-control" placeholder="Search..." /> </form> -->
				</div>
			</div>
		</div>

	</xsl:template>










	<xsl:template match="mod_admin_menu_">
		<xsl:if test="( //mod_users/sub_login/user/role mod ( 1 * 2 ) ) - ( //mod_users/sub_login/user/role mod ( 1 ) ) and admin_menu='show'">
			<link rel="stylesheet" href="/engine/modules/admin_menu/admin_menu.css" type="text/css" />
			<link rel="stylesheet" type="text/css" href="/css/jquery.slidemenu.css" />
			<!--[if lte IE 7]> <style type="text/css"> html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/ </style> <![endif] -->
			<script type="text/javascript" src="/engine/js/jquery.slidemenu.js" />
			<script type="text/javascript" src="/engine/js/jquery.cookie.js" />
			<script type="text/javascript" src="/engine/modules/admin_menu/admin_menu.js" />
			<div class="admin_menu">
				<div class="cont_menu">
					<div class="navbar navbar-fixed-top_ navbar-inverse">
						<div class="navbar-inner">
							<div class="container">

								<div class="nav-collapse collapse in">
									<ul class="nav">
										<li>
											<xsl:choose>
												<xsl:when test="//requests/get/ADMIN">
													<a href="{$get}">
														<xsl:choose>
															<xsl:when test="$get='/1/'">
																<xsl:attribute name="href">/</xsl:attribute>
															</xsl:when>
															<xsl:otherwise>
																<xsl:choose>
																	<xsl:when test="//requests/get/EDIT">
																		<xsl:attribute name="href"><xsl:value-of select="concat($get, '?ITEM=', //requests/get/EDIT)" /></xsl:attribute>
																	</xsl:when>
																	<xsl:otherwise>
																		<xsl:attribute name="href"><xsl:value-of select="$get" /></xsl:attribute>
																	</xsl:otherwise>
																</xsl:choose>
															</xsl:otherwise>
														</xsl:choose>
														Смотреть
													</a>
												</xsl:when>
												<xsl:otherwise>
													<xsl:choose>
														<xsl:when test="//requests/get/ITEM">
															<a href="/{//section/current_path}/?ADMIN&amp;EDIT={//requests/get/ITEM}">Редактировать</a>
														</xsl:when>
														<xsl:otherwise>
															<a href="{$get}?ADMIN">Редактировать</a>
														</xsl:otherwise>
													</xsl:choose>
												</xsl:otherwise>
											</xsl:choose>
										</li>



										<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">
												Страницы
												<b class="caret"></b>
											</a>
											<ul class="dropdown-menu">
												<xsl:for-each select="article_section/item">
													<li>
														<a href="/{path}/?ADMIN">
															<xsl:value-of select="name" />
														</a>
													</li>
												</xsl:for-each>
											</ul>
										</li>
										<xsl:if test="count(gallery_section/item)>0">
											<xsl:choose>
												<xsl:when test="count(gallery_section/item)=1">
													<xsl:for-each select="gallery_section/item">
														<li>
															<a href="/{path}/?ADMIN">
																<xsl:value-of select="name" />
															</a>
														</li>
													</xsl:for-each>
												</xsl:when>
												<xsl:otherwise>
													<li class="dropdown">
														<a href="#" class="dropdown-toggle" data-toggle="dropdown">
															Галереи
															<b class="caret"></b>
														</a>
														<ul class="dropdown-menu">
															<xsl:for-each select="gallery_section/item">
																<li>
																	<a href="/{path}/?ADMIN">
																		<xsl:value-of select="name" />
																	</a>
																</li>
															</xsl:for-each>
														</ul>
													</li>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:if>
										<xsl:apply-templates select="menu_item" />
										<li class="dropdown">
											<xsl:if test="//requests/get/subclass='profile'">
												<xsl:attribute name="class">dropdown active</xsl:attribute>
											</xsl:if>
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">
												<i class="icon-user icon-white"></i>
												<xsl:value-of select="concat(' ',//mod_users/sub_login/user/login)" />
												<b class="caret"></b>
											</a>
											<ul class="dropdown-menu">
												<xsl:if test="//mod_users/sub_login/user/position='superadmin'">
													<li>
														<xsl:if test="//requests/get/path='admin'">
															<xsl:attribute name="class">active</xsl:attribute>
														</xsl:if>
														<a href="/admin/?ADMIN">
															<i class="icon-list-alt"></i>
															Разделы
														</a>
													</li>
													<li>
														<xsl:if test="//requests/get/path='configs'">
															<xsl:attribute name="class">active</xsl:attribute>
														</xsl:if>
														<a href="/configs/?ADMIN">
															<i class="icon-wrench"></i>
															Настройки
														</a>
													</li>
												</xsl:if>
												<li>
													<xsl:if test="//requests/get/path='finder'">
														<xsl:attribute name="class">active</xsl:attribute>
													</xsl:if>
													<a href="/finder/?ADMIN">
														<i class="icon-th"></i>
														Finder
													</a>
												</li>
												<li>
													<xsl:if test="//requests/get/path='counters'">
														<xsl:attribute name="class">active</xsl:attribute>
													</xsl:if>
													<a href="/counters/?ADMIN">
														<i class="icon-star-empty"></i>
														Счетчики
													</a>
												</li>
												<li>
													<xsl:if test="//requests/get/path='profile'">
														<xsl:attribute name="class">active</xsl:attribute>
													</xsl:if>
													<a href="/profile/?ADMIN">
														<i class="icon-user"></i>
														Профиль
													</a>
												</li>
												<li class="divider"></li>
												<li>
													<a href="/?logout">
														<i class="icon-off"></i>
														Выход
													</a>
												</li>
											</ul>
										</li>
										<!-- <li class="brand"> <xsl:value-of select="concat('&#160;', //date_time/date,' ', //date_time/time)" /> </li> -->
									</ul>
								</div>
							</div>
						</div>
					</div>

				</div>
				<dl>
					<dd id="but">&#160;</dd>
				</dl>
			</div>
			<div id="admin_pad" class="admin_pad_"></div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="menu_item">
		<xsl:apply-templates mode="menu_item" />
	</xsl:template>

	<xsl:template match="item" mode="menu_item">
		<xsl:choose>
			<xsl:when test="count(item)>0">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<xsl:value-of select="name" />
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<xsl:apply-templates select="item" mode="menu_item" />
					</ul>
				</li>
			</xsl:when>

			<xsl:otherwise>
				<li>
					<a href="{url}">
						<xsl:value-of select="name" />
					</a>
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>