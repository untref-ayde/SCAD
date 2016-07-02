SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `dedicacion` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `semana` date NOT NULL,
  `porcentaje` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `empleado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `sueldo_bruto` int(10) UNSIGNED NOT NULL,
  `cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `proyecto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `capacitacion` tinyint(1) NOT NULL,
  `computable` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `dedicacion`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `dedicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `empleado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
 
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;