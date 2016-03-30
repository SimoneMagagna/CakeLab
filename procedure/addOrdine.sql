DROP PROCEDURE IF EXISTS addOrdine;

DELIMITER $

CREATE PROCEDURE addOrdine

(IN listaProdotti VARCHAR(255), IN clienteOrdine INT, OUT esito BOOL, OUT codIntOrdineInserito VARCHAR(255))

BEGIN
DECLARE idOrdine integer;

DECLARE idIntOrdine integer;
DECLARE anno integer;

DECLARE nProdChar varchar(255);
DECLARE nProd integer;
DECLARE idProdChar varchar(255);
DECLARE idProd integer;
DECLARE quantitaChar varchar(255);
DECLARE quantita integer;

DECLARE x integer;
DECLARE conta integer;


SELECT EXTRACT(YEAR FROM CURDATE()) into anno;

SELECT MAX(idInternoOrdine) INTO idIntOrdine FROM ORDINI WHERE annoOrdine = anno;

IF idIntOrdine IS NULL THEN 
	SET idIntOrdine = 0;
END IF;
SET idIntOrdine = idIntOrdine +1;

SET AUTOCOMMIT=0;
START TRANSACTION;

	INSERT INTO ORDINI(idInternoOrdine, annoOrdine, clienteOrdine) VALUES (idIntOrdine, anno, clienteOrdine);
	SELECT DISTINCT LAST_INSERT_ID() INTO idOrdine FROM ORDINI;

	SELECT SPLIT_STRING(listaProdotti, ',', 1) INTO nProdChar; -- equivalente a: SET nProdChar = SPLIT_STRING(listaProdotti, ',', 1);
	SELECT CAST(nProdChar AS UNSIGNED INTEGER) INTO nProd;

	SET x=2;
	SET conta=0;

	WHILE(conta<nProd) DO

		SELECT SPLIT_STRING(listaProdotti, ',', x) INTO idProdChar;
		SELECT SPLIT_STRING(listaProdotti, ',', x+1) INTO quantitaChar;

		SELECT CAST(idProdChar AS UNSIGNED INTEGER) INTO idProd;
		SELECT CAST(quantitaChar AS UNSIGNED INTEGER) INTO quantita;

		INSERT INTO ORDINIPRODOTTI(ordineOP, prodottoOP, quantitaOP) VALUES (idOrdine, idProd, quantita);

		SET x = x+2;
		SET conta=conta+1;

	END WHILE;
	
	SELECT CONCAT(idIntOrdine, '-', anno) INTO codIntOrdineInserito;

COMMIT;

SET AUTOCOMMIT=1;

SET esito = TRUE;

END $

DELIMITER ;

-- Procedura che inserisce un nuovo ordine
-- ESEMPIO
-- addOrdine('4, 1, 2, 2, 3, 3, 4, 4, 5',1,@esito,@codiceOrdineInterno);
-- inserisce un ordine con 4 prodotti(primo numero della stringa primo parametro) rispettivamente
-- 2 prodotti con idProdotto=1
-- 3 prodotti con idProdotto=2
-- 4 prodotti con idProdotto=3
-- 5 prodotti con idProdotto=4
-- lo assegna al cliente con idCliente=1, secondo parametro di addOrdine.
-- l'esito dell'inserimento e' restituito all'interno di @esito e se l'inserimento ha successo l'id interno appena inserito viene restituito su @codiceOrdineInterno.

-- Trigger opportuni mantengono aggiornati automaticamente disponibilita' e importo totale. L'eventuale mancanza di disponibilita' di un prodotto puo' far fallore l'intera operazione.
