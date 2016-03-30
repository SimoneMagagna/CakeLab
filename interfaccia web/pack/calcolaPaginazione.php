// Calcola quante righe mettere per una pagina in base al numero di record totale e alle pagine che gli vengono date.
<?php
// suddivido gli nRecord risultanti da una query in pagine di $nMaxRighe per la visualizzazione tabellare
function calcolaPaginazione($nRecord, $nMaxRighe, &$nPages, &$inizio, &$quanti, &$isErrore, &$errori){
	$page = 0;
	// controllo se il valore $nMaxRighe e' valido
	if(($nMaxRighe < 1) || ($nMaxRighe > 50)){
		$isErrore = TRUE;
		$errori .= "Il numero massimo di righe richiesto non e' valido. Contatta l'amministratore";
		return $page;
	}else{
		$nPages = floor($nRecord/$nMaxRighe); // floor() arrotonda per eccesso. L'ultima sara' la pagina parziale
		if($nRecord%$nMaxRighe==0) // Controllo se l'ultima pagina parziale e' necessaria
			$nPages--;
		if((!(isset($_GET['pagina'])))||($_GET['pagina']<2)||($_GET['pagina']>($nPages+1))){
			$pages = 1; // se la richiesta e' fuori dal range mostro sempre la prima pagina
			if($nRecord<$nMaxRighe) // se il numero massimo di righe che posso visualizzare e' minore di tutti gli utenti salvati mostrero' tutti gli utenti salvati
				$quanti = $nRecord; 
			else // altrimenti mostro $nMaxRighe utenti
				$quanti = $nMaxRighe;
			$inizio = 0;
		}else{
			if((ctype_digit($_GET['pagina']))&&($_GET['pagina']<=($nPages+1))){
				$page = $_GET['pagina'];
				if(($page-1)==$nPages)
					$quanti = $nRecord-($nPages*$nMaxRighe);
				else
					$quanti = $nMaxRighe;
				$inizio = ($page-1)*$nMaxRighe;  // il margine inferiore da cui contare i $nDisp utenti da visualizzare(e' il funzionamento di LIMIT)
			}//if
		}//else-if
		return $page;
	}//else-if
}
?>
