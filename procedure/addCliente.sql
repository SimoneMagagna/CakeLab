DROP PROCEDURE IF EXISTS addCliente;

DELIMITER $

CREATE PROCEDURE addCliente


(IN newUserCliente VARCHAR(255), IN newPasswordCliente CHAR(32), IN newRipetiPasswordCliente CHAR(32), IN newNomeCliente VARCHAR(255), IN newCognomeCliente VARCHAR(255), IN newMailCliente VARCHAR(255), IN newIndirizzoCliente VARCHAR(255), IN newComuneCliente VARCHAR(255), IN newProvCliente CHAR(2), IN newCapCliente CHAR(5), IN newCellCliente VARCHAR(255), OUT esito BOOL, OUT tipoErrore VARCHAR(255))
BEGIN

DECLARE userRiservato BOOL;
DEClARE mailRiservata BOOL;

SET esito = TRUE;
SET @tipoErrore = "";

IF(newPasswordCliente != newRipetiPasswordCliente) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'I campi di verifica password non coincidono','<br />') INTO @tipoErrore;
	-- INSERT INTO ERRORI(idErrore, descErrore)  Non faccio fallire subito l'operazione per collezionare la lista errori su tipoErrore
	-- VALUES(10, 'I campi di verifica password non coincidono');
END IF;

SELECT COUNT(*)>0 INTO userRiservato
FROM CLIENTI
WHERE userCliente=newUserCliente;

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
WHERE mailCliente=newMailCliente;

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

INSERT INTO CLIENTI (userCliente, passwordCliente, nomeCliente, cognomeCliente, mailCliente, indirizzoCliente, comuneCliente, provCliente, capCliente, cellCliente)
VALUES (newUserCliente, newPasswordCliente, newNomeCliente, newCognomeCliente, newMailCliente, newIndirizzoCliente, newComuneCliente, newProvCliente, newCapCliente, newCellCliente);

END $

DELIMITER ;

-- Procedura che inserisce un nuovo cliente. 
-- Il parametro di OUT esito restituisce l'esito dell'operazione mentre tipoErrore contiene le descrizioni degli eventuali errori, in formatto adatto per la stampa su pagina HTML

-- Per mantenere la praticita' per l'utente di avere tutti gli errori in una volta sola, prima sono controllati tutti gli errori senza far fallire l'operazione artificialmente, concatenendoli su tipoErrore; solo al termine se si e' verificato almeno un un errore l'operazione viene fatta fallire attraverso l'inserimento di una chiave ripetuta su ERRORI. 
