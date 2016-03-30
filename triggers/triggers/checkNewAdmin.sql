DROP TRIGGER IF EXISTS checkNewAdmin;

DELIMITER $

CREATE TRIGGER checkNewAdmin
BEFORE INSERT ON ADMIN FOR EACH ROW

BEGIN

DECLARE quanti integer;

SELECT count(*) INTO quanti
FROM CLIENTI
WHERE userCliente=NEW.userAdmin OR mailCliente=NEW.mailAdmin;

IF (quanti=1) THEN
	INSERT INTO ERRORI VALUES (2, "user o mail Admin riservata");
END IF;

END $

DELIMITER ;

-- Trigger passivo: verifica una condizione che, se non soddisfatta, determina il fallimento dell'inserimento.
-- Il trigger assicura che l'inserimento di un nuovo admin non utilizzi uno username o un indirizzo di posta già registrato per un cliente