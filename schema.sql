/* Create Database */
CREATE DATABASE IF NOT EXISTS `polo-seven`
  DEFAULT CHARACTER SET latin1
  COLLATE latin1_swedish_ci;

/* Set database index */
USE `polo-seven`;

/* Create tables */
CREATE TABLE IF NOT EXISTS `logins` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(128) NOT NULL,
  `senha`      VARCHAR(128) NOT NULL,
  `id_usuario` INT          NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`    INT      NOT NULL AUTO_INCREMENT,
  `nivel` SMALLINT NOT NULL DEFAULT 3,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `produtos` (
  `id`             INT         NOT NULL AUTO_INCREMENT,
  `nome`           VARCHAR(64) NOT NULL,
  `preco_unitario` FLOAT       NOT NULL,
  `total_unidades` INT         NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `vendas` (
  `id`             INT      NOT NULL AUTO_INCREMENT,
  `id_usuario`     INT      NOT NULL,
  `id_pagamento`   INT      NOT NULL,
  `id_produtos`    TEXT     NOT NULL,
  `preco_produtos` TEXT     NOT NULL,
  `valor`          FLOAT    NOT NULL,
  `data_registro`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `pagamentos` (
  `id`   INT        NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(8) NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

/* Add table constraints */
ALTER TABLE `logins`
  ADD CONSTRAINT `logins_id_usuarios_id` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_id_usuario_usuarios_id` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_id_pagamento_pagamentos_id` FOREIGN KEY (`id_pagamento`) REFERENCES `pagamentos` (`id`) ON DELETE CASCADE;

/* Add root user */
INSERT INTO `usuarios` (`nivel`) VALUES ('1');

INSERT INTO `logins` (`nome`, `senha`, `id_usuario`) VALUES ('root', 'toor', '1');