SET GLOBAL log_bin_trust_function_creators=1;  -- richiesto perche' questa funzione non interagisce con la base dati e altrimenti restituisce l'errore #1418 

CREATE FUNCTION SPLIT_STRING(str VARCHAR(255), delimitatore VARCHAR(12), posizione INT)
RETURNS VARCHAR(255)
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delimitatore, posizione),
       LENGTH(SUBSTRING_INDEX(str, delimitatore, posizione-1)) + 1),
       delimitatore, '');

-- Questa funzione scorre posizione elementi di una stringa str separati da delimitatore. (non è possibile restiruire un array come avviene per explode di php).

-- SPIEGAZIONE:
-- SUBSTRING_INDEX prende la stringa str, conta posizione occorrenze del delimitatore e calcella tutto quello che segue
-- ESEMPIO: SELECT SUBSTRING_INDEX('www.CakeLab.it','.',2);  RISULTATO: www.CakeLab

-- SUBSTRING riceve come primo parametro il risultato di SUBSTRING_INDEX, come secondo lo scarto tra l'inizio della stringa iniziale e il punto desiderato e poiche' il terzo e' omesso restituisce tutto fino alla fine della stringa: 

-- ESEMPIO sulla stringa str="2,prodotto1,2,prodotto1,1"; 
-- volendo il secondo pezzo, ossia "prodotto1" chiamerò:

-- SUBSTRING(SUBSTRING_INDEX(str, ',', 2),LENGTH(SUBSTRING_INDEX(str, ',', 1)) + 1) =
-- = SUBSTRING("2,prodotto1", LENGTH("2")+1)
-- = SUBSTRING("2,prodotto1", 2) = ",prodotto1"

-- A questo punto il dato è quello che volevo a meno della virgola iniziale.
-- Mi basta chiamare la funzione replace sul risultato di tutto quello che ho catturato sopra:
-- REPLACE(",prodotto1", ",", "") = "prodotto1" :)

