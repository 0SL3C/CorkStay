-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/05/2025 às 15:49
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `eircode` varchar(20) DEFAULT NULL,
  `property_type` varchar(50) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `available_from` date DEFAULT NULL,
  `furnished` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `properties`
--

INSERT INTO `properties` (`id`, `landlord_id`, `title`, `description`, `image`, `category`, `tenancy_length`, `rental_price`, `created_at`, `address`, `city`, `eircode`, `property_type`, `bedrooms`, `bathrooms`, `price`, `available_from`, `furnished`) VALUES
(20, 3, 'Apartment rebuilded low cost', 'new building in cork city', NULL, '1 bed', '1 year', 0.00, '2025-05-07 12:51:51', '4 devonshire street west', 'Cork', 'T12F3GJ', 'Apartment', 2, 2, 500.00, '2025-05-07', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `testimonial_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `testimonials`
--

INSERT INTO `testimonials` (`id`, `tenant_id`, `service_name`, `comment`, `rating`, `testimonial_date`, `created_at`, `content`) VALUES
(1, 2, 'Customer Support', 'The agency was super helpful and friendly during my move.', NULL, '2025-04-22', '2025-04-22 20:59:52', NULL),
(2, 3, 'Cleaning Service', 'teste', 5, '2025-05-05', '2025-05-04 22:58:34', NULL);

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
(2, 'bolsonarotaok', 'mariataok@example.com', '$2y$10$abcdefghijABCDEFGHIJabcdefghijABCDEFGHIJabcdefghijABC', 'tenant', '2025-04-22 21:59:29', NULL, NULL),
(3, 'Michel', 'michel_wolf@hotmail.com', '$2y$10$A5s1qesReGWqB9VW4OjZ5OF5Np2hK1o3cqV0YNgCC5VAjryDt7t2C', 'landlord', '2025-05-04 22:12:52', NULL, NULL),
(4, 'Michel', 'michel.lordalbinus@gmail.com', '$2y$10$Qrmew3ilXNi.N2fg9gZDi..tguyM8w7VhLLxN33Fwm5L1PFZGhzmO', 'tenant', '2025-05-06 21:55:19', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
