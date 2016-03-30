DROP PROCEDURE IF EXISTS addPagamento;

DELIMITER $

CREATE PROCEDURE addPagamento
(IN idOrdinePagato INT, IN tipoPag ENUM('Bonifico Bancario', 'Carta di Credito', 'PayPal', 'Postagiro'), OUT esito BOOL, OUT tipoErrore VARCHAR(255))

BEGIN

DECLARE ultimoID integer;
DECLARE isPO bool;
SET esito = TRUE;

-- Controllo che l'ordine non sia gia' stato pagato o in uno stato successivo, perche' nel caso ha sicuramente un record di pagamento associato
SELECT isPagatoOrdine INTO isPO
FROM ORDINI
WHERE idOrdine=idOrdinePagato;

IF(isPO=TRUE) THEN
    SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'impossibile registrare un nuovo pagamento per un ordine gia\' saldato','<br />') INTO @tipoErrore;
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES(6, 'impossibile registrare un nuovo pagamento per un ordine gia\' saldato');
END IF;


SET AUTOCOMMIT=0;
START TRANSACTION;

INSERT INTO PAGAMENTI(tipoPagamento)
VALUES(tipoPag);

SELECT last_insert_id() INTO ultimoID;

UPDATE ORDINI
SET statoOrdine='Pagato', isPagatoOrdine=TRUE, pagamentoAssOrdine=ultimoID
WHERE idOrdine=idOrdinePagato;


COMMIT;
SET AUTOCOMMIT=1;

END $

DELIMITER ;

-- Procedura che registra il pagamento per un dato ordine. Se l'ordine e' già stato pagato l'operazione fallisce
