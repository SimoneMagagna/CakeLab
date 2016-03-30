//Per gli amministratori cerca i clienti.
<?php
session_start();

$avvisi = ""; // raccolgo gli eventuali errori da recapitare all'utente
$isAvvisi = FALSE;

// Controllo che l'utente sia loggato e abbia i provilegi per accedere a questa pagina(admin)
if((!(isset($_SESSION['loggedIn'])))||($_SESSION['loggedIn'] != 'youAreLogged')||($_SESSION['gruppo_U']!=1))
	header("Location: index.php?errore=noadmin");

// Se la pagina e' stata richiesta dal link di Logout, eseguo il logout dell'utente connesso
if(isset($_GET['logout']) && $_GET['logout']==true){
	session_destroy();
	
	header("Location: ".$_SERVER['PHP_SELF']);  // valido per tutte le pagine: posso fare copia incolla :)
}
?>


<html>
<head>
<meta charset="UTF-8">
<title>The CakeLab - Ricerca Clienti</title>
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
<div class="form">

<form method="POST" action="getUtenti.php">
<fieldset>
  	<legend>Cerca un Cliente</legend> 
    
  <fieldset>
  	<legend>Trova tutti i Clienti che soddisfano le occorrenza di un valore specifico</legend>
    
    <div><label for="campoS" class="etichettaVicina">Nel campo: </label>
    <select name="campoS"><option value="id">id</option><option value="user">user</option><option value="nome">nome</option><option value="cognome">cognome</option><option value="mail">mail</option><option value="prov">provincia</option><option value="cell">cellulare</option> </select>
    con valore: 
    <input type="radio" name="valore" value="uguale" checked>
    pari a
    <input type="radio" name="valore" value="contenente">
    contenente :
    <input class="mainForm" type="text" id="val" name="val" size="10" maxlength="255" value="<?php echo $_POST['val'];?>">
    <br /></div>
    
    <div><label for="ordineS" class="etichettaVicina">Ordinamento risultati: </label>
    <select name="ordineS"><option value="id">Per ID Cliente</option><option value="cognome">Alfabetico crescente, Cognome Cliente</option><option value="user">Alfabetico crescente, User Cliente</option></select></div>
  </fieldset>
   
    <button type="submit" id="searchC" name="searchC" value="Cerca Cliente">Cerca Cliente</button>
    
</fieldset>
</form>
</div> <!-- Chiusura div id='form' -->

</div> <!-- Chiusura div id='center' -->
</body>
</html>
