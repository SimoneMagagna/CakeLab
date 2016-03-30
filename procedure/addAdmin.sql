DROP PROCEDURE IF EXISTS addAdmin;

DELIMITER $

CREATE PROCEDURE addAdmin


(IN newUserAdmin VARCHAR(255), IN newPasswordAdmin CHAR(32), IN newRipetiPasswordAdmin CHAR(32), IN newNomeAdmin VARCHAR(255), IN newCognomeAdmin VARCHAR(255), IN newMailAdmin VARCHAR(255), OUT esito BOOL, OUT tipoErrore VARCHAR(255))
BEGIN

DECLARE userRiservato BOOL;
DEClARE mailRiservata BOOL;

SET esito = TRUE;
SET @tipoErrore = "";

IF(newPasswordAdmin != newRipetiPasswordAdmin) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'I campi di verifica password non coincidono','<br />') INTO @tipoErrore;
	-- INSERT INTO ERRORI(idErrore, descErrore)  Non faccio fallire subito l'operazione per collezionare la lista errori su tipoErrore
	-- VALUES(10, 'I campi di verifica password non coincidono');
END IF;

SELECT COUNT(*)>0 INTO userRiservato
FROM CLIENTI
WHERE userCliente=newUserAdmin;

IF(!(userRiservato)) THEN
	SELECT COUNT(*)>0 INTO userRiservato
	FROM ADMIN
	WHERE userAdmin=newUserAdmin;
END IF;

IF(userRiservato) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'Username gia\' utilizzata','<br />') INTO @tipoErrore;
	-- INSERT INTO ERRORI(idErrore, descErrore) Non faccio fallire subito l'operazione per collezionare la lista errori su tipoErrore
	-- VALUES(8, 'username gia\' utilizzata');
END IF;

SELECT COUNT(*)>0 INTO mailRiservata
FROM CLIENTI
WHERE mailCliente=newMailAdmin;

IF(!(mailRiservata)) THEN
	SELECT COUNT(*)>0 INTO mailRiservata
	FROM ADMIN
	WHERE mailAdmin=newMailAdmin;
END IF;

IF(mailRiservata) THEN
	SET esito = FALSE;
	SELECT CONCAT(@tipoErrore,'Mail gia\' utilizzata','<br />') INTO @tipoErrore;
	-- INSERT INTO ERRORI(idErrore, descErrore) Non faccio fallire subito l'operazione per collezionare la lista errori su tipoErrore
	-- VALUES(9, 'mail gia\' utilizzata');
END IF;

IF(!esito) THEN
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES(11, 'Errore nell\'inserimento del nuovo utente');
END IF;

INSERT INTO ADMIN (userAdmin, passwordAdmin, nomeAdmin, cognomeAdmin, mailAdmin)
VALUES (newUserAdmin, newPasswordAdmin, newNomeAdmin, newCognomeAdmin, newMailAdmin);

END $

DELIMITER ;

-- Procedura che inserisce un nuovo cliente. 
-- Il parametro di OUT esito restituisce l'esito dell'operazione mentre tipoErrore contiene le descrizioni degli eventuali errori, in formatto adatto per la stampa su pagina HTML

-- Per mantenere la praticita' per l'utente di avere tutti gli errori in una volta sola, prima sono controllati tutti gli errori senza far fallire l'operazione artificialmente, concatenendoli su tipoErrore; solo al termine se si e' verificato almeno un un errore l'operazione viene fatta fallire attraverso l'inserimento di una chiave ripetuta su ERRORI. 
