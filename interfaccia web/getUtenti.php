// Solo per gli admin da gli utenti iscritti
<?php

require_once "pack/setup.php" ;
require_once "pack/connessione.php" ;
require_once "pack/validazione.php" ;
require_once "pack/calcolaPaginazione.php" ;

session_start();

$avvisi = ""; // raccolgo gli eventuali errori da recapitare all'utente
$isAvvisi = FALSE;

$query = "SELECT idUtente, userUtente, nomeUtente, cognomeUtente, mailUtente, gruppoUtente FROM UTENTI LIMIT ?, ?;"; // le query base, se la pagina non e' chiamata da una operazione di ricerca 
$queryCount = "SELECT COUNT(*) FROM UTENTI;";

// Controllo che l'utente sia loggato e abbia i provilegi per accedere a questa pagina(admin)
if((!(isset($_SESSION['loggedIn'])))||($_SESSION['loggedIn'] != 'youAreLogged')||($_SESSION['gruppo_U']!=1))
	header("Location: index.php?errore=noadmin");
	
// Controllo che la pagina non sia stata richiesta da una operazione di ricerca
if(isset($_POST['searchC'])){
	
	
	if($_POST['val']==""){
		$avvisi = "Il valore da cercare non puo' essere vuoto!";
		$isAvvisi = TRUE;
	}
		
		$campoWhere = $_POST['campoS']."Cliente"; // Trasformo il parametro name del select nel nome del campo corrispondente nella tabella CLIENTI
			
		if($_POST['valore']=="uguale")
			$valoreWhere = "= '".$_POST['val']."'";
		else
			$valoreWhere ="LIKE '%".$_POST['val']."%'";
			
		$order = $_POST['ordineS']."Cliente"; 
		
	if(!($isAvvisi)){
		$queryCount = "SELECT COUNT(*) FROM CLIENTI WHERE $campoWhere $valoreWhere";
		$query = "SELECT idCliente, userCliente, nomeCliente, cognomeCliente, mailCliente, '0' FROM CLIENTI WHERE $campoWhere $valoreWhere ORDER BY $order LIMIT ?, ?;";
	}
}

// Controllo che l'utente sia loggato e abbia i provilegi per accedere a questa pagina(admin)
if((!(isset($_SESSION['loggedIn'])))||($_SESSION['loggedIn'] != 'youAreLogged')||($_SESSION['gruppo_U']!=1))
	header("Location: index.php?errore=noadmin");
	
// Controllo non sia stato richiesto il delete di un cliente/admin
if((isset($_GET['delete'])) && (isset($_GET['gruppo']))){
	
	if($_GET['gruppo']=="Cliente"){  // e' stata chiesta la cancallazione di un Cliente
	
		try{
		//Determino il numero totale di utenti salvati nel programma
			if (!($stmt = $mysqli->prepare("CALL delCliente(?,@ris,@tipoErrore);")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("i", $_GET['delete'])))// l'id del cliente da cancellare
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
		
		$isAvvisi = TRUE;
		if($risultato){
			$avvisi = "Calcellazione del Cliente riuscita!";
		}else{
			$avvisi = $TipoErrore;
		}
		
	}else{ // e' stata richiesta la cancellazione di un Admin
		
		try{
			//Determino il numero totale di utenti salvati nel programma
			if (!($stmt = $mysqli->prepare("CALL delAdmin(?,@ris,@tipoErrore);")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("i", $_GET['delete'])))// l'id del cliente da cancellare
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
		
		$isAvvisi = TRUE;
		if($risultato){
			$avvisi = "Calcellazione del Admin riuscita!";
		}else{
			$avvisi = $TipoErrore;
		}
		
	}
}

// Controllo non sia stato richiesto il superdelete di un cliente
if(isset($_GET['superDelete'])){
	
	try{
	
		if (!($stmt = $mysqli->prepare("CALL superdelCliente(?);")))
			throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
		if (!($stmt->bind_param("i", $_GET['superDelete'])))// l'id del cliente da supercancellare
			throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
		if (!($stmt->execute()))
			throw new Exception ('prepare fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
			
		if (!($stmt->close()))   
			throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);

	}catch (Exception $excp) {
  		echo $excp -> getMessage();
	}
	
	$isAvvisi = TRUE;
	$avvisi .= "Supercalcellazione del Cliente riuscita!";
		
}

try{
	//Determino il numero totale di utenti salvati nel programma
	if (!($stmt = $mysqli->prepare($queryCount)))
		throw new Exception ('SELECT COUNT(*) fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
		
	if (!($stmt->execute()))
		throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);		  
				 
	if (!($stmt->bind_result($nUtenti)))
		throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
			
	if (!($stmt->fetch()))
		throw new Exception ('Non ho trovato la riga relativa al SELECT COUNT.');
			
	if (!($stmt->close()))   
		throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);

}catch (Exception $excp) {
    echo $excp -> getMessage();
}

$page = calcolaPaginazione($nUtenti, $nMaxRighe, $nPages, $inizio, $quanti, $isAvvisi, $avvisi);

// Creo l'input select per la scelta della pagina
$selectBody = "";
for($i=1; $i<=$nPages+1;$i++){
	if($i == $page)
		$selectBody .= "<option value='$i' selected>$i</option>";
	else
		$selectBody .= "<option value='$i'>$i</option>";
}
	
try{
	//Determino il numero totale di utenti salvati nel programma
	if (!($stmt = $mysqli->prepare($query)))
		throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error); 
		
	if (!($stmt->bind_param("ii", $inizio, $quanti)))
		throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
	if (!($stmt->execute()))
		throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);		  
				 
	if (!($stmt->bind_result($idUtente, $userUtente, $nomeUtente, $cognomeUtente, $mailUtente, $gruppoUtente)))
		throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
	$tabellaUtenti = "";
	if($nUtenti==0){ // Se la ricerca non ha prodotto risultati lo comunico
		$tabellaUtenti .= "Nessun Utente Trovato.";
	}else{ //la ricerca ha prodotto risultati 
		$tabellaUtenti .= "<table class='righeSelect' id='getUsers'>";
		$tabellaUtenti .= "<tr style='background-color: #d3d3d3;font-weight: bold;'><td align='center'><input type='checkbox' name='selAllCheck' id='selAllCheck' onClick='return CheckboxSeleziona_onclick(this, \"getUsers\")' /></td><td>Username</td><td>Nome</td><td>Cognome</td><td>Contatto email</td><td>Gruppo</td><td>Operazioni</td></tr>";
		
		$i = 0;
		while($stmt->fetch()){
				 
			if($gruppoUtente == 0){
				$gruppoUtente = "Cliente";
				$pgUpdate = "updCliente.php";	
				$opExtra = "[<a href='getUtenti.php?superDelete=$idUtente' class='operazione'><img src='media/delete.png' width='16px' height='16px'/><img src='media/delete.png' width='16px' height='16px'/>superdel</a>]";
			}else{
				$gruppoUtente = "Admin";
				$pgUpdate = "updAdmin.php";
				$opExtra = "";
			}
					
			if($i%2==1){
				$tabellaUtenti .= "<tr class='unpair' onMouseOver='setColor(this, 0, \"#cc6600\")' onMouseOut='setColor(this, 1, \"#282828\")'><td align='center'><input type='checkbox' name='$idUtente' id='$idUtente' onclick='select_row(this);' /></td><td>$userUtente</td><td>$nomeUtente</td><td>$cognomeUtente</td><td>$mailUtente</td><td>$gruppoUtente</td><td> [<a href='$pgUpdate?mod=$idUtente' class='operazione'><img src='media/edit.png' width='16px' height='16px'/>upd</a>] [<a href='getUtenti.php?delete=$idUtente&gruppo=$gruppoUtente' class='operazione'><img src='media/delete.png' width='16px' height='16px'/>del</a>] $opExtra </td></tr>";
			}else{
				$tabellaUtenti .= "<tr class='pair' onMouseOver='setColor(this, 0, \"#cc6600\")' onMouseOut='setColor(this, 1, \"#282828\")'><td align='center'><input type='checkbox' name='$idUtente' id='$idUtente' onclick='select_row(this);' /></td><td>$userUtente</td><td>$nomeUtente</td><td>$cognomeUtente</td><td>$mailUtente</td><td>$gruppoUtente</td><td> [<a href='$pgUpdate?mod=$idUtente' class='operazione'><img src='media/edit.png' width='16px' height='16px'/>upd</a>] [<a href='getUtenti.php?delete=$idUtente&gruppo=$gruppoUtente' class='operazione'><img src='media/delete.png' width='16px' height='16px'/>del</a>] $opExtra </td></tr>";
			}//else-if
			$i++;
		}//while    
		$tabellaUtenti .= "<tr><td colspan='7'>Numero record visualizzati: $quanti/$nUtenti<a href='srcClienti.php' class='operazione'>[<img src='media/search.png' width='16px' height='16px'/>src]</a></td></tr>";
		$tabellaUtenti .= "<tr><td colspan='7'><form method='GET' ACTION='getUtenti.php'>Vai alla pagina: <select name='pagina'>$selectBody</select><input type='submit' value='Vai'></form></td></tr>";
		$tabellaUtenti .= "</table>";
	}//else-if	
			
	if (!($stmt->close()))   
		throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);

}catch (Exception $excp) {
    echo $excp -> getMessage();
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
<title>The CakeLab - Visualizza Clienti</title>
<link href="main.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="pack/js/setColor.js"></script>
<script type="text/javascript" src="pack/js/select_row.js"></script>
<script type="text/javascript" src="pack/js/CheckboxSeleziona_onclick.js"></script>
   
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
        		<a class="pulsanteLoggedBox" href="getOrdini.php">Gestisci Ordini</a>
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
<!-- CONTENUTO VARIABILE -->
<?php   // Se la pagina e' stata richiamata a seguito dell'invio del form di creazione utente visualizzo il messaggio con risultato
if($isAvvisi){
?>
<div class="risultato">
<?php echo $avvisi;?>
</div>
<?php
}
?>
<div class="visualizza">
<?php
	echo $tabellaUtenti;
?>
</div>
</div> <!-- Chiusura div id='center' -->
</body>
</html>
