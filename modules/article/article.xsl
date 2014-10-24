<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="list_admin" mode="config">
	   <script src="/modules/article/article.js"></script>
		<div class="well">
			<h3 class="lead text-uppercase text-right text-danger">Настройки</h3>
			<form method="post" class="form-horizontal" enctype="multipart/form-data">
				<div class="row">
					<div class="form-group">						
							<label>
								Фото:
							</label>
							<br />
							<span class="btn btn-success fileinput-button">
								<i class="glyphicon glyphicon-plus"></i>
								<span>Загрузить...</span>
								<input type="file" multiple="" name="files[]" id="fileupload" />
							</span>						
					</div>
				</div>
				<div>
					<xsl:call-template name="saveButton">
						<xsl:with-param name="noBut" select="1" />
						<xsl:with-param name="saveText" select="'Сохранить настройки'" />
					</xsl:call-template>
				</div>
				<input type="submit" value="старт" />
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>