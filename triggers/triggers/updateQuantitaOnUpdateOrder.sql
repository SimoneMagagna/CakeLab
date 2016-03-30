DROP TRIGGER IF EXISTS updateQuantitaOnUpdateOrder;

DELIMITER $
CREATE TRIGGER updateQuantitaOnUpdateOrder
AFTER UPDATE ON ORDINIPRODOTTI FOR EACH ROW

BEGIN
	CALL updQuantitaBody(NEW.prodottoOP, NEW.quantitaOP);
END $

DELIMITER ;

-- Trigger attivo perchè modifica lo stato della BD.
-- Questo e' il trigger che si preoccupa di tenere aggiornate le disponibilita dei prodotti quando vengono modificati ordini inoltrati.