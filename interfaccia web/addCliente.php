// aggiunge un nuovo cliente
 <?php

require_once "pack/connessione.php" ;
require_once "pack/validazione.php" ;

$erroreLogin = false; // Variabili per segnalare errori nel form di login
$descErrore = "";

// Se la pagina e' stata richiesta dal form dati, processo il login
if(isset($_POST['Login']))
{
	if($_POST['user']=="" || $_POST['pwd']=="")
	{
		$erroreLogin = true;
		$descErrore = "ERRORE: Campi Mancanti!";
	}else{
		$esitoLogin = false;
		$gruppoUtente = 0;
		$nomeUtente = "";
		$cognomeUtente = "";
		$idUtente = 0;
		
		$criptPsw = md5(sha1($_POST['pwd']));
		
		try{
			if (!($stmt = $mysqli->prepare("CALL tryLogin(?,?, @esito, @gruppoUtente, @nomeUtente, @cognomeUtente, @idUtente);")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("ss", $_POST['user'], $criptPsw)))
				throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
			if (!($stmt->execute()))
				throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	
		
			if (!($stmt = $mysqli->prepare("SELECT @esito, @gruppoUtente, @nomeUtente, @cognomeUtente, @idUtente;")))
				throw new Exception ('prepare fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
			if (!($stmt->execute()))
				throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
				 
			if (!($stmt->bind_result($esitoU, $gruppoU, $nomeU, $cognomeU, $idU)))
				throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
			
			if (!($stmt->fetch()))
				throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
			
			if (!($stmt->close()))   
				throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);  

		}catch (Exception $excp) {
    		echo $excp -> getMessage();
		}
	
		if($esitoU){
			session_start();
			$_SESSION['loggedIn'] = 'youAreLogged';
			$_SESSION['id_U'] = $idU;
			$_SESSION['nome_U'] = $nomeU;
			$_SESSION['cognome_U'] = $cognomeU;
			$_SESSION['gruppo_U'] =  $gruppoU;
		}else{
			$erroreLogin = true;
			$descErrore = "ERRORE: Utente sconosciuto!";
		}
		
	}
}else{  // a pagina e' stata richiesta da un Utente. Ho bisogno dei suoi dati di sessione eventualmente fosse gia' loggato
	session_start();
}

// Se la pagina e' stata richiesta dal form di registrazione di un nuovo Cliente, processo la richiesta
if(isset($_POST['creaC'])){
	
	$isErroreForm = false;
	$esitoForm = "";
	
	if(!validateNome("nomeC", "nome cliente", false, $esitoForm))
		$isErroreForm = true;
	
	if(!validateNome("cognomeC", "cognome cliente", false, $esitoForm))
		$isErroreForm = true;
		
	if(!validateMail("emailC", "mail cliente", false, $esitoForm))
		$isErroreForm = true;
		
	if(!validateText("userC", "username cliente", 4, false, $esitoForm))
		$isErroreForm = true;
		
	if(!validateText("indirizzoC", "indirizzo cliente", 0, false, $esitoForm))
		$isErroreForm = true;
	
	if(!validateCAP("capC", "CAP cliente", false, $esitoForm))
		$isErroreForm = true;
		
	if(!validateNome("comuneC", "comune cliente", false, $esitoForm))
		$isErroreForm = true;
	
	if(!validateProv("provC", "provincia cliente", false, $esitoForm))
		$isErroreForm = true;
		
	if(!validateTEL("celC", "cellulare cliente", false, $esitoForm))
		$isErroreForm = true;
		
	if(!validateText("pwdC", "password cliente", 4, false, $esitoForm))
		$isErroreForm = true;
	
	if(!validateText("ripPwdC", "ripeti password cliente", 4, false, $esitoForm))
		$isErroreForm = true;
		
	$pswCif = md5(sha1($_POST['pwdC']));
	$ripPswCif = md5(sha1($_POST['ripPwdC']));
	
	if(!$isErroreForm){
		
		try{
	//Determino il numero totale di utenti salvati nel programma
			if (!($stmt = $mysqli->prepare("CALL addCliente(?,?,?,?,?,?,?,?,?,?,?,@ris,@tipoErrore);")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("sssssssssss", $_POST['userC'], $pswCif, $ripPswCif, $_POST['nomeC'], $_POST['cognomeC'], $_POST['emailC'], $_POST['indirizzoC'], $_POST['comuneC'], $_POST['provC'], $_POST['capC'], $_POST['celC'])))
				throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
			$stmt->execute(); // potrebbe fallire, ma non me ne curo perché l'errore e' gia' registrato su @tipoErrore in OUT
		
			if (!($stmt = $mysqli->prepare("SELECT @ris, @tipoErrore;")))
				throw new Exception ('prepare fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
			if (!($stmt->execute()))
				throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
				 
			if (!($stmt->bind_result($risultato, $TipoErrore)))
				throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
			
			if (!($stmt->fetch()))
				throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
			
			if (!($stmt->close()))   
				throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);

		}catch (Exception $excp) {
   			echo $excp -> getMessage();
		}
		
		if($risultato){
			$esitoForm = "Inserimento del Cliente riuscito!";
		}else{
			$esitoForm = $TipoErrore;
		}

	}
}

// Interazione col DB terminata. Chiudo la connessione
$mysqli->close();

// Se la pagina e' stata richiesta dal link di Logout, eseguo il logout dell'utente connesso
if(isset($_GET['logout']) && $_GET['logout']==true){
	session_destroy();
	
	header("Location: ".$_SERVER['PHP_SELF']);  // valido per tutte le pagine: posso fare copia incolla :)
}
?>


<html>
<head>
<meta charset="UTF-8">
<title>The CakeLab HomePage</title>
<link href="main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="top">
	<div id="CakeLabLogo"><img src="media/logo.png" width="220px" height="86" alt="CakeLab Logo" /></div>
	<div id="menuTop">
    	<div id="home" class="pulsanteTop"><a href="index.php">Home</a></div>
        <?php
        if(!(isset($_SESSION['loggedIn']))){ // Se non e' loggato nessun utente permetto ad un eventuale cliente di registrarsi
		?>
    	<div id="nuovoUtente" class="pulsanteTop"><a href="addCliente.php">Sono Nuovo</a></div>
        <?php
		}else if(($_SESSION['loggedIn'] == 'youAreLogged')&&($_SESSION['gruppo_U']==1)){ // se invece e' loggato un admin gli permetto di creare un nuovo utente, ma con parole diverse :)
		?>
        <div id="nuovoUtente" class="pulsanteTop"><a href="addCliente.php">Nuovo Cliente</a></div>
        <?php	
		} // chiusura if-else nessun utente loggato/loggato un admin
		
		if((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn'] == 'youAreLogged')&&($_SESSION['gruppo_U']==1)){ // E' loggato un admin: solo un admin puo' aggiungere altri admin
		?>
        <div id="nuovoAdmin" class="pulsanteTop"><a href="addAdmin.php">Nuovo Admin</a></div>
        <?php
		}
		?>
    </div>
	<div id="login">
    	<?php
		if((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn'] == 'youAreLogged')) // L'utente è gia' loggato
		{
				
		?>
        <div id="loggedBox">Benvenuto <span class="dato"><?php echo $_SESSION['nome_U'];?></span>(<a class="pulsanteLoggedBox" href="index.php?logout=true">Logout</a>) <hr />
            <div id="iltuoCakeLab">
        	<?php
			if($_SESSION['gruppo_U']==1){ // L'utente loggato e' un admin
			?>
            	<a class="pulsanteLoggedBox" href="getUtenti.php">Gestisci Utenti</a>
            <?php
			}else{  // L'utente loggato e' un cliente
			?>
        		<a class="pulsanteLoggedBox" href="getOrdini.php">I tuoi ordini</a>
            <?php
            }// chiusura if-else utente loggato admin/cliente
        	?>
            </div>
        	<div id="carrello">
            <?php
			if($_SESSION['gruppo_U']==1){ // L'utente loggato e' un admin
			?>
        		<a class="pulsanteLoggedBox" href="">Gestisci prodotti</a>
        	<?php
			}else{  // L'utente loggato e' un cliente
			?>
            	<img width="16px" height="16px" src="media/carrello.gif" alt="un piccolo carrello" /><a class="pulsanteLoggedBox" href="">Carrello</a>   	
            <?php
            }// chiusura if-else utente loggato admin/cliente
        	?>
            </div>
        </div>
        <?php
			}else{  // L'utente non e' loggato
		 ?>
   				<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; /*L'invio del form sara' processato da questa stessa pagina*/?>">
    				<div id="logTextFields">
  						Username: <input type="text" name="user" size="15" /><br />
 						Password:&nbsp; <input type="password" name="pwd" size="15" /><br />
                        <?php
							if($erroreLogin){
						?>
                        		<div class="errore"><?php echo $descErrore;?></div>
                        <?php
							}// errore campi login mancanti
						?>
 					</div>
     				<div id="logPulsante">
   						<input type="submit" name="Login" value="Login" />
  					</div>
				</form>
                
    
   		 
    
  	    <?php	
			}// chiusura if-else utente loggato/non loggato
		?>
    </div>
</div>
<div id="center">
<?php   // Se la pagina e' stata richiamata a seguito dell'invio del form di creazione utente visualizzo il messaggio con risultato
if(isset($_POST['creaC'])){
?>
<div class="risultato">
<?php echo $esitoForm;?>
</div>
<?php
}
?>

<!-- CONTENUTO VARIABILE -->
<div class="form">

<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
<fieldset>
  	<legend>Crea un nuovo Cliente</legend>
    
  <fieldset>
  	<legend>Contatto</legend>
    <div><label for="nomeC">Nome: </label>
    <input class="mainForm" type="text" id="nomeC" name="nomeC" size="10" maxlength="255" value="<?php echo $_POST['nomeC'];?>"></div>
    <div><label for="cognomeC">Cognome: </label>
    <input class="mainForm" type="text" id="cognomeC" name="cognomeC" size="10" maxlength="255" value="<?php echo $_POST['cognomeC'];?>"></div>
    <div><label for="emailC">e-Mail: </label>
    <input class="mainForm" type="text" id="emailC" name="emailC" size="10" maxlength="255" value="<?php echo $_POST['emailC'];?>"></div>
  </fieldset>
  
  <fieldset>
    <legend>Info di Accesso</legend>
    <div><label for="userC">Username: </label>
    <input class="mainForm" type="text" id="userC" name="userC" size="10" maxlength="255" value="<?php echo $_POST['userC'];?>"></div>
    <div><label for="pwdC">Password: </label>
    <input class="mainForm" type="password" id="pwdC" name="pwdC" size="10" maxlength="255"></div>
    <div><label for="ripPwdC">Ripeti Password: </label>
    <input class="mainForm" type="password" id="ripPwdC" name="ripPwdC" size="10" maxlength="255"></div>
   </fieldset>
   
   <fieldset>
    <legend>Anagrafica</legend>
    <div><label for="indirizzoC">Indirizzo: </label>
    <input class="mainForm" type="text" id="indirizzoC" name="indirizzoC" size="10" maxlength="255" value="<?php echo $_POST['indirizzoC'];?>"></div>
    <div><label for="capC">CAP: </label>
    <input class="mainForm" type="text" id="capC" name="capC" size="10" maxlength="5" value="<?php echo $_POST['capC'];?>"></div>
    <div><label for="comuneC">Comune: </label>
    <input class="mainForm" type="text" id="comuneC" name="comuneC" size="10" maxlength="255" value="<?php echo $_POST['comuneC'];?>"></div>
    <div><label for="provC">Provincia: </label>
    <input class="mainForm" type="text" id="provC" name="provC" size="10" maxlength="2" value="<?php echo $_POST['provC'];?>"></div>
    <div><label for="celC">Cellulare: </label>
    <input class="mainForm" type="text" id="celC" name="celC" size="10" maxlength="255" value="<?php echo $_POST['celC'];?>"></div>
   </fieldset> 
   
    <button type="submit" id="creaC" name="creaC" value="Crea Cliente">Crea Cliente</button>
    
</fieldset>
</form>
</div> <!-- Chiusura div id='form' -->
</div> <!-- Chiusura div id='center' -->
</body>
</html>
