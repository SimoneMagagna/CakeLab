DROP PROCEDURE IF EXISTS tryLogin;

DELIMITER $

CREATE PROCEDURE tryLogin


(IN userUtente VARCHAR(255), IN passwordUtente CHAR(32), OUT esito BOOL, OUT gruppoUtente BOOL, OUT nomeUtente VARCHAR(255), OUT cognomeUtente VARCHAR(255), OUT idUtente INT)

BEGIN
DECLARE utenteTrovato BOOL;
DECLARE adminTrovato BOOL;


SET utenteTrovato = FALSE;
SET adminTrovato = FALSE;

SELECT COUNT(*)>0 INTO utenteTrovato
FROM CLIENTI 
WHERE userCliente=userUtente AND passwordCliente=passwordUtente;

IF(utenteTrovato) THEN
	SET esito = TRUE;
	SET gruppoUtente = FALSE;
	
	SELECT nomeCliente, cognomeCliente, idCliente INTO nomeUtente, cognomeUtente, idUtente
	FROM CLIENTI
	WHERE userCliente=userUtente AND passwordCliente=passwordUtente;
ELSE
	SELECT COUNT(*)>0 INTO adminTrovato
	FROM ADMIN
	WHERE userAdmin=userUtente AND passwordAdmin=passwordUtente;
	
	IF(adminTrovato) THEN
		SET esito = TRUE;
		SET gruppoUtente = TRUE;
		
		SELECT nomeAdmin, cognomeAdmin, idAdmin INTO nomeUtente, cognomeUtente, idUtente
		FROM ADMIN
		WHERE userAdmin=userUtente AND passwordAdmin=passwordUtente;
    ELSE
	    SET esito = FALSE;
	END IF;
END IF;

END $

DELIMITER ;

-- Peocedura che tenta un Login dato uno userUtente e una pwd gia' criptata md5. 
-- Questa funzione puo' riconoscere sia un cliente che un admin
-- In caso di successo esito=1, e in questo caso assumono significato anche tutti gli altri parametri:
-- gruppo viene settato a 0(login di un cliente) o 1(login di un admin) e vengono restituiti i principali dati relativi al cliente in oggetto
-- altrimenti esito=0 e tutti gli altri parametri non hanno significato
