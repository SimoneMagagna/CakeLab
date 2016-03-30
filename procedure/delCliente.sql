DROP PROCEDURE IF EXISTS delCliente;

DELIMITER $

CREATE PROCEDURE delCliente
(IN idClienteDaCanc integer, OUT esito BOOL, OUT tipoErrore VARCHAR(255))

BEGIN
DECLARE quantiOrdini integer;

SET esito = TRUE;
SET @tipoErrore = "";

SELECT COUNT(*) INTO quantiOrdini
FROM ORDINI
WHERE clienteOrdine = idClienteDaCanc;

IF (quantiOrdini>0) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'impossibile cancellare il cliente perche\' ha almeno un ordine associato','<br />') INTO @tipoErrore;
	
	INSERT INTO ERRORI (idErrore, descErrore)
	VALUES (3, 'impossibile cancellare il cliente perche\' ha almeno un ordine associato');
END IF;

DELETE FROM CLIENTI
WHERE idCliente=idClienteDaCanc;

END $

DELIMITER ;

-- Peocedura che elimina un cliente, se questo non è coinvolto in altre tabelle(ORDINI)
