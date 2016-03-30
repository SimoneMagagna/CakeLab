DROP PROCEDURE IF EXISTS addProdotto;

DELIMITER $

CREATE PROCEDURE addProdotto
(IN nomeProdotto VARCHAR(255), IN prezzoProdotto DECIMAL(7,2), IN pesoProdotto DECIMAL(7,2), IN porzioniProdotto INT, IN categoriaProdotto INT, IN disponibilitaProdotto INT, IN descrizione VARCHAR(500))

BEGIN
DECLARE codiceDesc INT;

SET AUTOCOMMIT=0;
START TRANSACTION;

INSERT INTO DESCRIZIONIPRODOTTI VALUES (NULL, descrizione);

SELECT DISTINCT LAST_INSERT_ID() INTO codiceDesc FROM DESCRIZIONIPRODOTTI;

INSERT INTO PRODOTTI (nomeProdotto, prezzoProdotto, pesoProdotto, porzioniProdotto, categoriaProdotto, disponibilitaProdotto, descrizioneAssProdotto) VALUES (nomeProdotto, prezzoProdotto, pesoProdotto, porzioniProdotto, categoriaProdotto, disponibilitaProdotto, codiceDesc);

COMMIT;
SET AUTOCOMMIT=1;

END $

DELIMITER ;

-- Peocedura che inserisce un nuovo prodotto CALL addProdotto('spezzatino di gnu', 50.50, 50000, 15, 4, 1, 'Un gnugnouovo')
