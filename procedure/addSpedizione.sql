DROP PROCEDURE IF EXISTS addSpedizione;

DELIMITER $

CREATE PROCEDURE addSpedizione
(IN idOrdineSpedito INT, IN vettoreUsato VARCHAR(255), IN trackingSped VARCHAR(255), OUT esito BOOL, OUT tipoErrore VARCHAR(255))

BEGIN

DECLARE ultimoID integer;
DECLARE isPO bool;
SET esito = TRUE;

-- Controllo cha all'ordine sia associato un pagamento, e nel caso faccio fallire l'operazione
SELECT isPagatoOrdine INTO isPO
FROM ORDINI
WHERE idOrdine=idOrdineSpedito;

IF(isPO=FALSE) THEN
    SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'impossibile spedire un ordine non ancora saldato','<br />') INTO @tipoErrore;
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES(7, 'impossibile spedire un ordine non ancora saldato');
END IF;

SET AUTOCOMMIT=0;
START TRANSACTION;

INSERT INTO SPEDIZIONI(vettoreSpedizione, trackingSpedizione)
VALUES(vettoreUsato, trackingSped);

SELECT last_insert_id() INTO ultimoID;

UPDATE ORDINI
SET statoOrdine='Chiuso', isChiusoOrdine=TRUE, spedizioneAssOrdine=ultimoID
WHERE idOrdine=idOrdineSpedito;

COMMIT;
SET AUTOCOMMIT=1;

END $

DELIMITER ;

-- Procedura che registra la spedizine per un dato ordine. Se l'ordine NON e' già stato pagato l'operazione fallisce
