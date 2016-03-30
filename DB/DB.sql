-- CakeLab DB © Manuel Sgaravato & Simone Magagna   v1.2 del 16/06/14

CREATE DATABASE IF NOT EXISTS `CakeLab`;

-- -----------------------------------------------------
-- Table `CakeLab`.`ADMIN`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`ADMIN` (   
  `idAdmin` INT NOT NULL AUTO_INCREMENT,
  `userAdmin` VARCHAR(255) NOT NULL ,
  `passwordAdmin` CHAR(32) NOT NULL ,
  `nomeAdmin` VARCHAR(255) NOT NULL ,
  `cognomeAdmin` VARCHAR(255) NOT NULL ,
  `mailAdmin` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`idAdmin`),
  unique (`userAdmin`), 
  unique (`mailAdmin`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `CakeLab`.`CLIENTI`        
-- IPOTESI: i documenti fiscali prodotti sono scontrini. Non è richiesto CF Cliente
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`CLIENTI` ( 
  `idCliente` INT NOT NULL AUTO_INCREMENT, 
  `userCliente` VARCHAR(255) NOT NULL ,
  `passwordCliente` CHAR(32) NOT NULL ,
  `nomeCliente` VARCHAR(255) NOT NULL ,
  `cognomeCliente` VARCHAR(255) NOT NULL ,
  `mailCliente` VARCHAR(255) NOT NULL ,
  `indirizzoCliente` VARCHAR(255) NOT NULL ,   
  `comuneCliente` VARCHAR(255) NOT NULL ,
  `provCliente` CHAR(2) NOT NULL ,  
  `capCliente` CHAR(5) NOT NULL , 
  `cellCliente` VARCHAR(255) NOT NULL , 
  PRIMARY KEY (`idCliente`) ,
  unique (`userCliente`), 
  unique (`mailCliente`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `CakeLab`.`CATEGORIE`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`CATEGORIE` ( 
  `idCategoria` INT NOT NULL AUTO_INCREMENT,
  `nomeCategoria` VARCHAR(255) NOT NULL , 
  unique (`nomeCategoria`),  
  PRIMARY KEY (`idCategoria`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CakeLab`.`DESCRIZIONIPRODOTTI`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`DESCRIZIONIPRODOTTI` ( 
  `idDescrizione` INT NOT NULL AUTO_INCREMENT,
  `descrizione` VARCHAR(500) NOT NULL ,  
  PRIMARY KEY (`idDescrizione`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CakeLab`.`PRODOTTI`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`PRODOTTI` ( 
  `idProdotto` INT NOT NULL AUTO_INCREMENT,
  `nomeProdotto` VARCHAR(255) NOT NULL ,  
  `prezzoProdotto` DECIMAL(7,2) NOT NULL ,  
  `pesoProdotto` DECIMAL(7,2) NULL ,
  `porzioniProdotto` INT NOT NULL ,  
  `categoriaProdotto` INT NOT NULL ,
  `disponibilitaProdotto` INT NOT NULL ,
  `descrizioneAssProdotto` INT NOT NULL ,
  PRIMARY KEY (`idProdotto`) ,
  unique (`nomeProdotto`),   
  INDEX `fk_PRODOTTI_categoriaProdotto` (`categoriaProdotto` ASC) ,
  CONSTRAINT `fk_PRODOTTI_categoriaProdotto`
   FOREIGN KEY (`categoriaProdotto` )
   REFERENCES `CakeLab`.`CATEGORIE`(`idCategoria`)
   ON DELETE RESTRICT
   ON UPDATE CASCADE,
  INDEX `fk_PRODOTTI_descrizioneAssProdotto` (`descrizioneAssProdotto` ASC) ,
  CONSTRAINT `fk_PRODOTTI_descrizioneAssProdotto`
   FOREIGN KEY (`descrizioneAssProdotto`)
   REFERENCES `CakeLab`.`DESCRIZIONIPRODOTTI`(`idDescrizione`)
   ON DELETE RESTRICT
   ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CakeLab`.`MEDIA`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`MEDIA` ( 
  `idMedia` INT NOT NULL AUTO_INCREMENT,
  `tipoMedia` INT NOT NULL ,
  `linkMedia` VARCHAR(255) NOT NULL ,
  `prodottoMedia` INT NOT NULL ,  
  PRIMARY KEY (`idMedia`) ,
  INDEX `fk_MEDIA_prodottoMedia` (`prodottoMedia` ASC) ,
  CONSTRAINT `fk_MEDIA_prodottoMedia`
   FOREIGN KEY (`prodottoMedia` )
   REFERENCES `CakeLab`.`PRODOTTI`(`idProdotto`)
   ON DELETE CASCADE
   ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `CakeLab`.`PAGAMENTI`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`PAGAMENTI` ( 
  `idPagamento` INT NOT NULL AUTO_INCREMENT,
  `tipoPagamento` ENUM('Bonifico Bancario', 'Carta di Credito', 'PayPal', 'Postagiro') NOT NULL , 
  `dataPagamento` TIMESTAMP NOT NULL DEFAULT NOW(),  
  PRIMARY KEY (`idPagamento`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CakeLab`.`SPEDIZIONI`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`SPEDIZIONI` ( 
  `idSpedizione` INT NOT NULL AUTO_INCREMENT,
  `vettoreSpedizione` VARCHAR(255) NOT NULL , 
  `trackingSpedizione` VARCHAR(255) NOT NULL,
  `dataSpedizione` TIMESTAMP NOT NULL DEFAULT NOW(),  
  PRIMARY KEY (`idSpedizione`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `CakeLab`.`ORDINI`        

-- NOTA1: un ordine da saldare presenta il bool isPagatoOrdine a 0 e PagamentoAssOrdine a NULL. 
-- Altrimenti isPagatoOrdine vale 1 e PagamentoAssOrdine punta al pagamento sulla tabella PAGAMENTI.
-- NOTA2: un ordine da spedire presenta il bool isChiusoOrdine a 0 e spedizioneAssOrdine a NULL. 
-- Altrimenti isChiusoOrdine vale 1 e spedizioneAssOrdine punta alla spedizione sulla tabella SPEDIZIONI.
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`ORDINI` ( 
  `idOrdine` INT NOT NULL AUTO_INCREMENT,
  `idInternoOrdine` INT NOT NULL , -- nelle interfaccie web un ordine appare sempre identificato come num-anno. La chiave ufficiale però è idOrdine per abbassare la complessita' 
  `annoOrdine` INT NOT NULL ,
  `dataOrdine` TIMESTAMP NOT NULL DEFAULT NOW() ,
  `importoOrdine` INT NOT NULL DEFAULT 0,
  `clienteOrdine` INT NOT NULL ,
  `statoOrdine` ENUM('Registrato', 'Pagato', 'In lavorazione', 'Chiuso') NOT NULL DEFAULT 'Registrato',
  `isPagatoOrdine` BOOLEAN NOT NULL DEFAULT FALSE, 
  `isChiusoOrdine` BOOLEAN NOT NULL DEFAULT FALSE,  
  `pagamentoAssOrdine` INT DEFAULT NULL ,
  `spedizioneAssOrdine` INT DEFAULT NULL ,
  `adminAssOrdine` INT DEFAULT NULL ,
  PRIMARY KEY (`idOrdine`) ,
  unique (`idInternoOrdine`,`annoOrdine`), 
  INDEX `fk_ORDINI_clienteOrdine` (`clienteOrdine` ASC) ,
  CONSTRAINT `fk_ORDINI_clienteOrdine`
   FOREIGN KEY (`clienteOrdine`)
   REFERENCES `CakeLab`.`CLIENTI`(`idCliente`)
   ON DELETE RESTRICT
   ON UPDATE CASCADE,
  INDEX `fk_ORDINI_spedizioneAssOrdine` (`spedizioneAssOrdine` ASC) ,
  CONSTRAINT `fk_ORDINI_spedizioneAssOrdine`
   FOREIGN KEY (`spedizioneAssOrdine`)
   REFERENCES `CakeLab`.`SPEDIZIONI`(`idSpedizione`)
   ON DELETE SET NULL
   ON UPDATE CASCADE,
  INDEX `fk_ORDINI_adminAssOrdine` (`adminAssOrdine` ASC) ,
  CONSTRAINT `fk_ORDINI_adminAssOrdine`
   FOREIGN KEY (`adminAssOrdine`)
   REFERENCES `CakeLab`.`ADMIN`(`idAdmin`)
   ON DELETE RESTRICT
   ON UPDATE CASCADE,
  INDEX `fk_ORDINI_pagamentoAssOrdineI` (`pagamentoAssOrdine` ASC) ,
  CONSTRAINT `fk_ORDINI_pagamentoAssOrdineI`
   FOREIGN KEY (`pagamentoAssOrdine`)
   REFERENCES `CakeLab`.`PAGAMENTI`(`idPagamento`)
   ON DELETE SET NULL
   ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `CakeLab`.`ORDINIPRODOTTI`        
-- NOTA: un ordine da saldare presenta il bit PagatoOrdine a 0 e PagamentoAssOrdine a NULL. 
-- Altrimenti PagatoOrdine vale 1 e PagamentoAssOrdine punta al pagamento sulla tabella PAGAMENTI.
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`ORDINIPRODOTTI` ( 
  `ordineOP` INT NOT NULL ,
  `prodottoOP` INT NOT NULL ,
  `quantitaOP` INT NOT NULL ,
  PRIMARY KEY (`ordineOP`,`prodottoOP`) ,
  CONSTRAINT `fk_ORDINIPRODOTTI_ordineOP`
   FOREIGN KEY (`ordineOP`)
   REFERENCES `CakeLab`.`ORDINI`(`idOrdine`)
   ON DELETE CASCADE
   ON UPDATE CASCADE,
  CONSTRAINT `fk_ORDINIPRODOTTI_prodottoOP`
   FOREIGN KEY (`prodottoOP`)
   REFERENCES `CakeLab`.`PRODOTTI`(`idProdotto`)
   ON DELETE RESTRICT
   ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `CakeLab`.`ERRORI`        
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `CakeLab`.`ERRORI` ( 
  `idErrore` INT NOT NULL AUTO_INCREMENT,
  `descErrore` VARCHAR(255) NOT NULL , 
  unique (`descErrore`),  
  PRIMARY KEY (`idErrore`) )
ENGINE = InnoDB;
