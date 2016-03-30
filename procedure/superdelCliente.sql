DROP PROCEDURE IF EXISTS superdelCliente;

DELIMITER $

CREATE PROCEDURE superdelCliente
(IN idClienteDaCanc integer)

BEGIN
DECLARE quantiOrdini integer;
DECLARE numOrdine integer;
DECLARE Done integer default 0;
DECLARE cursoreOrdini CURSOR FOR SELECT idOrdine
FROM ORDINI
WHERE clienteOrdine=idClienteDaCanc;

DECLARE CONTINUE HANDLER FOR NOT FOUND
SET Done = 1;
 

SELECT COUNT(*) INTO quantiOrdini
FROM ORDINI
WHERE clienteOrdine=idClienteDaCanc;

SET AUTOCOMMIT=0;
START TRANSACTION;

IF (quantiOrdini>0) THEN

OPEN cursoreOrdini;
	REPEAT
		FETCH cursoreOrdini INTO numOrdine;
		IF NOT Done THEN
			CALL delOrdine(numOrdine);
		END IF;
	UNTIL Done END REPEAT;
CLOSE cursoreOrdini;

END IF;

DELETE FROM CLIENTI
WHERE idCliente=idClienteDaCanc;

COMMIT;
SET AUTOCOMMIT=1;

END $

DELIMITER ;

-- Peocedura che elimina un cliente, e tutti gli ordini ad esso associati(PERICOLOSA!!)
