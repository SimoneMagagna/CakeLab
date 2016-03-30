-- seleziono tutti i clienti che hanno effettuato acquisti nel mese di giugno 2014 e il totale speso. Per i clienti che non hanno effettuato acquisti ritorna 0

SELECT c.nomeCliente, c.cognomeCliente, t.totSpesa 
FROM CLIENTI c INNER JOIN (
SELECT clienteOrdine, sum(importoOrdine) AS totSpesa 
FROM ORDINI 
WHERE dataOrdine BETWEEN '2014-06-01' AND '2014-06-30'
GROUP BY clienteOrdine) t 
ON t.clienteOrdine = c.idCliente
UNION
SELECT c.nomeCliente, c.cognomeCliente, 0 as totSpesa 
FROM CLIENTI c 
WHERE c.idCliente NOT IN (
SELECT clienteOrdine 
FROM ORDINI 
WHERE dataOrdine between '2014-06-01' and '2014-06-30')


-- OUTPUT:

nomeCliente	cognomeCliente	totSpesa
Mario		Tubi			80
Guido		Ferrari			45
Fabio		Marra			320
Tony		Ryan			1935
Jeffrey		Bezos			1210
Carlo		Bianchi			0