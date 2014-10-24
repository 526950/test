<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_signup">
		<xsl:apply-templates mode="signup" />
	</xsl:template>

	<xsl:template match="edit" mode="signup">
		<script type="text/javascript" src="/core/modules/users/signup/_signup.js" />
		<div class="well">
			<form method="post">
				<h4 class="lead">Регистрация нового пользователя</h4>
				<div id="messageError" />

				<div class="row">
					<div class="form-group col-md-3">
						<label class="control-label">Логин (от 3 симв)</label>
						<input type="text" name="login" value="{//requests/post/login}" required="required" class="form-control" />
					</div>

					<div class="form-group col-md-3">
						<label class="control-label">E-mail</label>
						<input type="text" name="email" value="{//requests/post/email}" required="required" class="form-control" />
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-3">
						<label class="control-label">Пароль</label>
						<input type="password" name="password" required="required" class="form-control" />
					</div>
					<div class="form-group col-md-3">
						<label class="control-label">Пароль (повтор)</label>
						<input type="password" name="repassword" required="required" class="form-control" />
					</div>
				</div>

				<xsl:if test="//config/ENABLE_CAPTCHA_SIGNUP=1">
					<div class="row">
						<div class="form-group col-md-3 text-nowrap">
							<label class="control-label">Код подтверждения</label>
							<input type="text" name="captcha" value="{//requests/post/email}" required="required" class="form-control" />
							<img src="captcha.php" class="captcha" />
							&#160;
							<a href="" class="reload_captcha">Обновить</a>
						</div>
					</div>
				</xsl:if>

				<div class="row">
					<div class="form-group col-md-3">
						<input type="submit" name="save" value="Зарегистрироваться" class="btn btn-success" />
					</div>
				</div>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="signup_info" mode="signup">
		<div class="alert alert-info">
			<p>
				Для завершения регистрации
				<i class="pen">
					нажмите на ссылку в письме, которое мы отправили на
					<b>
						<xsl:value-of select="." />
					</b>
				</i>
			</p>
			<p>
				<p class="small">
					<b>Не получили письмо?</b>
					Проверьте папку "Спам/Сомнительные" вашего почтового ящика - наше письмо могло по ошибке попасть туда. Если это так, отметьте его как "Не спам" и оно автоматически перейдет в папку "Входящие".
				</p>
				<i>Если все таки не удалось получить письмо, свяжитесь с поддержкой</i>
			</p>
		</div>

	</xsl:template>

</xsl:stylesheet>