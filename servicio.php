<?php
error_reporting(E_ALL);

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL & ~E_DEPRECATED);

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Allow: GET, POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
  http_response_code(200);
  exit;
}

if (isset($_GET["PING"])) {
  exit;
}

date_default_timezone_set("America/Matamoros");

if (isset($_GET["DATETIME"])) {
  echo date("Y-m-d H:i:s");
  exit;
}


// ------------------------------------------------------
// ------------------------------------------------------
// Debajo de este comentario irá la configuración a la BD
// y las funciones del servicio para la aplicación móvil.
session_start(); // Iniciar sesión para manejar autenticación y otras funcionalidades relacionadas con el usuario.

require "conexion.php";
require "enviarCorreo.php";

$con = new Conexion(array(  //phpMyAdmin MySQL
  "tipo"       => "mysql",
  "servidor"   => "localhost",
  "bd"         => "practica6",
  "usuario"    => "root",
  "contrasena" => ""
));

if (isset($_GET["btn_registrar"])) { //Para el registro desde la pagina de login
  $nombre = $_POST["txt_usuario_registro"];
  $email = $_POST["txt_email_registro"];
  $contrasena = $_POST["txt_contrasena_registro"];

  $checkNombre = $con->select("usuarios", "nombre")
                   ->where("nombre","=",$nombre)
                   ->execute();

  $checkEmail = $con->select("usuarios", "email")
                    ->where("email","=",$email)
                    ->execute();
  if (!empty($checkNombre)) {
    echo json_encode(["status" =>"error", "message" => "¡El nombre de usuario ya existe!"]);
    exit;
  }else if (!empty($checkEmail)) {
    echo json_encode(["status" =>"error", "message" => "¡Ya hay una cuenta con este correo!"]);
    exit;
  } else {
    $con->insert("usuarios", [
      "nombre" => $nombre,
      "contrasena" => $contrasena,
      "email" => $email,
      "id_tipousuario" => 2
    ])->execute();
    header("Content-Type: application/json");
    echo json_encode(["status" => "ok"]);
    exit;
  }
    
}
elseif(isset($_GET["btn_login"])) { //Para el login desde la pagina de login
  $nombre = $_POST["txt_usuario"];
  $contrasena = $_POST["txt_contrasena"];

  $datos = $con->select("usuarios", "*")
               ->where("nombre","=",$nombre)
               ->execute();

  if (!empty($datos)) {
    $usuario = $datos[0]; // primer registro

    if ($contrasena == $usuario["contrasena"]) { // no se esta usando password_hash, por lo que se compara directamente
      
      $_SESSION["id_usuario"] = $usuario["id_usuario"];
      $_SESSION["nombre"] = $usuario["nombre"];
      $_SESSION["id_tipousuario"] = $usuario["id_tipousuario"];

      echo json_encode([
        "status" => "ok",
        "id_usuario" => $usuario["id_usuario"],
        "nombre" => $usuario["nombre"],
        "email" => $usuario["email"],
        "id_tipousuario" => $usuario["id_tipousuario"]
        
        
      ]);
      exit;
    }
  }
  echo json_encode(["status" => "error", "message" => "¡Nombre de usuario o contraseña incorrectos!"]);
  exit;
}
?>

