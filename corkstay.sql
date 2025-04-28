-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/04/2025 às 00:13
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `corkstay`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` enum('1 bed','2 bed','3 bed','4 bed') NOT NULL,
  `tenancy_length` enum('3 months','6 months','1 year') NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `properties`
--

INSERT INTO `properties` (`id`, `landlord_id`, `title`, `description`, `image`, `category`, `tenancy_length`, `rental_price`, `created_at`) VALUES
(3, 1, 'Modern 2 Bedroom Apartment in Cork City', 'Spacious and fully furnished apartment close ark cinema.', 'apartment1.jpg', '2 bed', '6 months', 1100.00, '2025-04-22 20:58:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `testimonial_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `testimonials`
--

INSERT INTO `testimonials` (`id`, `tenant_id`, `service_name`, `comment`, `testimonial_date`, `created_at`) VALUES
(1, 2, 'Customer Support', 'The agency was super helpful and friendly during my move.', '2025-04-22', '2025-04-22 20:59:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('landlord','tenant') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `email`, `password`, `role`, `created_at`, `reset_token`, `reset_expiry`) VALUES
(1, 'Sweetthoot', 'john@example.com', '$2y$10$123456789012345678901uMqkZhJlfYXUjpjZ2GzKfI1edSsq4HNu', 'landlord', '2025-04-22 21:58:18', NULL, NULL),
(2, 'bolsonarotaok', 'mariataok@example.com', '$2y$10$abcdefghijABCDEFGHIJabcdefghijABCDEFGHIJabcdefghijABC', 'tenant', '2025-04-22 21:59:29', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `landlord_id` (`landlord_id`);

--
-- Índices de tabela `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`email`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`user_id`);

--
-- Restrições para tabelas `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
