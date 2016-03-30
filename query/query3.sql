-- Nome prodotto che a giugno 2014 è stato venduto più di 80 volte(anche sul singolo ordine)

SELECT  idProdotto, nomeProdotto 
FROM PRODOTTI INNER JOIN ORDINIPRODOTTI ON idProdotto=prodottoOP
WHERE idProdotto IN (
SELECT prodottoOP FROM ORDINIPRODOTTI INNER JOIN ORDINI ON idOrdine = ordineOP
WHERE statoOrdine = 'chiuso'
AND dataOrdine BETWEEN '2014-06-01' AND '2014-06-30'
)
GROUP BY idProdotto
HAVING SUM(quantitaOP)>80;


-- OUTPUT:

idProdotto	nomeProdotto
3			Brioche alla crema