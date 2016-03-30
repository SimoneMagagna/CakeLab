DROP PROCEDURE IF EXISTS updCliente;

DELIMITER $

CREATE PROCEDURE updCliente


(IN updIdCliente INT,IN newUserCliente VARCHAR(255), IN newNomeCliente VARCHAR(255), IN newCognomeCliente VARCHAR(255), IN newMailCliente VARCHAR(255), IN newIndirizzoCliente VARCHAR(255), IN newComuneCliente VARCHAR(255), IN newProvCliente CHAR(2), IN newCapCliente CHAR(5), IN newCellCliente VARCHAR(255), OUT esito BOOL, OUT tipoErrore VARCHAR(255))
BEGIN

DECLARE userRiservato BOOL;
DEClARE mailRiservata BOOL;

SET esito = TRUE;
SET @tipoErrore = "";


SELECT COUNT(*)>0 INTO userRiservato
FROM CLIENTI
WHERE userCliente=newUserCliente
AND idCliente <> updIdCliente;

IF(!(userRiservato)) THEN
	SELECT COUNT(*)>0 INTO userRiservato
	FROM ADMIN
	WHERE userAdmin=newUserCliente;
END IF;

IF(userRiservato) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'Username gia\' utilizzata','<br />') INTO @tipoErrore;
	-- INSERT INTO ERRORI(idErrore, descErrore) Non faccio fallire subito l'operazione per collezionare la lista errori su tipoErrore
	-- VALUES(8, 'username gia\' utilizzata');
END IF;

SELECT COUNT(*)>0 INTO mailRiservata
FROM CLIENTI
WHERE mailCliente=newMailCliente
AND idCliente <> updIdCliente;

IF(!(mailRiservata)) THEN
	SELECT COUNT(*)>0 INTO mailRiservata
	FROM ADMIN
	WHERE mailAdmin=newMailCliente;
END IF;

IF(mailRiservata) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'Mail gia\' utilizzata','<br />') INTO @tipoErrore;
	-- INSERT INTO ERRORI(idErrore, descErrore) Non faccio fallire subito l'operazione per collezionare la lista errori su tipoErrore
	-- VALUES(9, 'mail gia\' utilizzata');
END IF;

IF(!esito) THEN
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES(11, 'Errore nell\'inserimento del nuovo cliente');
END IF;

UPDATE CLIENTI SET userCliente = newUserCliente, nomeCliente = newNomeCliente, cognomeCliente = newCognomeCliente, mailCliente = newMailCliente, indirizzoCliente = newIndirizzoCliente, comuneCliente = newComuneCliente, provCliente = newProvCliente, capCliente = newCapCliente, cellCliente = newCellCliente WHERE idCliente = updIdCliente;

END $

DELIMITER ;

-- Procedura che modifica un cliente. 
-- Il parametro di OUT esito restituisce l'esito dell'operazione mentre tipoErrore contiene le descrizioni degli eventuali errori, in formatto adatto per la stampa su pagina HTML

-- Per mantenere la praticita' per l'utente di avere tutti gli errori in una volta sola, prima sono controllati tutti gli errori senza far fallire l'operazione artificialmente, concatenendoli su tipoErrore; solo al termine se si e' verificato almeno un un errore l'operazione viene fatta fallire attraverso l'inserimento di una chiave ripetuta su ERRORI. 
