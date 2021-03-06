// crea un nuovo admin 
 <?php

require_once "pack/connessione.php" ;
require_once "pack/validazione.php" ;

session_start();

$avvisi = ""; // raccolgo gli eventuali errori da recapitare all'utente
$isAvvisi = FALSE;

// Controllo che l'utente sia loggato e abbia i provilegi per accedere a questa pagina(admin)
if((!(isset($_SESSION['loggedIn'])))||($_SESSION['loggedIn'] != 'youAreLogged')||($_SESSION['gruppo_U']!=1))
	header("Location: index.php?errore=noadmin");

// Se la pagina e' stata richiesta dal form di registrazione di un nuovo Cliente, processo la richiesta
if(isset($_POST['creaA'])){
	
	if(!validateNome("nomeA", "nome cliente", false, $avvisi))
		$isAvvisi = true;
	
	if(!validateNome("cognomeA", "cognome cliente", false, $avvisi))
		$isAvvisi = true;
		
	if(!validateMail("emailA", "mail cliente", false, $avvisi))
		$isAvvisi = true;
		
	if(!validateText("userA", "username cliente", 4, false, $avvisi))
		$isAvvisi = true;
		
	if(!validateText("pwdA", "password cliente", 4, false, $avvisi))
		$isAvvisi = true;
	
	if(!validateText("ripPwdA", "ripeti password cliente", 4, false, $avvisi))
		$isAvvisi = true;
		
	$pswCif = md5(sha1($_POST['pwdA']));
	$ripPswCif = md5(sha1($_POST['ripPwdA']));
	
	if(!$isAvvisi){
		
		try{
			//Determino il numero totale di utenti salvati nel programma
			if (!($stmt = $mysqli->prepare("CALL addAdmin(?,?,?,?,?,?,@ris,@tipoErrore);")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("ssssss", $_POST['userA'], $pswCif, $ripPswCif, $_POST['nomeA'], $_POST['cognomeA'], $_POST['emailA'])))
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
			$avvisi .= "Inserimento dell'Admin riuscito!";
		}else{
			$avvisi .= $TipoErrore;
		}
		
		$isAvvisi = TRUE;
		
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
if($isAvvisi){
?>
<div class="risultato">
<?php echo $avvisi;?>
</div>
<?php
}
?>

<!-- CONTENUTO VARIABILE -->
<div class="form">

<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
<fieldset>
  	<legend>Crea un nuovo Admin</legend>
    
  <fieldset>
  	<legend>Contatto</legend>
    <div><label for="nomeC">Nome: </label>
    <input class="mainForm" type="text" id="nomeA" name="nomeA" size="10" maxlength="255" value="<?php echo $_POST['nomeC'];?>"></div>
    <div><label for="cognomeC">Cognome: </label>
    <input class="mainForm" type="text" id="cognomeA" name="cognomeA" size="10" maxlength="255" value="<?php echo $_POST['cognomeC'];?>"></div>
    <div><label for="emailC">e-Mail: </label>
    <input class="mainForm" type="text" id="emailA" name="emailA" size="10" maxlength="255" value="<?php echo $_POST['emailC'];?>"></div>
  </fieldset>
  
  <fieldset>
    <legend>Info di Accesso</legend>
    <div><label for="userC">Username: </label>
    <input class="mainForm" type="text" id="userA" name="userA" size="10" maxlength="255" value="<?php echo $_POST['userC'];?>"></div>
    <div><label for="pwdC">Password: </label>
    <input class="mainForm" type="password" id="pwdA" name="pwdA" size="10" maxlength="255"></div>
    <div><label for="ripPwdC">Ripeti Password: </label>
    <input class="mainForm" type="password" id="ripPwdA" name="ripPwdA" size="10" maxlength="255"></div>
   </fieldset>
   
    <button type="submit" id="creaA" name="creaA" value="Crea Admin">Crea Admin</button>
    
</fieldset>
</form>
</div> <!-- Chiusura div id='form' -->
</div> <!-- Chiusura div id='center' -->
</body>
</html>
