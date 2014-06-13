<?php

require "HTTP.php";

$connection = Http::connect('velkoobchod.erostore.cz/api');


echo "<pre>";

try {
	$data = [
		"objednavka" => [
			"jmeno"			 => "John",
			"prijmeni"		 => "Doe",
			"email"			 => "john.doe@example.com",
			"telefon"		 => 123654789,
			"adresa"		 => "U stodoly 15",
			"poznamka"		 => "Děkuji",
			"dodaci_metoda"	 => 2,
			"mesto"			 => "České Budějovice",
			"psc"			 => "370 01",
			"zeme"			 => "ČR",
		],
		"produkty"	 => [
			["id" => 1206, "pocet" => 1, "cena" => 600],
			["id" => 1827, "pocet" => 1, "cena" => 700]
		]
	];

	print_r($connection->doPost("nova-objednavka?key=.......................................&development=1", $data));
} catch (Exception $exc) {
	if ($exc instanceof Api_Exception) {
		echo $exc->getMessage() . "\n";
	}
	if ($exc instanceof Http_Exception) {
		echo $exc->getMessage() . "\n";
	}
} 
