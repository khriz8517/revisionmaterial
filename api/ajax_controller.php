<?php

// use core_completion\progress;
// use core_course\external\course_summary_exporter;

error_reporting(E_ALL);
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/enrol/externallib.php');

try {
	global $USER, $PAGE;
	$details = $_POST;
	$returnArr = array();

	if (!isset($_REQUEST['request_type']) || strlen($_REQUEST['request_type']) == false) {
		throw new Exception();
	}

	switch ($_REQUEST['request_type']) {
		case 'getMateriales':
			$returnArr = getMateriales();
			break;
		case 'materialesMarcadosByUser':
			$materialid = $_REQUEST['materialid'];
			$sesskey = $_REQUEST['sesskey'];
			$returnArr = materialesMarcadosByUser($materialid, $sesskey);
			break;
	}

} catch (Exception $e) {
	$returnArr['status'] = false;
	$returnArr['data'] = $e->getMessage();
}

header('Content-type: application/json');

echo json_encode($returnArr);
exit();

/**
 * getMateriales
 * * obtiene los registros para la actividad revision material
 */
function getMateriales(){
	global $DB, $USER;

	$data = [];
	$materiales = $DB->get_records('aq_material_data', [
		'active' => 1
	]);

	foreach ($materiales as $key => $value) {
		$if_marked = $DB->get_records('aq_material_revisado_data', [
			'userid' => $USER->id,
			'materialid' => $value->id
		]);
		array_push($data, [
			'id' => $value->id,
			'material_title' => $value->material_title,
			'material_icon' => $value->material_icon,
			'link_file' => $value->link_file,
			'format' => $value->format,
			'marked' => count($if_marked) ? true : false

		]);
	}
	return $data;
}

/**
 * materialesMarcadosByUser
 * * son los materiales marcasdos por un usuario
 * @param materialid es el id del material
 * @param sesskey es la sesion del usuario
 */

function materialesMarcadosByUser($materialid, $sesskey){
	global $DB, $USER;
	require_sesskey();

	$if_marked = $DB->get_records('aq_material_revisado_data', [
		'userid' => $USER->id,
		'materialid' => $materialid
	]);

	if(count($if_marked)){
		foreach ($if_marked as $key => $value) {
			$data = array(
				'id' => $value->id,
				'userid' => $USER->id,
				'materialid' => $materialid,
				// 'updated_at' => time()
			);
			$DB->delete_records('aq_material_revisado_data', $data);
		}
		return 'updated';
	}else{
		$data = array(
			'userid' => $USER->id,
			'materialid' => $materialid,
			'created_at' => time()
		);
		$insert_id = $DB->insert_record('aq_material_revisado_data', $data);
		return 'inserted';
	}
}

// TODO: implementar funciones crud de las tablas generadas

/**
 * insertMaterial
 * * funcion para agregar materiales
 * ! los iconos son basados en GOOGLE ICONS
 * @param material_title el titulo del material
 * @param link_file el link del material
 * @param material_icon el icono del material a mostrar
 * @param format el formato del @link_file ej. [.pdf, .mp4, .jpg, .???]
 * @param active 1 = el material se mostrara, 0 = el material NO se mostrara
 * @param sesskey es la sesion del usuario
 */
function insertMaterial($material_title, $link_file, $material_icon, $format, $active, $sesskey){
	global $DB;
	require_sesskey();

	$data = array(
		'material_title' => $material_title,
		'link_file' => $link_file,
		'material_icon' => $material_icon,
		'format' => $format,
		'active' => $active,
		'created_at' => time()
	);
	$insert_id = $DB->insert_record('aq_material_data', $data);
	return 'inserted';
}

/**
 * actualizarMaterial
 * * funcion para actualizar materiales
 * ! los iconos son basados en GOOGLE ICONS
 * @param materialid el identificador del material a actualizar
 * @param material_title el titulo del material
 * @param link_file el link del material
 * @param material_icon el icono del material a mostrar
 * @param format el formato del @link_file ej. [.pdf, .mp4, .jpg, .???]
 * @param active 1 = el material se mostrara, 0 = el material NO se mostrara
 * @param sesskey es la sesion del usuario
 */
function actualizarMaterial($materialid, $material_title, $link_file, $material_icon, $format, $active, $sesskey){
	global $DB;
	require_sesskey();

	$data = array(
		'id' => $materialid,
		'material_title' => $material_title,
		'link_file' => $link_file,
		'material_icon' => $material_icon,
		'format' => $format,
		'active' => $active,
		'updated_at' => time()
	);
	$DB->update_record('aq_material_data', $data);
	return 'updated';
}

/**
 * eliminarMaterial
 * * funcion para eliminar materiales
 * @param materialid el identificador del material a eliminar
 * @param sesskey es la sesion del usuario
 */
function eliminarMaterial($materialid, $sesskey){
	global $DB;
	require_sesskey();

	$data = array(
		'id' => $materialid
	);
	$DB->delete_records('aq_material_data', $data);
	return 'deleted';
}