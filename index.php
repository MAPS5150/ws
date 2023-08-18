<?php
   /*
	   Web Service
	   Carmelo Hernández
   */

include 'conexion.php';
$username=$_GET['username'];
$pass = $_GET['pass'];
$passHash = password_hash($pass, PASSWORD_BCRYPT);
$lastname=$_GET['lastname'];
$firstname=$_GET['firstname'];
$email=$_GET['email'];

            if(isset($username) and isset($pass) and isset($lastname) and isset($firstname) and isset($email)){
	            $sql = "select * from mdl_user where username='$username' or email='$email'";
	            $result = mysqli_query($conexion, $sql);
	             if(mysqli_num_rows($result)>0)
	            {
			        if($row = $result->fetch_assoc()) {
			            $idRegistrado=$row["id"];
			             echo "<hr> Id: " . $row["id"] . "-Nombre Usuario: " . $row["firstname"] . "<hr>";
				     	//Necesitamos ver si el curso esta registrado en la bd ENROL
	                    	$idcourse='4';
		               $sqlCurse = "SELECT id FROM mdl_enrol WHERE courseid=$idcourse AND enrol='manual'";
	                   $resultC = mysqli_query($conexion, $sqlCurse);
	           	if(!$resultC){
		         	echo "NO EXISTE EN ENROL";
	                     	}
		if($row = $resultC->fetch_assoc()) {
			$idenrol = $row["id"];
			ECHO $idenrol;
		}
		//Ahora necesitamos el contexto
		$sqlCtx = "SELECT id FROM mdl_context WHERE contextlevel=50 AND instanceid=$idcourse";
		$resultCtx = mysqli_query($conexion, $sqlCtx);
		if(!$resultCtx){
			echo "NO EXISTE EL CONTEXTO";
		}
		if($row = $resultCtx->fetch_assoc()) {
			$idcontext = $row["id"];
			ECHO $idcontext;
		}
		//Primero validamos que no este registrado ya en el curso
		$sqlValida = "select * from mdl_user_enrolments where userid='$idRegistrado' and enrolid='$idenrol'";
	$result = mysqli_query($conexion, $sqlValida);
	if(mysqli_num_rows($result)>0)
	 {
		echo "EL USUARIO YA ESTA REGISTRADO EN EL CURSO";
	 }else{
			//Empezamos a registrar al usuario al curso.
			$time = time();
			$ntime = $time + 60*60*24*0; //How long will it last enroled $duration = days, this can be 0 for unlimited.
			$sqlU = "INSERT INTO mdl_user_enrolments (status, enrolid, userid, timestart, timeend, timecreated, timemodified)
			VALUES (0, $idenrol, $idRegistrado, '$time', '$ntime', '$time', '$time')";
			if ($conexion->query($sqlU) === TRUE) {
			} else {
			   ECHO "ERROR AL REGISTRAR EL USUARIO AL CURSO ENROLMENTS";
			}
			$sqlR = "INSERT INTO mdl_role_assignments (roleid, contextid, userid, timemodified)
			VALUES (5, $idcontext, '$idRegistrado', '$time')"; //Roleid = 5, means student.
			if ($conexion->query($sqlR) === TRUE) {
			} else {
				ECHO "ERROR AL REGISTRAR AL USUARIO ASSIGNMENTS";
			}
	 }
		}
		echo "YA EXISTE EL USUARIO DENTRO DEL SISTEMA";
	 }else{
		$sqlinsert = "INSERT INTO mdl_user (auth,username,password,lastname,firstname,email,confirmed,mnethostid,city,country,lang,timezone,maildisplay)
VALUES('manual','$username','$passHash','$lastname','$firstname','$email','1','1','mexico','mx','es_mx','America/Monterrey','1');";
	if ($conexion->query($sqlinsert) === TRUE) {
		echo "SE REGISTRO NUEVO USUARIO A MOODLE";
		$sql = "select * from mdl_user where username='$username' or email='$email'";
	$result = mysqli_query($conexion, $sql);
	if(mysqli_num_rows($result)>0)
	 {
			if($row = $result->fetch_assoc()) {
			$idRegistrado=$row["id"];
			echo "<hr> Id: " . $row["id"] . "-Nombre Usuario: " . $row["firstname"] . "<hr>";
					//Necesitamos ver si el curso esta registrado en la bd ENROL
		$idcourse='4';
		$sqlCurse = "SELECT id FROM mdl_enrol WHERE courseid=$idcourse AND enrol='manual'";
		$resultC = mysqli_query($conexion, $sqlCurse);
		if(!$resultC){
			echo "nO existe";
		}
		if($row = $resultC->fetch_assoc()) {
			$idenrol = $row["id"];
			ECHO $idenrol;
		}
		//Ahora necesitamos el contexto
		$sqlCtx = "SELECT id FROM mdl_context WHERE contextlevel=50 AND instanceid=$idcourse";
		$resultCtx = mysqli_query($conexion, $sqlCtx);
		if(!$resultCtx){
			echo "nO existe";
		}
		if($row = $resultCtx->fetch_assoc()) {
			$idcontext = $row["id"];
			ECHO $idcontext;
		}
		//Primero validamos que no este registrado ya en el curso
		$sqlValida = "select * from mdl_user_enrolments where userid='$idRegistrado' and enrolid='$idenrol'";
	$result = mysqli_query($conexion, $sqlValida);
	if(mysqli_num_rows($result)>0)
	 {
		echo "Ya esta registrado en el curso";
	 }else{
			//Empezamos a registrar al usuario al curso.
			$time = time();
			$ntime = $time + 60*60*24*0; 
			$sqlU = "INSERT INTO mdl_user_enrolments (status, enrolid, userid, timestart, timeend, timecreated, timemodified)
			VALUES (0, $idenrol, $idRegistrado, '$time', '$ntime', '$time', '$time')";
			if ($conexion->query($sqlU) === TRUE) {
			} else {
			   ECHO "ERROR AL REGISTRAR EL USUARIO AL CURSO ENROLMENTS";
			}
			$sqlR = "INSERT INTO mdl_role_assignments (roleid, contextid, userid, timemodified)
			VALUES (5, $idcontext, '$idRegistrado', '$time')"; //Roleid = 5, means student.
			if ($conexion->query($sqlR) === TRUE) {
			} else {
				ECHO "ERROR AL REGISTRAR AL USUARIO ASSIGNMENTS";
			}
	 }
		}
		echo "YA EXISTE EL USUARIO DENTRO DE LA BD";
	 }
	} else {
		echo $conexion->error;
	}
	$conexion->close();
		echo "NO EXISTE EL USUARIO";
	 }
}else{
	echo "NO LLEGO LA INFORMACIÓN";
}
 ?>