DROP PROCEDURE IF EXISTS delAdmin;

DELIMITER $

CREATE PROCEDURE delAdmin
(IN idAdminDaCanc integer, OUT esito BOOL, OUT tipoErrore VARCHAR(255))

BEGIN
DECLARE quantiOrdini integer;

SET esito = TRUE;
SET @tipoErrore = "";

SELECT COUNT(*) INTO quantiOrdini
FROM ORDINI
WHERE adminAssOrdine = idAdminDaCanc;

IF (quantiOrdini>0) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'impossibile cancellare l\'admin perche\' ha in gestione almeno un ordine','<br />') INTO @tipoErrore;
	
	INSERT INTO ERRORI (idErrore, descErrore)
	VALUES (12, 'impossibile cancellare l\'admin perche\' ha in gestione almeno un ordine');
END IF;

DELETE FROM ADMIN
WHERE idAdmin=idAdminDaCanc;

END $

DELIMITER ;

-- Peocedura che elimina un admin, se questo non è coinvolto in altre tabelle(ORDINI)
