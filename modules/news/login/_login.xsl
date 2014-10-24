<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />
	
	<xsl:template match="mod_login" mode="brief">
		<xsl:choose>
			<xsl:when test="user/id">
				<li class="first">
					<a href="/?logout">Выход</a>
				</li>
				<li class="last">
					<a href="/profile/">Профиль</a>
				</li>
				<li class="first">
					<a>
						<b>
							Здравствуйте,
							<xsl:value-of select="user/login" />
						</b>
					</a>
				</li>
			</xsl:when>
			<xsl:otherwise>
				<li class="first">
					<a href="/signup/">Регистрация</a>
				</li>
				<li class="last">
					<a href="/login/">Вход</a>
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="form_login" mode="login">
		<div class="well">
			<form method="post" class="form-horizontal">
				<legend>Авторизация</legend>
				<div class="control-group">

					<label class="control-label" for="login">логин</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">
								<i class="icon-user"></i>
							</span>
							<input id="login" type="text" name="login" value="{//requests/post/login}" required="" />
						</div>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="password">пароль</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">
								<i class="icon-lock"></i>
							</span>
							<input id="password" type="password" name="password" required="" />
						</div>
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<input name="remember" id="rem" type="checkbox" value="1">
								<xsl:if test="//requests/post/remember">
									<xsl:attribute name="checked" select="'1'" />
								</xsl:if>
							</input>
							запомнить меня
						</label>
					</div>
				</div>

				<xsl:if test="//config/ENABLE_CAPTCHA_SIGNUP=1">
					<div class="control-group">
						<label class="control-label">
							<a href="" class="reload_captcha">Обновить</a>
						</label>
						<div class="controls">
							<img src="captcha.php" class="captcha img-polaroid" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">
							Captcha
						</label>
						<div class="controls">
							<input type="text" name="captcha" class="input-short" />
						</div>
					</div>
				</xsl:if>
				<div class="control-group">
					<div class="controls">
						<button class="btn btn-inverse" type="submit" name="save">
							<i class="icon-ok-sign icon-white"></i>
							&#160;Войти&#160;
						</button>
					</div>
				</div>
			</form>
		</div>
	</xsl:template>


	<xsl:template match="form_change_password" mode="login">
		<div class="frame">
			<div class="line">
				<h2>Восстановление доступа</h2>
			</div>
			<form method="post" id="signInForm">

				<div class="line">
					<label for="login" class="field-name">
						YPIN или Email:
					</label>
					<span class="field-value">
						<xsl:choose>
							<xsl:when test="//messages/login">
								<div class="messageError">
									<xsl:value-of select="//messages/login/error/item" />
								</div>
								<input name="login" id="login" value="{//requests/post/login}" type="text" class="inputField inputError" />
								&#160;
								<i class="ico-attention"></i>
							</xsl:when>
							<xsl:otherwise>
								<input name="login" id="login" value="{//requests/post/login}" type="text" class="inputField" />
								<xsl:if test="//requests/post/login">
									&#160;
									<i class="ico-ok"></i>
								</xsl:if>
							</xsl:otherwise>
						</xsl:choose>
					</span>
				</div>

				<div class="line">
					<span class="field-name"></span>
					<span class="field-value">
						<button type="submit" name="save" class="btn">Отправить</button>
					</span>
				</div>

				<div class="line">
					<span class="field-name"></span>
					<span class="field-value">
						На ваш email будет выслана ссылка для изменения
						пароля
					</span>
				</div>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="form_new_password" mode="login">
		<div class="frame">
			<div class="line">
				<h2>Смена пароля</h2>
			</div>
			<form method="post" id="signInForm">

				<div class="line">
					<label for="password" class="field-name">
						Пароль:
						<span class="star">*</span>
					</label>
					<xsl:choose>
						<xsl:when test="//messages/password">
							<span class="field-value">
								<div class="messageError">
									<xsl:value-of select="//messages/password/error/item" />
								</div>
								<input name="password" id="password" type="password" value="{//requests/post/password}" class="inputField inputError" />
								&#160;
								<i class="ico-attention"></i>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span class="field-value">
								<input name="password" id="password" type="password" value="{//requests/post/password}" class="inputField" />
								<xsl:if test="//requests/post/password">
									&#160;
									<i class="ico-ok"></i>
								</xsl:if>
							</span>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div class="line">
					<label for="repassword" class="field-name">
						Повтор пароля:
						<span class="star">*</span>
					</label>
					<xsl:choose>
						<xsl:when test="//messages/repassword">
							<span class="field-value">
								<div class="messageError">
									<xsl:value-of select="//messages/repassword/error/item" />
								</div>
								<input name="repassword" id="repassword" type="password" value="{//requests/post/repassword}" class="inputField inputError" />
								&#160;
								<i class="ico-attention"></i>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span class="field-value">
								<input name="repassword" id="repassword" type="password" value="{//requests/post/repassword}" class="inputField" />
								<xsl:if test="//requests/post/repassword">
									&#160;
									<i class="ico-ok"></i>
								</xsl:if>
							</span>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div class="line">
					<span class="field-name"></span>
					<span class="field-value">
						<button type="submit" name="save" class="btn">Сменить</button>
					</span>
				</div>
			</form>
		</div>
	</xsl:template>
	<!-- <xsl:template match="*" mode="login" /> -->
</xsl:stylesheet>