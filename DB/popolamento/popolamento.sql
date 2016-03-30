-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generato il: 21 giu, 2014 at 02:16 AM
-- Versione MySQL: 5.1.36
-- Versione PHP: 5.3.26

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `CakeLab`
--

--
-- Dump dei dati per la tabella `ADMIN`
--

INSERT INTO `ADMIN` (`idAdmin`, `userAdmin`, `passwordAdmin`, `nomeAdmin`, `cognomeAdmin`, `mailAdmin`) VALUES
(1, 'Manuelito', '0c7540eb7e65b553ec1ba6b20de79608', 'Manuel', 'Sgaravato', 'manuel.sgaravato@gmail.com'),
(2, 'Simone', '2492f96272cac4a6128b037992a1cb03', 'Simone', 'Magagna', 'simone.magagna91@gmail.com');

--
-- Dump dei dati per la tabella `CATEGORIE`
--

INSERT INTO `CATEGORIE` (`idCategoria`, `nomeCategoria`) VALUES
(5, 'Crostate di frutta'),
(8, 'Gastronomia'),
(2, 'Paste giganti'),
(1, 'Pasticceria mignon'),
(7, 'Torte d''amore'),
(11, 'Torte da cerimonia'),
(4, 'Torte da forno'),
(3, 'Torte fantasia'),
(10, 'Torte nuziali'),
(9, 'Torte per bambini'),
(6, 'Torte personalizzate');

--
-- Dump dei dati per la tabella `CLIENTI`
--

INSERT INTO `CLIENTI` (`idCliente`, `userCliente`, `passwordCliente`, `nomeCliente`, `cognomeCliente`, `mailCliente`, `indirizzoCliente`, `comuneCliente`, `provCliente`, `capCliente`, `cellCliente`) VALUES
(1, 'mario89', '101228bff0fe418ad44410cb5dc67950', 'Mario', 'Tubi', 'marioebros.tubi@msn.it', 'via del castello della principessa, 8', 'Monopoli', 'BA', '79238', '3423765355'),
(2, 'guido76', 'f1967399bde09d030b25e1b9dfe4a171', 'Guido', 'Ferrari', 'guido.ferrari@email.it', 'via del mare, 45', 'Venezia', 'VE', '41232', '34234235235'),
(3, 'carlo', '1619d7adc23f4f633f11014d2f22b7d8', 'Carlo', 'Bianchi', 'whitecarlo@icloud.com', 'Via Piave, 9', 'Venezia', 'VE', '35078', '9875435678'),
(4, 'fabio1989', '1619d7adc23f4f633f11014d2f22b7d8', 'Fabio', 'Marra', 'fabio.marra@msn.com', 'Via Medina, 14', 'Napoli', 'NA', '80122', '0818437291'),
(5, 'ryan', '1619d7adc23f4f633f11014d2f22b7d8', 'Tony', 'Ryan', 'tiziodeivoli@libero.it', 'Corso Vittorio Emanuele, 32', 'Roma', 'RM', '00118', '3382453554'),
(6, 'jeff', '1619d7adc23f4f633f11014d2f22b7d8', 'Jeffrey', 'Bezos', 'jb@teletu.it', 'Via Monte Napoleone, 1', 'Milano', 'MI', '20121', '3924593827');

--
-- Dump dei dati per la tabella `DESCRIZIONIPRODOTTI`
--

INSERT INTO `DESCRIZIONIPRODOTTI` (`idDescrizione`, `descrizione`) VALUES
(1, 'Servire fredda. Eccellente per iniziare la giornata. \\n Ingredienti: \\n- farina 00,\\n- uova,\\n- zucchero,\\n- lievito,\\n- burro,\\n- limone,\\n- vaniglia,\\n- latte,\\n- cannella.'),
(2, 'Ottima per colazione. Lasciare raffreddare 5 minuti dopo averla sfornata. \\n Ingredienti: \\n- Farina 00, \\n- uova, \\n- zucchero, \\n- lievito, \\n- ricotta.'),
(3, 'Un modo classico per iniziare la giornata è concedersi una buona brioche calda. \\n- Ingredienti: \\n- farina manitoba, \\n- farina 00, \\n- uova,\\n- burro,\\n- latte,\\n- lievito di birra,\\n- miele,\\n- cannella,\\n- vaniglia,\\n- buccia di limone.'),
(4, 'Una soffice fetta di torta paradiso, ideale in tutte le situazioni. \\n- Ingredienti: \\n- uova,\\n- zucchero,\\n- lievito,\\n- farina,\\n- fecola.'),
(5, 'Per una serata particolare.\\n- Ingredienti:\\n- pasta sfoglia,\\n- speck,\\n- mele,\\n- radicchio di Treviso,\\n- uova,\\n- Grana Padano,\\n- timo,\\n- rosmarino,\\n- pepe.'),
(6, 'Ideale per un pranzo delicato. \\n-Ingredienti: \\n-pasta sfoglia,\\n- erbette,\\n- prosciutto,\\n- ricotta,\\n- uova,\\n- grana.'),
(7, 'Adatta per una sana merenda dei bambini. \\n- Ingredienti: \\n-farina 00,\\n- uova,\\n- burro,\\n- buccia di limone,\\n- lievito,\\n- latte,\\n- cannella,\\n- vaniglia,\\n- more,\\n- lamponi,\\n- ribes.'),
(8, 'Per una sfiziosa cena di pesce. \\n- Ingredienti: \\n- tranci di pesce spada fresco,\\n- pistacchi,\\n- pan grattato,\\n- semi di sesamo,\\n- sale,\\n- olio,\\n- limone,\\n- pepe.'),
(9, 'Un primo piatto di pesce per stupire i vostri ospiti. \\n- Ingredienti: \\n- cous cous,\\n- piovra fresca,\\n- zafferano,\\n- sale,\\n- sedano,\\n- erba cipollina,\\n- pepe,\\n- fave.'),
(10, 'Un delizioso primo piatto per iniziare una cena di pesce coi fiocchi. \\n- Ingredienti: \\n- calamari,\\n- gamberi,\\n- seppie,\\n- coda di rospo,\\n- scampi,\\n- cozze,\\n- sedano,\\n- cipolla,\\n- carote,\\n- pelati,\\n- sale,\\n- peperoncino,\\n- crostini di pane tostato.');

--
-- Dump dei dati per la tabella `ERRORI`
--

INSERT INTO `ERRORI` (`idErrore`, `descErrore`) VALUES
(11, 'Errore nell''inserimento del nuovo utente'),
(10, 'I campi di verifica password non coincidono'),
(3, 'impossibile cancellare il cliente perche'' ha almeno un ordine associato'),
(12, 'impossibile cancellare l''admin perche'' ha in gestione almeno un ordine'),
(4, 'impossibile registrare l''ordine: la quantita'' richiesta eccede quella disponibile'),
(6, 'impossibile registrare un nuovo pagamento per un ordine gia'' saldato'),
(13, 'impossibile registrare un prezzo prodotto negativo o nullo'),
(5, 'impossibile registrare una disponibilita'' prodotto negativa'),
(7, 'impossibile spedire un ordine non ancora saldato'),
(9, 'mail gia'' utilizzata'),
(2, 'user o mail admin gia usata da un cliente'),
(1, 'user o mail cliente gia usata da un admin'),
(8, 'username gia'' utilizzata');

--
-- Dump dei dati per la tabella `MEDIA`
--

INSERT INTO `MEDIA` (`idMedia`, `tipoMedia`, `linkMedia`, `prodottoMedia`) VALUES
(1, 1, 'media/fotoProdotti/1.jpg', 1),
(2, 1, 'media/fotoProdotti/2.jpg', 2),
(3, 1, 'media/fotoProdotti/3.jpg', 3),
(4, 1, 'media/fotoProdotti/4.jpg', 4),
(5, 1, 'media/fotoProdotti/5.jpg', 5),
(6, 1, 'media/fotoProdotti/6.jpg', 6),
(7, 1, 'media/fotoProdotti/7.jpg', 7),
(8, 1, 'media/fotoProdotti/8.jpg', 8),
(9, 1, 'media/fotoProdotti/9.jpg', 9),
(10, 1, 'media/fotoProdotti/10.jpg', 10);

--
-- Dump dei dati per la tabella `ORDINI`
--

INSERT INTO `ORDINI` (`idOrdine`, `idInternoOrdine`, `annoOrdine`, `dataOrdine`, `importoOrdine`, `clienteOrdine`, `statoOrdine`, `isPagatoOrdine`, `isChiusoOrdine`, `pagamentoAssOrdine`, `spedizioneAssOrdine`, `adminAssOrdine`) VALUES
(2, 1, 2014, '2014-06-21 01:43:14', 125, 4, 'Pagato', 1, 0, 4, NULL, NULL),
(3, 2, 2014, '2014-06-21 01:48:11', 60, 4, 'Registrato', 0, 0, NULL, NULL, NULL),
(4, 3, 2014, '2014-06-21 01:48:57', 425, 5, 'In lavorazione', 1, 0, 2, NULL, 1),
(5, 4, 2014, '2014-06-21 01:56:50', 80, 1, 'Registrato', 0, 0, NULL, NULL, NULL),
(6, 5, 2014, '2014-06-21 01:58:35', 45, 2, 'Pagato', 1, 0, 5, NULL, NULL),
(7, 6, 2014, '2014-06-21 01:59:22', 40, 4, 'Registrato', 0, 0, NULL, NULL, NULL),
(8, 7, 2014, '2014-06-21 01:59:45', 10, 4, 'Registrato', 0, 0, NULL, NULL, NULL),
(9, 8, 2014, '2014-06-21 02:00:31', 85, 4, 'Pagato', 1, 0, 3, NULL, NULL),
(10, 9, 2014, '2014-06-21 02:01:24', 1510, 5, 'Chiuso', 1, 1, 1, 1, 2),
(11, 10, 2014, '2014-06-21 02:13:58', 1210, 6, 'Chiuso', 1, 1, 6, 2, 1);

--
-- Dump dei dati per la tabella `ORDINIPRODOTTI`
--

INSERT INTO `ORDINIPRODOTTI` (`ordineOP`, `prodottoOP`, `quantitaOP`) VALUES
(2, 1, 4),
(2, 2, 1),
(2, 3, 3),
(2, 4, 1),
(3, 2, 6),
(4, 3, 15),
(4, 7, 10),
(5, 8, 1),
(5, 9, 1),
(5, 10, 1),
(6, 2, 1),
(6, 5, 1),
(6, 8, 1),
(7, 7, 2),
(8, 6, 1),
(9, 5, 1),
(9, 9, 3),
(10, 3, 82),
(10, 4, 28),
(11, 2, 29),
(11, 6, 20),
(11, 7, 36);

--
-- Dump dei dati per la tabella `PAGAMENTI`
--

INSERT INTO `PAGAMENTI` (`idPagamento`, `tipoPagamento`, `dataPagamento`) VALUES
(1, 'PayPal', '2014-06-21 02:04:36'),
(2, 'PayPal', '2014-06-21 02:05:27'),
(3, 'Carta di Credito', '2014-06-21 02:06:02'),
(4, 'Bonifico Bancario', '2014-06-21 02:06:13'),
(5, 'Carta di Credito', '2014-06-21 02:06:23'),
(6, 'Carta di Credito', '2014-06-21 02:14:17');

--
-- Dump dei dati per la tabella `PRODOTTI`
--

INSERT INTO `PRODOTTI` (`idProdotto`, `nomeProdotto`, `prezzoProdotto`, `pesoProdotto`, `porzioniProdotto`, `categoriaProdotto`, `disponibilitaProdotto`, `descrizioneAssProdotto`) VALUES
(1, 'Crostata di crema cotta', 15.00, 500.00, 12, 6, 26, 1),
(2, 'Treccia di ricotta', 10.00, 600.00, 15, 4, 13, 2),
(3, 'Brioche alla crema', 15.00, 500.00, 15, 1, 0, 3),
(4, 'Torta paradiso', 10.00, 400.00, 12, 4, 1, 4),
(5, 'Sfogliata di mele e radicchio', 10.00, 400.00, 10, 4, 28, 5),
(6, 'Sfogliata alle erbette', 10.00, 400.00, 10, 8, 9, 6),
(7, 'Crostata ai frutti di bosco', 20.00, 500.00, 12, 5, 2, 7),
(8, 'Trancio di pesce spada in crosta', 25.00, 600.00, 5, 8, 8, 8),
(9, 'Cous cous con piovra', 25.00, 1000.00, 8, 8, 6, 9),
(10, 'Zuppa di pesce', 30.00, 1000.00, 8, 8, 9, 10);

--
-- Dump dei dati per la tabella `SPEDIZIONI`
--

INSERT INTO `SPEDIZIONI` (`idSpedizione`, `vettoreSpedizione`, `trackingSpedizione`, `dataSpedizione`) VALUES
(1, 'UPS', 'IT932ILM4234OU', '2014-06-21 02:05:07'),
(2, 'BRT', 'j342lo124u', '2014-06-21 02:15:42');
