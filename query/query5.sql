-- Query che restituisce l'id, il nome e la categoria dei prodotti con piu' di 10 vendite nell'anno

SELECT p.idProdotto, p.nomeProdotto, p.categoriaProdotto
FROM PRODOTTI p INNER JOIN ORDINIPRODOTTI op ON idProdotto=prodottoOP
WHERE p.idProdotto IN( 
SELECT pr.idProdotto
FROM ORDINI o INNER JOIN ORDINIPRODOTTI opr ON (o.idOrdine=opr.ordineOP) JOIN PRODOTTI pr ON (opr.prodottoOP=pr.idProdotto)
WHERE o.statoOrdine = 'chiuso' AND EXTRACT(YEAR FROM o.dataOrdine)=EXTRACT(YEAR FROM CURDATE()))
GROUP BY p.idProdotto
HAVING SUM(op.quantitaOP)>10;


-- OUTPUT:

idProdotto	nomeProdotto					categoriaProdotto
2			Treccia di ricotta				4
3			Brioche alla crema				1
4			Torta paradiso					4
6			Sfogliata alle erbette			8
7			Crostata ai frutti di bosco		5