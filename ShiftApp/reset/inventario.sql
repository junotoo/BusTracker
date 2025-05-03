-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 09-Mar-2025 às 15:50
-- Versão do servidor: 11.5.2-MariaDB
-- versão do PHP: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dados: `inventario`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `desc` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `locations`
--

INSERT INTO `locations` (`id`, `desc`) VALUES
(100, 'Major'),
(101, 'Major'),
(102, 'Major'),
(103, 'Middle'),
(104, 'Minor'),
(111, 'Electronics'),
(117, 'Surgical'),
(200, 'Picking'),
(201, 'Stock Out');

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_sessions`
--

CREATE TABLE `login_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `login_sessions`
--

INSERT INTO `login_sessions` (`id`, `userId`, `token`, `expiry`, `ip_address`) VALUES
(1, 1, 'eed80756ec206834f2bb027af133124e', '2025-03-23 22:07:32', '127.0.0.1'),
(2, 1, 'fa702b4efbd406e644a54b999f3cff44', '2025-03-24 02:41:48', '127.0.0.1'),
(3, 1, 'ffc5d0907ba23280232bd611343f52e7', '2025-03-24 02:43:17', '127.0.0.1'),
(4, 1, '6ac2505317b8cd430d9ab2ca85c45c88', '2025-03-24 02:44:22', '127.0.0.1'),
(5, 1, '66e2d3207179d4da34bfc4a3db12cdda', '2025-03-24 02:44:27', '127.0.0.1'),
(6, 1, '0d28fa41310609898c6df650ae05da30', '2025-03-24 02:45:28', '127.0.0.1'),
(7, 1, 'f7f98e4b418d6522a754df3050cd19ec', '2025-03-24 02:46:51', '127.0.0.1'),
(8, 1, '09500a0f4c8217a3f66ce0708e69a54c', '2025-03-24 02:49:45', '127.0.0.1'),
(9, 1, '75b063d8d0abf915938ed27f0bcc4157', '2025-03-24 02:50:40', '127.0.0.1'),
(10, 1, 'af9252f74e3e5bec9e9844e0b1801187', '2025-03-24 02:52:00', '127.0.0.1'),
(11, 1, 'e778295785eaa00a20ed40097924880f', '2025-03-24 02:55:14', '127.0.0.1'),
(12, 1, '78d2eead7a50f2d7fbab9e55356fbd58', '2025-03-24 02:55:36', '127.0.0.1'),
(13, 1, '591884b1eee546a85da9f2f88de6631b', '2025-03-24 03:00:41', '127.0.0.1'),
(14, 1, '3296985a9e998875f934e70fa9b3f8f5', '2025-03-24 03:02:27', '127.0.0.1'),
(15, 1, '579d67e6b11e4f02d8d406129bfd2c1f', '2025-03-24 03:04:33', '127.0.0.1'),
(16, 1, '956499986af54a50754bbe9a12cb6808', '2025-03-10 04:13:43', '127.0.0.1'),
(17, 2, '439b99595f4c36f91e8ed7d9006bb988', '2025-03-24 06:22:29', '127.0.0.1'),
(18, 1, '4cc83c500f074016a4ac0a937e5e01e6', '2025-03-24 07:53:39', '127.0.0.1'),
(19, 1, '30ce36961a62d9dd9e0c8233968373fd', '2025-03-24 08:01:52', '127.0.0.1'),
(20, 2, '99381463cd3a74fee44f3d3cc783d973', '2025-03-24 08:03:06', '127.0.0.1'),
(21, 2, 'b9b4020e2d3860f17ca2e1971ae890a3', '2025-03-24 08:53:36', '127.0.0.1'),
(22, 1, 'dcd6e01ccd09fd75779ee08e40120289', '2025-03-24 08:53:47', '127.0.0.1'),
(23, 2, 'cbfaa89d797c11cbadda16d9fd36135c', '2025-03-24 09:14:43', '127.0.0.1'),
(24, 1, '261889ebfcaed2ab43f5fc541b9886e5', '2025-03-24 09:15:33', '127.0.0.1'),
(25, 3, 'e94f2c6465f5c2a33a7c6c18225d9eda', '2025-03-24 09:20:41', '127.0.0.1'),
(26, 1, '4bd3a8b1a90729545bb0c6689eb5d682', '2025-03-24 09:23:26', '127.0.0.1'),
(27, 2, '9e4db4281c9ed76bbf380c6d9f8ae049', '2025-03-24 09:27:42', '127.0.0.1'),
(28, 2, '2dbe2df0204d802164380f292ba76f34', '2025-03-24 09:28:55', '127.0.0.1'),
(29, 2, '884102037e2ef63f0b4790c121e7b497', '2025-03-24 09:29:39', '127.0.0.1'),
(30, 1, '3d89a70ab2241fedef91b49fdda41f65', '2025-03-24 09:30:04', '127.0.0.1'),
(31, 1, '8ce45b042cc9cb6e59c76f33e861defb', '2025-03-24 09:30:27', '127.0.0.1'),
(32, 1, '3f95859265b5cd5f30de64d200d8a7eb', '2025-03-24 10:25:30', '127.0.0.1'),
(33, 2, '8134ea49cf1b4705b2488655e8c6103c', '2025-03-24 10:25:40', '127.0.0.1'),
(34, 1, '373103874cdae8bc7e86d1be9e0e5109', '2025-03-24 11:18:13', '127.0.0.1'),
(35, 1, '7bf0923086fa7903dae9af8899cee3e8', '2025-03-24 11:18:30', '127.0.0.1'),
(36, 1, 'd497062ffb4e3d9f0521ab152ab5c20c', '2025-03-24 11:20:52', '127.0.0.1'),
(37, 2, 'ce3a00a1c8ecc45f4c39458343603e6d', '2025-03-24 11:23:06', '127.0.0.1'),
(38, 1, 'e3fad4bb39b50ee8b016ad7df67ab50a', '2025-03-24 11:46:19', '127.0.0.1'),
(39, 2, 'e7e48750b38a511f10072fd68745ab18', '2025-03-24 11:46:36', '127.0.0.1'),
(40, 1, '40d7d8cc1fd10999f72ed47dec9da1f9', '2025-03-24 11:47:15', '127.0.0.1'),
(41, 2, 'f603851ff3c9d8ebd8623bb17c6ad387', '2025-03-24 11:57:37', '127.0.0.1'),
(42, 1, '326fca2836811b71e51d570c6a3fa51e', '2025-03-24 11:58:01', '127.0.0.1'),
(43, 2, '12eb0a40d42081d509fcd98d80ca36de', '2025-03-24 11:58:52', '127.0.0.1'),
(44, 1, '7a52450c601c5ede494f32d7e5a5d956', '2025-03-24 11:59:28', '127.0.0.1'),
(45, 2, 'e213eba320ef868cda551fb4e4ece5a6', '2025-03-24 12:06:08', '127.0.0.1'),
(46, 1, '7225932df3045f1821d98a5c2eb2689a', '2025-03-24 12:07:43', '127.0.0.1'),
(47, 2, 'a995bed1f0dc7646fcfb27cc98a8650f', '2025-03-24 12:08:29', '127.0.0.1'),
(48, 1, '0ad42c0d4aa926086caff22461fb54ad', '2025-03-24 12:09:01', '127.0.0.1'),
(49, 1, '3a074abded81e369ef3789f3a3316c9c', '2025-03-24 12:10:14', '127.0.0.1'),
(50, 3, '9a298727e39e73b2adf40f189852b8c6', '2025-03-24 12:10:49', '127.0.0.1'),
(51, 1, '5f3fd936b82c274d4732521902bcbf47', '2025-03-24 12:22:09', '127.0.0.1'),
(52, 2, '932c31d603becba660a3e6924e96286c', '2025-03-24 12:24:25', '127.0.0.1'),
(53, 2, 'ae0dde496735ae5bd7069680ce50923f', '2025-03-24 12:30:00', '127.0.0.1'),
(54, 1, '7b749af3ff72e6672a345e4176a055ba', '2025-03-24 12:56:34', '127.0.0.1'),
(55, 1, '051f1b04d59a782151f46ad5c61b8b6c', '2025-03-24 14:30:22', '10.16.1.232'),
(56, 1, '354c304fdcf8d99afc138b94224909df', '2025-03-24 14:31:44', '10.6.0.230'),
(57, 2, 'a37d9b76d2653cc86ef0381a98555edd', '2025-03-24 14:37:32', '10.16.1.232'),
(58, 1, '7f6f7de4aafa1741cbd00a526816b11d', '2025-03-24 14:38:17', '10.16.1.232'),
(59, 1, '93df862863ef1278359d3c4a428fcb51', '2025-03-24 14:54:23', '10.6.0.143'),
(60, 1, 'c96735c076751c99f405ae822b556d57', '2025-03-24 14:55:35', '10.6.0.143'),
(61, 1, '44d6423152a7096fa1951ad0bcaf5772', '2025-03-24 14:55:49', '10.6.0.143'),
(62, 2, 'af6fcf272990dbd63c6bb2467e48479b', '2025-03-24 15:05:30', '127.0.0.1'),
(63, 1, '026d62d1620bb323023c400ff96294ef', '2025-03-24 15:05:45', '10.6.0.143'),
(64, 1, '888ec56ae3281d0b385f5f9296eb0877', '2025-03-24 15:06:17', '10.6.0.143'),
(65, 2, '80ab41c57f5b81c29937c4b423042c87', '2025-03-24 15:21:06', '10.6.0.143'),
(66, 4, 'e5138b8014bbf453113129569aa4ac82', '2025-03-24 15:22:44', '127.0.0.1'),
(67, 1, '4658fae81c7b0c6c49f4282d662015dc', '2025-03-24 15:23:31', '127.0.0.1'),
(68, 5, 'f448113b8728693860d99dbd15bafe1e', '2025-03-24 15:25:56', '10.6.0.230'),
(69, 2, '477257da892c785081fe09aa1c2c7c3b', '2025-03-24 15:26:30', '127.0.0.1'),
(70, 1, '5dbe31717630aa1ff22dfdda1ca74784', '2025-03-24 15:26:43', '10.6.0.230'),
(71, 3, 'dbf72f9ce6a7c2083ba9061ea96e016b', '2025-03-24 15:26:53', '10.6.0.230'),
(72, 2, '5f652f2251ebee91df95ccfd3af19866', '2025-03-24 15:30:04', '127.0.0.1'),
(73, 1, '436ce4cad48560141637d8fe3c3399f9', '2025-03-24 15:30:16', '10.6.0.230'),
(74, 3, 'aede8865e52d71493a9c8f90b7269df4', '2025-03-24 15:30:29', '10.6.0.230'),
(75, 1, '55663f0e14aa89cdb852053cd6bc1689', '2025-03-24 15:34:18', '192.168.205.187'),
(76, 1, '3db31d306f4377f857deebb72edd1303', '2025-03-24 15:34:25', '192.168.205.128'),
(77, 2, '5177ba642d4c37e29dadb01a083c109c', '2025-03-24 15:35:16', '192.168.205.128'),
(78, 1, 'eff801ce1438539e80e8aa9418e29b9b', '2025-03-24 15:35:40', '192.168.205.128'),
(79, 2, '97e718c0c266cb0852481cf1f08079bc', '2025-03-24 15:35:44', '127.0.0.1'),
(80, 1, 'a5a96516a65242620df4344fec56ff14', '2025-03-24 15:35:54', '192.168.205.187'),
(81, 3, 'b9bc8ba9cceba8824afd152d52ec5105', '2025-03-24 15:36:20', '192.168.205.187');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `logs`
--

INSERT INTO `logs` (`id`, `desc`) VALUES
(1, 'Added new picking list entry: OrderNumber=213213, Type=104, SparePart=123ewdw, Tec=1'),
(2, 'Technician  updated order location: OrderId=12312319, NewLocation=WSB168'),
(3, 'Technician  updated order location: OrderId=12312318, NewLocation=WSB208'),
(4, 'Technician  updated order location: OrderId=0, NewLocation=WSB208'),
(5, 'Technician  updated order location: OrderId=231, NewLocation=WSB208'),
(6, 'Order in StockOut: OrderId=1231231, OrderNumber=, SparePart=, Type=2, Tecnico='),
(8, 'Order in StockOut: OrderId=12312366, OrderNumber=, SparePart=, Type=2, Tecnico='),
(9, 'Order arrived: OrderId=1231231, OrderNumber=, SparePart=, Type=2, Tecnico='),
(10, 'Order arrived: OrderId=12312366, OrderNumber=, SparePart=, Type=2, Tecnico='),
(11, 'Technician  updated order location: OrderId=4345, NewLocation=WSB208'),
(12, 'Technician  updated order location: OrderId=1231231, NewLocation=WSB208'),
(13, 'Technician  updated order location: OrderId=2312312, NewLocation=WSB208'),
(14, 'Technician  updated order location: OrderId=12321312, NewLocation=WSB208'),
(15, 'New order: OrderId=4588, OrderNumber=9, SparePart=juj, Type=100, Tecnico=1'),
(16, 'Order in StockOut: OrderId=4588, OrderNumber=, SparePart=, Type=2, Tecnico='),
(17, 'Technician  updated order location: OrderId=123412421, NewLocation=WSB208'),
(18, 'Technician  updated order location: OrderId=231231231, NewLocation=WSB208'),
(19, 'New order: OrderId=123123, OrderNumber=2323, SparePart=323t, Type=100, Tecnico=1'),
(20, 'Order in StockOut: OrderId=123123, OrderNumber=, SparePart=, Type=2, Tecnico='),
(21, 'Technician  updated order location: OrderId=3455555, NewLocation=WSB108'),
(22, 'Technician  updated order location: OrderId=12312366, NewLocation=WSB108'),
(23, 'Order arrived: OrderId=4588'),
(24, 'Order arrived: OrderId=123123'),
(25, 'Technician  updated order location: OrderId=4588, NewLocation=WSB208'),
(26, 'Technician  updated order location: OrderId=123123, NewLocation=WSB208'),
(27, 'Technician  updated order location: OrderId=123123, NewLocation=WSB208'),
(28, 'New order: OrderId=72617, OrderNumber=125, SparePart=WXP500AC, Type=117, Tecnico=1'),
(29, 'Order in StockOut: OrderId=72617'),
(30, 'Order arrived: OrderId=72617'),
(31, 'Technician  updated order location: OrderId=72617, NewLocation=WSB-1100'),
(32, 'New order: OrderId=1090, OrderNumber=908973, SparePart=JHK4567P, Type=102, Tecnico=3'),
(33, 'New order: OrderId=1054, OrderNumber=982734, SparePart=LOU7493R, Type=111, Tecnico=1'),
(34, 'Order in StockOut: OrderId=1090'),
(35, 'New order: OrderId=12345667, OrderNumber=4353543, SparePart=motor, Type=117, Tecnico=1'),
(36, 'Order in StockOut: OrderId=12345667'),
(37, 'Order arrived: OrderId=12345667'),
(38, 'Technician  updated order location: OrderId=12345667, NewLocation=WSB101'),
(39, 'Order arrived: OrderId=1090'),
(40, 'Technician  updated order location: OrderId=1090, NewLocation=WSB100'),
(41, 'New order: OrderId=2344545, OrderNumber=12374, SparePart=motor, Type=111, Tecnico=1'),
(42, 'Order in StockOut: OrderId=2344545'),
(43, 'Order arrived: OrderId=2344545'),
(44, 'Order in StockOut: OrderId=1054'),
(45, 'Technician  updated order location: OrderId=2344545, NewLocation=WSB101'),
(46, 'Order arrived: OrderId=1054'),
(47, 'Technician  updated order location: OrderId=1054, NewLocation=WSB101'),
(48, 'New order: OrderId=123456767, OrderNumber=4583, SparePart=motor, Type=111, Tecnico=1'),
(49, 'Order in StockOut: OrderId=123456767'),
(50, 'Technician  updated order location: OrderId=1054, NewLocation=WSB101'),
(51, 'Order arrived: OrderId=123456767'),
(52, 'Technician  updated order location: OrderId=1054, NewLocation=WSB101'),
(53, 'Technician  updated order location: OrderId=123456767, NewLocation=WSB101'),
(54, 'Technician  updated order location: OrderId=1054, NewLocation=WSB101');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pickingList`
--

CREATE TABLE `pickingList` (
  `OrderId` int(10) UNSIGNED NOT NULL,
  `OrderNumber` int(10) UNSIGNED NOT NULL,
  `SparePart` varchar(64) DEFAULT NULL,
  `type` smallint(5) UNSIGNED DEFAULT NULL,
  `location` varchar(32) DEFAULT '"200"',
  `tec` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `pickingList`
--

INSERT INTO `pickingList` (`OrderId`, `OrderNumber`, `SparePart`, `type`, `location`, `tec`) VALUES
(0, 0, 'fwedwedw', 101, 'WSB208', 1),
(231, 231231, 'erferferf', 101, 'WSB208', 1),
(1054, 982734, 'LOU7493R', 111, 'WSB101', 1),
(1090, 908973, 'JHK4567P', 102, 'WSB100', 3),
(4345, 453455, 'r', 100, 'WSB208', 1),
(4588, 9, 'juj', 100, 'WSB208', 1),
(72617, 125, 'WXP500AC', 117, 'WSB-1100', 1),
(123123, 2323, '323t', 100, 'WSB208', 1),
(1231231, 31312321, '231312', 103, 'WSB208', 1),
(2312312, 21312312, 'tgtrgrg', 102, 'WSB208', 1),
(2344545, 12374, 'motor', 111, 'WSB101', 1),
(3455555, 555666, 'y', 111, 'WSB108', 3),
(12312317, 2131231, 'dwed', 111, '111', 2),
(12312318, 12312, 'ewdewdwe', 102, 'WSB208', 1),
(12312319, 213213, '123ewdw', 104, 'WSB168', 1),
(12312366, 3453467, 'rt', 101, 'WSB108', 3),
(12321312, 1231231, '123123312313121', 100, 'WSB208', 1),
(12345667, 4353543, 'motor', 117, 'WSB101', 1),
(123412421, 1312312312, 'dferrtgrtrf', 102, 'WSB208', 1),
(123456767, 4583, 'motor', 111, 'WSB101', 1),
(231231231, 1231231231, 'wededw', 102, 'WSB208', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `types`
--

CREATE TABLE `types` (
  `id` int(10) UNSIGNED NOT NULL,
  `desc` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `types`
--

INSERT INTO `types` (`id`, `desc`) VALUES
(1, 'Técnico'),
(2, 'Logistica'),
(500, 'ADMIN');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `passwd` varchar(255) DEFAULT NULL,
  `type` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `bancada` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `username`, `passwd`, `type`, `bancada`) VALUES
(1, 'jorge', '$2y$10$72esAE0/Cj/rVjgEJMNgG.gEwLX2qSHuOFKUtGX7flyG1NjhBKn3e', 1, 'WSB101'),
(2, 'jorgelogi', '$2y$10$aOGLDqx2ROtnJ7m0SRbCCelIOL.S5xxSeDcc14we97DZZEyoaO5j2', 2, NULL),
(3, 'rodrigotec', '$2y$10$0pAWOLwsxn495QnbMUScD.D0XKWx9t3Mw5upf2M3g0NSE2yh6OcCO', 1, 'WSB100'),
(4, 'jorgeadmin', '$2y$10$I39PGmO5RsDpCmFEJV7ccOqOVvHF5lPYDCQPK7Cv6/5ZppUQoW6TG', 500, NULL),
(5, 'joaologi', '$2y$10$VohvQYPFvaVeCR9AC4rfLeElG3rjs/QGD/vyPb9BHj/EzmmjpQPfy', 2, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `login_sessions`
--
ALTER TABLE `login_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `pickingList`
--
ALTER TABLE `pickingList`
  ADD PRIMARY KEY (`OrderId`);

--
-- Índices para tabela `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `login_sessions`
--
ALTER TABLE `login_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `types`
--
ALTER TABLE `types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
