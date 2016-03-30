-- seleziono nome e cognome del cliente che ha effettuato il maggior numero di ordini relativi al prodotto 4

SELECT clienteOrdine FROM (
SELECT clienteOrdine, count( * ) AS numOrdine
FROM ORDINI
INNER JOIN ORDINIPRODOTTI ON idOrdine = ordineOP
WHERE prodottoOP =4
GROUP BY clienteOrdine) t
HAVING max(numOrdine)


-- OUTPUT:
clienteOrdine
4