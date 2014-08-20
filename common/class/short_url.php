<?php
class short_url {

	public static function shortener($long_url){
		$url = "https://www.googleapis.com/urlshortener/v1/url";
		$postfields = array('longUrl' => $long_url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
		));
		
		$response = curl_exec($ch);
		
		if (curl_errno($ch)) {
			return false;
		}
		curl_close($ch);
		
		$response = json_decode($response, true);
		return $response['id'];
	}
}
?>