DROP PROCEDURE IF EXISTS updDispProdotto;

DELIMITER $

CREATE PROCEDURE updDispProdotto
(IN idProdottoUpd int, IN nuovaDisp int)

BEGIN

IF(nuovaDisp<0) THEN
	INSERT INTO ERRORI(idErrore, descErrore)
	VALUES (5, 'impossibile registrare una disponibilita\' prodotto negativa');
END IF;

UPDATE PRODOTTI
SET disponibilitaProdotto=nuovaDisp
WHERE idProdotto=idProdottoUpd;

END $

DELIMITER ;

-- Peocedura che aggiorna la disponibilita' di un prodotto
