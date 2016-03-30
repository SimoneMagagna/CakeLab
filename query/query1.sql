-- per ogni prodotto numero di acquisti nell'anno 2014 in ordine di vendite

select prodottoOP, nomeProdotto, sum(quantitaOP) AS numVendite
from (ORDINIPRODOTTI INNER JOIN PRODOTTI ON prodottoOP=idProdotto) INNER JOIN ORDINI ON idOrdine = ordineOP
WHERE annoOrdine = 2014
GROUP BY prodottoOP
ORDER BY numVendite DESC

-- OUTPUT: 

prodottoOP	nomeProdotto					numVendite

3		Brioche alla crema				100
7		Crostata ai frutti di bosco			48
2		Treccia di ricotta				37
4		Torta paradiso					29
6		Sfogliata alle erbette				21
9		Cous cous con piovra				4
1		Crostata di crema cotta				4
5		Sfogliata di mele e radicchio			2
8		Trancio di pesce spada in crost			2
10		Zuppa di pesce					1
