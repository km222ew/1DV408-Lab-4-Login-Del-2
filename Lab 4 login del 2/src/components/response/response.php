<?php

//Manages server responses (html, json?)
class Response {

	//Should use %e instead of %d, but i get no ouput at all using that..
	private function customDate() {
		setlocale(LC_ALL, 'swedish');
		// setlocale(LC_ALL, 'sv_SE.UTF-8');

		return utf8_encode(ucfirst(strftime('%A, den %d %B %Y. '))) . strftime('Klockan är [%H:%M:%S].');
		// return ucfirst(strftime('%A, den %d %B %Y. ')) . strftime('Klockan är [%H:%M:%S].');
	}

	//Renders html page
	public function HTMLPage($body, $notifyView) {
		if ($body === NULL) {
			throw new Exception('HTMLView::echoHTML does not allow body to be null');
		}

		$notifications = '';

		//Don't fetch notifications on post, these pages should never be shown
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$notifications = $notifyView->showAll();
		}

		$date = $this->customDate();
		
		echo "
			<!DOCTYPE html>
			<html>
			<head>
				<title>1dv408, l2-login</title>
				<meta charset='utf-8'>
				<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
			</head>
			<body>
				<div class='container'>
				$notifications
				$body
				<br />
				$date
				<hr>
				</div>
				<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
				<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
			</body>
			</html>";
	}
}