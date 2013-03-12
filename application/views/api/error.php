<?php
	echo json_encode(
		array(
			'errors' => array(
				array(
					'message' => $message,
					'code' => $code,
					'class' => $class,
					'file' => $file,
					'line' => $line,
					'trace' => $trace
				)
			)
			)
		);
