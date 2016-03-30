DROP PROCEDURE IF EXISTS delProdotto;

DELIMITER $

CREATE PROCEDURE delProdotto
(IN idProdottoDaCanc VARCHAR(255), OUT esito BOOL)

BEGIN
DECLARE quantiOrdini integer;
DECLARE idDesc integer;

SET AUTOCOMMIT=0;
START TRANSACTION;

SELECT COUNT(*) INTO quantiOrdini
FROM ORDINIPRODOTTI
WHERE prodottoOP = idProdottoDaCanc;

IF (quantiOrdini>0) THEN
INSERT INTO ERRORI (idErrore, descErrore)
VALUES (3, 'impossibile cancellare il prodotto perche\' ha almeno un ordine associato');
END IF;

SET FOREIGN_KEY_CHECKS=0;


DELETE FROM PRODOTTI WHERE idProdotto = idProdottoDaCanc;

SELECT descrizioneAssProdotto INTO idDesc FROM PRODOTTI WHERE idProdotto = idProdottoDaCanc;
DELETE FROM DESCRIZIONIPRODOTTI WHERE idDesc = idDesc;

DELETE FROM MEDIA WHERE prodottoMedia = idProdottoDaCanc;

SET FOREIGN_KEY_CHECKS=1;

COMMIT;
SET AUTOCOMMIT=1;

END $

DELIMITER ;

-- Peocedura che elimina un prodotto, se questo non è coinvolto in altre tabelle
