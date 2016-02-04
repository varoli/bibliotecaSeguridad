<?php
    @session_start();
    
    require_once("vendor/autoload.php");
    $app = new \Slim\Slim(array("templates.path"=>"./vistas"));

    $app->contentType("text/html; charset=utf-8");
    define("BD_SERVIDOR","127.0.0.1");
    define("BD_USUARIO","root");
    define("BD_NOMBRE","biblioteca");
    define("BD_PASSWORD","");
    
    $bd = new PDO("mysql:host=". BD_SERVIDOR. ";dbname=" . BD_NOMBRE . ";charset=utf8" , BD_USUARIO, BD_PASSWORD);
    
    $app->get("/", function() use($app,$bd){
        $solicitud=$app->request();
        $consultaResultante = $bd->prepare("SELECT ip FROM bloqueoip WHERE ip='". $solicitud->getIp(). "'");
        $consultaResultante->execute(); 
        if($consultaResultante->rowCount()>0){
            echo("La ip a sido bloqueada");
            $app->stop();
        }
        $app->render("principal.php", array("contenidoDinamico"=>file_get_contents("vistas/login.php"),
                                           "menu"=>""));
    });
    $app->get("/salir", function() use($app,$bd){
        $solicitud=$app->request();
        guardarBitacora("Sesion terminada: [" .$_SESSION["usuario"]."]", $solicitud->getIp());
        session_unset();
        session_destroy();
        $app->redirect("/bibliotecaseguridad");
    });
    $app->get("/inicio", function() use($app, $bd){
        if(sesionExapirada()){
            $app->redirect("/bibliotecaseguridad/salir");
        }
        if(!empty($_SESSION["fechaPass"])){
            $date=date("Ymd",strtotime("+1 month",strtotime($_SESSION["fechaPass"])));
            if($date < date("Ymd")){
                echo("<script>alert('Por seguridad, su contraseña a expirado y es necesario que realice el cambio');</script>");
            }
        }
        $listaIp= '';
        $listaUsuario='';
        if($_SESSION["rol"]=="administrador"){
            $consultaResultante = $bd->prepare("SELECT id_ip,ip FROM bloqueoip");
            $consultaResultante->execute(); 
            $resultados= $consultaResultante->fetchAll(PDO::FETCH_ASSOC);
            $listaIp = "<form action='elimIp/' method='post'><ul class='list-group'>";
                foreach($resultados as $val){
                    $listaIp .= "<li class='list-group-item'>". $val["ip"]. "<input type='checkbox' name='ip[]' value='". $val
                        ["id_ip"]."'>". "</li>";
                }
            $listaIp.= "</ul><input type='submit' value='Eliminar IP'></form>";
            
            $consultaResultante = $bd->prepare("SELECT p.matricula,p.nombre FROM usuario u, persona p WHERE u.intentosFallidos>=3 AND u.matricula=p.matricula");
            $consultaResultante->execute(); 
            $resultados= $consultaResultante->fetchAll(PDO::FETCH_ASSOC);
            $listaUsuario = "<form action='elimUser/' method='post'><ul class='list-group'>";
                foreach($resultados as $val){
                    $listaUsuario .= "<li class='list-group-item'>". $val["matricula"]. $val["nombre"]. "<input type='checkbox' name='personas[]' value='". $val
                        ["matricula"]."'>". "</li>";
                }
            $listaUsuario.= "</ul><input type='submit' value='Eliminar usuario'></form>";
        }
      $app->render("liberarIp.php", array("ipBloqueada"=>$listaIp,
                                          "listaUser"=>$listaUsuario,
                                          "menu"=>"<li><a href='#camPassword' data-toggle='modal'>Cambiar password</a></li><li><a href='salir'>Cerrar sesion</a></li>"));
        include_once("vistas/camPass.php");
         if($_SESSION["rol"]=="administrador"){
             $app->render("registrar.php");
         }else {
             include_once("vistas/guiAlumno.php");
         }
        
    });
  $app->get("/camPass", function() use($app, $bd){
        if(sesionExapirada()){
            $app->redirect("/bibliotecaseguridad/salir");
        }
        $passActual= $app->request->post("passAnt");
        $passNueva= $app->request->post("passNueva");
        
        $consultaResultante = $bd->prepare("SELECT matricula FROM pass WHERE password=sha1('$passActual') AND matricula='".               $_SESSION["usuario"] ."';");
        $consultaResultante->execute();
       if($consultaResultante->rowCount()>0){
            $consultaResultante = $bd->prepare("SELECT matricula FROM pass WHERE password=sha1('$passNueva') AND matricula='".                 $_SESSION["usuario"] ."';");
            $consultaResultante->execute();
            if($consultaResultante->rowCount() == 0){
                $consultaResultante = $bd->prepare("SELECT fecha from pass where matricula='". $_SESSION["usuario"] ."' ORDER BY fecha                   ASC LIMIT 1;");
                $consultaResultante->execute();
                $consultaResultante2= $bd->prepare("DELETE FROM pass WHERE matricula='". $_SESSION["usuario"] ."' AND                                     fecha='$consultaResultante';");
                $consultaResultante2->execute();
            }
        }else {
           /* $consultaResultante = $bd->prepare("INSERT INTO pass(password) values('$passNueva')");
            $consultaResultante->execute(); 
            if($consultaResultante){
               echo("<script>alert('El cambio de contraseña se a realizado correctamente');</script>"); 
            }else {
                echo("<script>alert('No se realizó el cambio');</script>"); 
            }*/
        }
    });
    $app->post("/login/", function() use($app,$bd){
        expiraSesion();
        $solicitud=$app->request();
        $user= $app->request->post("user");
        $pass= $app->request->post("pass");
        if(empty($_SESSION["intentosIp"])){
            $_SESSION["intentosIp"]=0;
        }
        if(empty($_SESSION["$user"])){
            $consultaResultante = $bd->prepare("SELECT intentosFallidos FROM usuario WHERE matricula='$user'");
            $consultaResultante->execute(); 
            $resultados= $consultaResultante->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION["$user"]=$resultados[0]["intentosFallidos"];
        }
        $consultaResultante = $bd->prepare("SELECT p.matricula, p.nombre, p.apellidos, u.rol, ps.fecha FROM persona p, usuario u, pass ps WHERE p.matricula= u.matricula AND u.matricula=ps.matricula AND u.matricula='$user' AND ps.password=sha1('$pass') AND u.intentosFallidos<3");
        $consultaResultante->execute();
        if($consultaResultante->rowCount()>0){ //Si hay usuarios
            $resultados= $consultaResultante->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION["usuario"]=$resultados[0]["matricula"];
            $_SESSION["nombreCompleto"]=$resultados[0]["nombre"]. " ". $resultados[0]["apellidos"];
            $_SESSION["rol"]=$resultados[0]["rol"];
            $_SESSION["intentosIp"]=0;
            $_SESSION["fechaPass"]=$resultados[0]["fecha"];
            $solicitud=$app->request();
            guardarBitacora("Inicio de sesion del usuario: [$user]", $solicitud->getIp());
            $app->redirect("/bibliotecaseguridad/inicio");
        }else{
            $_SESSION["$user"] +=1;
            $consultaResultante = $bd->prepare("UPDATE usuario SET intentosFallidos=".$_SESSION["$user"]. " WHERE matricula='$user'");
            $consultaResultante->execute();
            $_SESSION["intentosIp"] += 1;
            if($_SESSION["$user"]>2){
                guardarBitacora("Usuario bloqueado por exceso de intentos: [$user]", $solicitud->getIp());
            }
            $solicitud=$app->request();
            if($_SESSION["intentosIp"]>8){
                $consultaResultante = $bd->prepare("INSERT INTO bloqueoip(ip) VALUES('". $solicitud->getIp(). "')");
                $consultaResultante->execute(); 
                guardarBitacora("IP bloqueada por exceso de intentos: [$user]", $solicitud->getIp());
                $_SESSION["intentosIp"]=0;
                $app->redirect("/bibliotecaseguridad/salir");
                $app->stop();
            }
            guardarBitacora("Ip intentos: [". $_SESSION["intentosIp"]. "]", $solicitud->getIp());
            guardarBitacora("Usuario intentos: [". $_SESSION["$user"]. "]", $solicitud->getIp());
            $app->redirect("/bibliotecaseguridad");
        }
            
    });

    $app->post("/elimIp/", function() use($app,$bd){
        if(sesionExapirada()){
            $app->redirect("/bibliotecaseguridad/salir");
        }
        $ip= $app->request->post("ip");
        foreach($ip as $val){
            $consultaResultante = $bd->prepare("DELETE FROM bloqueoip WHERE id_ip='$val'");
            $consultaResultante->execute(); 
            $solicitud=$app->request();
            guardarBitacora("Se ha liberado el usuario con IP", $solicitud->getIp());
        }
        $app->redirect("/bibliotecaseguridad/inicio");
    });

     $app->post("/elimUser/", function() use($app,$bd){
        if(sesionExapirada()){
            $app->redirect("/bibliotecaseguridad/salir");
        }
        $usuario= $app->request->post("personas");
        foreach($usuario as $val){
            $consultaResultante = $bd->prepare("UPDATE usuario SET intentosFallidos=0 WHERE matricula='$val'");
            $consultaResultante->execute(); 
             $solicitud=$app->request();
            guardarBitacora("Se ha liberado el usuario [" .$val ."]", $solicitud->getIp());
        }
        $app->redirect("/bibliotecaseguridad/inicio");
    });
    $app->post("/userRegist/", function() use($app,$bd){
        $user= $app->request->post("user");
        $pass= $app->request->post("pass");
        $nombre= $app->request->post("nombre");
        $apellidos= $app->request->post("apellidos");
        $rol= $app->request->post("rol");
        $consultaResultante = $bd->prepare("INSERT INTO usuario(matricula,rol,intentosFallidos) VALUES('$user','$rol',0)");
        $consultaResultante->execute(); 
        $consultaResultante = $bd->prepare("INSERT INTO persona(matricula,nombre,apellidos) VALUES('$user','$nombre','$apellidos')");
        $consultaResultante->execute(); 
        $consultaResultante = $bd->prepare("INSERT INTO pass(matricula,password,fecha) VALUES('$user',sha1('$pass'),". date("Ymd").")");
        $consultaResultante->execute(); 
        $solicitud=$app->request();
        guardarBitacora("Se ha registrado el usuario [" .$user."]", $solicitud->getIp());
        echo("<script>alert('El usuario se registro correctamente')</script>");
        $app->redirect("/bibliotecaseguridad/inicio");
    });

    $app->run();
    
   // function 
    function guardarBitacora($mensaje, $ip){
       date_default_timezone_set("America/Mexico_City");
       escribirArchivo("bitacora.log",date("Y-M-D H:m:s"). " ". $mensaje. " ". $ip, "a");
    }
    function escribirArchivo($nombre, $cadena, $tipo){
        $file = fopen($nombre,$tipo);
        fwrite($file, $cadena. PHP_EOL);
        fclose($file);
    }
    function expiraSesion(){
        @session_start();
        $_SESSION["intervalo"]=1;
        $_SESSION["inicio"]=time();
    }
    function sesionExapirada(){
        @session_start();
        $segundos=time();
        $tiempoTranscurrido=$segundos;
        if(empty($_SESSION["inicio"]) || empty($_SESSION["intervalo"])) {
            return false;
        }
        $tiempoMax= $_SESSION["inicio"] + ($_SESSION["intervalo"]*60);
        if($tiempoMax < $tiempoTranscurrido){
            return true;
        }
    }
    function validarPass($password, $tamMax, $tamMin){
        if(strlen($password)<$tamMin || strlen($password)>$tamMax){
            return -1; //fuera de limite
        }
        if(!preg_match("/[a-z/]",$password)){
            return 0; // no tiene minuscula
        }
        if(!preg_match("/[A-Z/]",$password)){
            return 0; // no tiene mayuscula
        }
        if(!preg_match("/[0-9/]",$password)){
            return 0; // no tiene #
        }
        return 1;
    }
?>