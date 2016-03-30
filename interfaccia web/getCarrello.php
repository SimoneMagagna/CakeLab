// presenta i prodotti inseriti nel carrello non accessibile da admin
<?php

require_once "pack/setup.php" ; // Mi serve per sapere che scadenza dare ai cookie
require_once "pack/connessione.php" ;
require_once "pack/validazione.php" ;

$erroreLogin = false; // Variabili per segnalare errori nel form di login
$descErrore = "";

$isAvvisi = FALSE; // Variabili per segnalare tutti i possibili messaggi
$avvisi = "";

if(isset($_GET['logout'])){
	$isAvvisi = TRUE;
	$avvisi = "Grazie della visita. Arrivederci.";
}

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

if((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn'] == 'youAreLogged')&&($_SESSION['gruppo_U']==1)){ // E' loggato un ADMIN: distruggo il carrello e nego l'accesso alla pagina
	setcookie ("carrello", "",time()-3600);
	
	header("Location: index.php?errore=noadmin");
}

// Se la pagina e' stata invocata da una richiesta di ordine. La processo
if(isset($_POST['addOrdine'])){
	
	$stringaOrdine = $_COOKIE['carrello'];
	
	try{
		if (!($stmt = $mysqli->prepare("CALL addOrdine(?,?, @esito, @codOrdine);")))
			throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
		if (!($stmt->bind_param("si", $stringaOrdine, $_SESSION['id_U'])))
			throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
		if (!($stmt->execute()))
			throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	
		
		if (!($stmt = $mysqli->prepare("SELECT @esito, @codOrdine;")))
			throw new Exception ('prepare fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
		if (!($stmt->execute()))
			throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					 
		if (!($stmt->bind_result($esitoOrdine, $codOrdine)))
			throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
			
		if (!($stmt->fetch()))
			throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
			
		if (!($stmt->close()))   
			throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);  

	}catch (Exception $excp) {
    	echo $excp -> getMessage();
	}
	
	
	// Una volta inoltrato l'ordine, distruggo il carrello
	if($esitoOrdine)
		setcookie ("carrello", "",time()-3600);
}

// Se la pagina e' stata chiamata da una richiesta di modifica quantita'. La processo
if(isset($_POST['modQnt'])){
	
	if(!validateID("prodRif", "ID prodotto da aggiornare", false, $avvisi))
		$isAvvisi = true;
		
	if(!validateID("nuovaQnt", "nuova quantita' prodotto", false, $avvisi))
		$isAvvisi = true;
	
	if(!$isAvvisi){
		
		if(isset($_COOKIE['carrello'])){ 
			
			$datiCarrello = explode(",",$_COOKIE['carrello']);
			$nProdottiCarrello = $datiCarrello[0];
			
			// Controllo che nel carrello non ci siano gia' altri prodotti dello stesso tipo di quello appena inserito. In questo caso basta aumentare il campo quantita' corrispondente
			$prodottoGiaInCarrello = FALSE;
			for($k=1;$k<=($nProdottiCarrello*2);$k=$k+2){
				
				if($datiCarrello[$k]==$_POST['prodRif']){
					$prodottoGiaInCarrello = TRUE;
					
					// se una quantita' e' stata impostata a 0 metto anche l'ID prodotto a 0, viene facile rimuoverlo dal cookie, inoltre decremento il numero di prodotti
					if($_POST['nuovaQnt']==0){
						$datiCarrello[$k] = 0;
						$datiCarrello[$k+1] = 0;	
						$datiCarrello[0]--;
					}else{
						$datiCarrello[$k+1] = $_POST['nuovaQnt'];
					}
				}
			}
			
			if(!($prodottoGiaInCarrello)){ // il prodotto non era gia' in carrello {in realta' allo stato attuale non e' possibile aggiungere nuovi prodotti dal carrello ma solo dalla index. Il caso e' comunque trattato per espansioni future}	
				$datiCarrello[0]++; // aumento di 1 il campo che indica il numero di prodotti in carrello
				$nuovoCarrello = implode(",",$datiCarrello); // ricompongo il nuovo carrello copiando i dati del vecchio ma con primo campo incrementato di 1
				$nuovoCarrello .= ",".$_POST['prodRif'].",".$_POST['nuovaQnt']; // aggiundo ul nuovo prosotto al carrello con la sua quantita'
				
			}else{ // il prodotto era gia' in carrello: devo solo ricopiare perche' la quantita' corrispondente e' gia' stata incrementata
				$nuovoCarrello = implode(",",$datiCarrello); // ricompongo il nuovo carrello copiando i dati del vecchio ma con primo campo incrementato di 1
			}
			
			// Sistemo il cookie nel caso sia stato rimosso un prodotto dal carrello, cioe' la sua quantita sia stata portata a 0
			if($nProdottiCarrello>1){ 
				if($_POST['nuovaQnt']==0){ // avevo piu' di un prodotto in carrello. mi basta rimuovere le occorrenze di ",0" dal cookie
					$nuovoCarrello = str_replace(",0", "", $nuovoCarrello);
				}
				setcookie ("carrello", $nuovoCarrello,time()+$tempoMaxCarrello); // il tempo massimo prima che il carrello scompaia dal computer dell'utente puo' essere impostato dal file di setup
			}else{ // avevo un solo prodotto in carrello
				if($_POST['nuovaQnt']==0){ // e' stato rimosso l'unico prodotto dal carrello: cancello il cookie
					setcookie ("carrello", "",time()-3600);
				}else{
					setcookie ("carrello", $nuovoCarrello,time()+$tempoMaxCarrello);
				}
			}
			
			header("Location: ".$_SERVER['PHP_SELF']."?updCarrello=true");
			
		}else{
			setcookie ("carrello", "1,".$_POST['prodRif'].",".$_POST['nuovaQnt'],time()+$tempoMaxCarrello); // il tempo massimo prima che il carrello scompaia dal computer dell'utente puo' essere impostato dal file di setup
			
			header("Location: ".$_SERVER['PHP_SELF']."?updCarrello=true");
		}
	
	}
}
// Leggo i dati relativi al carrello
if(isset($_COOKIE['carrello'])){
	$datiCarrello = explode(",",$_COOKIE['carrello']);
	$nProdottiCarrello = $datiCarrello[0];
}else{
	$nProdottiCarrello = 0;
}

$tabellaCarrello = "";
$granTotale = 0;

if($nProdottiCarrello > 0){
	$tabellaCarrello .= "<table class='righeSelect' id='getCarrello'>";
	$j = 0;  // Serve per colorare alternativamente le righe della tabella, a seconda che j sia pari o dispari
	for($i=1;$i<=($nProdottiCarrello*2);$i=$i+2){
		
		$prodottoCarrello = $datiCarrello[$i];
		$qntProdottoCarrello = $datiCarrello[$i+1];
		
		try{
			// Estraggo le informazioni di ogni singolo prodotto che compone la pagina richiesta
			if (!($stmt = $mysqli->prepare("SELECT nomeProdotto, prezzoProdotto, disponibilitaProdotto FROM PRODOTTI WHERE idProdotto=?")))
				throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error); 
		
			if (!($stmt->bind_param("i", $prodottoCarrello)))
				throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
			if (!($stmt->execute()))
				throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	
				  
			if (!($stmt->bind_result($nomeProdotto, $prezzoProdotto, $disponibilitaProdotto)))
				throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
			if (!($stmt->fetch()))
				throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
			if (!($stmt->close()))   
				throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error); 

		}catch (Exception $excp) {
   			echo $excp -> getMessage();
		}
		
		$formModificaQnt = "$prezzoProdotto x $qntProdottoCarrello<a href='getCarrello.php?modQuantita=".$prodottoCarrello."' class='operazione'>[<img src='media/edit.png' width='16px' height='16px'/>Modifica quantita']</a>";
		if((isset($_GET['modQuantita']))&&($_GET['modQuantita']==$prodottoCarrello)){  // Se e' stata richiesta una modifica quantita' faccio comparire il form
		
			$selectModQnt = "<select name='nuovaQnt'>";
			for($k=0;$k<=$disponibilitaProdotto;$k++){
				$selectModQnt .= "<option value='$k'>$k</option>";
			}
			$selectModQnt .= "</select>";
			$formModificaQnt = "<form method='POST' action='getCarrello.php'>$prezzoProdotto x $qntProdottoCarrello(Nuova quantita': $selectModQnt <input type='hidden' name='prodRif' value='".$prodottoCarrello."'>)<input type='submit' name='modQnt' value='Salva'></form>";
		}
		
		
		$totParziale = $prezzoProdotto*$qntProdottoCarrello;
		$granTotale += $totParziale;
		formattaImporto($prezzoProdotto);
		formattaImporto($totParziale);
		
		if($j%2==1){
			$tabellaCarrello .= "<tr class='unpair' onMouseOver='setColor(this, 0, \"#cc6600\")' onMouseOut='setColor(this, 1, \"#282828\")'><td align='center' style='width:24px;'><input type='checkbox' name='$prodottoCarrello' id='$prodottoCarrello' onclick='select_row(this);' /></td><td style='width:480px;'>$nomeProdotto</td><td align='right'>$formModificaQnt</td><td align='right'> $totParziale</td></tr>";
		}else{
			$tabellaCarrello .= "<tr class='pair' onMouseOver='setColor(this, 0, \"#cc6600\")' onMouseOut='setColor(this, 1, \"#282828\")'><td align='center' style='width:24px;'><input type='checkbox' name='$prodottoCarrello' id='$prodottoCarrello' onclick='select_row(this);' /></td><td style='width:480px;'>$nomeProdotto</td><td align='right'>$formModificaQnt</td><td align='right'> $totParziale</td></tr>";
		}//else-if
		$j++;
		
			
	}
	formattaImporto($granTotale);
	$tabellaCarrello .= "<tr><td colspan='3'></td><td align='right'><strong>Totale: $granTotale</strong></td></tr>";
	$tabellaCarrello .= "</table>";
}else{
	$tabellaCarrello .= "<div class='risultato'>Il carrello e' vuoto.</div>";
}


// Interazione col DB terminata. Chiudo la connessione
$mysqli->close();
	
	// Leggo i dati relativi al carrello
if(isset($_COOKIE['carrello'])){
	$datiCarrello = explode(",",$_COOKIE['carrello']);
	$nProdottiCarrello = $datiCarrello[0];
}else{
	$nProdottiCarrello = 0;
}
		



// Se la pagina e' stata richiesta dal link di Logout, eseguo il logout dell'utente connesso
if(isset($_GET['logout']) && $_GET['logout']=="true"){
	session_destroy();
	
	header("Location: ".$_SERVER['PHP_SELF']."?logout=messaggio");  // valido per tutte le pagine: posso fare copia incolla :)
}
?>


<html>
<head>
<meta charset="UTF-8">
<title>The CakeLab HomePage</title>
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
            	<img width="16px" height="16px" src="media/carrello.gif" alt="un piccolo carrello" /><a class="pulsanteLoggedBox" href="getCarrello.php">Carrello(<?php echo $nProdottiCarrello;?>)</a>   	
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
                    <div id="carrello"><img width="16px" height="16px" src="media/carrello.gif" alt="un piccolo carrello" /><a class="pulsanteLoggedBox" href="getCarrello.php">Carrello(<?php echo $nProdottiCarrello;?>)</a></div>
				</form>
                
    
   		 
    
  	    <?php	
			}// chiusura if-else utente loggato/non loggato
		?>
    </div>
</div>
<div id="center">
<?php
if(!(isset($_POST['addOrdine']))){
?>

<?php   // Se la pagina e' stata richiamata a seguito dell'aggiunta di un nuovo prodotto al carrello, lo segnalo
if((isset($_GET['addCarrello']))&&($_GET['addCarrello']=="true")){
?>
<div class="risultato">
Prodotto correttamente aggiunto nel carrello.
</div>
<?php
}
?>

<?php   // Se la pagina e' stata richiamata a seguito della modifica di un nuovo prodotto al carrello, lo segnalo
if((isset($_GET['updCarrello']))&&($_GET['updCarrello']=="true")){
?>
<div class="risultato">
Carrello aggiornato con la quantita' indicata.
</div>
<?php
}
?>

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
	
		<fieldset>
			<legend><img width="16px" height="16px" src="media/carrello.gif" alt="un piccolo carrello" />Il tuo Carrello</legend>
<?php
	echo $tabellaCarrello;
	if($nProdottiCarrello > 0){ // Mostro il pulsante per inviare l'ordine solo se il carrello non e' vuoto
?>
			<fieldset>
				<legend>Registra un Ordine</legend>
				<?php
					if(!(isset($_SESSION['loggedIn']))){
				?>
					<div class="risultato">Per poter registrare un ordine devi fare il Login!</div>
				<?php
					}else{
				?>
					<form method="POST" action="getCarrello.php"><hr /><input type="submit" name="addOrdine" value="Registra Ordine"><hr /><div style="font-size:60%;">Cliccando su "Registra Ordine" dichiari di accettare le nostre condizioni di vendita.</div></form>
				<?php
					}
				?>
			</fieldset>
<?php
	}
?>
		</fieldset>
	
</div>
<!-- CONTENUTO VARIABILE -->
<?php
}else{ // E' appena stato inserito un ordine. Ne do conferma
?>

<fieldset>
		<legend>Grazie!</legend>
		<div id='confermaOrdine'>Congratulazioni! L'ordine e' stato inserito correttamente, speriamo tu sia soddisfatto del tuo acquisto. <br />Il nostro staff e' a tua disposizione, puoi contattarci facendo riferimento all'ordine <span class="dato">#<?php echo $codOrdine;?></span>.<br/><br /> Puoi seguire lo stato del tuo ordine <a href="getOrdini.php">QUI</a></div>
</fieldset>

<?php
}
?>
</div> <!-- Chiusura div id='center' -->
</body>
</html>
