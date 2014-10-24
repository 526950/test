<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_profile">
		<xsl:apply-templates mode="profile" />
	</xsl:template>

	<xsl:template match="edit" mode="profile">
		<!-- <script type="text/javascript" src="/engine/js/swfobject.js" />
		<script type="text/javascript" src="/engine/js/jquery.uploadify.js" />
		<script type="text/javascript" src="/engine/modules/users/profile.js" /> -->
		<div class="well col-md-9">
			<form method="post">
				<fieldset>
					<legend>
						Профиль пользователя
					</legend>

					<div class="row"><div class="form-group">
							<div class="col-md-6">
							<label> пароль</label>
							<input type="password" name="password" id="password" class="form-control" required="" />
						</div>
							<div class="col-md-6">
								<label>повтор пароля</label>
								<input type="password" name="repassword" id="repassword" class="form-control" required="" />
							</div>
						</div>
						</div>
				

					<!-- <div class="row">
						<div class="span">
							<label> Аватар</label>
							<xsl:call-template name="upload_photo">
								<xsl:with-param name="xpath" select="'sub_profile/edit/item/'" />
								<xsl:with-param name="field" select="'foto'" />
								<xsl:with-param name="module" select="'users'" />
								<xsl:with-param name="path" select="item/path" />
							</xsl:call-template>
						</div>
					</div> -->
					<br />
					<div class="form-group">
						
						<xsl:call-template name="saveButton">
						<xsl:with-param name="noBut" select="1"/>
						<xsl:with-param name="saveText" select="'Сохранить профиль'" />
						</xsl:call-template>
					</div>

				</fieldset>
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>