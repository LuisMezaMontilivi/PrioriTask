<?php
class BdD {
	
    private static $connection;

	//La base de dades està posada de manera fixa al codi
    public static function connect() {
        self::$connection = new PDO("mysql:host=127.0.0.1;dbname=PrioriTaskBD", "adminer", "Bhun@89ble.oient");
    }

	//Tancar conexió a la BdD
    public static function close() {
        self::$connection = null;
    }
	
	//Funcionalitats a la BdD

}

?>