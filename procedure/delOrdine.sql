DROP PROCEDURE IF EXISTS delOrdine;

DELIMITER $

CREATE PROCEDURE delOrdine


(IN idOrdineDaCanc integer)

BEGIN
DECLARE statoOrdineDaCanc ENUM('Registrato', 'Pagato', 'In lavorazione', 'Chiuso');
DECLARE prodOP integer;
DECLARE quantOP integer;
DECLARE spedizioneAss integer;
DECLARE pagamentoAss integer;

DECLARE Done integer default 0;
DECLARE cursoreOP CURSOR FOR SELECT prodottoOP, quantitaOP
FROM ORDINIPRODOTTI
WHERE ordineOP=idOrdineDaCanc;

DECLARE CONTINUE HANDLER FOR NOT FOUND
SET Done = 1;

SELECT statoOrdine INTO statoOrdineDaCanc
FROM ORDINI
WHERE idOrdine=idOrdineDaCanc;

SET AUTOCOMMIT=0;
START TRANSACTION;

IF(statoOrdineDaCanc='Registrato') THEN

OPEN cursoreOP;
	REPEAT
		FETCH cursoreOP INTO prodOP, quantOP;
		IF NOT Done THEN
			UPDATE PRODOTTI
			SET disponibilitaProdotto=disponibilitaProdotto+quantOP
			WHERE idProdotto=prodOP;
		END IF;
	UNTIL Done END REPEAT;
CLOSE cursoreOP;

END IF;

SELECT spedizioneAssOrdine INTO spedizioneAss
FROM ORDINI
WHERE idOrdine = idOrdineDaCanc;

DELETE FROM SPEDIZIONI
WHERE idSpedizione=spedizioneAss;

SELECT pagamentoAssOrdine INTO pagamentoAss
FROM ORDINI
WHERE idOrdine = idOrdineDaCanc;

DELETE FROM PAGAMENTI
WHERE idPagamento=spedizioneAss;

DELETE FROM ORDINIPRODOTTI
WHERE ordineOP=idOrdineDaCanc;

DELETE FROM ORDINI
WHERE idOrdine=idOrdineDaCanc;


COMMIT;
SET AUTOCOMMIT=1;

END $

DELIMITER ;

-- Peocedura che cancella un ordine
-- Se l'ordine e' solo allo stato di registrato si preoccupa di riprostonare le disponibilita' 
