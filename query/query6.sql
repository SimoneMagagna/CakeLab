-- Query che restituisce l'id e lo stato degli ordini i quali non contengono Crostate di crema cotta 
-- e i prodotti ordinati sono in numero minore ugale a due.

SELECT o.idOrdine, o.statoOrdine
FROM ORDINI o
WHERE NOT EXISTS(SELECT * FROM ORDINIPRODOTTI op JOIN PRODOTTI p ON(op.prodottoOP=p.idProdotto)
WHERE o.idOrdine=op.ordineOP AND(p.nomeProdotto = 'Crostate di creama cotta' OR op.quantitaOP>2));


-- OUTPUT:

idOrdine	statoOrdine
5			Registrato
6			Pagato
7			Registrato
8			Registrato