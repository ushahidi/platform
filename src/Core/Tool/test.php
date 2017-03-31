<?php

include("./Signer.php");

$signer = new Signer("testestest");

$post = array(
	"id" => "1",
	"title" => "Update test post title",
  "webhook_uuid" => "test-test-test",
  "api_key" => "thisisatestapikeystring"
);
$fullUrl = "/api/v3/webhooks/posts";
$signature = $signer->sign($fullUrl, json_encode($post));
echo $signature;
?>
