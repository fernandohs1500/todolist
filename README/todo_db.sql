-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 09/04/2018 às 01:55
-- Versão do servidor: 10.1.26-MariaDB-0+deb9u1
-- Versão do PHP: 7.0.27-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `todo_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `profile`
--

INSERT INTO `profile` (`id`, `name`) VALUES
(1, 'Guest'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `todo_list`
--

CREATE TABLE `todo_list` (
  `uuid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `content` text,
  `sort_order` int(11) DEFAULT NULL,
  `done` tinyint(1) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `todo_list`
--

INSERT INTO `todo_list` (`uuid`, `type`, `content`, `sort_order`, `done`, `date_created`, `id_user`) VALUES
(8, 2, 'Content updated', 2, 0, '2018-04-09 04:31:08', 3),
(9, 2, 'Content task', 3, 0, '2018-04-09 04:39:43', 3),
(10, 2, 'Content task', 4, 0, '2018-04-09 04:39:48', 3),
(11, 2, 'Content task', 1, 0, '2018-04-09 04:39:51', 3),
(12, 2, 'Content task', 5, 0, '2018-04-09 04:39:56', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `type`
--

CREATE TABLE `type` (
  `uuid` int(11) NOT NULL,
  `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `type`
--

INSERT INTO `type` (`uuid`, `type`) VALUES
(1, 'shopping'),
(2, 'work');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `dat_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(256) DEFAULT NULL,
  `dat_token_expiration` timestamp NULL DEFAULT NULL,
  `cod_profile` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `user`
--

INSERT INTO `user` (`id`, `login`, `email`, `active`, `dat_register`, `token`, `dat_token_expiration`, `cod_profile`) VALUES
(1, 'admin', 'fernandimgts@gmail.com', 1, '2018-04-07 19:32:59', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', '2018-04-08 19:32:59', 3),
(3, 'guest', 'guest@guest.com', 1, '2018-04-08 03:00:00', NULL, NULL, 1);

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Índices de tabela `todo_list`
--
ALTER TABLE `todo_list`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `restrictfb2` (`type`),
  ADD KEY `restrictfb3` (`id_user`);

--
-- Índices de tabela `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`uuid`);

--
-- Índices de tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD KEY `fk_usuarios_perfis1_idx` (`cod_profile`),
  ADD KEY `token` (`token`(255));

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de tabela `todo_list`
--
ALTER TABLE `todo_list`
  MODIFY `uuid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `type`
--
ALTER TABLE `type`
  MODIFY `uuid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `todo_list`
--
ALTER TABLE `todo_list`
  ADD CONSTRAINT `restrictfb2` FOREIGN KEY (`type`) REFERENCES `type` (`uuid`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `restrictfb3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON UPDATE NO ACTION;

--
-- Restrições para tabelas `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_usuarios_perfis1` FOREIGN KEY (`cod_profile`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
