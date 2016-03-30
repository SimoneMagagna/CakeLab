DROP VIEW IF EXISTS UTENTI;

CREATE VIEW UTENTI AS
SELECT idAdmin AS idUtente, userAdmin AS userUtente, nomeAdmin AS nomeUtente, cognomeAdmin AS cognomeUtente, mailAdmin AS mailUtente, '1' AS gruppoUtente
FROM ADMIN
UNION
SELECT idCliente AS idUtente, userCliente AS userUtente, nomeCliente AS nomeUtente, cognomeCliente AS cognomeUtent, mailCliente AS mailUtente, '0' AS gruppoUtente
FROM CLIENTI;
-- view per semplificare le operazioni sugli utenti dell'applicazione nel pannello di amministrazione utenti per gli admin