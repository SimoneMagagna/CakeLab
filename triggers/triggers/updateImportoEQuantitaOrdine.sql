DROP TRIGGER IF EXISTS updateImportoEQuantitaOrdine;

DELIMITER $$
CREATE TRIGGER updateImportoEQuantitaOrdine
AFTER INSERT ON ORDINIPRODOTTI FOR EACH ROW

BEGIN
DECLARE prezzo DECIMAL;
DECLARE conto DECIMAL;

CALL updQuantitaBody(NEW.prodottoOP, NEW.quantitaOP); -- poichè richiamo una funzione delimitata con $ devo ussare un delimitatore diverso in questa funzione chiamante

SELECT prezzoProdotto INTO prezzo FROM PRODOTTI WHERE IdProdotto = NEW.prodottoOP;

SET conto = prezzo * NEW.quantitaOP;

UPDATE ORDINI SET importoOrdine = importoOrdine + conto WHERE idOrdine = NEW.ordineOP;
	
END $$

DELIMITER ;

-- Trigger attivo perchè modifica lo stato della BD.
-- Questo e' il trigger che si preoccupa di aggiornare l'importo dell'ordine appena inserito, prodotto per prodotto, e di mantenere il conto delle disponibilita'.