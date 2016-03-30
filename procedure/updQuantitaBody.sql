DROP PROCEDURE IF EXISTS updQuantitaBody;

DELIMITER $

CREATE PROCEDURE updQuantitaBody(prodOP integer, quantOP integer)

BEGIN

DECLARE vecchiaQuantita integer;
DECLARE nuovaQuantita integer;

SELECT disponibilitaProdotto INTO vecchiaQuantita
FROM PRODOTTI
WHERE idProdotto = prodOP;

SET nuovaQuantita = vecchiaQuantita-quantOP;
IF (nuovaQuantita<0) THEN
   INSERT INTO ERRORI(idErrore, descErrore)
   VALUES(4, 'impossibile registrare l\'ordine: la quantita\' richiesta eccede quella disponibile');
END IF;

UPDATE PRODOTTI
SET disponibilitaProdotto=nuovaQuantita
WHERE idProdotto=prodOP;

END $

DELIMITER ;

-- Peocedura richiamata dai trigger che aggiornano la disponibilita' dei prodotti a seguito di un ordine
