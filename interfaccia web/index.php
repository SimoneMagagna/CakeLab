<?php

require_once "pack/setup.php" ;
require_once "pack/connessione.php" ;
require_once "pack/validazione.php" ;
require_once "pack/calcolaPaginazione.php" ;

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

// Le query generiche per recuperare i prodotti quando non e' stata fatta una ricerca
$query = "SELECT idProdotto, nomeProdotto, prezzoProdotto, categoriaProdotto, disponibilitaProdotto, descrizioneAssProdotto, porzioniProdotto FROM PRODOTTI LIMIT ?, ?;"; // le query base, se la pagina non e' chiamata da una operazione di ricerca 
$queryCount = "SELECT COUNT(*) FROM PRODOTTI;";

// Leggo le categorie per la composizione dell'input select
try{
	
	if (!($stmt = $mysqli->prepare("SELECT idCategoria, nomeCategoria FROM CATEGORIE;")))
		throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
		
	if (!($stmt->execute()))
		throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);		  
				 
	if (!($stmt->bind_result($idCategoria, $nomeCategoria)))
		throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
			
	$selectCategorie = "<select name='cate'>";		
	$selectCategorie .= "<option value='0'>Tutte le categorie</option>";
	while($stmt->fetch()){
		$selectCategorie .= "<option value='$idCategoria'>$nomeCategoria</option>";
	}
	$selectCategorie .= "<select name='cate'>";
	
	if (!($stmt->close()))   
		throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);  

}catch (Exception $excp) {
    echo $excp -> getMessage();
}

// Processo una eventuale richiesta di update della quantita' di un prodotto
if(isset($_POST['invioUPDquantita'])){
	
	if(!validateID("prodottoqnt", "ID prodotto da aggiornare", false, $avvisi))
		$isAvvisi = true;
		
	if(!validateID("qnt", "nuova quantita' prodotto", false, $avvisi))
		$isAvvisi = true;
	
	if(!$isAvvisi){
		try{
			if (!($stmt = $mysqli->prepare("CALL updDispProdotto(?,?);")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("ii", $_POST['prodottoqnt'], $_POST['qnt'])))
				throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
			if (!($stmt->execute()))
				throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	
			
			if (!($stmt->close()))   
				throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);  

		}catch (Exception $excp) {
    		echo $excp -> getMessage();
		}
	
	}
	
	if(!($isAvvisi)){
		$avvisi = "Modifica del Prodotto riuscita!";
	}
	
	$isAvvisi = TRUE;  // settato in ogni caso per mostrare il messaggio di conferma operazione o gli eventuali errori
	
}

// Processo una eventuale richiesta di update del prezzo di un prodotto
if(isset($_POST['invioUPDprezzo'])){
	
	if(!validateID("prodottoprz", "ID prodotto da aggiornare", false, $avvisi))
		$isAvvisi = true;
		
	if(!validateImporto("prz", "nuova quantita' prodotto", false, false, $avvisi))
		$isAvvisi = true;
	
	if(!$isAvvisi){
		try{
			if (!($stmt = $mysqli->prepare("UPDATE PRODOTTI SET prezzoProdotto=? WHERE idProdotto=?;")))
				throw new Exception ('CALL fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
			   
			if (!($stmt->bind_param("di", $_POST['prz'], $_POST['prodottoprz'])))
				throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
			if (!($stmt->execute()))
				throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	
			
			if (!($stmt->close()))   
				throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);  

		}catch (Exception $excp) {
    		echo $excp -> getMessage();
		}
		
	}
	
	if(!($isAvvisi)){
		$avvisi = "Modifica del Prodotto riuscita!";
	}
	
	$isAvvisi = TRUE;  // settato in ogni caso per mostrare il messaggio di conferma operazione o gli eventuali errori
	
}

// Processo una eventuale ricerca sui prodotti
if(isset($_POST['ricercaProd'])){
	
	if(!(validateID("cate", "categoria prodotto", false, $avvisi)))
		$isAvvisi = true;
	
	if(!(validateText("valRic","testo da ricercare", 3, true, $avvisi)))
		$isAvvisi = true;
	
	if(!$isAvvisi){
		if($_POST['cate']==0)
			$categ = "";
		else
			$categ = " AND categoriaProdotto = ".$_POST['cate'];
		
		if($_POST['valRic']=="")
			$where = " WHERE 0=0";
		else
			$where = " WHERE nomeProdotto LIKE '%".$_POST['valRic']."%'";
		
		// Nel caso sia stata fatta una ricerca conpongo le query personalizzate
		$query = "SELECT idProdotto, nomeProdotto, prezzoProdotto, categoriaProdotto, disponibilitaProdotto, descrizioneAssProdotto, porzioniProdotto FROM PRODOTTI$where$categ LIMIT ?, ?;"; // le query base, se la pagina non e' chiamata da una operazione di ricerca 
		$queryCount = "SELECT COUNT(*) FROM PRODOTTI$where$categ;";
		
	}
	
}

try{
	
	//Determino il numero totale di prodotti salvati nel programma
	if (!($stmt = $mysqli->prepare($queryCount)))
		throw new Exception ('SELECT COUNT(*) fallita: (' . $mysqli->errno . ') ' . $mysqli->error);   
		
	if (!($stmt->execute()))
		throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);		  
				 
	if (!($stmt->bind_result($nProdotti)))
		throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
			
	if (!($stmt->fetch()))
		throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
			
	if (!($stmt->close()))   
		throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);  

}catch (Exception $excp) {
    echo $excp -> getMessage();
}


// Se non e' loggato un admin devo visualizzare anche le schede dei prodotti: visualizzo un numero di prodotti/pagina ridotto
if(((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn'] == 'youAreLogged')&&($_SESSION['gruppo_U']==0))||(!(isset($_SESSION['loggedIn'])))){
	$nMaxRighe = $nMaxRigheRidotto;
}


$page = calcolaPaginazione($nProdotti, $nMaxRighe, $nPages, $inizio, $quanti, $isAvvisi, $avvisi);

// Creo l'input select per la scelta della pagina
$selectBody = "";
for($i=1; $i<=$nPages+1;$i++){
	if($i == $page)
		$selectBody .= "<option value='$i' selected>$i</option>";
	else
		$selectBody .= "<option value='$i'>$i</option>";
}
	
try{
	// Estraggo le informazioni di ogni singolo prodotto che compone la pagina richiesta
	if (!($stmt = $mysqli->prepare($query)))
		throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error); 
		
	if (!($stmt->bind_param("ii", $inizio, $quanti)))
		throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
	if (!($stmt->execute()))
		throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	
	
	if (!($stmt->store_result())) // Serve per poter eseguire una query annidata nel fetch di questa senza dover aprire una seconda connessione sul DB
		throw new Exception ('store_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);	  
				  
	if (!($stmt->bind_result($idProdotto, $nomeProdotto, $prezzoProdotto, $categoriaProdotto, $disponibilitaProdotto, $descrizioneAssProdotto, $porzioniProdotto)))
		throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
		
	$tabellaProdotti = "";
	if($nProdotti==0){ // Se la ricerca non ha prodotto risultati lo comunico
		$tabellaProdotti .= "Nessun Prodotto Trovato.";
	}else{ //la ricerca ha prodotto risultati 
		$tabellaProdotti .= "<table class='righeSelect' id='getItems'>";
		
		$i = 0;
		while($stmt->fetch()){
			
			// Sottraggo alla disponibilita' corrente eventuali prodotti gia' nel carrello
			if(isset($_COOKIE['carrello'])){ 
				
				$datiCarrello = explode(",",$_COOKIE['carrello']);
				$nProdottiCarrello = $datiCarrello[0];
	
				for($k=1;$k<=($nProdottiCarrello*2);$k=$k+2){
					if($datiCarrello[$k]=="".$idProdotto){
						$disponibilitaProdotto = $disponibilitaProdotto-$datiCarrello[$k+1];
					}
				}	
			}
				 
			if($disponibilitaProdotto > 4)
				$disponibilita = "<img src='media/green.png' alt='in stock' />";
			else if($disponibilitaProdotto > 0)
				$disponibilita = "<img src='media/orange.png' alt='low stock' />";
			else
				$disponibilita = "<img src='media/red.png' alt='out of stock' />";
				
			$disponibilita .= " Disponibilita': $disponibilitaProdotto pezzi";
			
			// formatto l'importo con 2 decimali dopo la virgola
			formattaImporto($prezzoProdotto);
			
			$operazioni = "";
			$controlliCarrello = "";
			$schedaProdotto = "";
			
			if((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn'] == 'youAreLogged')&&($_SESSION['gruppo_U']==1)){ // E' loggato un admin: invece dei pulsanti di gestione del carrello visualizzo quelli di amministrazione
				
				$isAggiornaQnt = FALSE;
				$isAggiornaPrz = FALSE;
				
				$aggiornaQnt = "";
				if((isset($_GET['updQuantita']))&&($_GET['updQuantita']==$idProdotto)){
					$isAggiornaQnt = TRUE;
					$prod = $_GET['updQuantita'];
					$aggiornaQnt = "Inserisci Nuova Quantita' <form method='POST' action='index.php'><input type='text' name='qnt'><input type='hidden' name='prodottoqnt' value='$prod'><input type='submit' name='invioUPDquantita' value='Salva'> </form>";
				}else
					$aggiornaQnt = "[<a href='index.php?updQuantita=$idProdotto' class='operazione'><img src='media/edit.png' width='16px' height='16px'/>Aggiorna quantita'</a>]";
			
				$aggiornaPrz = "";
				if((isset($_GET['updPrezzo']))&&($_GET['updPrezzo']==$idProdotto)){
					$isAggiornaPrz = TRUE;
					$prod = $_GET['updPrezzo'];
					$aggiornaPrz = "Inserisci Nuovo Prezzo <form method='POST' action='index.php'><input type='text' name='prz'><input type='hidden' name='prodottoprz' value='$prod'><input type='submit' name='invioUPDprezzo' value='Salva'> </form>";
				}else
					$aggiornaPrz = "[<a href='index.php?updPrezzo=$idProdotto' class='operazione'><img src='media/edit.png' width='16px' height='16px'/>Aggiorna Prezzo</a>]";
					
				if($isAggiornaQnt)
					$aggiornaPrz = "";
				
				if($isAggiornaPrz)
					$aggiornaQnt = "";
					
				$operazioni .= "$aggiornaQnt $aggiornaPrz";
				
			}else{// E' loggato un Cliente oppure la pagina e' visitata da un ospite: visualizzo i pulsanti di gestione del carrello
						
				if($disponibilitaProdotto>0){
				// creo la select per la selezione della quantita'
					$j=1;
					$quantita = "(Quantita'<select name='quantitaP' style='width:50px;'>";
					while($j<=$disponibilitaProdotto){
						$quantita .= "<option value='$j'>$j</option>";
						$j++;	
					}
					$quantita .= "</select>)";
					$pulsante = "<input type='submit' name='aggProd' value='Aggiungi al Carrello'>";
					$controlliCarrello = "<form method='POST' action='index.php'>$quantita <input type='hidden' name='idProd' value='$idProdotto'> $pulsante</form>";
				}else{
					$controlliCarrello = "Prodotto non disponibile";
				}
				
				// Per ogni prodotto, leggo una foto ad esso associata per la creazione della sua scheda
				if (!($stmt2 = $mysqli->prepare("SELECT linkMedia FROM MEDIA WHERE prodottoMedia=? AND tipoMedia=1;")))
					throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error); 
		
				if (!($stmt2->bind_param("i", $idProdotto)))
					throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
				if (!($stmt2->execute()))
					throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->store_result())) // Serve per poter eseguire una query annidata nel fetch di questa senza dover aprire una seconda connessione sul DB
					throw new Exception ('store_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
				  
				if (!($stmt2->bind_result($linkProdotto)))
					throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->fetch()))
					throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->close()))   
					throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				// Per ogni prodotto, leggo la categoria esso associata per la creazione della sua scheda
				if (!($stmt2 = $mysqli->prepare("SELECT nomeCategoria FROM CATEGORIE WHERE idCategoria=?")))
					throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error); 
		
				if (!($stmt2->bind_param("i", $categoriaProdotto)))
					throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
				if (!($stmt2->execute()))
					throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->store_result())) // Serve per poter eseguire una query annidata nel fetch di questa senza dover aprire una seconda connessione sul DB
					throw new Exception ('store_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
				  
				if (!($stmt2->bind_result($nomeCatProdotto)))
					throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->fetch()))
					throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->close()))   
					throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);
				
				// Per ogni prodotto, leggo la descrizione esso associata per la creazione della sua scheda
				if (!($stmt2 = $mysqli->prepare("SELECT descrizione FROM DESCRIZIONIPRODOTTI WHERE idDescrizione=?;")))
					throw new Exception ('SELECT fallita: (' . $mysqli->errno . ') ' . $mysqli->error); 
		
				if (!($stmt2->bind_param("i", $descrizioneAssProdotto)))
					throw new Exception ('bind_param fallita: (' . $mysqli->errno . ') ' . $mysqli->error);  
		
				if (!($stmt2->execute()))
					throw new Exception ('execute fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->store_result())) // Serve per poter eseguire una query annidata nel fetch di questa senza dover aprire una seconda connessione sul DB
					throw new Exception ('store_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
				  
				if (!($stmt2->bind_result($descrizioneProdotto)))
					throw new Exception ('bind_result fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->fetch()))
					throw new Exception ('fetch fallita: (' . $mysqli->errno . ') ' . $mysqli->error);
					
				if (!($stmt2->close()))   
					throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);
				
				// Rimuovo i ritorni a capo nella descrizione, non richiesti
				$descrizioneProdotto = str_replace("\\n", "", $descrizioneProdotto);
				$descrizioneProdotto = str_replace("-", "", $descrizioneProdotto);
				
				if($i%2==1){
					$schedaProdotto = "<tr><td colspan='7' class='unpair'><div class='fotoProdotto'><img src='$linkProdotto' alt='foto prodotto $idProdotto' width='80px' height='50px' /></div><div class='datiProdotto'>$nomeCatProdotto<br/>($porzioniProdotto porzioni)</div><div class='descrizioneProdotto'>$descrizioneProdotto</div></td></tr>";
				}
				else{
					$schedaProdotto = "<tr><td colspan='7' class='pair'><div class='fotoProdotto'><img src='$linkProdotto' alt='foto prodotto $idProdotto' width='80px' height='50px' /></div><div class='datiProdotto'>$nomeCatProdotto<br/>($porzioniProdotto porzioni)</div><div class='descrizioneProdotto'>$descrizioneProdotto</div></td></tr>";
				}
				
				
			}
					
			if($i%2==1){
				$tabellaProdotti .= "<tr class='unpair' onMouseOver='setColor(this, 0, \"#cc6600\")' onMouseOut='setColor(this, 1, \"#282828\")'><td align='center' style='width:24px;'><input type='checkbox' name='$idProdotto' id='$idProdotto' onclick='select_row(this);' /></td><td style='width:268px;'>$nomeProdotto</td><td>$disponibilita</td><td align='center'>$prezzoProdotto</td><td align='center'> $operazioni$controlliCarrello</td></tr>
				$schedaProdotto";
			}else{
				$tabellaProdotti .= "<tr class='pair' onMouseOver='setColor(this, 0, \"#cc6600\")' onMouseOut='setColor(this, 1, \"#282828\")'><td align='center' style='width:24px;'><input type='checkbox' name='$idProdotto' id='$idProdotto' onclick='select_row(this);' /></td><td style='width:268px;'>$nomeProdotto</td><td>$disponibilita</td><td align='center'>$prezzoProdotto</td><td align='center'> $operazioni$controlliCarrello</td></tr>
				$schedaProdotto";
			}//else-if
			$i++;
		}//while    
		$tabellaProdotti .= "<tr><td colspan='7'>Numero record visualizzati: $quanti/$nProdotti</td></tr>";
		$tabellaProdotti .= "<tr><td colspan='7'><form method='GET' ACTION='index.php'>Vai alla pagina: <select name='pagina'>$selectBody</select><input type='submit' value='Vai'></form></td></tr>";
		$tabellaProdotti .= "</table>";
	}//else-if	
			
	if (!($stmt->close()))   
		throw new Exception ('chiusura oggetto risultato fallito: (' . $mysqli->errno . ') ' . $mysqli->error);

}catch (Exception $excp) {
    echo $excp -> getMessage();
}


// Interazione col DB terminata. Chiudo la connessione
$mysqli->close();

// Se la pagina e' stata chiamata da una richiesta di aggiungere prodotti al carrello, la processo
if(isset($_POST['aggProd'])){
	
	if(!(validateID("quantitaP", "quantita' prodotto", false, $avvisi)))
		$isAvvisi = true;
		
	if(!(validateID("idProd", "ID prodotto", false, $avvisi)))
		$isAvvisi = true;
		
	if(!($isAvvisi)){
		if(isset($_COOKIE['carrello'])){ 
			
			$datiCarrello = explode(",",$_COOKIE['carrello']);
			$nProdottiCarrello = $datiCarrello[0];
			
			// Controllo che nel carrello non ci siano gia' altri prodotti dello stesso tipo di quello appena inserito. In questo caso basta aumentare il campo quantita' corrispondente
			$prodottoGiaInCarrello = FALSE;
			for($k=1;$k<=($nProdottiCarrello*2);$k=$k+2){
				if($datiCarrello[$k]==$_POST['idProd']){
					$prodottoGiaInCarrello = TRUE;
					$datiCarrello[$k+1] += $_POST['quantitaP'];
				}
			}
			
			if(!($prodottoGiaInCarrello)){ // il prodotto non era gia' in carrello
				
				$datiCarrello[0]++; // aumento di 1 il campo che indica il numero di prodotti in carrello
				$nuovoCarrello = implode(",",$datiCarrello); // ricompongo il nuovo carrello copiando i dati del vecchio ma con primo campo incrementato di 1
				$nuovoCarrello .= ",".$_POST['idProd'].",".$_POST['quantitaP']; // aggiundo ul nuovo prosotto al carrello con la sua quantita'
				
			}else{ // il prodotto era gia' in carrello: devo solo ricopiare perche' la quantita' corrispondente e' gia' stata incrementata
			
				$nuovoCarrello = implode(",",$datiCarrello); // ricompongo il nuovo carrello copiando i dati del vecchio ma con primo campo incrementato di 1
				
			}
			
			// salvo il cookie :)
			setcookie ("carrello", $nuovoCarrello,time()+$tempoMaxCarrello); // il tempo massimo prima che il carrello scompaia dal computer dell'utente puo' essere impostato dal file di setup
			
			header("Location: ".$_SERVER['PHP_SELF']."?addCarrello=true");
			
		}else{
			setcookie ("carrello", "1,".$_POST['idProd'].",".$_POST['quantitaP'],time()+$tempoMaxCarrello); // il tempo massimo prima che il carrello scompaia dal computer dell'utente puo' essere impostato dal file di setup
			
			header("Location: ".$_SERVER['PHP_SELF']."?addCarrello=true");
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
<?php   // Se la pagina e' stata richiamata a seguito di tentativi di visitare pagine riservate agli admin visualizzo il relativo messaggio d'errore
if((isset($_GET['errore']))&&($_GET['errore']=="noadmin")){
?>
<div class="risultato">
Non hai il permesso per accedere alla pagina richiesta.
</div>
<?php
}
?>

<?php   // Se la pagina e' stata richiamata a seguito dell'aggiunta di un nuovo prodotto al carrello, lo segnalo
if((isset($_GET['addCarrello']))&&($_GET['addCarrello']=="true")){
?>
<div class="risultato">
Prodotto correttamente aggiunto al carrello.
</div>
<?php
}
?>
<div id="barraRicerca">
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']?>">
Ricerca
<?php
	echo $selectCategorie;
?>
<input class="mainRicerca" type="text" name="valRic" size="100" maxlength="255" >
<input type="submit" name="ricercaProd" value="Vai" >
</form>
</div>
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
	echo $tabellaProdotti;
?>
</div>
<!-- CONTENUTO VARIABILE -->

</div> <!-- Chiusura div id='center' -->
</body>
</html>