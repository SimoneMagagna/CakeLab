DROP TRIGGER IF EXISTS checkUpdateProdotto;

DELIMITER $
CREATE TRIGGER checkUpdateProdotto
AFTER UPDATE ON PRODOTTI FOR EACH ROW

BEGIN

IF(NEW.prezzoProdotto<=0) THEN
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES (13, 'impossibile registrare un prezzo prodotto negativo o nullo');
END IF;

IF(NEW.disponibilitaProdotto<0) THEN
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES (5, 'impossibile registrare una disponibilita\' prodotto negativa');
END IF;
	
END $

DELIMITER ;

-- Trigger passivo perchè NON modifica lo stato della BD.
-- Questo trigger assicura che a seguito di modifiche su prezzi e disponibilita' di prodotti, non vi possano essere assegnazioni di valori negativi