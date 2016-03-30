// Contiene variabili utilizzate 
<?php
/*
Variabili di configurazione e opzioni generali
*/

// #MaxRighe/Pagina visualizzate nella tabelle. DEFAULT: 10
$nMaxRighe = 10;

// #MaxRighe/Pagina per la visualizzazione dei prodotti con scheda prodotto. DEFAULT: 5
$nMaxRigheRidotto = 5;

// #MaxRighe/Pagina per la visualizzazione dettagliata degli ordini. DEFAULT: 4
$nMaxRigheDettaglioOrdini = 4;

// il tempo massimo prima che un carrello scompaia dal computer del cliente, in secondi. DEFAULT: 3600(1h)
$tempoMaxCarrello = 3600;

// Per permettere sempre la cancellazione di un ordine, anche se questo ha superato lo stato di "Registrato" {per il testing} DEFAULT: FALSE
$ordiniSempreCancellabili = FALSE;

?>
