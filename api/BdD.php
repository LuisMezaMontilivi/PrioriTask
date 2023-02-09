<?php
class BdD {
	
    private static $connection;

	//La base de dades està posada de manera fixa al codi
    public static function connect() {
        self::$connection = new PDO("mysql:host=localhost;dbname=PrioriTaskBD", "adminer", "1234");
    }

	//Tancar conexió a la BdD
    public static function close() {
        self::$connection = null;
    }
	
	//Funcionalitats a la BdD
    
    /* Function: guardarTokenBD
    
    */
    public static function guardarTokenBD($token){
		$guardat = true;
		try{
			$consulta = (BdD::$connection)->prepare("
				INSERT INTO TokenGenerat (Token)
				VALUES (:token);			
			");
			$consulta->bindParam('token',$token);
			$consulta->execute();
		}
		catch(PDOException $e){
			$guardat = false;
		}
		return $guardat;
	}

    /* Function: existeixTokenInicialBD

        Retorna un boleà si existeix o no

        Parameters:
            $token a cercar

        Returns:
            True o false si existeix o no
    */
    public static function existeixTokenInicialBD($token){
		$output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT *
                FROM TokenGenerat tg 
                WHERE token = :token ;
                "
            );
            $query->bindParam(':token', $token);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            $output = count($outcome)>0;
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;
	}

    /*  Function: guardarTascaBD

        Crea una tasca a la BdD

        Parameters:
            $tasca  -   Array associatiu on tindrà tot el contingut d'una tasca

    */
    public static function guardarTascaBD($tasca){
		$guardat = true;
		try{
			$consulta = (BdD::$connection)->prepare("
                INSERT INTO Tasca (titol,descripcio,prioritat,estat,id_gestor,id_tecnic)
                VALUES (:titol, :descripcio, :prioritat, :estat, :gestor, :tecnic);				
			");
			$consulta->bindParam('titol',$tasca["titol"]);
            $consulta->bindParam('descripcio',$tasca["descripcio"]);
            $consulta->bindParam('prioritat',$tasca["prioritat"]);
            $consulta->bindParam('estat',$tasca["estat"]);
            $consulta->bindParam('gestor',$tasca["gestor"]);
            $consulta->bindParam('tecnic',$tasca["tecnic"]);
			$consulta->execute();
		}
		catch(PDOException $e){
			$guardat = false;
		}
		return $guardat;
	}

    /* Function: relacionatsTascaBD

        Recupera d'una tasca el gestor i tècnic relacionats

        Parameters:
            $idTasca -  Tasca a cercar els tokens relacionats

        Returns:
            Array associatiu amb token_gestor i token_tecnic
    */
    public static function relacionatsTascaBD($idTasca){
		$output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT tas.data_inici, tas.estat, g.token AS token_gestor, t.token AS token_tecnic 
                FROM Tasca tas 
                    JOIN Usuari g 
                        ON (tas.id_gestor = g.id_usuari) 
                    JOIN Usuari t 
                        ON (tas.id_tecnic = t.id_usuari)
                WHERE id_tasca = :id ;
                "
            );
            $query->bindParam(':id', $idTasca);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            if(count($outcome)>0){
                $output["estat"] = $outcome[0]["estat"];
                $output["data_inici"] = $outcome[0]["data_inici"];
                $output["token_gestor"] = $outcome[0]["token_gestor"];
                $output["token_tecnic"] = $outcome[0]["token_tecnic"];
            }
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;
	}

    /* Function: modificarTascaTecnicBD

        Modifica de la tasca el comentari i l'estat

        Parameters:
            $tasca - Array associatiu amb id, comentari
     */
    public static function modificarTascaTecnicBD($tasca,$estat){
        $modificar = true;
		try{
            $sql = "UPDATE Tasca
                    SET comentari = :comentari,
                        estat = :estat ";
            if(isset($tasca["estat"]) && $tasca["estat"]!=$estat["estat"]){//si tenim un nou estat que és diferent de l'anterior
                if($tasca["estat"] == "p" && $estat["estat"]=="s") //si modifiquem a "en progress" des de "per iniciar" caldrà posar data inici
                    $sql = $sql . ", data_inici = CURRENT_TIMESTAMP ";
                elseif($tasca["estat"] == "d")//i si modifiquem a done caldrà posar data acabament
                    $sql = $sql . ", data_acabament = CURRENT_TIMESTAMP ";
            }
            $sql = $sql . " WHERE id_tasca = :id ;";
			$consulta = (BdD::$connection)->prepare($sql);
			$consulta->bindParam('estat',$tasca["estat"]);
			$consulta->bindParam('comentari',$tasca["comentari"]);
            $consulta->bindParam('id',$tasca["id"]);
			$consulta->execute();
		}
		catch(PDOException $e){
			$modificar = false;
		}
		return $modificar;
    }

    /* Function: modificarTascaGestorBD

        Modifica de la tasca el titol, descripcio, prioritat, estat, comentari i id_tecnic de la tasca

        Parameters:
            $tasca - Array associatiu amb els nous paràmetres de la tasca
            $estat - Array associatiu amb antics estat de la tasca
     */
    public static function modificarTascaGestorBD($tasca,$estat){
        $modificar = true;
		try{
            $sql = "UPDATE Tasca
                    SET titol=:titol,
                        descripcio=:descripcio,
                        prioritat=:prioritat,
                        id_tecnic=:tecnic,
                        estat=:estat ";
            if(isset($tasca["estat"]) && $tasca["estat"]!=$estat["estat"]){//si tenim un nou estat que és diferent de l'anterior
                if($tasca["estat"] == "p" && $estat["estat"]=="s") //si modifiquem a "en progress" des de "per iniciar" caldrà posar data inici
                    $sql = $sql . ", data_inici = CURRENT_TIMESTAMP ";
                elseif($tasca["estat"] == "d")//i si modifiquem a done caldrà posar data acabament
                    $sql = $sql . ", data_acabament = CURRENT_TIMESTAMP ";
            }
            $sql = $sql . " WHERE id_tasca = :id ;";
			$consulta = (BdD::$connection)->prepare($sql);
            $consulta->bindParam('titol',$tasca["titol"]);
			$consulta->bindParam('descripcio',$tasca["descripcio"]);
            $consulta->bindParam('prioritat',$tasca["prioritat"]);
            $consulta->bindParam('estat',$tasca["estat"]);
            $consulta->bindParam('tecnic',$tasca["tecnic"]);
            $consulta->bindParam('id',$tasca["id"]);
			$consulta->execute();
		}
		catch(PDOException $e){
			$modificar = false;
            echo $e->getMessage();
		}
		return $modificar;
    }
}

?>