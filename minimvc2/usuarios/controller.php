<?php
require_once('constants.php');
require_once('model.php');
require_once('view.php');
session_start();

//autenticacio, abans de passar al handler
if(isset($_SESSION["userName"])) {
	echo "identificado";
	handler();
}
else{
	//La sessio s'assigna si coincideixen username i password
	if($_POST['userName'] == 'paco' && $_POST['password'] == '1111'){
		$_SESSION["userName"] = $_POST['userName'];
		handler();
	}
	else{
		//Nomes mostra aquest missatge en enviar dades del form
		if(isset($_POST['userName'])){
			$data = array('mensaje'=>"Usuari o password incorrectes");
		};
		retornar_vista(VIEW_LOGIN_USER, $data);	
	}
	
}

function handler() {
	$event = VIEW_GET_USER;
	$uri = $_SERVER['REQUEST_URI'];
	$peticiones = array(SET_USER, GET_USER, DELETE_USER, EDIT_USER,
	VIEW_SET_USER, VIEW_GET_USER, VIEW_DELETE_USER,
	VIEW_EDIT_USER);
	foreach ($peticiones as $index=>$peticion) {
		$uri_peticion = MODULO.$peticion.'/';
		if( strpos($uri, $uri_peticion) == true ) {
			$event = $peticion;
			break;
		}
	}
	//echo $event;
	
	$user_data = helper_user_data();
	$usuario = set_obj();
	
	switch ($event) {
		case SET_USER:
		$usuario->set($user_data);
		$data = array('mensaje'=>$usuario->mensaje);
		retornar_vista(VIEW_SET_USER, $data);
		break;
		case GET_USER:
		$usuario->get($user_data);
		$data = array(
		'nombre'=>$usuario->nombre,
		'apellido'=>$usuario->apellido,
		'email'=>$usuario->email
		);
		retornar_vista(VIEW_EDIT_USER, $data);
		break;
		case DELETE_USER:
		$usuario->delete($user_data['email']);
		$data = array('mensaje'=>$usuario->mensaje);
		retornar_vista(VIEW_DELETE_USER, $data);
		break;
		case EDIT_USER:
		$usuario->edit($user_data);
		$data = array('mensaje'=>$usuario->mensaje);
		retornar_vista(VIEW_GET_USER, $data);
		break;
		default:
		retornar_vista($event);
	}
}

function set_obj() {
	$obj = new Usuario();
	return $obj;
}

function helper_user_data() {
	$user_data = array();
	if($_POST) {
		if(array_key_exists('nombre', $_POST)) {
			$user_data['nombre'] = $_POST['nombre'];
		}
		if(array_key_exists('apellido', $_POST)) {
			$user_data['apellido'] = $_POST['apellido'];
		}
		if(array_key_exists('email', $_POST)) {
			$user_data['email'] = $_POST['email'];
		}
		if(array_key_exists('clave', $_POST)) {
			$user_data['clave'] = $_POST['clave'];
		}
	} else if($_GET) {
		if(array_key_exists('email', $_GET)) {
			$user_data = $_GET['email'];
		}
	}
	return $user_data;
}

?>