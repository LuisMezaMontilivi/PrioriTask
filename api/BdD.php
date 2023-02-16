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
    
        Emmagatzema un token no identificatiu a la BdD

        Returns:
            Boleà si ha fet o no la inserció
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
                VALUES (:titol, :descripcio, :prioritat, :estat, ( SELECT id_usuari FROM Usuari WHERE token = :gestor ), :tecnic);				
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
            Array associatiu amb token_gestor, token_tecnic, estat i data_inici
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
            $estat - Array associatiu amb l'estat anterior

        Returns:
            Retorna un boleà de si s'ha pogut fer o no la modificació
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

        Returns:
            Retorna un boleà de si s'ha pogut fer o no la modificació
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

    /* Function: recuperarLlistatTasquesBD($token)

        Obté el llistat de tasques relacionades amb l'usuari del token

        Returns:
            Retorna un array d'arrays associatius de les diferents tasques

    */
    public static function recuperarLlistatTasquesBD($token){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT tas.id_tasca, tas.titol, tas.descripcio, tas.data_alta, tas.data_inici, tas.data_acabament , tas.prioritat , tas.estat , tas.comentari,
                id_gestor, id_tecnic, t.nom AS nom_tecnic
                FROM Tasca tas
                JOIN Usuari g
                    ON (g.id_usuari = id_gestor)
                JOIN Usuari t 
                    ON (t.id_usuari = id_tecnic)
                WHERE g.token = :tokenG OR t.token = :tokenT ;
                "
            );
            $query->bindParam(':tokenG', $token);
            $query->bindParam(':tokenT', $token);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            if(count($outcome)>0){
                $output = $outcome;
            }
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;
    }
    
    /* Function: loginBD
        Comprova si existeix un usuari amb el mail i password concrets, retorna el seu id i mail
        Parameters:
            $email, $contrasenya
        Returns:
            False en cas de no existir, array de 3 posicions 0=id 1=email 2= ultima_peticio 3= rol
    */

    public static function loginBD($email, $contrasenya){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT  * from Usuari u 
                where email = :email && contrasenya = :contrasenya
                ;
                "
            );
            $query->bindParam(':email', $email);
            $query->bindParam(':contrasenya', $contrasenya);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            if(count($outcome)>0){
                $output[0] = $outcome[0]["id_usuari"];
                $output[1] = $outcome[0]["email"];
                $output[2] = $outcome[0]["data_ultima_peticio"];
                $output[3] = $outcome[0]["rol"];
            }
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;
    }

      /* Function: guardarTokenIdentificatiuBD
        Retorna un boleà de false o el nombre de rows modificades en cas d'èxit
        Parameters:
            $tokenIdentificatiu, $id on insertar-lo
        Returns:
            False o Nombre de rows afectades
    */

    public static function guardarTokenIdentificatiuBD($tokenIdentificatiu, $id){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                Update Usuari 
                set token = :token
                WHERE id_usuari = :id
                ;
                "
            );
           
            $query->bindParam(':token', $tokenIdentificatiu);
            $query->bindParam(':id', $id);
            $outcome = $query->execute();
            $output =  $query->rowCount();
           
         }
         catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $output;
    }


       /* Function: canviContrasenyaBD
        Retorna un boleà de false o el nombre de rows modificades en cas d'èxit
        Parameters:
            $token, $contrasenya
        Returns:
            False o Nombre de rows afectades
    */
    public static function canviContrasenyaBD($token, $contrasenya){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                Update Usuari 
                set contrasenya = :contrasenya
                WHERE token = :token
                ;
                "
            );
            $query->bindParam(':contrasenya', $contrasenya);
            $query->bindParam(':token', $token);
            $outcome = $query->execute();
            $output =  $query->rowCount();
           
         }
         catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $output;

    }



    /* Function: validaTokenBD
        A partir del token donat, consulta a la BdD si existeix un usuari amb el determinat token
        Parameters:
            $tokenAValidar - token a comprovar
        Returns: rol del usuari o bé un bool de false en cas de no trobar-lo
    */
    public static function validaTokenBD($tokenAValidar){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                Select rol from Usuari WHERE token = :token ;
                ;
                "
            );
            $query->bindParam(':token', $tokenAValidar);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            if(count($outcome)== 1){
                $output = $outcome[0]["rol"];
            }
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;
    }
        /* Function: consultaUsuarisBD
        Retorna un llistat de tots els usuaris de l'aplicació
        Returns: array d'usuaris de l'aplicació
        */
    public static function consultaUsuarisBD(){

        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT id_usuari , nom, email , rol, data_alta , data_baixa , data_ultima_peticio  from Usuari u ;
                ;
                "
            );
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $output = $query->fetchAll();

        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;

    }


    /* Function: consultaTecnicsBD
        Retorna un llistat de tots els tècnics de l'aplicació amb el seu nom, email i id
        Returns: array de tècnics de l'aplicació amb el seu nom, email i id
    */
        public static function consultaTecnicsBD(){

            $output = false;
            try{
                $query = (self::$connection)->prepare(
                    "
                    SELECT id_usuari , nom , email  from Usuari u WHERE rol ='t';
                    ;
                    "
                );
                $query->execute();
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $output = $query->fetchAll();
    
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
                $output = $e->getMessage();
            }
            return $output;
    
        }



         /* Function: creaUsuariBD
          
           A partir de les dades rebudes en forma d'array associatiu, inserció del nou usuari amb les dades rebudes i retorn de booleà de l'execució de la query
           
           Returns: bool resultat de l'execució
        */  

        public static function creaUsuariBD($infoUsuari){
            $output = false;
            try{
                $query = (self::$connection)->prepare(
                    "
                    insert into Usuari (nom, contrasenya, email, rol)
                    values(:nom, :contrasenya, :email, :rol)
                    ;
                    "
                );
                $query->bindParam(':nom', $infoUsuari["nom"]);
                $query->bindParam(':contrasenya', $infoUsuari["contrasenya"]);
                $query->bindParam(':email', $infoUsuari["email"]);
                $query->bindParam(':rol', $infoUsuari["rol"]);
                $output=$query->execute();
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
                $output = $e->getMessage();
            }
            return $output;

        }

         /* Function: modificaUsuariBD
          
           A partir de les dades rebudes en forma d'array associatiu, inserció del nou usuari amb les dades rebudes i retorn de booleà de l'execució de la query
           
           Returns: bool resultat de l'execució
        */  

        public static function modificaUsuariBD($infoUsuari){
            $output = false;
            try{
                if($infoUsuari["actiu"]){
                    $query = (self::$connection)->prepare(
                        "
                        Update Usuari
                        set nom = :nom ,
                        email = :email,
                        rol = :rol ,
                        data_baixa = NULL
                        WHERE id_usuari = :id_usuari;
                        "
                    );
                }
                else if($infoUsuari["actiu"] == false){
                    $query = (self::$connection)->prepare(
                        "
                        Update Usuari
                        set nom = :nom ,
                        email = :email,
                        rol = :rol,
                        data_baixa = CURRENT_TIMESTAMP
                        WHERE id_usuari = :id_usuari
                        ;
                        "
                    );
                }
                $query->bindParam(':id_usuari', $infoUsuari["id_usuari"]);
                $query->bindParam(':nom', $infoUsuari["nom"]);
                $query->bindParam(':email', $infoUsuari["email"]);
                $query->bindParam(':rol', $infoUsuari["rol"]);
                $output=$query->execute();
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
                $output = $e->getMessage();
            }
            return $output;
        }

        /* Function: updateDataUltimaPeticioBD
          
            A partir del token de l'usuari, actualitzar la data de la darrera petició al moment de realització
           
           Returns: bool resultat de l'execució
        */  
        public static function updateDataUltimaPeticioBD($token){
            $output = false;
            try{
                $query = (self::$connection)->prepare(
                    "
                    UPDATE Usuari 
                    set data_ultima_peticio = CURRENT_TIMESTAMP 
                    where token = :token; 
                    "
                );
                $query->bindParam('token', $token);
                $output = $query->execute();
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
                $output = $e->getMessage();
            }
            return $output;

        }

		/* Function: recuperarEstatTasquesBD
          
            Obté l'estat de les tasques de l'últim més i les pendents (per començar, en progrés i en incidència) de més temps
           
           Returns: Array associatiu amb l'estat de les tasques de l'últim més 
        */  
        public static function recuperarEstatTasquesBD(){
            $output = false;
			try{
				$query = (self::$connection)->prepare(
					"
					SELECT 'mes_actual' AS mes, estat, COUNT(*) AS tasques
						FROM Tasca t
						WHERE data_alta > (CURRENT_TIMESTAMP - INTERVAL 1 MONTH)
						GROUP BY estat
					UNION
					SELECT 'pendents' AS mes, estat, COUNT(*) AS tasques
						FROM Tasca t
						WHERE data_alta < (CURRENT_TIMESTAMP - INTERVAL 1 MONTH) AND estat IN ('s','p','e')
						GROUP BY estat;
					"
				);
				$query->execute();
				$query->setFetchMode(PDO::FETCH_ASSOC);
				$outcome = $query->fetchAll();
				if(count($outcome)>0){
					$output = $outcome;
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
				$output = $e->getMessage();
			}
			return $output;
        }

		/* Function: recuperarEstatUsuarisBD
          
            Obté l'estat d'activitat dels usuaris en l'últim dia, setmana, més i any
           
           Returns: Array associatiu amb l'estat de les usuaris de l'últim més 
        */  
        public static function recuperarEstatUsuarisBD(){
            $output = false;
			try{
				$query = (self::$connection)->prepare(
					"
					SELECT 
						(SELECT COUNT(*) FROM Usuari WHERE data_ultima_peticio > (CURRENT_TIMESTAMP - INTERVAL 1 DAY)) AS ultim_dia,
						(SELECT COUNT(*) FROM Usuari WHERE data_ultima_peticio > (CURRENT_TIMESTAMP - INTERVAL 1 WEEK)) AS ultima_setmana,
						(SELECT COUNT(*) FROM Usuari WHERE data_ultima_peticio > (CURRENT_TIMESTAMP - INTERVAL 1 MONTH)) AS ultim_mes,
						(SELECT COUNT(*) FROM Usuari WHERE data_ultima_peticio > (CURRENT_TIMESTAMP - INTERVAL 1 YEAR)) AS ultim_any;
					"
				);
				$query->execute();
				$query->setFetchMode(PDO::FETCH_ASSOC);
				$outcome = $query->fetchAll();
				if(count($outcome)>0){
					$output = $outcome;
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
				$output = $e->getMessage();
			}
			return $output;
        }
}

?>