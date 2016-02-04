<div class="container espacioWell">
	<div class="row">
		<div class="col-sm-5 col-sm-offset-3">
			<form action="login" method="post" class="well" id="formAcceso">
				<h2 class="text-center">..:::  Acceso  :::..</h2>
				<input type="text" class="form-control espacioText" name="user" placeholder="Usuario" pattern=".{3,}" required="required" value="2011120175"/>
				<input type="password" class="form-control espacioText" name="pass" placeholder="Contraseña"  value="Liz912inf@"/>
				<!--input type="password" class="form-control espacioText" name="pass" placeholder="Contraseña" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z])(?=.*[¡@]).*$" required="required" title="Mínimo 8 caracteres, por lo menos 1 mayúscula, 1 minúscula y un caracter especial" value="Liz912¡nf@"/-->
				<input type="submit" class="btn btn-success pull-right" value="Enviar"/>
				<input type="hidden" name="tries" value="0">
				<div class="alert alert-warning clear-both">
					<strong>¡Atención!</strong> La contraseña debe tener como mínimo 8 caractéres.
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-5 col-sm-offset-3 espacioText">
			<p id="textoAcceso">Si no dispone de un nombre de usuario y una contraseña válidos,
			por favor, póngase en contacto con su administrador para que le asigne unos nuevos.</p>
		</div>
	</div>
</div>