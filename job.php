<?php
include "lib.php";

$messages = [
			'type' => 'text',
			'text' => $_GET['message'] 
		];	

pushMessage($messages);		