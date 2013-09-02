<?php

$data = array(
	'status' => $status
);

if (isset($errors) && !empty($errors)) {
	$data['errors'] = $errors;
}

echo json_encode($data);