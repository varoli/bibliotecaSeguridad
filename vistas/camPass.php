<script src="vistas/js/main.js"></script>
<div class="modal fade" id="camPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-lg modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">C A M B I O   D E   P A S W O R D<h4>
      </div>
      <div class="modal-body">
          <form action="camPass/" method="post" class="well" id="formAcceso">
               <input type="password" class="form-control espacioText" name="passAnt" placeholder="Contraseña actual" pattern="(?=^.{8,40}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z])(?=.*[¡@]).*$" required="required" title="Mínimo 8 caracteres, por lo menos 1 mayúscula, 1 minúscula y un caracter especial"/>
              <input type="password" class="form-control espacioText" name="passNueva" placeholder="Nueva Contraseña" pattern="(?=^.{8,40}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z])(?=.*[¡@]).*$" required="required" title="Mínimo 8 caracteres, por lo menos 1 mayúscula, 1 minúscula y un caracter especial"/>
              <input type="submit" class="btn btn-success pull-right" value="Enviar"/>
          </form>
      </div>
      <div class="modal-footer">
        <div class="alert alert-warning clear-both">
            <strong>¡Atención!</strong> La contraseña debe tener como mínimo 8 caracteres, deberá tener por lo menos 1 letra mayúscula, 1 minúscula, 1 caracter especial y 1 número.
        </div>
        <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div><!-- Termina modal 1 -->