DROP TRIGGER IF EXISTS checkNewCliente;

DELIMITER $

CREATE TRIGGER checkNewCliente
BEFORE INSERT ON CLIENTI FOR EACH ROW

BEGIN

DECLARE quanti integer;

SELECT count(*) INTO quanti
FROM ADMIN
WHERE userAdmin=NEW.userCliente OR mailAdmin=NEW.mailCliente;

IF (quanti=1) THEN
	INSERT INTO ERRORI VALUES (1, "user o mail Cliente riservata");
END IF;

END $

DELIMITER ;

-- Trigger passivo: verifica una condizione che, se non soddisfatta, determina il fallimento dell'inserimento.
-- Il trigger assicura che l'inserimento di un nuovo cliente non utilizzi uno username o un indirizzo di posta già registrato per un admin