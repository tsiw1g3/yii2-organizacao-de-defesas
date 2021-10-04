-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14-Set-2021 às 00:31
-- Versão do servidor: 10.4.19-MariaDB
-- versão do PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `organizacao_defesa`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `banca`
--

CREATE TABLE `banca` (
  `id` int(11) NOT NULL,
  `titulo_trabalho` varchar(255) NOT NULL,
  `resumo` text NOT NULL,
  `abstract` text NOT NULL,
  `palavras_chave` text NOT NULL,
  `data_realizacao` datetime NOT NULL,
  `nota_final` double DEFAULT NULL,
  `local` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `banca_documento`
--

CREATE TABLE `banca_documento` (
  `id` int(11) NOT NULL,
  `id_banca` int(11) NOT NULL,
  `id_documento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `descricao` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `data_submissao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_has` varchar(255) NOT NULL,
  `auth_key` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `school` varchar(64) NOT NULL,
  `academic_title` varchar(64) NOT NULL,
  `lattesUrl` varchar(64) NOT NULL,
  `status` varchar(12) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_banca`
--

CREATE TABLE `usuario_banca` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_banca` int(11) NOT NULL,
  `role` varchar(64) NOT NULL,
  `nota` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Estrutura da tabela `session`
--
CREATE TABLE `session`
(
    `id` varchar(40) NOT NULL,
    `expire` INTEGER,
    `data` BLOB,
    `token_access`varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `banca`
--
ALTER TABLE `banca`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `banca_documento`
--
ALTER TABLE `banca_documento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuario_banca`
--
ALTER TABLE `usuario_banca`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuario_banca`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);
--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `banca`
--
ALTER TABLE `banca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `banca_documento`
--
ALTER TABLE `banca_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario_banca`
--
ALTER TABLE `usuario_banca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `organizacao_defesa`.`usuario` ADD UNIQUE `auth_key` (`auth_key`) USING BTREE;
ALTER TABLE `organizacao_defesa`.`usuario` ADD UNIQUE `username` (`username`) USING BTREE;
ALTER TABLE `organizacao_defesa`.`usuario` ADD UNIQUE `email` (`email`) USING BTREE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
