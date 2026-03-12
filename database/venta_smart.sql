-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 18-05-2025 a las 17:00:59
-- Versión del servidor: 8.0.17
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `venta_smart`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertarusuario` (IN `n` VARCHAR(255), IN `ape` VARCHAR(50), IN `apema` VARCHAR(50), IN `r` INT, IN `c` VARCHAR(255))  BEGIN
    INSERT INTO usuarios (rol, nombre, apepat, apemat, contra)
    VALUES (r, n, ape, apema, c);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarVenta` (IN `p_id_producto` INT, IN `p_cantidad` INT)  BEGIN
    DECLARE v_stock INT;

    
    SELECT Stock INTO v_stock
    FROM Productos
    WHERE id_producto = p_id_producto;

    IF v_stock >= p_cantidad THEN
        
        INSERT INTO det_venta (id_producto, cantidad)
        VALUES (p_id_producto, p_cantidad);

        
        SELECT 'Venta registrada correctamente.' AS Mensaje;
    ELSE
        
        SELECT 'Error: No hay suficiente stock o el producto no existe.' AS Mensaje;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_movimientos`
--

CREATE TABLE `caja_movimientos` (
  `id_movimiento` int(11) NOT NULL,
  `tipo` enum('ingreso','retiro') NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_comprasprov` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('activo','cancelado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `caja_movimientos`
--

INSERT INTO `caja_movimientos` (`id_movimiento`, `tipo`, `monto`, `fecha_hora`, `id_usuario`, `id_comprasprov`, `descripcion`, `estado`) VALUES
(1, 'ingreso', '3450.00', '2025-05-08 15:13:59', 6, NULL, 'Caja', 'activo'),
(2, 'ingreso', '500.00', '2025-05-08 15:14:24', 6, NULL, 'Cambio', 'activo'),
(3, 'retiro', '287.50', '2025-05-08 15:17:41', 6, 56, 'Compra a proveedor Karla Itzel', 'activo'),
(4, 'ingreso', '3450.00', '2025-05-09 22:41:13', 6, NULL, 'Corte', 'activo'),
(5, 'retiro', '315.00', '2025-05-09 22:49:11', 8, 57, 'Compra a Ana Isabel', 'activo'),
(7, 'retiro', '187.50', '2025-05-09 23:16:37', 8, 59, 'Compra a Karla Itzel', 'activo'),
(8, 'retiro', '260.00', '2025-05-10 00:08:11', 8, 60, 'Compra a Luis Miguel', 'activo'),
(9, 'retiro', '107.50', '2025-05-10 00:13:05', 8, 61, 'Compra a Hugo', 'activo'),
(10, 'retiro', '45.50', '2025-05-12 16:40:06', 6, 62, 'Compra a Felipe Armando', 'activo'),
(11, 'retiro', '175.00', '2025-05-12 16:58:18', 6, 63, 'Compra a Carmen Lucía', 'activo'),
(12, 'retiro', '450.00', '2025-05-12 17:16:39', 6, 64, 'Compra a Natalia', 'activo'),
(13, 'retiro', '155.00', '2025-05-12 18:16:30', 6, 65, 'Compra a Jorge', 'activo'),
(14, 'ingreso', '3450.00', '2025-05-13 15:27:34', 6, NULL, 'Corte', 'activo'),
(15, 'retiro', '440.00', '2025-05-13 15:38:24', 6, 66, 'Compra a Rosa', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fechabaja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `fechabaja`) VALUES
(1, 'General', NULL),
(26, 'Aceites comestibles', NULL),
(27, 'Arroz y frijoles', NULL),
(28, 'Atún y sardinas', NULL),
(29, 'Azúcar y endulzantes', NULL),
(30, 'Bebidas alcohólicas', NULL),
(32, 'Bebidas energéticas', NULL),
(33, 'Café y té', NULL),
(34, 'Cereal de desayuno', NULL),
(35, 'Charcutería y embutidos', NULL),
(36, 'Cuidado personal', NULL),
(37, 'Dulces y golosinas', NULL),
(38, 'Enlatados', NULL),
(39, 'Especias y condimentos', NULL),
(40, 'Frutas y verduras frescas', NULL),
(41, 'Galletas y productos de panadería', NULL),
(42, 'Harinas y féculas', NULL),
(43, 'Helados y postres congelados', NULL),
(44, 'Lácteos (leche, queso, yogurt)', NULL),
(45, 'Limpieza del hogar', NULL),
(46, 'Mantequilla y margarina', NULL),
(47, 'Mermeladas y jaleas', NULL),
(48, 'Pan de caja y panadería', NULL),
(49, 'Pasta y fideos', NULL),
(50, 'Productos de limpieza', NULL),
(51, 'Productos para el hogar', NULL),
(52, 'Refrescos y gaseosas', NULL),
(53, 'Salsas y aderezos', NULL),
(54, 'Sopas instantáneas', NULL),
(55, 'Snacks y botanas', NULL),
(56, 'Tequila y otros licores', NULL),
(57, 'Té y café', NULL),
(58, 'Tortillas y productos de maíz', NULL),
(59, 'Vino y cervezas', NULL),
(60, 'Aceitunas y encurtidos', NULL),
(61, 'Alimentos congelados', NULL),
(62, 'Alimentos para mascotas', NULL),
(63, 'Alimentos para bebés', NULL),
(64, 'Aves y carnes frías', NULL),
(65, 'Bolsas y empaques', NULL),
(66, 'Caldo de pollo y verduras', NULL),
(67, 'Cereal y granos', NULL),
(68, 'Cereal instantáneo', NULL),
(69, 'Cereales para desayuno', NULL),
(70, 'Chocolates y caramelos', NULL),
(71, 'Cuidado del cabello', NULL),
(72, 'Cuidado de la piel', NULL),
(73, 'Desinfectantes y detergentes', NULL),
(74, 'Ensaladas preempacadas', NULL),
(75, 'Escabeches y conservas', NULL),
(76, 'Frutos secos y semillas', NULL),
(77, 'Galletas saladas', NULL),
(78, 'Galletas dulces', NULL),
(79, 'Gaseosas', NULL),
(80, 'Helados y sorbetes', NULL),
(81, 'Jugo de frutas y néctares', NULL),
(82, 'Lentejas, garbanzos y frijoles', NULL),
(83, 'Manteca y aceites comestibles', NULL),
(84, 'Mermeladas, jaleas y miel', NULL),
(85, 'Nuez, almendras y pistaches', NULL),
(86, 'Papel higiénico y toallas desechables', NULL),
(87, 'Panadería', NULL),
(88, 'Pasta y fideos instantáneos', NULL),
(89, 'Pescados y mariscos congelados', NULL),
(90, 'Productos de limpieza general', NULL),
(91, 'Productos para la salud y higiene', NULL),
(92, 'Salsas para pasta', NULL),
(93, 'Sopas de sobre y instantáneas', NULL),
(94, 'Tetra Pak de jugos y néctares', NULL),
(95, 'Vajillas y utensilios de cocina', NULL),
(96, 'Yogurt y cremas', NULL),
(97, 'Aceites para cocinar', NULL),
(98, 'Alimentos para dietas especiales', NULL),
(99, 'Alimentos orgánicos', NULL),
(100, 'Arroz integral y semillas', NULL),
(101, 'Bebidas lácteas y alternativas vegetales', NULL),
(102, 'Café molido y en grano', NULL),
(103, 'Carnes frescas y congeladas', NULL),
(104, 'Condimentos y aderezos en polvo', NULL),
(105, 'Frutas tropicales y de temporada', NULL),
(106, 'Galletas de chocolate y rellenas', NULL),
(107, 'Harinas de maíz y trigo', NULL),
(108, 'Jabón líquido y detergentes', NULL),
(109, 'Lentejas y legumbres', NULL),
(110, 'Mantecas y mantequillas', NULL),
(111, 'Nueces y frutos secos', NULL),
(112, 'Papel higiénico y servilletas', NULL),
(113, 'Pasta de dientes y enjuagues bucales', NULL),
(114, 'Pescado enlatado y congelado', NULL),
(115, 'Productos para el hogar y oficina', NULL),
(116, 'Refrescos de dieta y light', NULL),
(117, 'Salsas para carnes y mariscos', NULL),
(118, 'Sopas y cremas', NULL),
(119, 'Té de hierbas y frutales', NULL),
(120, 'Tortillas y pan de maíz', NULL),
(121, 'Vinagre y aderezos', NULL),
(122, 'Zapatos y calzado de cocina', NULL),
(123, 'Aguacates y jitomates', NULL),
(124, 'Almohadas y cobijas', NULL),
(125, 'Cereal integral y muesli', NULL),
(126, 'Dulces de leche y chocolate', NULL),
(127, 'Galletas saladas y crackers', NULL),
(129, 'Alimentos congelados', NULL),
(130, 'Alimentos preparados', NULL),
(131, 'Alimentos sin gluten', NULL),
(132, 'Amasadora y utensilios de cocina', NULL),
(133, 'Aperitivos saludables', NULL),
(134, 'Arroz y pasta integral', NULL),
(135, 'Bebidas isotónicas', NULL),
(136, 'Bebidas para deportistas', NULL),
(137, 'Bebidas vegetales', NULL),
(138, 'Bocadillos y snacks saludables', NULL),
(139, 'Café y cacao instantáneo', NULL),
(140, 'Carne de cerdo y embutidos', NULL),
(141, 'Cereal para bebé', NULL),
(142, 'Cereales integrales', NULL),
(143, 'Chicles y caramelos', NULL),
(144, 'Comida para animales pequeños', NULL),
(145, 'Comida para perros', NULL),
(146, 'Comida para gatos', NULL),
(147, 'Conservas de frutas', NULL),
(148, 'Conservas de vegetales', NULL),
(149, 'Cortes de carne', NULL),
(150, 'Crema de cacahuate', NULL),
(151, 'Crema para café', NULL),
(152, 'Cremas y salsas para ensaladas', NULL),
(153, 'Dulces de chocolate', NULL),
(154, 'Ensaladas en bolsa', NULL),
(155, 'Galletas de arroz', NULL),
(156, 'Galletas integrales', NULL),
(157, 'Galletas sin azúcar', NULL),
(158, 'Gaseosas sin azúcar', NULL),
(159, 'Harinas sin gluten', NULL),
(160, 'Helados sin azúcar', NULL),
(161, 'Lactosa libre', NULL),
(162, 'Leche condensada', NULL),
(163, 'Leche evaporada', NULL),
(164, 'Mantecas vegetales', NULL),
(165, 'Mermeladas bajas en azúcar', NULL),
(166, 'Mermeladas sin azúcar', NULL),
(167, 'Nueces, almendras y semillas', NULL),
(168, 'Pan integral', NULL),
(169, 'Pasta de arroz', NULL),
(170, 'Pasta de trigo sarraceno', NULL),
(171, 'Pasta sin gluten', NULL),
(172, 'Pescado fresco', NULL),
(173, 'Pescado en aceite', NULL),
(174, 'Pistaches y frutos secos', NULL),
(175, 'Pollo fresco y congelado', NULL),
(176, 'Postres instantáneos', NULL),
(177, 'Refrescos sin azúcar', NULL),
(178, 'Salsas BBQ y picantes', NULL),
(179, 'Sopas y cremas caseras', NULL),
(180, 'Sopas y caldos orgánicos', NULL),
(181, 'Té verde y negro', NULL),
(182, 'Té en bolsitas', NULL),
(183, 'Té herbal y medicinal', NULL),
(184, 'Tortillas de harina y maíz', NULL),
(186, 'Vino espumoso', NULL),
(187, 'Yogur griego y natural', NULL),
(188, 'Yogurt sin azúcar', NULL),
(189, 'Alimentos orgánicos frescos', NULL),
(190, 'Alimentos sin conservadores', NULL),
(191, 'Alimentos para diabéticos', NULL),
(192, 'Bebidas frutales', NULL),
(193, 'Bebidas sin cafeína', NULL),
(194, 'Bocadillos saludables', NULL),
(195, 'Café de comercio justo', NULL),
(196, 'Carnes curadas', NULL),
(197, 'Cereal y avena integral', NULL),
(198, 'Chocolate oscuro', NULL),
(199, 'Comida vegana y vegetariana', NULL),
(200, 'Comida natural', NULL),
(201, 'Condimentos sin sal', NULL),
(202, 'Conservas sin azúcar', NULL),
(203, 'Desayunos rápidos', NULL),
(204, 'Especias orgánicas', NULL),
(205, 'Frutas y vegetales orgánicos', NULL),
(206, 'Frutas deshidratadas', NULL),
(207, 'Galletas orgánicas', NULL),
(208, 'Galletas veganas', NULL),
(209, 'Harinas sin gluten', NULL),
(210, 'Lácteos orgánicos', NULL),
(211, 'Lácteos sin lactosa', NULL),
(212, 'Legumbres orgánicas', NULL),
(213, 'Mermeladas orgánicas', NULL),
(214, 'Miel orgánica', NULL),
(215, 'Productos para celíacos', NULL),
(216, 'Salsas orgánicas', NULL),
(217, 'Sopas instantáneas orgánicas', NULL),
(218, 'Snacks orgánicos', NULL),
(219, 'Té y hierbas medicinales', NULL),
(220, 'Tortillas orgánicas', NULL),
(221, 'Vino orgánico', NULL),
(222, 'Yogurt vegano', NULL),
(223, 'Aceites vegetales', NULL),
(224, 'Alimentos gourmet', NULL),
(225, 'Alimentos para celíacos', NULL),
(226, 'Alimentos sin lactosa', NULL),
(227, 'Alimentos sin azúcar', NULL),
(229, 'Bebidas sin gas', NULL),
(230, 'Bebidas saborizadas', NULL),
(231, 'Cereal integral', NULL),
(232, 'Cereal para niños', NULL),
(233, 'Chocolate y bombones', NULL),
(234, 'Cocina mexicana', NULL),
(235, 'Cosechas en conserva', NULL),
(236, 'Dulces tradicionales', NULL),
(237, 'Especias exóticas', NULL),
(238, 'Frescos y congelados', NULL),
(239, 'Frutos secos y snacks', NULL),
(240, 'Galletas sin gluten', NULL),
(241, 'Galletas para diabéticos', NULL),
(242, 'Gaseosas sin azúcar añadida', NULL),
(243, 'Helados veganos', NULL),
(244, 'Jugo natural y orgánico', NULL),
(245, 'Lácteos sin azúcar', NULL),
(246, 'Leche de almendra y soja', NULL),
(247, 'Leche en polvo', NULL),
(248, 'Lentejas y legumbres secas', NULL),
(249, 'Manteca de cerdo', NULL),
(250, 'Mantequilla sin sal', NULL),
(251, 'Mermeladas bajas en azúcar', NULL),
(252, 'Mermeladas sin azúcar', NULL),
(253, 'Nueces, almendras y semillas', NULL),
(254, 'Pan integral', NULL),
(255, 'Pasta de arroz', NULL),
(256, 'Pasta de trigo sarraceno', NULL),
(257, 'Pasta sin gluten', NULL),
(258, 'Pescado fresco', NULL),
(259, 'Pescado en aceite', NULL),
(260, 'Pistaches y frutos secos', NULL),
(261, 'Pollo fresco y congelado', NULL),
(262, 'Postres instantáneos', NULL),
(263, 'Refrescos sin azúcar', NULL),
(264, 'Salsas BBQ y picantes', NULL),
(266, 'Sopas y caldos orgánicos', NULL),
(267, 'Té verde y negro', NULL),
(268, 'Té en bolsitas', NULL),
(269, 'Té herbal y medicinal', NULL),
(270, 'Tortillas de harina y maíz', NULL),
(271, 'Vino tinto y blanco', NULL),
(272, 'Vino espumoso', NULL),
(273, 'Yogur griego y natural', NULL),
(274, 'Yogurt sin azúcar', NULL),
(275, 'Alimentos orgánicos frescos', NULL),
(276, 'Alimentos sin conservadores', NULL),
(277, 'Alimentos para diabéticos', NULL),
(278, 'Bebidas frutales', NULL),
(279, 'Bebidas sin cafeína', NULL),
(280, 'Bocadillos saludables', NULL),
(281, 'Café de comercio justo', NULL),
(282, 'Carnes curadas', NULL),
(283, 'Cereal y avena integral', NULL),
(284, 'Chocolate oscuro', NULL),
(285, 'Comida vegana y vegetariana', NULL),
(286, 'Comida natural', NULL),
(287, 'Condimentos sin sal', NULL),
(288, 'Conservas sin azúcar', NULL),
(289, 'Desayunos rápidos', NULL),
(290, 'Especias orgánicas', NULL),
(291, 'Frutas y vegetales orgánicos', NULL),
(292, 'Frutas deshidratadas', NULL),
(293, 'Galletas orgánicas', NULL),
(294, 'Galletas veganas', NULL),
(295, 'Harinas sin gluten', NULL),
(296, 'Lácteos orgánicos', NULL),
(297, 'Lácteos sin lactosa', NULL),
(298, 'Legumbres orgánicas', NULL),
(299, 'Mermeladas orgánicas', NULL),
(300, 'Miel orgánica', NULL),
(301, 'Productos para celíacos', NULL),
(302, 'Salsas orgánicas', NULL),
(303, 'Sopas instantáneas orgánicas', NULL),
(304, 'Snacks orgánicos', NULL),
(305, 'Té y hierbas medicinales', NULL),
(306, 'Tortillas orgánicas', NULL),
(307, 'Vino orgánico', NULL),
(308, 'Yogurt vegano', NULL),
(309, 'Pomadas', NULL),
(311, 'Cremas para el cuerpo', NULL),
(312, 'Consomés', NULL),
(313, 'Bebés', NULL),
(314, 'Bebidas en polvo saborizadas', NULL),
(315, 'Frituras', NULL),
(316, 'Suavizantes de telas', NULL),
(317, 'Comida congelada', NULL),
(318, 'Dulcería', NULL),
(319, 'Desechables', NULL),
(320, 'Papeleria', NULL),
(321, 'Juguetería', NULL),
(322, 'Productos de temporada', NULL),
(323, 'Desodorantes', NULL),
(324, 'Higiene femenina', NULL),
(325, 'Tintes', NULL),
(326, 'Productos de belleza', NULL),
(327, 'Cuidado de la piel (skincare)', NULL),
(328, 'Farmacia', NULL),
(329, 'Productos para el cabello', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apepat` varchar(100) NOT NULL,
  `apemat` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `apepat`, `apemat`) VALUES
(1, 'Juan', 'Alatorre', 'Torres'),
(2, 'Adele Laurie', 'Blue ', 'Adkins'),
(3, 'Katheryn Elizabeth', 'Hudson', 'Pérez'),
(5, 'Octavio', 'Cerda', 'Becerra'),
(6, 'Jorge Luis', 'Castello', 'Marín'),
(7, 'Perla Marina', 'Hernández', 'Torres'),
(8, 'Patricia Estrella', 'Trillo', 'Nodal'),
(9, 'María José', 'Arana', 'Corona'),
(10, 'Emilio', 'Becerra', 'Zárate'),
(11, 'Bernandino ', 'Llopis', 'Acosta'),
(12, 'Jimena', 'Olmos', 'Aguilar'),
(13, 'Adelaida', 'Lozano', 'Alvarado'),
(14, 'Antonia', 'Salvador', 'Arellano'),
(15, 'Miriam', 'Rojo', 'Rojitas'),
(16, 'Adriana', 'Mares', 'Cabrera'),
(17, 'Osvaldo', 'Gómez', 'Morales'),
(18, 'Rodrigo', 'Cárdenas', 'Campos'),
(19, 'Fernando', 'Castañeda', 'Olmos'),
(20, 'Georgina', 'Molina', 'Barranco'),
(21, 'María de la luz', 'Castro', 'Chávez'),
(22, 'Nabil', 'Carrera', 'Ortiz'),
(23, 'Valentin', 'Gil', 'Contreras'),
(24, 'Pascual ', 'Andrade', 'Tejeda'),
(25, 'Axel', 'Megias', 'Cortés'),
(26, 'Pedro José', 'Franco', 'Escamilla'),
(27, 'Alejandro', 'González', 'Zaragoza'),
(28, 'Enriqueta', 'Matos', 'Olivares'),
(29, 'Jennifer', 'Font', 'Cruz'),
(30, 'Carmen ', 'Catala', 'Delgado'),
(31, 'Jorge Juan', 'Echeverria', 'Díaz'),
(32, 'Maria Gracia', 'Madrid', 'Domínguez'),
(33, 'Eduardo', 'Cuevas', 'Hernández'),
(34, 'Isaac', 'Maestre', 'Huerta'),
(35, 'Enriqueta', 'Taboada', 'Jiménez'),
(36, 'Jesús Manuel', 'Aroca', 'Ibarra'),
(37, 'Anna', 'Martín', 'Juárez'),
(38, 'Sebastian', 'Herreros', 'Lara'),
(39, 'Anastasia', 'Moral', 'León'),
(40, 'Beatriz', 'Ares', 'Luna'),
(41, 'Guillermo', 'Chico', 'Maldonado'),
(42, 'Briel', 'Laguna', 'Márquez'),
(44, 'Juan', 'Gómez', 'Hernández'),
(45, 'María', 'López', 'Martínez'),
(46, 'Carlos', 'Rodríguez', 'González'),
(47, 'Ana', 'Pérez', 'Díaz'),
(48, 'Luis', 'Fernández', 'Torres'),
(49, 'Sofía', 'Ramírez', 'Castro'),
(50, 'Miguel', 'Vargas', 'Jiménez'),
(51, 'Laura', 'Ortega', 'Ruiz'),
(52, 'Javier', 'Mendoza', 'Silva'),
(53, 'Paola', 'Herrera', 'Morales'),
(54, 'Fernando', 'Castillo', 'Reyes'),
(55, 'Andrea', 'Sánchez', 'Flores'),
(56, 'Ricardo', 'Medina', 'Chávez'),
(57, 'Valeria', 'Gutiérrez', 'Ramos'),
(58, 'Héctor', 'Paredes', 'Acosta'),
(59, 'Gabriela', 'Núñez', 'Peña'),
(60, 'José', 'Cortés', 'Delgado'),
(61, 'Diana', 'Ortiz', 'Navarro'),
(62, 'Roberto', 'Escobar', 'Carrillo'),
(63, 'Monica', 'Salazar', 'Fuentes'),
(64, 'Alejandro', 'Molina', 'Aguirre'),
(65, 'Camila', 'Mejía', 'Rojas'),
(66, 'Daniel', 'Guerrero', 'Luna'),
(67, 'Patricia', 'Ríos', 'Ávila'),
(68, 'Francisco', 'Santos', 'Montes'),
(69, 'Beatriz', 'Miranda', 'Esquivel'),
(70, 'Emilio', 'Cárdenas', 'Vera'),
(71, 'Rocío', 'Bautista', 'León'),
(72, 'Esteban', 'Ponce', 'Suárez'),
(73, 'Natalia', 'Ibarra', 'Cano'),
(74, 'Jorge', 'González', 'Serrano'),
(75, 'Claudia', 'Vega', 'Álvarez'),
(76, 'Ricardo', 'Ramos', 'Delgado'),
(77, 'Elena', 'Jiménez', 'Cordero'),
(78, 'Álvaro', 'Méndez', 'Pérez'),
(79, 'Silvia', 'Figueroa', 'Sánchez'),
(80, 'Antonio', 'Ramírez', 'Bravo'),
(81, 'Verónica', 'Escobar', 'López'),
(82, 'Luis', 'Jiménez', 'Martínez'),
(83, 'Alberto', 'Guerrero', 'Vargas'),
(84, 'Marta', 'Navarro', 'Reyes'),
(85, 'Héctor', 'Vega', 'Bautista'),
(86, 'Carmen', 'Morales', 'Romero'),
(87, 'Joaquín', 'Torres', 'Salazar'),
(88, 'Beatriz', 'González', 'Ibarra'),
(89, 'Santiago', 'Díaz', 'Ferrer'),
(90, 'Lucía', 'Martínez', 'López'),
(91, 'Oscar', 'Pérez', 'Núñez'),
(92, 'Cristina', 'Fernández', 'Campos'),
(93, 'Manuel', 'Castaño', 'Martínez'),
(94, 'Leticia', 'Ferrer', 'Montes'),
(95, 'Raúl', 'Álvarez', 'López'),
(96, 'Verónica', 'Jiménez', 'Vega'),
(97, 'Lola', 'Torres', 'Moreno'),
(98, 'Gabriel', 'Figueroa', 'Méndez'),
(99, 'Margarita', 'Navarro', 'González'),
(100, 'Felipe', 'Torres', 'Hernández'),
(101, 'Carlos', 'Morales', 'Reyes'),
(102, 'Alicia', 'González', 'Delgado'),
(103, 'Francisco', 'Figueroa', 'Pérez'),
(104, 'Laura', 'Cordero', 'Martínez'),
(105, 'Ramón', 'Ruiz', 'Pérez'),
(106, 'Gonzalo', 'Ortega', 'Mendoza'),
(107, 'Isabel', 'Morales', 'Cordero'),
(108, 'Luis', 'Vázquez', 'González'),
(109, 'Sara', 'Delgado', 'Hernández'),
(110, 'David', 'Ramírez', 'Pérez'),
(111, 'Felipe', 'Torres', 'Jiménez'),
(112, 'Eva', 'González', 'Vega'),
(113, 'Antonio', 'Méndez', 'Figueroa'),
(114, 'Rafael', 'Vargas', 'Montes'),
(115, 'José', 'Morales', 'González'),
(116, 'Ana', 'Serrano', 'Martínez'),
(117, 'Ángel', 'Vega', 'Hernández'),
(118, 'Manuela', 'Ferrer', 'Martínez'),
(119, 'Raúl', 'Martínez', 'Salazar'),
(120, 'Alejandra', 'Mendoza', 'Bravo'),
(121, 'Antonio', 'Moreno', 'Cordero'),
(122, 'Nicolás', 'Ruiz', 'Castaño'),
(123, 'Victoria', 'Hernández', 'Pérez'),
(124, 'Pedro', 'Martínez', 'Álvarez'),
(125, 'Mercedes', 'Vargas', 'Torres'),
(126, 'Alejandra', 'Ruiz', 'Vega'),
(127, 'Guadalupe', 'Sánchez', 'Torres'),
(128, 'Antonio', 'Navarro', 'Jiménez'),
(129, 'Carlos', 'Vázquez', 'Hernández'),
(130, 'José', 'Ibarra', 'Vega'),
(131, 'Victoria', 'Ruiz', 'González'),
(132, 'Joaquín', 'Torres', 'Pérez'),
(133, 'Carmen', 'Martínez', 'Torres'),
(134, 'Pedro', 'Ramírez', 'Méndez'),
(135, 'José', 'Vázquez', 'Ramírez'),
(136, 'Santiago', 'Fernández', 'González'),
(137, 'Beatriz', 'Martínez', 'Cordero'),
(138, 'Salvador', 'Navarro', 'Vázquez'),
(139, 'Laura', 'Ibarra', 'Jiménez'),
(140, 'Rafael', 'Ramírez', 'Sánchez'),
(141, 'Sara', 'Morales', 'Serrano'),
(142, 'Verónica', 'Torres', 'Castaño'),
(143, 'Pablo', 'Sánchez', 'Torres'),
(144, 'Rosa', 'González', 'Hernández'),
(145, 'Guillermo', 'Serrano', 'Vega'),
(146, 'Ángel', 'Vargas', 'Martínez'),
(147, 'Raúl', 'Ramírez', 'Méndez'),
(148, 'Margarita', 'Torres', 'González'),
(149, 'José', 'Méndez', 'Cordero'),
(150, 'Antonio', 'Serrano', 'Vega'),
(151, 'Carlos', 'Figueroa', 'González'),
(152, 'Daniel', 'Hernández', 'Torres'),
(153, 'Andrea', 'Sánchez', 'Vega'),
(154, 'Verónica', 'Ramos', 'González'),
(155, 'David', 'Torres', 'Morales'),
(156, 'Felipe', 'Morales', 'Pérez'),
(157, 'Alicia', 'Serrano', 'Sánchez'),
(158, 'Héctor', 'González', 'Martínez'),
(159, 'Marta', 'Ramírez', 'Hernández'),
(160, 'Beatriz', 'López', 'Méndez'),
(161, 'Javier', 'Vega', 'Moreno'),
(162, 'Lucía', 'Méndez', 'Torres'),
(163, 'Carlos', 'Vega', 'Ramírez'),
(164, 'María', 'Serrano', 'Martínez'),
(165, 'José', 'López', 'Figueroa'),
(166, 'Pilar', 'Ramírez', 'Méndez'),
(167, 'Carmen', 'González', 'Ramírez'),
(168, 'Santiago', 'Vega', 'Martínez'),
(169, 'Javier', 'Vázquez', 'González'),
(170, 'Ana', 'Méndez', 'Torres'),
(171, 'Laura', 'Vázquez', 'Ferrer'),
(172, 'Ramón', 'Ferrer', 'Serrano'),
(173, 'Francisco', 'Ibarra', 'Sánchez'),
(174, 'Margarita', 'Moreno', 'González'),
(175, 'José', 'Torres', 'Serrano'),
(176, 'Mónica', 'Cordero', 'González'),
(177, 'Ricardo', 'Vega', 'Martínez'),
(178, 'Antonio', 'Morales', 'Sánchez'),
(179, 'María', 'Vargas', 'Torres'),
(180, 'Ana', 'Ramos', 'Ferrer'),
(181, 'Carlos', 'Serrano', 'Vega'),
(182, 'Javier', 'Cordero', 'Serrano'),
(183, 'Carlos', 'Hernández', 'Morales'),
(184, 'Miguel', 'Torres', 'Ferrer'),
(185, 'Antonio', 'Moreno', 'Vargas'),
(186, 'Alfredo', 'González', 'Serrano'),
(187, 'Claudia', 'Méndez', 'Torres'),
(188, 'Sara', 'Hernández', 'González'),
(189, 'Javier', 'Serrano', 'Morales'),
(190, 'Fernando', 'Pérez', 'González'),
(191, 'José', 'Vargas', 'Serrano'),
(192, 'Raquel', 'Ibarra', 'Torres'),
(193, 'Luis', 'Ramírez', 'Méndez'),
(194, 'Álvaro', 'Morales', 'Vega'),
(195, 'Nicolás', 'Méndez', 'González'),
(196, 'Fernando', 'Martínez', 'Ferrer'),
(197, 'Carlos', 'Vega', 'Morales'),
(198, 'Ana', 'Vega', 'González'),
(199, 'Patricia', 'Vázquez', 'Ibarra'),
(200, 'Antonio', 'Hernández', 'Ramírez'),
(201, 'Santiago', 'Ramírez', 'Vargas'),
(202, 'Ángel', 'Vega', 'Ferrer'),
(203, 'Manuel', 'González', 'Serrano'),
(204, 'José', 'Sánchez', 'Ramírez'),
(205, 'Patricia', 'Cordero', 'Moreno'),
(206, 'Verónica', 'Sánchez', 'Morales'),
(207, 'Carlos', 'Torres', 'Serrano'),
(208, 'Adriana', 'Romero', 'Castillo'),
(209, 'Bernardo', 'Mendoza', 'Gutiérrez'),
(210, 'Carolina', 'Martínez', 'Ávila'),
(211, 'Eduardo', 'Ramírez', 'Méndez'),
(212, 'Francisca', 'González', 'Torres'),
(213, 'Guillermo', 'Vega', 'Pérez'),
(214, 'Hilda', 'López', 'Ramos'),
(215, 'Ignacio', 'Díaz', 'Torres'),
(216, 'Joaquín', 'Vázquez', 'Méndez'),
(217, 'Karla', 'Cordero', 'Fernández'),
(218, 'Luis', 'Pérez', 'Rodríguez'),
(219, 'Margarita', 'Hernández', 'López'),
(220, 'Nicolás', 'García', 'Ríos'),
(221, 'Olga', 'Serrano', 'Méndez'),
(222, 'Pablo', 'López', 'Vega'),
(223, 'Quirina', 'Ramírez', 'Martínez'),
(224, 'Raúl', 'Sánchez', 'Serrano'),
(225, 'Silvia', 'Figueroa', 'González'),
(226, 'Tomás', 'Méndez', 'Ibarra'),
(227, 'Ulises', 'Moreno', 'López'),
(228, 'Verónica', 'Paredes', 'Morales'),
(229, 'Ximena', 'Torres', 'González'),
(230, 'Yolanda', 'Ruiz', 'Martínez'),
(231, 'Zulema', 'Castaño', 'Vega'),
(232, 'Alejandro', 'Gutiérrez', 'Morales'),
(233, 'Berta', 'Mendoza', 'Torres'),
(234, 'Cristian', 'Hernández', 'Ríos'),
(235, 'Débora', 'Jiménez', 'González'),
(236, 'Esteban', 'García', 'Martínez'),
(237, 'Fabiola', 'Ramírez', 'Pérez'),
(238, 'Gabriel', 'Ortega', 'Vargas'),
(239, 'Héctor', 'Serrano', 'Reyes'),
(240, 'Iván', 'Martínez', 'Ibarra'),
(241, 'Julieta', 'Paredes', 'Castro'),
(242, 'Karina', 'Vega', 'Torres'),
(243, 'León', 'Fernández', 'González'),
(244, 'Miguel', 'Hernández', 'Méndez'),
(245, 'Néstor', 'Pérez', 'Torres'),
(246, 'Omar', 'Méndez', 'Ramírez'),
(247, 'Patricia', 'Vega', 'Serrano'),
(248, 'Raquel', 'Jiménez', 'González'),
(249, 'Sergio', 'Sánchez', 'Martínez'),
(250, 'Tania', 'Hernández', 'Pérez'),
(251, 'Ubaldo', 'López', 'Torres'),
(252, 'Valeria', 'Vargas', 'González'),
(253, 'Walter', 'Moreno', 'Vega'),
(254, 'Xavier', 'Ruiz', 'Méndez'),
(255, 'Yadira', 'Cordero', 'Méndez'),
(256, 'Zacarías', 'Torres', 'Serrano'),
(257, 'Ana', 'González', 'Ramírez'),
(258, 'Blanca', 'Serrano', 'Morales'),
(259, 'Carlos', 'Vargas', 'González'),
(260, 'Dolores', 'Méndez', 'Hernández'),
(261, 'Emilia', 'Castaño', 'Serrano'),
(262, 'Federico', 'Martínez', 'Ruiz'),
(263, 'Guadalupe', 'Ramírez', 'Pérez'),
(264, 'Hugo', 'Torres', 'González'),
(265, 'Inés', 'Paredes', 'López'),
(266, 'Javier', 'Moreno', 'García'),
(267, 'Karla', 'González', 'Serrano'),
(268, 'Luis', 'Torres', 'Méndez'),
(269, 'Marcos', 'Hernández', 'González'),
(270, 'Nidia', 'López', 'Pérez'),
(271, 'Óscar', 'Ramírez', 'Ibarra'),
(272, 'Paola', 'Serrano', 'Vega'),
(273, 'Raúl', 'González', 'Méndez'),
(274, 'Susana', 'Martínez', 'Ramírez'),
(275, 'Teodoro', 'Vega', 'Torres'),
(276, 'Ulises', 'Paredes', 'González'),
(277, 'Violeta', 'Jiménez', 'Martínez'),
(278, 'Walter', 'Figueroa', 'Morales'),
(279, 'Ximena', 'Hernández', 'Cordero'),
(280, 'Yadira', 'Sánchez', 'Vargas'),
(281, 'Zulema', 'Torres', 'Ibarra'),
(282, 'Alejandro', 'Ruiz', 'Méndez'),
(283, 'Berta', 'González', 'Ramírez'),
(284, 'César', 'Serrano', 'García'),
(285, 'Diana', 'Pérez', 'Torres'),
(286, 'Esteban', 'Méndez', 'González'),
(287, 'Federico', 'Vargas', 'Ramírez'),
(288, 'Gabriela', 'Serrano', 'López'),
(289, 'Héctor', 'Ibarra', 'Morales'),
(290, 'Ivette', 'Vega', 'Paredes'),
(291, 'Joaquín', 'González', 'Serrano'),
(292, 'Karina', 'Pérez', 'Torres'),
(293, 'Luis', 'García', 'Serrano'),
(294, 'Margarita', 'Serrano', 'Ibarra'),
(295, 'Natividad', 'Torres', 'Figueroa'),
(296, 'Olga', 'Sánchez', 'Ramírez'),
(297, 'Pablo', 'González', 'Vega'),
(298, 'Raúl', 'Paredes', 'Morales'),
(299, 'Sonia', 'González', 'Pérez'),
(300, 'Tomasina', 'Méndez', 'Serrano'),
(301, 'Ubaldo', 'Ramírez', 'González'),
(302, 'Valeria', 'Paredes', 'Figueroa'),
(303, 'Wendy', 'Vega', 'Martínez'),
(304, 'Ximena', 'Moreno', 'Ibarra'),
(305, 'Yamila', 'Serrano', 'Vega'),
(306, 'Zenaida', 'Paredes', 'Ramírez'),
(307, 'Alejandra', 'Ibarra', 'Pérez'),
(308, 'Bernardo', 'Torres', 'Serrano'),
(309, 'Claudia', 'Figueroa', 'Sánchez'),
(310, 'David', 'González', 'Ibarra'),
(311, 'Estela', 'Cordero', 'Vega'),
(312, 'Francisco', 'González', 'Pérez'),
(313, 'Guadalupe', 'Ibarra', 'Torres'),
(314, 'Horacio', 'Pérez', 'Méndez'),
(315, 'Iván', 'Sánchez', 'Vega'),
(316, 'Julia', 'Serrano', 'Martínez'),
(317, 'Kevin', 'Ramírez', 'Serrano'),
(318, 'Luz', 'Torres', 'González'),
(319, 'Miguel', 'Serrano', 'Pérez'),
(320, 'Nicolás', 'Ibarra', 'Morales'),
(321, 'Óscar', 'Figueroa', 'Cordero'),
(322, 'Paola', 'Vega', 'Ramírez'),
(323, 'Raul', 'Martínez', 'Torres'),
(324, 'Sonia', 'Vargas', 'Martínez'),
(325, 'Teodoro', 'González', 'Figueroa'),
(326, 'Ursula', 'Serrano', 'Pérez'),
(327, 'Violeta', 'Méndez', 'González'),
(328, 'Walter', 'Ibarra', 'Martínez'),
(329, 'Ximena', 'Torres', 'Paredes'),
(330, 'Yolanda', 'González', 'Torres'),
(331, 'Zoraida', 'García', 'Serrano'),
(332, 'Adriana', 'Torres', 'González'),
(333, 'Blanca', 'Pérez', 'González'),
(334, 'Carlos', 'González', 'Torres'),
(335, 'Diana', 'Ibarra', 'Méndez'),
(336, 'Eduardo', 'Ramírez', 'Serrano'),
(337, 'Fabiola', 'Serrano', 'Pérez'),
(338, 'Gabriel', 'Martínez', 'González'),
(339, 'Hugo', 'Vargas', 'Ramírez'),
(340, 'Ismael', 'Torres', 'Serrano'),
(341, 'Juan', 'Pérez', 'Martínez'),
(342, 'Karina', 'González', 'Paredes'),
(343, 'Lidia', 'Serrano', 'Martínez'),
(344, 'María', 'Ramírez', 'Pérez'),
(345, 'Nina', 'Figueroa', 'González'),
(346, 'Oscar', 'Morales', 'Pérez'),
(347, 'Patricia', 'Martínez', 'Serrano'),
(348, 'Raúl', 'Vega', 'González'),
(349, 'Sofia', 'Moreno', 'Torres'),
(350, 'Teodoro', 'Serrano', 'Ramírez'),
(351, 'Ubaldo', 'Pérez', 'González'),
(352, 'Víctor', 'Serrano', 'Vega'),
(353, 'Wendy', 'Vargas', 'Pérez'),
(354, 'Ximena', 'Serrano', 'Ibarra'),
(355, 'Yamila', 'Vega', 'González'),
(356, 'Zoe', 'González', 'Ramírez'),
(357, 'Alberto', 'Jaramillo', 'Olmos'),
(358, 'Fernando', 'Olivares', 'Orozco'),
(360, 'Cecilia', 'Ordoñez', 'Pérez');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_prov`
--

CREATE TABLE `compras_prov` (
  `id_comprasprov` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `total_compra` decimal(12,2) NOT NULL,
  `folio` varchar(45) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `estado` enum('activa','cancelada') NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `compras_prov`
--

INSERT INTO `compras_prov` (`id_comprasprov`, `id_proveedor`, `fecha`, `total_compra`, `folio`, `id_usuario`, `estado`) VALUES
(1, 7, '2025-04-09 22:03:11', '111.00', '', 6, 'activa'),
(2, 10, '2025-04-09 22:44:35', '15.50', '', 6, 'activa'),
(3, 9, '2025-04-09 23:40:45', '275.00', '', 6, 'activa'),
(4, 10, '2025-04-09 23:42:12', '55.00', '', 6, 'activa'),
(5, 8, '2025-04-10 00:13:04', '355.00', '', 6, 'activa'),
(6, 12, '2025-04-10 00:21:58', '31.00', '', 6, 'activa'),
(7, 10, '2025-04-10 00:22:38', '175.50', '', 6, 'activa'),
(8, 17, '2025-04-10 00:25:33', '22.50', '', 6, 'activa'),
(9, 13, '2025-04-10 00:26:31', '35.00', '', 6, 'activa'),
(10, 18, '2025-04-10 00:29:56', '16.50', '', 6, 'activa'),
(11, 144, '2025-04-10 00:33:11', '85.50', '', 6, 'activa'),
(12, 7, '2025-04-10 16:12:49', '8.50', '0012', 6, 'activa'),
(13, 7, '2025-04-10 16:13:13', '8.50', '0013', 6, 'activa'),
(14, 4, '2025-04-12 00:03:40', '5.50', '0014', 6, 'activa'),
(15, 13, '2025-04-15 10:04:30', '114.00', '0015', 7, 'cancelada'),
(16, 9, '2025-04-15 10:13:04', '400.00', '0016', 7, 'activa'),
(17, 41, '2025-04-15 10:18:06', '75.00', '0017', 7, 'activa'),
(18, 23, '2025-04-15 10:24:04', '127.50', '0018', 7, 'activa'),
(19, 10, '2025-04-16 10:07:03', '382.50', '0019', 7, 'activa'),
(20, 147, '2025-04-16 10:36:42', '195.00', '0020', 7, 'activa'),
(21, 53, '2025-04-21 12:01:45', '215.00', '0021', 7, 'activa'),
(22, 53, '2025-04-21 12:01:47', '215.00', '0022', 7, 'activa'),
(23, 53, '2025-04-21 12:02:05', '215.00', '0023', 7, 'activa'),
(24, 25, '2025-04-21 13:55:18', '20.00', '0024', 7, 'activa'),
(25, 10, '2025-04-21 14:00:18', '92.50', '0025', 7, 'activa'),
(26, 54, '2025-04-21 14:19:07', '115.00', '0026', 7, 'activa'),
(27, 25, '2025-04-21 14:24:25', '55.00', '0027', 7, 'activa'),
(28, 38, '2025-04-21 14:34:07', '31.00', '0028', 7, 'activa'),
(29, 43, '2025-04-21 13:42:19', '47.00', '0029', 7, 'activa'),
(30, 43, '2025-04-21 13:42:22', '47.00', '0030', 7, 'activa'),
(31, 43, '2025-04-21 13:42:26', '47.00', '0031', 7, 'activa'),
(32, 43, '2025-04-21 14:45:38', '47.00', '0032', 7, 'activa'),
(33, 9, '2025-04-25 13:17:58', '90.00', '0033', 7, 'activa'),
(34, 9, '2025-04-25 13:18:57', '90.00', '0034', 7, 'activa'),
(36, 14, '2025-04-25 14:53:31', '22.50', '0035', 7, 'activa'),
(37, 14, '2025-04-25 14:55:14', '22.50', '0037', 7, 'activa'),
(38, 14, '2025-04-25 14:59:00', '22.50', '0038', 7, 'activa'),
(39, 14, '2025-04-25 15:03:39', '22.50', '0039', 7, 'activa'),
(40, 14, '2025-04-25 15:08:39', '22.50', '0040', 7, 'activa'),
(41, 14, '2025-04-25 15:10:09', '22.50', '0041', 7, 'activa'),
(42, 14, '2025-04-26 11:15:17', '22.50', '0042', 7, 'activa'),
(43, 1, '2025-04-26 13:18:30', '90.00', '0043', 7, 'activa'),
(44, 13, '2025-04-26 13:40:45', '35.00', '0044', 7, 'activa'),
(45, 95, '2025-04-26 13:54:07', '205.00', '0045', 7, 'activa'),
(46, 95, '2025-04-26 13:56:37', '78.00', '0046', 7, 'activa'),
(47, 33, '2025-04-28 14:50:51', '8550.00', '0047', 7, 'activa'),
(48, 33, '2025-04-28 14:54:16', '175.00', '0048', 7, 'activa'),
(49, 95, '2025-04-29 14:28:21', '875.00', '0049', 7, 'activa'),
(50, 2, '2025-05-05 09:41:43', '1250.00', '0050', 8, 'activa'),
(51, 38, '2025-05-05 09:55:33', '3960.00', '0051', 8, 'activa'),
(52, 9, '2025-05-05 10:11:36', '37.50', '0052', 8, 'activa'),
(53, 59, '2025-05-05 13:53:06', '350.00', '0053', 8, 'activa'),
(54, 29, '2025-05-06 15:14:29', '982.50', '0054', 348, 'cancelada'),
(55, 9, '2025-05-08 15:15:25', '277.50', '0055', 6, 'activa'),
(56, 2, '2025-05-08 15:17:41', '287.50', '0056', 6, 'activa'),
(57, 7, '2025-05-09 22:49:11', '315.00', '0057', 8, 'cancelada'),
(59, 2, '2025-05-09 23:16:37', '187.50', '0058', 8, 'activa'),
(60, 8, '2025-05-10 00:08:11', '260.00', '0060', 8, 'activa'),
(61, 17, '2025-05-10 00:13:05', '107.50', '0061', 8, 'activa'),
(62, 10, '2025-05-12 16:40:06', '45.50', '0062', 6, 'activa'),
(63, 9, '2025-05-12 16:58:18', '175.00', '0063', 6, 'activa'),
(64, 16, '2025-05-12 17:16:39', '450.00', '0064', 6, 'activa'),
(65, 27, '2025-05-12 18:16:30', '155.00', '0065', 6, 'activa'),
(66, 140, '2025-05-13 15:38:24', '440.00', '0066', 6, 'activa');

--
-- Disparadores `compras_prov`
--
DELIMITER $$
CREATE TRIGGER `registrar_retiro_actualizado` AFTER UPDATE ON `compras_prov` FOR EACH ROW BEGIN
    DECLARE nombre_prov VARCHAR(100);
    
    IF NEW.total_compra > 0 AND (OLD.total_compra = 0 OR OLD.total_compra IS NULL) THEN
        
        SELECT nombre INTO nombre_prov
        FROM proveedores
        WHERE id_proveedor = NEW.id_proveedor;
        
        
        INSERT INTO caja_movimientos (
            tipo,
            monto,
            fecha_hora,
            id_usuario,
            id_comprasprov,
            descripcion,
            estado
        ) VALUES (
            'retiro',
            NEW.total_compra,
            NOW(),
            NEW.id_usuario,
            NEW.id_comprasprov,
            CONCAT('Compra a ', nombre_prov),
            'activo'
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_prov_backup`
--

CREATE TABLE `compras_prov_backup` (
  `id_comprasprov` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `total_compra` decimal(12,2) NOT NULL,
  `num_factura` varchar(20) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `estado` enum('activa','cancelada') NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `compras_prov_backup`
--

INSERT INTO `compras_prov_backup` (`id_comprasprov`, `id_proveedor`, `fecha`, `total_compra`, `num_factura`, `id_usuario`, `estado`) VALUES
(1, 2, '2025-02-26 18:13:37', '0.00', NULL, 2, 'activa'),
(4, 1, '2025-04-08 13:06:00', '500.00', NULL, 1, 'activa'),
(6, 8, '2025-04-08 14:10:09', '22.00', '', 6, 'activa'),
(7, 8, '2025-04-08 14:10:17', '22.00', '', 6, 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_compra`
--

CREATE TABLE `det_compra` (
  `id_detcompra` int(11) NOT NULL,
  `id_comprasprov` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `precio_uni` float(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `porcentaje_ganancia` decimal(5,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `importe` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `det_compra`
--

INSERT INTO `det_compra` (`id_detcompra`, `id_comprasprov`, `id_producto`, `precio_uni`, `precio_venta`, `porcentaje_ganancia`, `cantidad`, `importe`) VALUES
(1, 1, 427, 18.50, '20.00', '8.10', '6.00', '111.00'),
(2, 2, 433, 15.50, '20.00', '29.00', '1.00', '15.50'),
(3, 3, 434, 27.50, '34.00', '23.60', '10.00', '275.00'),
(4, 4, 443, 20.00, '26.00', '30.00', '1.00', '20.00'),
(5, 4, 607, 35.00, '40.00', '14.30', '1.00', '35.00'),
(6, 5, 431, 35.50, '40.00', '12.70', '10.00', '355.00'),
(7, 6, 442, 15.50, '17.00', '9.70', '2.00', '31.00'),
(8, 7, 437, 175.50, '193.00', '10.00', '1.00', '175.50'),
(9, 8, 439, 22.50, '25.00', '11.10', '1.00', '22.50'),
(10, 9, 728, 35.00, '39.00', '11.40', '1.00', '35.00'),
(11, 10, 670, 16.50, '18.00', '9.10', '1.00', '16.50'),
(12, 11, 569, 85.50, '96.00', '12.30', '1.00', '85.50'),
(20, 12, 645, 8.50, '11.00', '29.40', '1.00', '8.50'),
(21, 13, 645, 8.50, '11.00', '29.40', '1.00', '8.50'),
(22, 14, 651, 5.50, '7.00', '27.30', '1.00', '5.50'),
(23, 15, 434, 28.50, '37.00', '29.80', '4.00', '114.00'),
(24, 16, 473, 20.00, '22.00', '10.00', '20.00', '400.00'),
(25, 17, 643, 5.00, '6.50', '30.00', '15.00', '75.00'),
(26, 18, 495, 25.50, '33.00', '29.40', '5.00', '127.50'),
(27, 19, 561, 25.50, '33.00', '29.40', '15.00', '382.50'),
(28, 20, 688, 19.50, '23.00', '17.90', '10.00', '195.00'),
(32, 24, 427, 20.00, '20.00', '0.00', '1.00', '20.00'),
(40, 36, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(41, 37, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(42, 38, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(43, 39, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(44, 40, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(45, 41, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(46, 42, 469, 22.50, '29.00', '28.89', '1.00', '22.50'),
(47, 43, 504, 22.50, '29.00', '28.89', '4.00', '90.00'),
(48, 44, 497, 17.50, '23.00', '31.43', '2.00', '35.00'),
(49, 45, 740, 20.50, '27.00', '31.71', '10.00', '205.00'),
(50, 46, 740, 19.50, '25.00', '28.21', '4.00', '78.00'),
(51, 47, 569, 85.50, '111.00', '29.82', '100.00', '8550.00'),
(52, 48, 569, 87.50, '114.00', '30.29', '2.00', '175.00'),
(53, 49, 741, 17.50, '23.00', '31.43', '50.00', '875.00'),
(54, 50, 452, 12.50, '16.00', '28.00', '100.00', '1250.00'),
(55, 51, 447, 198.00, '228.00', '15.15', '20.00', '3960.00'),
(56, 52, 472, 12.50, '16.00', '28.00', '3.00', '37.50'),
(57, 53, 427, 17.50, '25.00', '42.86', '20.00', '350.00'),
(58, 54, 460, 22.50, '30.00', '33.33', '12.00', '270.00'),
(59, 54, 435, 28.50, '34.00', '19.30', '25.00', '712.50'),
(60, 55, 476, 18.50, '25.00', '35.14', '15.00', '277.50'),
(61, 56, 452, 11.50, '15.00', '30.43', '25.00', '287.50'),
(62, 57, 435, 31.50, '38.00', '20.63', '10.00', '315.00'),
(70, 64, 467, 45.00, '60.00', '33.33', '10.00', '450.00'),
(71, 65, 427, 15.50, '22.00', '41.94', '10.00', '155.00'),
(72, 66, 744, 22.00, '24.00', '9.09', '20.00', '440.00');

--
-- Disparadores `det_compra`
--
DELIMITER $$
CREATE TRIGGER `actualizar_producto_despues_detalle` AFTER INSERT ON `det_compra` FOR EACH ROW BEGIN
    IF EXISTS (SELECT 1 FROM productos WHERE id_producto = NEW.id_producto) THEN
        UPDATE productos
        SET
            precio = NEW.precio_venta,
            cantidad = IFNULL(cantidad, 0) + NEW.cantidad
        WHERE id_producto = NEW.id_producto;

        INSERT INTO log_trigger (mensaje, fecha)
        VALUES (CONCAT('Producto ', NEW.id_producto, ' actualizado con precio ', NEW.precio_venta), NOW());
    ELSE
        INSERT INTO log_trigger (mensaje, fecha)
        VALUES (CONCAT('Producto no encontrado: ', NEW.id_producto), NOW());
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `actualizar_total_compra` AFTER INSERT ON `det_compra` FOR EACH ROW BEGIN
    UPDATE compras_prov 
    SET total_compra = (
        SELECT SUM(cantidad * precio_uni) 
        FROM det_compra
        WHERE id_comprasprov = NEW.id_comprasprov
    )
    WHERE id_comprasprov = NEW.id_comprasprov;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `actualizar_total_delete` AFTER DELETE ON `det_compra` FOR EACH ROW BEGIN
    UPDATE compras_prov 
    SET total_compra = (
        SELECT SUM(cantidad * precio_compra)
        FROM det_compra
        WHERE id_comprasprov = OLD.id_comprasprov
    )
    WHERE id_comprasprov = OLD.id_comprasprov;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `actualizar_total_update` AFTER UPDATE ON `det_compra` FOR EACH ROW BEGIN
    UPDATE compras_prov 
    SET total_compra = (
        SELECT SUM(cantidad * precio_compra)
        FROM det_compra
        WHERE id_comprasprov = NEW.id_comprasprov
    )
    WHERE id_comprasprov = NEW.id_comprasprov;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_compra_backup`
--

CREATE TABLE `det_compra_backup` (
  `id_detcompra` int(11) NOT NULL,
  `id_comprasprov` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `precio_uni` float(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `porcentaje_ganancia` decimal(5,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `importe` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `det_compra_backup`
--

INSERT INTO `det_compra_backup` (`id_detcompra`, `id_comprasprov`, `id_producto`, `precio_uni`, `precio_venta`, `porcentaje_ganancia`, `cantidad`, `importe`) VALUES
(2, 6, 429, 22.00, '28.60', '30.00', '1.00', '22.00'),
(3, 7, 429, 22.00, '28.60', '30.00', '1.00', '22.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_venta`
--

CREATE TABLE `det_venta` (
  `id_detventa` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` double DEFAULT NULL,
  `precio_uni` double DEFAULT NULL,
  `importe` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `det_venta`
--

INSERT INTO `det_venta` (`id_detventa`, `id_venta`, `id_producto`, `cantidad`, `precio_uni`, `importe`) VALUES
(1, 1, 433, 1, 20, NULL),
(2, 1, 434, 1, 18, NULL),
(3, 2, 433, 1, 20, NULL),
(4, 2, 434, 1, 18, NULL),
(5, 3, 431, 2, 40, NULL),
(6, 4, 436, 1, 45, NULL),
(7, 4, 438, 1, 65, NULL),
(8, 5, 434, 1, 34, NULL),
(9, 6, 427, 1, 20, NULL),
(10, 7, 427, 1, 20, NULL),
(11, 8, 428, 1, 28, NULL),
(12, 9, 430, 1, 15, NULL),
(13, 10, 440, 1, 15, NULL),
(14, 11, 433, 1, 20, NULL),
(15, 12, 509, 5, 18, NULL),
(16, 13, 740, 1, 18.5, NULL),
(17, 14, 740, 3, 18.5, NULL),
(18, 15, 740, 10, 18.5, NULL),
(19, 16, 741, 8, 20, NULL),
(20, 17, 434, 1, 37, NULL),
(21, 18, 451, 1, 16, NULL),
(22, 18, 484, 1, 50, NULL),
(23, 19, 576, 4, 40, NULL),
(24, 20, 435, 1, 34, NULL),
(25, 21, 441, 2, 85, NULL),
(26, 24, 435, 2, 34, 0),
(27, 25, 513, 2, 30, 0),
(28, 25, 524, 3, 45, 0),
(29, 26, 740, 9, 25, 0),
(30, 27, 427, 5, 25, 0),
(31, 28, 740, 25, 25, 0),
(32, 29, 434, 12, 37, 0),
(33, 30, 427, 89999999998, 25, 0),
(34, 31, 427, 10, 25, 0),
(35, 32, 431, 5, 60, 0),
(36, 32, 436, 2, 45, 0),
(37, 32, 454, 2, 30, 0);

--
-- Disparadores `det_venta`
--
DELIMITER $$
CREATE TRIGGER `restar_cantidad_producto` AFTER INSERT ON `det_venta` FOR EACH ROW BEGIN
  UPDATE productos
  SET cantidad = cantidad - NEW.cantidad
  WHERE id_producto = NEW.id_producto;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_trigger`
--

CREATE TABLE `log_trigger` (
  `id_log` int(11) NOT NULL,
  `mensaje` text,
  `fecha` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `log_trigger`
--

INSERT INTO `log_trigger` (`id_log`, `mensaje`, `fecha`) VALUES
(9, 'Producto 3 actualizado con precio 15.00', '2025-04-08 22:53:30'),
(10, 'Producto 433 actualizado con precio 30.00', '2025-04-08 22:55:19'),
(11, 'Producto 433 actualizado con precio 36.00', '2025-04-08 22:56:35'),
(12, 'Producto 645 actualizado con precio 11.00', '2025-04-09 14:54:33'),
(13, 'Producto 428 actualizado con precio 72.00', '2025-04-09 14:57:40'),
(14, 'Producto 427 actualizado con precio 29.00', '2025-04-09 15:24:38'),
(15, 'Producto 427 actualizado con precio 20.00', '2025-04-09 18:49:39'),
(16, 'Producto 427 actualizado con precio 18.00', '2025-04-09 18:51:11'),
(17, 'Producto 428 actualizado con precio 28.00', '2025-04-09 19:18:01'),
(18, 'Producto 427 actualizado con precio 31.00', '2025-04-09 20:47:36'),
(19, 'Producto 432 actualizado con precio 20.00', '2025-04-09 20:49:21'),
(20, 'Producto 427 actualizado con precio 20.00', '2025-04-09 21:03:11'),
(21, 'Producto 433 actualizado con precio 20.00', '2025-04-09 21:44:35'),
(22, 'Producto 434 actualizado con precio 34.00', '2025-04-09 22:40:45'),
(23, 'Producto 443 actualizado con precio 26.00', '2025-04-09 22:42:12'),
(24, 'Producto 607 actualizado con precio 40.00', '2025-04-09 22:42:12'),
(25, 'Producto 431 actualizado con precio 40.00', '2025-04-09 23:13:04'),
(26, 'Producto 442 actualizado con precio 17.00', '2025-04-09 23:21:58'),
(27, 'Producto 437 actualizado con precio 193.00', '2025-04-09 23:22:38'),
(28, 'Producto 439 actualizado con precio 25.00', '2025-04-09 23:25:34'),
(29, 'Producto 728 actualizado con precio 39.00', '2025-04-09 23:26:31'),
(30, 'Producto 670 actualizado con precio 18.00', '2025-04-09 23:29:56'),
(31, 'Producto 569 actualizado con precio 96.00', '2025-04-09 23:33:11'),
(32, 'Producto 645 actualizado con precio 11.00', '2025-04-10 15:12:50'),
(33, 'Producto 645 actualizado con precio 11.00', '2025-04-10 15:13:13'),
(34, 'Producto 651 actualizado con precio 7.00', '2025-04-11 23:03:40'),
(35, 'Producto 434 actualizado con precio 37.00', '2025-04-15 09:04:30'),
(36, 'Producto 473 actualizado con precio 22.00', '2025-04-15 09:13:04'),
(37, 'Producto 643 actualizado con precio 6.50', '2025-04-15 09:18:06'),
(38, 'Producto 495 actualizado con precio 33.00', '2025-04-15 09:24:04'),
(39, 'Producto 561 actualizado con precio 33.00', '2025-04-16 09:07:03'),
(40, 'Producto 688 actualizado con precio 23.00', '2025-04-16 09:36:42'),
(41, 'Producto 427 actualizado con precio 20.00', '2025-04-21 12:55:18'),
(42, 'Producto 469 actualizado con precio 29.00', '2025-04-25 14:53:31'),
(43, 'Producto 469 actualizado con precio 29.00', '2025-04-25 14:55:14'),
(44, 'Producto 469 actualizado con precio 29.00', '2025-04-25 14:59:00'),
(45, 'Producto 469 actualizado con precio 29.00', '2025-04-25 15:03:39'),
(46, 'Producto 469 actualizado con precio 29.00', '2025-04-25 15:08:39'),
(47, 'Producto 469 actualizado con precio 29.00', '2025-04-25 15:10:09'),
(48, 'Producto 469 actualizado con precio 29.00', '2025-04-26 11:15:17'),
(49, 'Producto 504 actualizado con precio 29.00', '2025-04-26 13:18:30'),
(50, 'Producto 497 actualizado con precio 23.00', '2025-04-26 13:40:45'),
(51, 'Producto 740 actualizado con precio 27.00', '2025-04-26 13:54:07'),
(52, 'Producto 740 actualizado con precio 25.00', '2025-04-26 13:56:37'),
(53, 'Producto 569 actualizado con precio 111.00', '2025-04-28 14:50:51'),
(54, 'Producto 569 actualizado con precio 114.00', '2025-04-28 14:54:16'),
(55, 'Producto 741 actualizado con precio 23.00', '2025-04-29 14:28:21'),
(56, 'Producto 452 actualizado con precio 16.00', '2025-05-05 09:41:43'),
(57, 'Producto 447 actualizado con precio 228.00', '2025-05-05 09:55:33'),
(58, 'Producto 472 actualizado con precio 16.00', '2025-05-05 10:11:36'),
(59, 'Producto 427 actualizado con precio 25.00', '2025-05-05 13:53:06'),
(60, 'Producto 460 actualizado con precio 30.00', '2025-05-06 15:14:29'),
(61, 'Producto 435 actualizado con precio 34.00', '2025-05-06 15:14:29'),
(62, 'Producto 476 actualizado con precio 25.00', '2025-05-08 15:15:25'),
(63, 'Producto 452 actualizado con precio 15.00', '2025-05-08 15:17:42'),
(64, 'Producto 435 actualizado con precio 38.00', '2025-05-09 22:49:11'),
(71, 'Producto 467 actualizado con precio 60.00', '2025-05-12 17:16:39'),
(72, 'Producto 427 actualizado con precio 22.00', '2025-05-12 18:16:30'),
(73, 'Producto 744 actualizado con precio 24.00', '2025-05-13 15:38:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `baja_alta` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `id_categoria` int(11) NOT NULL,
  `codigo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio`, `precio_compra`, `cantidad`, `stock`, `baja_alta`, `id_categoria`, `codigo`) VALUES
(3, 'Producto de prueba', '15.00', '11.25', '22.00', 12, 'activo', 1, 'P003'),
(427, 'Salsa picante valentina etiqueta amarilla 370ml', '22.00', '15.50', '99999999.99', 10, 'activo', 178, '427'),
(428, 'Aceite de oliva virgen extra Carbonell 500ml', '28.00', '17.50', '30.00', 10, 'activo', 97, '428'),
(429, 'Arroz integral Great Value 1kg', '25.00', '20.00', '40.00', 10, 'activo', 100, '429'),
(430, 'Atún Dolores en agua 140g', '15.00', '9.50', '40.00', 10, 'activo', 28, '430'),
(431, 'Café soluble Nescafé Clásico 200g', '60.00', '32.50', '9.00', 10, 'activo', 33, '431'),
(432, 'Galletas Oreo 355g', '20.00', '18.50', '20.00', 10, 'activo', 78, '432'),
(433, 'Jugo de frutas Del Valle mango 1L', '20.00', '13.00', '20.00', 10, 'activo', 81, '433'),
(434, 'Leche entera Lala 1L', '37.00', '21.50', '-1.00', 10, 'activo', 44, '434'),
(435, 'Pan integral Bimbo 680g', '38.00', '31.50', '60.00', 10, 'activo', 254, '435'),
(436, 'Salsa BBQ Heinz 538g', '45.00', '32.50', '38.00', 10, 'activo', 264, '436'),
(437, 'Tequila José Cuervo Tradicional 700ml', '193.00', '144.75', '20.00', 10, 'activo', 56, '437'),
(438, 'Aceite de oliva virgen extra Carbonell 500ml', '65.00', '48.75', '20.00', 10, 'activo', 97, '438'),
(439, 'Arroz integral Great Value 1kg', '25.00', '18.75', '20.00', 10, 'activo', 100, '439'),
(440, 'Atún Dolores en agua 140g', '15.00', '11.25', '20.00', 10, 'activo', 28, '440'),
(441, 'Café soluble Nescafé Clásico 200g', '85.00', '63.75', '20.00', 10, 'activo', 33, '441'),
(442, 'Galletas Oreo 354g', '17.00', '12.75', '20.00', 10, 'activo', 78, '442'),
(443, 'Jugo de frutas Del Valle mango 1L', '26.00', '19.50', '20.00', 10, 'activo', 81, '443'),
(444, 'Leche entera Lala 1L', '18.00', '13.50', '20.00', 10, 'activo', 44, '444'),
(445, 'Pan integral Bimbo 680g', '34.00', '25.50', '20.00', 10, 'activo', 254, '445'),
(447, 'Tequila José Cuervo Tradicional 700ml', '228.00', '198.00', '21.00', 10, 'activo', 56, '447'),
(448, 'Azúcar refinada Zulka 1kg', '20.00', '15.00', '80.00', 70, 'activo', 29, '448'),
(449, 'Chocolate abuelita en tablillas 540g', '90.00', '67.50', '45.00', 35, 'activo', 70, '449'),
(450, 'Desinfectante Cloralex 1L', '30.00', '22.50', '35.00', 25, 'activo', 50, '450'),
(451, 'Frijoles refritos Isadora 430g', '16.00', '12.00', '50.00', 40, 'activo', 27, '451'),
(452, 'Galletas Marías Gamesa 170g', '15.00', '11.50', '126.00', 80, 'activo', 78, '452'),
(453, 'Mermelada de fresa Smucker\'s 340g', '45.00', '33.75', '45.00', 35, 'activo', 252, '453'),
(454, 'Pasta Fusilli Barilla 500g', '30.00', '22.50', '58.00', 50, 'activo', 49, '454'),
(455, 'Leche evaporada Carnation Clavel 360g', '18.00', '13.50', '70.00', 60, 'activo', 163, '455'),
(456, 'Cereal Choco Krispis 650g', '55.00', '41.25', '50.00', 40, 'activo', 34, '456'),
(457, 'Lentejas La Moderna 500g', '20.00', '15.00', '80.00', 70, 'activo', 109, '457'),
(458, 'Helado de vainilla Häagen-Dazs 473ml', '90.00', '67.50', '35.00', 25, 'activo', 43, '458'),
(459, 'Yogurt griego Oikos natural 150g', '18.00', '13.50', '55.00', 45, 'activo', 273, '459'),
(460, 'Pan de caja Bimbo blanco 680g', '29.00', '21.50', '82.00', 60, 'activo', 48, '460'),
(461, 'Refresco Coca-Cola sin azúcar 355ml', '15.00', '11.25', '88.00', 78, 'activo', 177, '461'),
(462, 'Atún Tuny en aceite 140g', '15.00', '11.25', '45.00', 35, 'activo', 28, '462'),
(463, 'Arroz blanco La Merced 1kg', '22.50', '16.88', '60.00', 50, 'activo', 134, '463'),
(465, 'Jugo de naranja Del Valle 1L', '25.00', '18.75', '65.00', 55, 'activo', 81, '465'),
(466, 'Galletas Marías Gamesa 170g', '12.00', '9.00', '80.00', 70, 'activo', 78, '466'),
(467, 'Queso panela Lala 400g', '60.00', '45.00', '45.00', 25, 'activo', 44, '467'),
(468, 'Salsa de tomate Hunt\'s 680g', '30.00', '22.50', '60.00', 50, 'activo', 53, '468'),
(469, 'Tortillas de maíz Maseca 1kg', '29.00', '21.75', '90.00', 80, 'activo', 58, '469'),
(470, 'Refresco Pepsi 355ml', '12.00', '9.00', '110.00', 100, 'activo', 52, '470'),
(471, 'Café soluble Nescafé Dolca 100g', '40.00', '30.00', '70.00', 60, 'activo', 33, '471'),
(472, 'Frijoles negros La Costeña 560g', '16.00', '12.50', '45.00', 35, 'activo', 27, '472'),
(473, 'Mantequilla Lala sin sal 200g', '22.00', '16.50', '55.00', 45, 'activo', 250, '473'),
(474, 'Sardinas en tomate Dolores 200g', '18.00', '13.50', '80.00', 70, 'activo', 28, '474'),
(475, 'Cereal Corn Flakes Kellogg\'s 720g', '40.00', '30.00', '60.00', 50, 'activo', 34, '475'),
(476, 'Mermelada de chabacano La Costeña 250g', '25.00', '18.50', '55.00', 30, 'activo', 252, '476'),
(477, 'Galletas Ritz 300g', '28.00', '21.00', '65.00', 55, 'activo', 77, '477'),
(478, 'Queso manchego Lala 250g', '60.00', '45.00', '35.00', 25, 'activo', 44, '478'),
(479, 'Jabón líquido Dove 1L', '45.00', '33.75', '50.00', 40, 'activo', 108, '479'),
(480, 'Té negro Lipton 100 sobres', '50.00', '37.50', '80.00', 70, 'activo', 57, '480'),
(481, 'Mermelada de fresa Smucker\'s 340g', '45.00', '33.75', '45.00', 35, 'activo', 252, '481'),
(482, 'Pasta Fusilli Barilla 500g', '30.00', '22.50', '60.00', 50, 'activo', 49, '482'),
(483, 'Chocolate oscuro Hershey\'s 120g', '35.00', '26.25', '50.00', 40, 'activo', 284, '483'),
(484, 'Cereal integral Kellogg\'s All-Bran 440g', '50.00', '37.50', '40.00', 30, 'activo', 283, '484'),
(485, 'Galletas veganas Sarchio 250g', '60.00', '45.00', '30.00', 20, 'activo', 208, '485'),
(486, 'Leche de almendra Silk 1L', '45.00', '33.75', '35.00', 25, 'activo', 246, '486'),
(487, 'Pistaches Wonderful 200g', '80.00', '60.00', '45.00', 35, 'activo', 174, '487'),
(488, 'Yogurt vegano Silk natural 150g', '30.00', '22.50', '60.00', 50, 'activo', 222, '488'),
(489, 'Frutas deshidratadas Great Value 200g', '45.00', '33.75', '50.00', 40, 'activo', 206, '489'),
(490, 'Té herbal Twinings 20 sobres', '70.00', '52.50', '40.00', 30, 'activo', 269, '490'),
(491, 'Miel orgánica Miel Mexicana 500g', '80.00', '60.00', '30.00', 20, 'activo', 300, '491'),
(492, 'Queso manchego Fud 300g', '75.00', '56.25', '35.00', 25, 'activo', 44, '492'),
(493, 'Pan integral Oroweat 680g', '50.00', '37.50', '70.00', 60, 'activo', 254, '493'),
(494, 'Helado vegano Ben & Jerry\'s 473ml', '100.00', '75.00', '25.00', 15, 'activo', 243, '494'),
(495, 'Sardinas en aceite Herdez 190g', '33.00', '24.75', '50.00', 40, 'activo', 28, '495'),
(496, 'Salsa verde La Costeña 360g', '21.00', '15.50', '40.00', 30, 'activo', 53, '496'),
(497, 'Galletas de avena Quaker 144g', '23.00', '17.25', '60.00', 50, 'activo', 78, '497'),
(498, 'Refresco de cola Pepsi light 355ml', '15.00', '11.25', '110.00', 100, 'activo', 177, '498'),
(499, 'Queso cottage Lala 200g', '35.00', '26.25', '40.00', 30, 'activo', 44, '499'),
(500, 'Tortillas de harina Mission 650g', '28.00', '21.00', '70.00', 60, 'activo', 270, '500'),
(501, 'Refresco Fanta naranja 355ml', '12.00', '9.00', '110.00', 100, 'activo', 52, '501'),
(502, 'Pasta spaghetti Barilla 500g', '30.00', '22.50', '60.00', 50, 'activo', 49, '502'),
(503, 'Aceitunas verdes La Española 300g', '45.00', '33.75', '44.00', 34, 'activo', 60, '503'),
(504, 'Galletas saladas Crackers 200g', '29.00', '21.75', '90.00', 80, 'activo', 77, '504'),
(505, 'Leche de coco Aroy-D 1L', '60.00', '45.00', '44.00', 34, 'activo', 246, '505'),
(506, 'Cereal Cheerios 500g', '55.00', '41.25', '55.00', 45, 'activo', 34, '506'),
(507, 'Mermelada de moras La Vieja Fábrica 350g', '50.00', '37.50', '35.00', 25, 'activo', 252, '507'),
(508, 'Jugo de manzana Boing 1L', '20.00', '15.00', '80.00', 70, 'activo', 81, '508'),
(509, 'Galletas María McVitie\'s 200g', '18.00', '13.50', '60.00', 50, 'activo', 78, '509'),
(510, 'Café de grano Starbucks 250g', '120.00', '90.00', '40.00', 30, 'activo', 33, '510'),
(511, 'Atún Herdez en agua 170g', '25.00', '18.75', '50.00', 40, 'activo', 28, '511'),
(512, 'Refresco Sprite sin azúcar 355ml', '15.00', '11.25', '110.00', 100, 'activo', 177, '512'),
(513, 'Lentejas Premium La Asturiana 500g', '30.00', '22.50', '70.00', 60, 'activo', 109, '513'),
(514, 'Pan integral Oroweat 680g', '50.00', '37.50', '70.00', 60, 'activo', 254, '514'),
(515, 'Helado vegano Ben & Jerry\'s 473ml', '100.00', '75.00', '25.00', 15, 'activo', 243, '515'),
(516, 'Sardinas en aceite Herdez 190g', '22.00', '16.50', '50.00', 40, 'activo', 28, '516'),
(517, 'Salsa verde La Costeña 360g', '18.00', '13.50', '40.00', 30, 'activo', 53, '517'),
(518, 'Galletas de avena Quaker 144g', '25.00', '18.75', '60.00', 50, 'activo', 78, '518'),
(519, 'Refresco de cola Pepsi light 355ml', '15.00', '11.25', '110.00', 100, 'activo', 177, '519'),
(520, 'Queso cottage Lala 200g', '35.00', '26.25', '40.00', 30, 'activo', 44, '520'),
(521, 'Tortillas de harina Mission 650g', '28.00', '21.00', '70.00', 60, 'activo', 270, '521'),
(522, 'Refresco Fanta naranja 355ml', '12.00', '9.00', '110.00', 100, 'activo', 52, '522'),
(523, 'Pasta spaghetti Barilla 500g', '30.00', '22.50', '60.00', 50, 'activo', 49, '523'),
(524, 'Aceitunas verdes La Española 300g', '45.00', '33.75', '45.00', 35, 'activo', 60, '524'),
(525, 'Galletas saladas Crackers 200g', '20.00', '15.00', '90.00', 80, 'activo', 77, '525'),
(526, 'Leche de coco Aroy-D 1L', '60.00', '45.00', '50.00', 40, 'activo', 246, '526'),
(527, 'Cereal Cheerios 500g', '55.00', '41.25', '55.00', 45, 'activo', 34, '527'),
(528, 'Mermelada de moras La Vieja Fábrica 350g', '50.00', '37.50', '35.00', 25, 'activo', 252, '528'),
(529, 'Jugo de manzana Boing 1L', '20.00', '15.00', '80.00', 70, 'activo', 81, '529'),
(530, 'Galletas María McVitie\'s 200g', '18.00', '13.50', '60.00', 50, 'activo', 78, '530'),
(531, 'Café de grano Starbucks 250g', '120.00', '90.00', '40.00', 30, 'activo', 33, '531'),
(532, 'Atún Herdez en agua 170g', '25.00', '18.75', '50.00', 40, 'activo', 28, '532'),
(533, 'Refresco Sprite sin azúcar 355ml', '15.00', '11.25', '110.00', 100, 'activo', 177, '533'),
(534, 'Lentejas Premium La Asturiana 500g', '30.00', '22.50', '70.00', 60, 'activo', 109, '534'),
(535, 'Salsa de soya Kikkoman 150ml', '35.00', '26.25', '35.00', 25, 'activo', 53, '535'),
(536, 'Té verde Bigelow 20 sobres', '65.00', '48.75', '40.00', 30, 'activo', 268, '536'),
(537, 'Mantequilla Gloria sin sal 225g', '50.00', '37.50', '45.00', 35, 'activo', 250, '537'),
(538, 'Pasta de dientes Colgate Total 75ml', '35.00', '26.25', '90.00', 80, 'activo', 113, '538'),
(539, 'Cereal Special K Kellogg\'s 390g', '55.00', '41.25', '55.00', 45, 'activo', 34, '539'),
(540, 'Galletas integrales Marías Gamesa 200g', '17.00', '12.50', '60.00', 50, 'activo', 78, '540'),
(541, 'Café descafeinado Nescafé 100g', '45.00', '33.75', '45.00', 35, 'activo', 33, '541'),
(542, 'Helado de fresa Nestlé 1L', '70.00', '52.50', '30.00', 20, 'activo', 43, '542'),
(543, 'Sardinas en tomate Herdez 200g', '20.00', '15.00', '40.00', 30, 'activo', 28, '543'),
(544, 'Jugo de uva Boing 1L', '22.00', '16.50', '55.00', 45, 'activo', 81, '544'),
(545, 'Leche condensada La Lechera 387g', '25.00', '18.75', '60.00', 50, 'activo', 162, '545'),
(546, 'Queso crema Philadelphia 190g', '40.00', '30.00', '45.00', 35, 'activo', 44, '546'),
(547, 'Cereal Nesquik Kellogg\'s 375g', '50.00', '37.50', '50.00', 40, 'activo', 34, '547'),
(548, 'Atún Van Camps en agua 170g', '22.00', '16.50', '70.00', 60, 'activo', 28, '548'),
(549, 'Galletas saladas Saladitas 145g', '18.00', '13.50', '80.00', 70, 'activo', 77, '549'),
(550, 'Lentejas San Lázaro 500g', '18.00', '13.50', '90.00', 80, 'activo', 109, '550'),
(551, 'Refresco 7Up 355ml', '12.00', '9.00', '110.00', 100, 'activo', 52, '551'),
(552, 'Café cappuccino soluble Nescafé 170g', '75.00', '56.25', '50.00', 40, 'activo', 33, '552'),
(553, 'Pasta de dientes Sensodyne 100g', '65.00', '48.75', '65.00', 55, 'activo', 113, '553'),
(554, 'Helado de chocolate Santa Clara 1L', '85.00', '63.75', '40.00', 30, 'activo', 43, '554'),
(555, 'Sardinas en aceite Van Camps 200g', '19.00', '14.25', '45.00', 35, 'activo', 28, '555'),
(556, 'Jugo de toronja Del Valle 1L', '23.00', '17.25', '70.00', 60, 'activo', 81, '556'),
(557, 'Mantequilla sin sal Lurpak 250g', '65.00', '48.75', '40.00', 30, 'activo', 250, '557'),
(558, 'Galletas Marías Emperador 170g', '15.00', '11.25', '60.00', 50, 'activo', 78, '558'),
(559, 'Cereal integral Fitness Nestlé 350g', '55.00', '41.25', '55.00', 45, 'activo', 283, '559'),
(560, 'Queso manchego Chilchota 250g', '70.00', '52.50', '35.00', 25, 'activo', 44, '560'),
(561, 'Tortillas integrales Bimbo 600g', '33.00', '24.75', '70.00', 60, 'activo', 270, '561'),
(562, 'Aceite de canola Mazola 900ml', '50.00', '37.50', '50.00', 40, 'activo', 26, '785'),
(563, 'Yogurt sin azúcar Danone 150g', '20.00', '15.00', '60.00', 50, 'activo', 274, '563'),
(564, 'Cereal Zucaritas Kellogg\'s 500g', '60.00', '45.00', '55.00', 45, 'activo', 34, '564'),
(565, 'Pasta corta La Moderna 200g', '15.00', '11.25', '80.00', 70, 'activo', 49, '565'),
(566, 'Refresco Dr Pepper 355ml', '12.00', '9.00', '110.00', 100, 'activo', 52, '566'),
(567, 'Galletas de arroz Quaker 300g', '25.00', '18.75', '90.00', 80, 'activo', 155, '567'),
(568, 'Jabón líquido Palmolive 1L', '45.00', '33.75', '60.00', 50, 'activo', 108, '568'),
(569, 'Cereal Corn Pops Kellogg\'s 500g', '114.00', '85.50', '204.00', 60, 'activo', 34, '569'),
(570, 'Helado de vainilla Breyers 1L', '75.00', '56.25', '40.00', 30, 'activo', 43, '570'),
(571, 'Atún Calvo en aceite 170g', '20.00', '15.00', '45.00', 35, 'activo', 28, '571'),
(572, 'Galletas Chokis 225g', '30.00', '22.50', '80.00', 70, 'activo', 78, '572'),
(573, 'Pasta de dientes Oral-B 75ml', '35.00', '26.25', '65.00', 55, 'activo', 113, '573'),
(574, 'Yogurt sin lactosa Lala 150g', '20.00', '15.00', '60.00', 50, 'activo', 211, '574'),
(575, 'Mermelada orgánica de fresa Bonne Maman 370g', '75.00', '56.25', '30.00', 20, 'activo', 213, '575'),
(576, 'Cereal de avena Quaker 400g', '40.00', '30.00', '50.00', 40, 'activo', 67, '576'),
(577, 'Té de hierbas manzanilla McCormick 20 sobres', '35.00', '26.25', '40.00', 30, 'activo', 183, '577'),
(578, 'Café orgánico San Jorge 250g', '100.00', '75.00', '25.00', 15, 'activo', 195, '578'),
(579, 'Refresco sin azúcar Pepsi black 355ml', '16.00', '12.00', '100.00', 90, 'activo', 263, '579'),
(580, 'Lentejas verdes orgánicas 500g', '28.00', '21.00', '60.00', 50, 'activo', 298, '580'),
(581, 'Aceite de coco orgánico AWA 250ml', '60.00', '45.00', '35.00', 25, 'activo', 223, '581'),
(582, 'Galletas de arroz Saníssimo 150g', '25.00', '18.75', '70.00', 60, 'activo', 155, '582'),
(583, 'Atún orgánico en agua Wild Planet 142g', '45.00', '33.75', '45.00', 35, 'activo', 28, '583'),
(584, 'Mantequilla de almendra orgánica Justin\'s 454g', '180.00', '135.00', '30.00', 20, 'activo', 164, '584'),
(585, 'Pasta penne sin gluten Barilla 340g', '50.00', '37.50', '55.00', 45, 'activo', 171, '585'),
(586, 'Helado de leche de coco Alpro 500ml', '70.00', '52.50', '40.00', 30, 'activo', 243, '586'),
(587, 'Café espresso en cápsulas Nespresso 10 cápsulas', '120.00', '90.00', '60.00', 50, 'activo', 33, '587'),
(588, 'Refresco de limón sin azúcar Sprite 355ml', '16.00', '12.00', '85.00', 75, 'activo', 263, '588'),
(589, 'Galletas veganas sin gluten Mary\'s Gone Crackers 184g', '75.00', '56.25', '40.00', 30, 'activo', 208, '589'),
(590, 'Jugo de naranja orgánico Simply Orange 1L', '60.00', '45.00', '60.00', 50, 'activo', 244, '590'),
(591, 'Queso vegano estilo mozzarella Daiya 200g', '90.00', '67.50', '30.00', 20, 'activo', 222, '591'),
(592, 'Miel de agave orgánica Kirkland 680g', '70.00', '52.50', '50.00', 40, 'activo', 214, '592'),
(593, 'Galletas de chocolate sin gluten Schär 150g', '45.00', '33.75', '45.00', 35, 'activo', 240, '593'),
(594, 'Leche de almendra orgánica Califia Farms 1L', '95.00', '71.25', '54.00', 44, 'activo', 246, '594'),
(595, 'Cereal sin gluten Rice Krispies 340g', '60.00', '45.00', '70.00', 60, 'activo', 142, '595'),
(596, 'Sopa de lentejas orgánica Amy\'s 400g', '65.00', '48.75', '35.00', 25, 'activo', 217, '596'),
(597, 'Aceite de oliva extra virgen orgánico Kirkland 1L', '150.00', '112.50', '40.00', 30, 'activo', 223, '597'),
(598, 'Yogurt vegano de coco So Delicious 150g', '40.00', '30.00', '30.00', 20, 'activo', 222, '598'),
(599, 'Manteca de cerdo orgánica Iberia 250g', '50.00', '37.50', '70.00', 60, 'activo', 249, '599'),
(600, 'Queso orgánico cheddar Tillamook 200g', '80.00', '60.00', '60.00', 50, 'activo', 210, '600'),
(601, 'Galletas de avena orgánicas Quaker 250g', '55.00', '41.25', '50.00', 40, 'activo', 293, '601'),
(602, 'Leche de arroz orgánica Rice Dream 1L', '90.00', '67.50', '45.00', 35, 'activo', 246, '602'),
(603, 'Refresco de cola orgánico Whole Earth 330ml', '55.00', '41.25', '80.00', 70, 'activo', 221, '603'),
(604, 'Galletas de avena sin gluten McVitie\'s 150g', '45.00', '33.75', '40.00', 30, 'activo', 240, '604'),
(605, 'Jugo de piña orgánico Del Monte 1L', '60.00', '45.00', '60.00', 50, 'activo', 244, '605'),
(606, 'Leche de soya Silk 946ml', '40.00', '30.00', '50.00', 40, 'activo', 246, '606'),
(607, 'Manteca vegetal Inca 250g', '40.00', '30.00', '70.00', 60, 'activo', 164, '607'),
(608, 'Galletas de chocolate Oreo sin gluten 154g', '40.00', '30.00', '45.00', 35, 'activo', 240, '608'),
(609, 'Sardinas en salsa de tomate La Costeña 155g', '20.00', '15.00', '80.00', 70, 'activo', 28, '609'),
(610, 'Aceite de aguacate Kirkland 1L', '150.00', '112.50', '35.00', 25, 'activo', 223, '562'),
(611, 'Yogurt orgánico de vainilla Stonyfield 170g', '45.00', '33.75', '60.00', 50, 'activo', 210, '611'),
(612, 'Refresco de cola Zevia 355ml', '25.00', '18.75', '85.00', 75, 'activo', 263, '612'),
(613, 'Café instantáneo orgánico Mount Hagen 100g', '120.00', '90.00', '30.00', 20, 'activo', 195, '613'),
(614, 'Queso cheddar orgánico Horizon 226g', '80.00', '60.00', '40.00', 30, 'activo', 210, '614'),
(615, 'Pasta de garbanzo Banza 227g', '50.00', '37.50', '45.00', 35, 'activo', 171, '615'),
(616, 'Helado de coco sin azúcar Rebel 473ml', '100.00', '75.00', '25.00', 15, 'activo', 160, '616'),
(617, 'Refresco de jengibre Ginger Ale Canada Dry 355ml', '16.00', '12.00', '70.00', 60, 'activo', 52, '617'),
(618, 'Galletas de avena y pasas Nature Valley 210g', '55.00', '41.25', '80.00', 70, 'activo', 156, '618'),
(619, 'Mermelada orgánica de frambuesa Cascadian Farm 312g', '85.00', '63.75', '35.00', 25, 'activo', 213, '619'),
(620, 'Cereal orgánico de quinoa Nature\'s Path 375g', '70.00', '52.50', '50.00', 40, 'activo', 67, '620'),
(621, 'Leche de avellana Alpro 1L', '90.00', '67.50', '40.00', 30, 'activo', 246, '621'),
(622, 'Galletas de almendra sin gluten Tate\'s Bake Shop 198g', '85.00', '63.75', '60.00', 50, 'activo', 240, '622'),
(623, 'Aceite de girasol orgánico Spectrum 946ml', '120.00', '90.00', '70.00', 60, 'activo', 223, '623'),
(624, 'Jugo de manzana orgánico Honest Kids 6x200ml', '55.00', '41.25', '55.00', 45, 'activo', 244, '624'),
(625, 'Yogurt de coco sin azúcar Culina 150g', '50.00', '37.50', '30.00', 20, 'activo', 222, '625'),
(626, 'Pasta fettuccine sin gluten Jovial 340g', '65.00', '48.75', '45.00', 35, 'activo', 171, '626'),
(627, 'Refresco de toronja Squirt 355ml', '14.00', '10.50', '90.00', 80, 'activo', 52, '627'),
(628, 'Helado de leche de almendra Almond Dream 473ml', '75.00', '56.25', '40.00', 30, 'activo', 243, '628'),
(629, 'Café en grano orgánico Equal Exchange 340g', '150.00', '112.50', '35.00', 25, 'activo', 195, '629'),
(630, 'Queso rallado Parmesano Kraft 227g', '90.00', '67.50', '60.00', 50, 'activo', 44, '630'),
(631, 'Mantequilla de maní orgánica MaraNatha 454g', '95.00', '71.25', '50.00', 40, 'activo', 164, '631'),
(632, 'Galletas de avena sin azúcar McVitie\'s 150g', '45.00', '33.75', '70.00', 60, 'activo', 157, '632'),
(633, 'Refresco sabor naranja sin azúcar Crush 355ml', '20.00', '15.00', '85.00', 75, 'activo', 263, '633'),
(634, 'Helado de chocolate vegano NadaMoo! 473ml', '90.00', '67.50', '35.00', 25, 'activo', 243, '634'),
(636, 'Pan dulce pieza navarrete', '10.00', '7.50', '25.00', 15, 'activo', 41, '636'),
(637, 'Tortillinas Tía Rosa 1kg', '22.00', '16.50', '22.00', 12, 'activo', 270, '637'),
(638, 'Mole Doña María 150ml', '19.50', '14.63', '25.00', 15, 'activo', 312, '638'),
(639, 'Chiles serranos en rajas Clemente Jacques 280ml', '29.00', '21.75', '33.00', 23, 'activo', 75, '639'),
(640, 'Toallas húmedas bebin 80pz', '23.50', '17.63', '33.00', 23, 'activo', 86, '640'),
(641, 'Sopa knor arroz blanco 80g', '18.00', '13.50', '33.00', 23, 'activo', 49, '641'),
(642, 'Pañales brazil recién nacido 44pz', '98.00', '73.50', '25.00', 15, 'activo', 313, '642'),
(643, 'Zuko sabor piña 13g', '6.50', '4.88', '60.00', 50, 'activo', 314, '643'),
(644, 'Zuko sabor fresa 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '644'),
(645, 'Zuko sabor jamaica 13g', '11.00', '8.25', '80.00', 70, 'activo', 314, '645'),
(646, 'Zuko sabor horchata 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '646'),
(647, 'Zuko sabor mango 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '647'),
(648, 'Zuko sabor naranja 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '648'),
(649, 'Zuko sabor mandarina 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '649'),
(650, 'Zuko sabor uva 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '650'),
(651, 'Zuko sabor maracuya 13g', '7.00', '5.25', '60.00', 50, 'activo', 314, '651'),
(652, 'Zuko sabor guayaba 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '652'),
(653, 'Zuko sabor cebada 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '653'),
(654, 'Zuko sabor cereza 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '654'),
(655, 'Zuko sabor limón 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '655'),
(656, 'Zuko sabor melón 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '656'),
(657, 'Zuko sabor arándano 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '657'),
(658, 'Zuko sabor manzana 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '658'),
(659, 'Zuko sabor toronja 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '659'),
(660, 'Zuko sabor coco 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '660'),
(661, 'Zuko sabor té verde 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '661'),
(662, 'Zuko sabor sandía 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '662'),
(663, 'Zuko sabor papaya 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '663'),
(664, 'Zuko sabor naranja-plátano 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '664'),
(665, 'Zuko sabor frambuesa 13g', '6.00', '4.50', '60.00', 50, 'activo', 314, '665'),
(666, 'Powerade Sabor Frutas 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '666'),
(667, 'Powerade Sabor Lima-Limón 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '667'),
(668, 'Powerade Sabor Moras 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '668'),
(669, 'Powerade Sabor Naranja 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '669'),
(670, 'Powerade Sabor Uva 1L', '18.00', '13.50', '110.00', 100, 'activo', 32, '670'),
(671, 'Powerade Sabor Frutas 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '671'),
(672, 'Powerade Sabor Lima-Limón 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '672'),
(673, 'Powerade Sabor Moras 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '673'),
(674, 'Powerade Sabor Naranja 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '674'),
(675, 'Powerade Sabor Uva 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '675'),
(676, 'Powerade Sabor Frutas 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '676'),
(677, 'Powerade Sabor Lima-Limón 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '677'),
(678, 'Powerade Sabor Moras 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '678'),
(679, 'Powerade Sabor Naranja 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '679'),
(680, 'Powerade Sabor Uva 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '680'),
(681, 'Powerade Sabor Frutas Lata 453ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '681'),
(682, 'Powerade Sabor Moras Lata 453ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '682'),
(683, 'Powerade Sabor Uva Lata 453ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '683'),
(684, 'Powerade Fit Sabor Menta-Uva 1L', '30.00', '22.50', '110.00', 100, 'activo', 32, '684'),
(685, 'Powerade Fit Sabor Arándano-Açaí 1L', '30.00', '22.50', '110.00', 100, 'activo', 32, '685'),
(686, 'Powerade Fit Sabor Menta-Uva 500ml', '22.00', '16.50', '110.00', 100, 'activo', 32, '686'),
(687, 'Powerade Fit Sabor Arándano-Açaí 500ml', '22.00', '16.50', '110.00', 100, 'activo', 32, '687'),
(688, 'Gatorade Sabor Naranja 1L', '23.00', '17.25', '110.00', 100, 'activo', 32, '688'),
(689, 'Gatorade Sabor Moras 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '689'),
(690, 'Gatorade Sabor Uva 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '690'),
(691, 'Gatorade Sabor Lima-Limón 1L', '29.00', '21.75', '110.00', 100, 'activo', 32, '691'),
(692, 'Gatorade Sabor Naranja 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '692'),
(693, 'Gatorade Sabor Moras 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '693'),
(694, 'Gatorade Sabor Uva 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '694'),
(695, 'Gatorade Sabor Lima-Limón 600ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '695'),
(696, 'Gatorade Sabor Naranja 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '696'),
(697, 'Gatorade Sabor Moras 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '697'),
(698, 'Gatorade Sabor Uva 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '698'),
(699, 'Gatorade Sabor Lima-Limón 500ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '699'),
(700, 'Gatorade Sabor Naranja 350ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '700'),
(701, 'Gatorade Sabor Moras 350ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '701'),
(702, 'Gatorade Sabor Uva 350ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '702'),
(703, 'Gatorade Sabor Lima-Limón 350ml', '15.00', '11.25', '110.00', 100, 'activo', 32, '703'),
(704, 'Gatorade Polvo Sabor Naranja 521g', '50.00', '37.50', '110.00', 100, 'activo', 32, '704'),
(705, 'Vive 100 Sabor Original 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '705'),
(706, 'Vive 100 Sabor Cherry Fresa 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '706'),
(707, 'Vive 100 Sabor Manzana Mix 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '707'),
(708, 'Vive 100 Sabor Moringa Tropical Punch 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '708'),
(709, 'Vive 100 Sabor Açaí Fruitmix 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '709'),
(710, 'Vive 100 Zero Azúcar 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '710'),
(711, 'Vive 100 Sabor Blueberry 500ml', '20.00', '15.00', '110.00', 100, 'activo', 32, '711'),
(712, 'Vive 100 Sabor Original 400ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '712'),
(713, 'Vive 100 Sabor Fusión (Frutos Rojos) 380ml', '18.00', '13.50', '110.00', 100, 'activo', 32, '713'),
(714, 'Monster Energy Original 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '714'),
(715, 'Monster Energy Zero Ultra 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '715'),
(716, 'Monster Energy Green 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '716'),
(717, 'Monster Energy Ultra Red 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '717'),
(718, 'Monster Energy Ultra Violet 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '718'),
(719, 'Monster Energy Mango Loco 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '719'),
(720, 'Monster Energy Pacific Punch 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '720'),
(721, 'Monster Energy Ultra Sunrise 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '721'),
(722, 'Monster Energy Monster Hydro 500ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '722'),
(723, 'Monster Energy Rehab 500ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '723'),
(724, 'Monster Energy Ripper 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '724'),
(725, 'Monster Energy Chaos 500ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '725'),
(726, 'Monster Energy Java Coffee 500ml', '35.00', '26.25', '110.00', 100, 'activo', 32, '726'),
(727, 'Red Bull Energy Drink 250ml', '25.00', '18.75', '110.00', 100, 'activo', 32, '727'),
(728, 'Red Bull Energy Drink 355ml', '39.00', '29.25', '110.00', 100, 'activo', 32, '728'),
(729, 'Red Bull Sugarfree 250ml', '25.00', '18.75', '110.00', 100, 'activo', 32, '729'),
(730, 'Red Bull Sugarfree 355ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '730'),
(731, 'Red Bull Zero Calories 250ml', '25.00', '18.75', '110.00', 100, 'activo', 32, '731'),
(732, 'Red Bull Zero Calories 355ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '732'),
(733, 'Red Bull Tropical 355ml', '30.00', '22.50', '110.00', 100, 'activo', 32, '733'),
(734, 'Red Bull Coconut Edition 250ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '734'),
(735, 'Red Bull Blue Edition 250ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '735'),
(736, 'Red Bull Green Edition 250ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '736'),
(737, 'Red Bull Orange Edition 250ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '737'),
(738, 'Red Bull Summer Edition 250ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '738'),
(739, 'Red Bull Winter Edition 250ml', '28.00', '21.00', '110.00', 100, 'activo', 32, '739'),
(740, 'Cerveza estrella 600ml', '25.00', '21.50', '4.00', 10, 'activo', 30, '333'),
(741, 'cerveza victoria', '23.00', '17.25', '47.00', 5, 'activo', 30, '614524'),
(742, 'Importe Caguama', '7.00', '5.00', '150.00', 50, 'activo', 30, 'importecaguama'),
(744, 'Tortillas 1kg', '24.00', '22.00', '40.00', 10, 'activo', 58, '5846');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apepat` varchar(100) NOT NULL,
  `apemat` varchar(100) NOT NULL,
  `empresa` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `apepat`, `apemat`, `empresa`, `telefono`) VALUES
(1, 'Juan Eduardo', 'Pérez', 'Murillo', 'Marinela galletas', '348-201-4413'),
(2, 'Karla Itzel', 'Torres ', 'García', 'Gamesa', '348-133-0207'),
(3, 'Daniela', 'Alatorre', 'Marín', 'Marinela dulceria', '331-201-4589'),
(4, 'Juan Carlos', 'Hernández', 'García', 'Papitas Los Coyotes', '555-201-7890'),
(5, 'María Elena', 'López', 'Ramírez', 'Pan del Panqué', '555-123-4567'),
(6, 'José Antonio', 'Martínez', 'Domínguez', 'Bimbo Dulce', '555-765-4321'),
(7, 'Ana Isabel', 'González', 'Santos', 'Bimbo Salado', '555-876-5432'),
(8, 'Luis Miguel', 'Pérez', 'Jiménez', 'Jugos del Valle', '555-678-1234'),
(9, 'Carmen Lucía', 'Vargas', 'Flores', 'La Costeña', '555-987-6543'),
(10, 'Felipe Armando', 'Sánchez', 'Moreno', 'Chocolates Turin', '555-345-6789'),
(12, 'Javier', 'Sánchez', 'Moreno', 'Chocolates Turin', '555-345-6789'),
(13, 'Sofía', 'González', 'Rico', 'Galletas del Rincón', '555-321-4567'),
(14, 'Cecilia', 'López', 'Vega', 'Salsas El Mexicano', '555-654-7890'),
(15, 'Enrique', 'Rodríguez', 'Flores', 'Productos La Abuela', '555-789-0123'),
(16, 'Natalia', 'Fernández', 'Rivera', 'Embutidos La Vaca', '555-654-3210'),
(17, 'Hugo', 'Ramírez', 'Ortiz', 'Panes y Pasteles', '555-123-7890'),
(18, 'Patricia', 'Pérez', 'Maldonado', 'Aguas Frescas', '555-432-1098'),
(19, 'Miguel', 'Jiménez', 'Valencia', 'Tortillas El Molino', '555-678-5432'),
(20, 'Gabriela', 'Serrano', 'Galván', 'Lácteos de Calidad', '555-210-9876'),
(21, 'Andrés', 'Maldonado', 'Martínez', 'Carnes y Más', '555-456-7890'),
(22, 'Rosa', 'Flores', 'Rivas', 'Frutas y Verduras El Crazy', '555-908-7654'),
(23, 'Luis', 'Cruz', 'Torres', 'Botanas El Rey', '555-234-5678'),
(24, 'Elena', 'Aguilar', 'Cortez', 'Pastas Gourmet', '555-765-4321'),
(25, 'Antonio', 'Luna', 'Esparza', 'Quesos Tradicionales', '555-543-2109'),
(26, 'Monica', 'Peña', 'Reyes', 'Abarrotes El Amigo', '555-109-8765'),
(27, 'Jorge', 'Navarro', 'Campos', 'Chiles y Salsas', '555-678-2345'),
(28, 'Paula', 'Salinas', 'Figueroa', 'Licores y Vinos El Pacheco', '555-987-1234'),
(29, 'Ricardo', 'Silva', 'Ibarra', 'Pescados y Mariscos', '555-321-6547'),
(30, 'Valeria', 'Moreno', 'Cruz', 'Panadería La Flor', '555-765-8765'),
(31, 'Manuel', 'Santos', 'Medina', 'Helados y Paletas', '555-432-5678'),
(32, 'Cristina', 'Ruiz', 'Mejía', 'Legumbres del Campo', '555-345-7890'),
(33, 'Daniel', 'Ortega', 'Núñez', 'Cereales y Avenas Kellogs', '555-654-8765'),
(34, 'Silvia', 'Medrano', 'Hernández', 'Aceites y Especias', '555-123-4567'),
(35, 'Luis', 'Téllez', 'López', 'Galletas y Panes', '555-987-6543'),
(36, 'María Fernanda', 'Rodríguez', 'Cruz', 'Productos El Sol', '348-101-7890'),
(37, 'José Luis', 'García', 'López', 'Miel La Dulce', '348-212-4567'),
(38, 'Ana María', 'Sánchez', 'Martínez', 'Vinos La Cava', '348-323-4321'),
(39, 'Miguel Ángel', 'Hernández', 'Gómez', 'Panadería El Buen Pan', '348-434-5432'),
(40, 'Carlos Alberto', 'Pérez', 'Santos', 'Frutas Frescas', '348-545-1234'),
(41, 'Laura Patricia', 'González', 'Mora', 'Verduras Naturales', '348-656-6543'),
(42, 'Juan Manuel', 'López', 'Aguilar', 'Quesos Finos', '348-767-6789'),
(43, 'Mónica', 'Martínez', 'Sierra', 'Carnicería El Toro', '348-878-4567'),
(44, 'Ricardo', 'Serrano', 'Domínguez', 'Lácteos Los Andes', '348-989-7890'),
(45, 'Cristina', 'Castillo', 'Hidalgo', 'Abarrotes La Plaza', '348-101-0123'),
(46, 'Fernando', 'Cruz', 'Zamora', 'Chocolates Delicia', '348-212-3210'),
(47, 'Elena', 'Ruiz', 'Mendoza', 'Pan Integral', '348-323-7890'),
(48, 'Javier', 'Ramírez', 'Nava', 'Jugos Exóticos', '348-434-9876'),
(49, 'Patricia', 'Ortiz', 'Olvera', 'Botanas Sabor', '348-545-6543'),
(50, 'Luis Enrique', 'Rojas', 'Morales', 'Cereales Sanos', '348-656-3210'),
(51, 'Rosa', 'Flores', 'Salas', 'Harinas Especiales', '348-767-9876'),
(52, 'Adriana', 'Guzmán', 'Vargas', 'Tortillas Artesanales', '348-878-5433'),
(53, 'David', 'Medina', 'Campos', 'Chiles Picantes', '348-989-6789'),
(54, 'Gabriela', 'Mendoza', 'Pérez', 'Aceites Gourmet', '348-101-3456'),
(55, 'Mario', 'Sánchez', 'Rivera', 'Pastas Finas', '348-212-7890'),
(56, 'Lorena', 'Aguilar', 'Martínez', 'Galletas La Dulce', '348-323-4567'),
(57, 'Hugo', 'Sosa', 'Hernández', 'Productos Del Mar', '348-434-9876'),
(58, 'Claudia', 'Moreno', 'Vargas', 'Mermeladas Artesanales', '348-545-6789'),
(59, 'Alberto', 'Martínez', 'Juárez', 'Salsas Picantes', '348-656-7890'),
(60, 'Verónica', 'Luna', 'García', 'Pasteles La Gloria', '348-767-1234'),
(61, 'Oscar', 'Peña', 'Jiménez', 'Frutos Secos', '348-878-3456'),
(62, 'Paula', 'Cortés', 'Romero', 'Aceitunas Gourmet', '348-989-4567'),
(63, 'Daniel', 'Hernández', 'Salinas', 'Embutidos El Campo', '348-101-6543'),
(64, 'Marina', 'Pérez', 'González', 'Café Fino', '348-212-3210'),
(65, 'Álvaro', 'Castro', 'Santos', 'Té Exquisito', '348-323-7890'),
(66, 'Julio', 'Bautista', 'Luna', 'Abarrotes Los Álamos', '345-567-8901'),
(67, 'Lorena', 'Navarro', 'Gómez', 'Bebidas La Fresca', '345-678-9012'),
(68, 'Fernando', 'Pérez', 'Sánchez', 'Frutas El Paraíso', '345-789-0123'),
(69, 'Luisa', 'Gutiérrez', 'Rojas', 'Verduras El Huerto', '345-890-1234'),
(70, 'Martín', 'Castro', 'Díaz', 'Panadería El Trigo', '345-901-2345'),
(71, 'Silvia', 'Hernández', 'Paredes', 'Productos del Rancho', '345-123-3456'),
(72, 'Javier', 'Aguilar', 'Cortés', 'Lácteos La Sierra', '345-234-4567'),
(73, 'Isabel', 'López', 'Vargas', 'Pescados y Mariscos La Playa', '345-345-5678'),
(74, 'Daniel', 'Ramírez', 'Estrada', 'Helados y Nieves El Frío', '345-456-6789'),
(75, 'Patricia', 'Morales', 'Ávila', 'Jugos y Licuados La Energía', '345-567-7890'),
(76, 'Roberto', 'Mendoza', 'García', 'Quesos y Cremas La Hacienda', '345-678-8901'),
(77, 'Mariana', 'Flores', 'Hernández', 'Dulces y Chocolates La Tradición', '345-789-9012'),
(78, 'Alberto', 'Jiménez', 'Peña', 'Botanas y Snacks El Sabor', '345-890-0123'),
(79, 'Cristina', 'Vega', 'Ponce', 'Abarrotes y Más La Central', '345-901-1234'),
(80, 'Eduardo', 'Ríos', 'Campos', 'Chiles y Especias La Casa', '345-012-2345'),
(81, 'Andrea', 'Sosa', 'Blanco', 'Pastas y Harinas La Cocina', '345-123-3457'),
(82, 'Raúl', 'Mejía', 'Molina', 'Galletas y Panes El Horno', '345-234-4568'),
(83, 'Lucía', 'Luna', 'Campos', 'Aceites y Vinagres El Olivo', '345-345-5679'),
(84, 'Héctor', 'Ortega', 'Ramos', 'Carnes y Aves La Granja', '345-456-6780'),
(85, 'Gabriela', 'Salazar', 'Cabrera', 'Frutos Secos y Semillas La Bodega', '345-567-7891'),
(86, 'Antonio', 'Reyes', 'Montoya', 'Pasteles y Postres El Dulce', '345-678-8902'),
(87, 'Mónica', 'Santos', 'Figueroa', 'Mermeladas y Conservas El Jarro', '345-789-9013'),
(88, 'Ricardo', 'Núñez', 'Cervantes', 'Embutidos y Jamones La Finca', '345-890-0124'),
(89, 'Verónica', 'Álvarez', 'Herrera', 'Granos y Cereales La Cosecha', '345-901-1235'),
(90, 'José', 'Rodríguez', 'Maldonado', 'Harinas Especiales El Molino', '345-012-2346'),
(91, 'Claudia', 'Velázquez', 'Franco', 'Té y Café La Taza', '345-123-3458'),
(92, 'Armando', 'Quintero', 'Delgado', 'Salsas y Aderezos El Sazón', '345-234-4569'),
(93, 'Laura', 'Ramírez', 'Silva', 'Botanas y Dulces El Rincón', '345-345-5670'),
(94, 'Mauricio', 'Vargas', 'Medina', 'Productos Orgánicos La Vida', '345-456-6781'),
(95, 'Alejandra', 'Cruz', 'Navarro', 'Bebidas y Licores El Barril', '345-567-7892'),
(96, 'Elena', 'Ramírez', 'Sánchez', 'Vinos La Viña', '345-678-9013'),
(97, 'Andrés', 'García', 'Hernández', 'Panes y Postres La Dulzura', '345-789-0124'),
(98, 'Verónica', 'Sosa', 'Martínez', 'Productos del Campo', '345-890-1235'),
(99, 'Miguel', 'Pérez', 'López', 'Lácteos La Granja', '345-901-2346'),
(100, 'Laura', 'Gómez', 'Gutiérrez', 'Pescados El Oceáno', '345-012-3457'),
(101, 'Roberto', 'Díaz', 'Paredes', 'Carne y Más', '345-123-4568'),
(102, 'Ana', 'Hernández', 'Zamora', 'Helados y Paletas La Nieve', '345-234-5679'),
(103, 'José', 'Martínez', 'Aguilar', 'Abarrotes La Esquina', '345-345-6780'),
(104, 'Claudia', 'López', 'Vázquez', 'Dulces y Chocolates La Fábrica', '345-456-7891'),
(105, 'Fernando', 'González', 'Mendoza', 'Quesos y Crema La Vaca', '345-567-8902'),
(106, 'Gabriela', 'Rodríguez', 'Ramírez', 'Panadería La Familia', '345-678-9014'),
(107, 'Javier', 'Vega', 'Rojas', 'Frutas y Verduras El Jardín', '345-789-0125'),
(108, 'Patricia', 'García', 'Hernández', 'Jugos y Smoothies La Fruta', '345-890-1236'),
(109, 'Rosa', 'Castro', 'Pérez', 'Verduras Frescas El Huerto', '345-901-2347'),
(110, 'Luis', 'Díaz', 'Gómez', 'Tortillas y Harinas El Molino', '345-012-3458'),
(111, 'Mónica', 'Jiménez', 'Paredes', 'Pasteles y Galletas El Dulce', '345-123-4569'),
(112, 'Carlos', 'Aguilar', 'Hernández', 'Bebidas y Refrescos La Fuente', '345-234-5670'),
(113, 'Carmen', 'Navarro', 'Martínez', 'Botanas La Fiesta', '345-345-6781'),
(114, 'Roberto', 'Peña', 'López', 'Aceites y Especias La Cocina', '345-456-7892'),
(115, 'Sofía', 'González', 'García', 'Embutidos El Rancho', '345-567-8903'),
(116, 'Héctor', 'Luna', 'Méndez', 'Mermeladas y Conservas El Tarro', '345-678-9015'),
(117, 'Elena', 'Ruiz', 'Salinas', 'Frutos Secos La Cosecha', '345-789-0126'),
(118, 'Pedro', 'Ortega', 'Santos', 'Carnes Finas La Res', '345-890-1237'),
(119, 'Mariana', 'Reyes', 'Hernández', 'Abarrotes El Mercado', '345-901-2348'),
(120, 'David', 'Serrano', 'Pérez', 'Galletas y Panes La Estrella', '345-012-3459'),
(121, 'Paula', 'Romero', 'Luna', 'Té y Café La Taza', '345-123-4560'),
(122, 'Jorge', 'Hernández', 'Flores', 'Licores y Bebidas El Barril', '345-234-5671'),
(123, 'Lorena', 'Mora', 'Cortés', 'Productos Orgánicos El Bosque', '345-345-6782'),
(124, 'Esteban', 'Sosa', 'Gómez', 'Chocolates La Delicia', '345-456-7893'),
(125, 'Silvia', 'Martínez', 'Ramírez', 'Quesos La Vaquita', '345-567-8904'),
(126, 'Lucía', 'Vargas', 'Flores', 'Conservas Doña Marta', '345-789-0127'),
(127, 'Roberto', 'Navarrete', 'González', 'Aceitunas El Olivo', '345-890-1238'),
(128, 'Carmen', 'Salinas', 'Hernández', 'Chocolates Delicia Suprema', '345-901-2349'),
(129, 'Fernando', 'Vázquez', 'Martínez', 'Quesos y Cremas El Campesino', '345-012-3450'),
(130, 'Laura', 'Morales', 'Pérez', 'Panadería Las Delicias', '345-123-4561'),
(131, 'Javier', 'Guzmán', 'Ramírez', 'Abarrotes Mi Tienda', '345-234-5672'),
(132, 'Mariana', 'Castillo', 'López', 'Jugos Naturales El Manantial', '345-345-6783'),
(133, 'Carlos', 'Ríos', 'Díaz', 'Verduras Frescas El Huerto', '345-456-7894'),
(134, 'Patricia', 'Hernández', 'Aguilar', 'Salsas y Aderezos La Cocina', '345-567-8905'),
(135, 'Martín', 'Pérez', 'Gómez', 'Frutos Secos y Semillas La Hacienda', '345-678-9016'),
(136, 'Ana', 'García', 'Mendoza', 'Galletas y Pasteles El Sabor', '345-789-0128'),
(137, 'José', 'Martínez', 'Flores', 'Bebidas Naturales El Paraíso', '345-890-1239'),
(138, 'Claudia', 'Rodríguez', 'Mora', 'Aceites y Vinagres La Casa', '345-901-2340'),
(139, 'David', 'González', 'Vega', 'Carne y Más El Establo', '345-012-3451'),
(140, 'Rosa', 'Luna', 'Cruz', 'Tortillas y Harinas La Tradición', '345-123-4562'),
(141, 'Luis', 'Sánchez', 'Moreno', 'Productos Orgánicos El Bosque', '345-234-5673'),
(142, 'Mónica', 'Reyes', 'Ortega', 'Chiles y Especias El Sabor', '345-345-6784'),
(143, 'Ricardo', 'Fernández', 'Salas', 'Pastas y Harinas El Molino', '345-456-7895'),
(144, 'Sofía', 'Medina', 'Cortés', 'Mermeladas y Conservas La Jarrita', '345-567-8906'),
(145, 'Héctor', 'Álvarez', 'Méndez', 'Quesos y Crema La Vaquita', '345-678-9017'),
(146, 'Elena', 'Romero', 'Sosa', 'Botanas La Fiesta', '345-789-0129'),
(147, 'Pedro', 'Jiménez', 'Peña', 'Harinas Especiales El Molino', '345-890-1240'),
(148, 'Gabriela', 'Martínez', 'Hernández', 'Té y Café La Taza', '345-901-2341'),
(149, 'Jorge', 'Vega', 'Díaz', 'Abarrotes La Central', '345-012-3452'),
(150, 'Patricia', 'Ruiz', 'Vargas', 'Lácteos La Granja', '345-123-4563'),
(151, 'Fernando', 'Cruz', 'López', 'Pasteles y Galletas La Dulzura', '345-234-5674'),
(152, 'Roberto', 'Aguilar', 'Ramírez', 'Frutas y Verduras El Jardín', '345-345-6785'),
(153, 'Ana', 'Rojas', 'Gómez', 'Dulces y Chocolates La Tradición', '345-456-7896'),
(154, 'Carlos', 'Méndez', 'Navarro', 'Jugos y Licuados La Energía', '345-567-8907'),
(155, 'Mónica', 'García', 'Morales', 'Verduras Frescas El Huerto', '345-678-9018'),
(156, 'Juliana', 'Cordero', 'Guzmán', 'Aceites La Calidad', '345-567-8908'),
(157, 'Raúl', 'Salazar', 'Mejía', 'Abarrotes La Esperanza', '345-678-9019'),
(158, 'Lorena', 'Dávila', 'Ponce', 'Quesos y Crema El Rancho', '345-789-0120'),
(159, 'Fernando', 'Moreno', 'Santos', 'Frutas Exóticas El Paraíso', '345-890-1230'),
(160, 'Carlos', 'Navarrete', 'Ruiz', 'Dulces y Chocolates La Fábrica', '345-901-2342'),
(161, 'Ana', 'Castro', 'Flores', 'Panadería El Buen Sabor', '345-012-3453'),
(162, 'Martín', 'Gómez', 'Núñez', 'Verduras Frescas La Huerta', '345-123-4564'),
(163, 'Patricia', 'Ortega', 'Hernández', 'Jugos y Batidos La Frescura', '345-234-5675'),
(164, 'Mariana', 'Vargas', 'Pérez', 'Pasteles y Galletas La Repostería', '345-345-6786'),
(165, 'Roberto', 'Gutiérrez', 'Martínez', 'Botanas y Snacks El Sabor', '345-456-7897'),
(166, 'Javier', 'Vega', 'Ramos', 'Aceites y Vinagres La Cocina', '345-567-8909'),
(167, 'Cristina', 'López', 'Hernández', 'Té y Café La Especialidad', '345-678-9010'),
(168, 'Luis', 'Aguilar', 'Vega', 'Lácteos Los Pinos', '345-789-0121'),
(169, 'Rosa', 'Serrano', 'Moreno', 'Abarrotes y Más El Mercado', '345-890-1231'),
(170, 'Gabriela', 'Rojas', 'Fernández', 'Carne y Pollo La Hacienda', '345-901-2343'),
(171, 'Héctor', 'Ramírez', 'Cruz', 'Productos Orgánicos La Vida', '345-012-3454'),
(172, 'Mónica', 'Mendoza', 'Gómez', 'Salsas y Aderezos El Sazón', '345-123-4565'),
(173, 'Ricardo', 'Vega', 'Juárez', 'Harinas y Pastas El Molino', '345-234-5676'),
(174, 'Claudia', 'Cruz', 'Paredes', 'Frutos Secos y Semillas El Campo', '345-345-6787'),
(175, 'David', 'García', 'Navarro', 'Mermeladas y Conservas La Cosecha', '345-456-7898'),
(176, 'Elena', 'Jiménez', 'Sánchez', 'Licores y Bebidas La Barrica', '345-567-8910'),
(177, 'José', 'Hernández', 'Luna', 'Quesos y Cremas El Pueblo', '345-678-9011'),
(178, 'Patricia', 'López', 'Gómez', 'Pasteles y Galletas La Dulzura', '345-789-0122'),
(179, 'Roberto', 'Santos', 'Pérez', 'Verduras Frescas El Mercado', '345-890-1232'),
(180, 'Fernando', 'Morales', 'Ruiz', 'Jugos Naturales La Fuente', '345-901-2344'),
(181, 'Ana', 'Rodríguez', 'Martínez', 'Botanas y Dulces El Rincón', '345-012-3455'),
(182, 'Mariana', 'González', 'Flores', 'Aceites y Especias La Cocina', '345-123-4566'),
(183, 'Carlos', 'Martínez', 'Núñez', 'Pastas y Harinas El Molino', '345-234-5677'),
(184, 'Lucía', 'Gómez', 'Vega', 'Frutas y Verduras El Jardín', '345-345-6788'),
(185, 'Andrés', 'Vázquez', 'Juárez', 'Productos Orgánicos La Tierra', '345-456-7899'),
(186, 'Julio', 'Bautista', 'López', 'Carnes Selectas El Corral', '345-567-8911'),
(187, 'Elena', 'Ramírez', 'Núñez', 'Productos Orgánicos La Tierra', '345-678-9022'),
(188, 'Isabel', 'Moreno', 'Paredes', 'Quesos y Cremas La Hacienda', '345-789-0133'),
(189, 'Fernando', 'González', 'Serrano', 'Aceitunas Gourmet El Olivo', '345-890-1244'),
(190, 'Laura', 'Hernández', 'Ríos', 'Abarrotes El Buen Precio', '345-901-2355'),
(191, 'José', 'Martínez', 'Lara', 'Frutas y Verduras La Huerta', '345-012-3466'),
(192, 'Patricia', 'López', 'Flores', 'Botanas y Snacks El Sabor', '345-123-4577'),
(193, 'Carlos', 'García', 'Mendoza', 'Jugos Naturales El Manantial', '345-234-5688'),
(194, 'Ana', 'Díaz', 'Nava', 'Pastas y Harinas El Molino', '345-345-6799'),
(195, 'Roberto', 'Rojas', 'Aguilar', 'Lácteos La Granja', '345-456-7800'),
(196, 'Cristina', 'Aguilar', 'Cruz', 'Salsas y Aderezos La Cocina', '345-567-8912'),
(197, 'Luis', 'Navarro', 'Reyes', 'Té y Café La Especialidad', '345-678-9023'),
(198, 'Mónica', 'Sánchez', 'Pérez', 'Abarrotes y Más El Mercado', '345-789-0134'),
(199, 'Gabriela', 'Vega', 'Salas', 'Dulces y Chocolates La Tradición', '345-890-1245'),
(200, 'Héctor', 'Mora', 'López', 'Quesos y Cremas El Buen Pastor', '345-901-2356'),
(201, 'Mariana', 'Guzmán', 'Estrada', 'Carnes y Aves La Granja', '345-012-3467'),
(202, 'Ricardo', 'Reyes', 'Gómez', 'Pasteles y Galletas La Repostería', '345-123-4578'),
(203, 'Sofía', 'Ramos', 'Juárez', 'Jugos y Smoothies La Fruta', '345-234-5689'),
(204, 'David', 'Flores', 'Cortés', 'Aceites y Vinagres La Casa', '345-345-6700'),
(205, 'Elena', 'Luna', 'Montoya', 'Verduras Frescas El Huerto', '345-456-7811'),
(206, 'Antonio', 'Peña', 'Fernández', 'Frutos Secos y Semillas El Campo', '345-567-8913'),
(207, 'Rosa', 'Hernández', 'Rodríguez', 'Mermeladas y Conservas El Tarro', '345-678-9024'),
(208, 'Martín', 'Ramírez', 'Santos', 'Lácteos Los Pinos', '345-789-0135'),
(209, 'Claudia', 'Ortega', 'Mora', 'Tortillas y Harinas El Molino', '345-890-1246'),
(210, 'Eduardo', 'Ríos', 'López', 'Botanas y Dulces El Rincón', '345-901-2357'),
(211, 'Verónica', 'Mejía', 'Núñez', 'Abarrotes y Más La Central', '345-012-3468'),
(212, 'José', 'Martínez', 'Paredes', 'Carnes Finas La Res', '345-123-4579'),
(213, 'Patricia', 'Hernández', 'Gómez', 'Frutas y Verduras La Estrella', '345-234-5690'),
(214, 'Roberto', 'López', 'Ramírez', 'Jugos y Batidos La Energía', '345-345-6701'),
(215, 'Fernando', 'Sánchez', 'Mendoza', 'Verduras Frescas El Jardín', '345-456-7812'),
(216, 'Julio', 'Pérez', 'López', 'Productos de la Granja', '345-567-8921'),
(217, 'Lucía', 'Ramírez', 'Martínez', 'Lácteos Los Altos', '345-678-9032'),
(218, 'Alberto', 'Gutiérrez', 'Sánchez', 'Panadería El Trigo', '345-789-0143'),
(219, 'María', 'Moreno', 'Hernández', 'Verduras Frescas El Campo', '345-890-1254'),
(220, 'Fernando', 'Díaz', 'Vega', 'Aceites y Vinagres El Olivar', '345-901-2365'),
(221, 'Sofía', 'Torres', 'Gómez', 'Dulces y Chocolates La Estrella', '345-012-3476'),
(222, 'Carlos', 'Luna', 'Ríos', 'Quesos y Cremas La Vaca', '345-123-4587'),
(223, 'Isabel', 'González', 'Mora', 'Tortillas y Harinas La Familia', '345-234-5698'),
(224, 'Raúl', 'Castro', 'Juárez', 'Carnes Finas La Reserva', '345-345-6709'),
(225, 'Patricia', 'Rodríguez', 'Flores', 'Jugos Naturales La Fuente', '345-456-7810'),
(226, 'Miguel', 'Navarrete', 'Núñez', 'Frutas y Verduras La Huerta', '345-567-8922'),
(227, 'Cristina', 'Ortega', 'Salas', 'Botanas y Snacks El Sabor', '345-678-9033'),
(228, 'Luis', 'Reyes', 'Hernández', 'Quesos y Crema El Campo', '345-789-0144'),
(229, 'Carmen', 'Mendoza', 'Ramírez', 'Salsas y Aderezos La Cocina', '345-890-1255'),
(230, 'Fernando', 'López', 'Martínez', 'Harinas y Pastas La Molino', '345-901-2366'),
(231, 'Laura', 'Ramos', 'Pérez', 'Té y Café La Especialidad', '345-012-3477'),
(232, 'Javier', 'Ríos', 'Gómez', 'Verduras Frescas La Jardín', '345-123-4588'),
(233, 'Ana', 'Serrano', 'Luna', 'Dulces y Chocolates La Casa', '345-234-5699'),
(234, 'Héctor', 'Ruiz', 'Paredes', 'Frutos Secos y Semillas El Molino', '345-345-6710'),
(235, 'Gabriela', 'Jiménez', 'Cruz', 'Pasteles y Galletas La Repostería', '345-456-7811'),
(236, 'David', 'Peña', 'Ortega', 'Aceites y Vinagres La Casa', '345-567-8923'),
(237, 'Mariana', 'Castillo', 'Morales', 'Jugos y Batidos La Energía', '345-678-9034'),
(238, 'Ricardo', 'Ramírez', 'García', 'Quesos y Cremas La Hacienda', '345-789-0145'),
(239, 'Sofía', 'García', 'Hernández', 'Tortillas y Harinas El Molino', '345-890-1256'),
(240, 'Roberto', 'Vega', 'Méndez', 'Botanas y Dulces El Rincón', '345-901-2367'),
(241, 'José', 'Vásquez', 'Rodríguez', 'Aceites y Especias La Cocina', '345-012-3478'),
(242, 'Patricia', 'Sosa', 'López', 'Carnes Finas La Granja', '345-123-4589'),
(243, 'Fernando', 'Martínez', 'Navarro', 'Frutas y Verduras La Huerta', '345-234-5700'),
(244, 'Carlos', 'Hernández', 'Mendoza', 'Dulces y Chocolates El Paraíso', '345-345-6711'),
(245, 'María', 'Aguilar', 'Díaz', 'Verduras Frescas El Mercado', '345-456-7812'),
(246, 'Elena', 'Santos', 'Villanueva', 'Verduras Frescas El Manantial', '345-567-8924'),
(247, 'Diego', 'Gómez', 'Hernández', 'Frutas Exóticas La Palma', '345-678-9035'),
(248, 'Carmen', 'López', 'García', 'Panadería El Trigo', '345-789-0146'),
(249, 'Julio', 'Rodríguez', 'Sánchez', 'Aceites y Vinagres La Cocina', '345-890-1257'),
(250, 'Laura', 'Hernández', 'Ramos', 'Dulces y Chocolates La Delicia', '345-901-2368'),
(251, 'Ricardo', 'Cruz', 'Paredes', 'Quesos y Crema La Vaquita', '345-012-3479'),
(252, 'Ana', 'Vega', 'Navarro', 'Tortillas y Harinas El Molino', '345-123-4590'),
(253, 'Luis', 'Salinas', 'Flores', 'Abarrotes El Buen Precio', '345-234-5701'),
(254, 'María', 'Gutiérrez', 'Ortega', 'Lácteos La Granja', '345-345-6712'),
(255, 'Fernando', 'García', 'Juárez', 'Jugos y Smoothies La Fruta', '345-456-7813'),
(256, 'Patricia', 'Martínez', 'Núñez', 'Mermeladas y Conservas El Tarro', '345-567-8925'),
(257, 'José', 'Pérez', 'Luna', 'Frutos Secos y Semillas El Bosque', '345-678-9036'),
(258, 'Héctor', 'Ramírez', 'Cortés', 'Aceites y Especias La Casa', '345-789-0147'),
(259, 'Mónica', 'Moreno', 'Hernández', 'Verduras Frescas El Huerto', '345-890-1258'),
(260, 'Javier', 'Hernández', 'Mendoza', 'Pasteles y Galletas La Repostería', '345-901-2369'),
(261, 'Cristina', 'López', 'Vega', 'Té y Café La Especialidad', '345-012-3480'),
(262, 'Raúl', 'Navarrete', 'Salinas', 'Botanas y Snacks El Sabor', '345-123-4591'),
(263, 'Claudia', 'Castillo', 'Santos', 'Dulces y Chocolates La Estrella', '345-234-5702'),
(264, 'Roberto', 'Sánchez', 'Ríos', 'Carnes Finas La Reserva', '345-345-6713'),
(265, 'Ana', 'Díaz', 'Vásquez', 'Aceites y Vinagres El Olivar', '345-456-7814'),
(266, 'Juliana', 'Aguilar', 'Gómez', 'Lácteos Los Altos', '345-567-8926'),
(267, 'Miguel', 'Vargas', 'Cruz', 'Quesos y Cremas La Hacienda', '345-678-9037'),
(268, 'Patricia', 'Ríos', 'Peña', 'Tortillas y Harinas La Familia', '345-789-0148'),
(269, 'Luis', 'Mora', 'Fernández', 'Carnes Finas La Estrella', '345-890-1259'),
(270, 'María', 'Ortega', 'Rojas', 'Verduras Frescas El Campo', '345-901-2370'),
(271, 'Fernando', 'Serrano', 'Pérez', 'Jugos Naturales La Fuente', '345-012-3481'),
(272, 'Héctor', 'Núñez', 'Lara', 'Pastas y Harinas El Molino', '345-123-4592'),
(273, 'Ricardo', 'Reyes', 'García', 'Dulces y Chocolates La Casa', '345-234-5703'),
(274, 'Gabriela', 'Flores', 'Juárez', 'Verduras Frescas El Mercado', '345-345-6714'),
(275, 'David', 'Cruz', 'Méndez', 'Quesos y Cremas El Buen Pastor', '345-456-7815'),
(276, 'Juliana', 'Ramos', 'Hernández', 'Frutas Tropicales El Paraíso', '345-567-8927'),
(277, 'Mario', 'Gómez', 'Martínez', 'Aceites Gourmet La Cocina', '345-678-9038'),
(278, 'Laura', 'López', 'García', 'Dulces y Chocolates La Delicia', '345-789-0149'),
(279, 'Roberto', 'Pérez', 'Vega', 'Quesos y Cremas La Hacienda', '345-890-1250'),
(280, 'Ana', 'Ramírez', 'Sánchez', 'Verduras Frescas El Huerto', '345-901-2360'),
(281, 'Fernando', 'Díaz', 'Hernández', 'Panadería El Buen Pan', '345-012-3470'),
(282, 'Patricia', 'Moreno', 'Juárez', 'Lácteos La Granja', '345-123-4580'),
(283, 'José', 'González', 'Ríos', 'Salsas y Aderezos La Casa', '345-234-5691'),
(284, 'Héctor', 'Aguilar', 'Pérez', 'Tortillas y Harinas El Molino', '345-345-6702'),
(285, 'Carmen', 'Martínez', 'Vega', 'Botanas y Snacks El Sabor', '345-456-7816'),
(286, 'Luis', 'Reyes', 'López', 'Carnes Finas La Reserva', '345-567-8928'),
(287, 'Mónica', 'Navarrete', 'García', 'Frutas y Verduras La Estrella', '345-678-9039'),
(288, 'Javier', 'Salinas', 'Hernández', 'Jugos Naturales La Fuente', '345-789-0150'),
(289, 'Gabriela', 'Castillo', 'Ramírez', 'Pasteles y Galletas La Repostería', '345-890-1261'),
(290, 'David', 'Peña', 'Martínez', 'Aceites y Vinagres El Olivar', '345-901-2371'),
(291, 'Isabel', 'Ortega', 'Vega', 'Lácteos Los Altos', '345-012-3471'),
(292, 'Ricardo', 'Ríos', 'Flores', 'Té y Café La Especialidad', '345-123-4593'),
(293, 'Lucía', 'Serrano', 'Juárez', 'Quesos y Cremas La Vaquita', '345-234-5704'),
(294, 'Julio', 'Hernández', 'Gómez', 'Verduras Frescas El Huerto', '345-345-6715'),
(295, 'Elena', 'Mora', 'Pérez', 'Dulces y Chocolates La Estrella', '345-456-7817'),
(296, 'Andrés', 'Ramírez', 'Navarro', 'Frutos Secos y Semillas El Molino', '345-567-8929'),
(297, 'Patricia', 'López', 'Sánchez', 'Botanas y Dulces El Rincón', '345-678-9040'),
(298, 'Roberto', 'García', 'Hernández', 'Pasteles y Galletas La Dulzura', '345-789-0151'),
(299, 'Fernando', 'Pérez', 'Mendoza', 'Aceites y Especias La Cocina', '345-890-1262'),
(300, 'Mariana', 'Díaz', 'Ríos', 'Quesos y Cremas La Hacienda', '345-901-2372'),
(301, 'Miguel', 'Martínez', 'García', 'Jugos y Smoothies La Fruta', '345-012-3472'),
(302, 'Laura', 'González', 'Juárez', 'Mermeladas y Conservas El Tarro', '345-123-4594'),
(303, 'Ana', 'Ramírez', 'Navarrete', 'Verduras Frescas El Jardín', '345-234-5705'),
(304, 'Carlos', 'Hernández', 'Martínez', 'Frutas y Verduras El Mercado', '345-345-6716'),
(305, 'Sofía', 'López', 'García', 'Panadería El Trigo', '345-456-7818'),
(306, 'Julian Fernando', 'Romero', 'Rodriguez', 'Leche 19 hermanos', '391-302-4412'),
(307, 'Rodrigo', 'Escobedo', 'Alameda', 'Bolillo el gallero', '3481256932');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `rol` tinyint(1) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apepat` varchar(50) NOT NULL,
  `apemat` varchar(50) NOT NULL,
  `contra` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `rol`, `nombre`, `apepat`, `apemat`, `contra`) VALUES
(2, 2, 'Francisco', 'López ', 'Arriaga', '$2y$10$AqH96SGkvp9dbBhXyhekFulZAOx.yK/4JSVbnSBZyw5rjMhvfOEMS'),
(3, 2, 'Karina', 'Zavala ', 'León', '$2y$10$rmPouUzYSOyttsaklhknSecNl/wqlVOJGAdmEK/Ib0gGkarTeVpeu'),
(4, 2, 'Kenia ', 'Flores', 'Cortéz', '$2y$10$kjs4GbxjsULsYKtFM9cMp.YX31h5J3Yq.PjieIFIevIP1zHZOBs/y'),
(6, 1, 'Karla', 'Torres ', 'García', '$2y$10$CSLZQ4trnYssDesOFrIDnuKnbVT.ZhLSaqKnKxJ1jzyiZxIll9vB.'),
(7, 1, 'Isabel', 'Gómez', 'Morales', '$2y$10$feDUpICulZGEWJghBC7SkueFTLybvG7SBABTqJM.CSlLmMDeFLgvi'),
(8, 1, 'Silvia', 'Morales ', 'Melgoza', '$2y$10$N735XsxwLpcCVRSj.xtUpumrfY2Cg.MQas1eDn1znspFTqBgqv/ju'),
(12, 2, 'Juan', 'Pérez', 'González', '$2y$10$d6gD3RXg0GWr6/igbn1Bq.4XoxGuiWNwqvYm0pDOXhtL3/rtamQPi'),
(13, 2, 'Ana', 'Rodríguez', 'Sánchez', '$2y$10$3iycgEZtUCrl2EzlHAosP.CIf2jATU5yxhfb9DGjcDF8Zz70P17Nm'),
(14, 2, 'Luis', 'Fernández', 'Ramírez', '$2y$10$41TnNEhXz8GqZ7BPDFR2G.T6aZ5O0AL2CDiNAZPtgQGXdDKapzVj.'),
(16, 2, 'Jorge', 'Vargas', 'Mendoza', '1234'),
(19, 2, 'Gabriela', 'Morales', 'Silva', '1234'),
(20, 2, 'Ricardo', 'Castro', 'Rojas', '1234'),
(21, 2, 'Daniela', 'Navarro', 'Guerrero', '1234'),
(22, 2, 'Fernando', 'Molina', 'Flores', '1234'),
(23, 2, 'Paola', 'Ortega', 'Delgado', '1234'),
(24, 2, 'Diego', 'Soto', 'Paredes', '1234'),
(25, 2, 'Andrea', 'Hernández', 'Aguirre', '$2y$10$yVYRlplqR1/69M.kxYDfjedrGpBfIqDcKXI0ulJMSKcGv3r1TMP2O'),
(26, 2, 'Roberto', 'Chávez', 'Espinoza', '1234'),
(27, 2, 'Verónica', 'Mejía', 'Cordero', '1234'),
(28, 2, 'Hugo', 'Salazar', 'Pérez', '1234'),
(29, 2, 'Monserrat', 'Ramos', 'Luna', '1234'),
(30, 2, 'David', 'García', 'Estrada', '1234'),
(31, 2, 'Natalia', 'Vega', 'Campos', '1234'),
(32, 2, 'Emilio', 'Peña', 'Santos', '$2y$10$U03So4EJuJbYk9N03qMwvOADbBR5oMwTgShsdOTGBDedn9Uurg5q6'),
(33, 2, 'Cecilia', 'Flores', 'Hidalgo', '1234'),
(34, 2, 'Marco', 'Miranda', 'Fuentes', '1234'),
(35, 2, 'Lorena', 'Romero', 'Gutiérrez', '1234'),
(36, 2, 'Francisco', 'Domínguez', 'Montes', '1234'),
(37, 2, 'Camila', 'Castañeda', 'López', '1234'),
(38, 2, 'Sergio', 'Beltrán', 'Ortiz', '1234'),
(39, 2, 'Liliana', 'Zamora', 'Jiménez', '1234'),
(40, 2, 'Oscar', 'Morales', 'Pérez', '1234'),
(41, 2, 'Sofía', 'Gutiérrez', 'Jiménez', '1234'),
(42, 2, 'Héctor', 'Alvarez', 'Martín', '1234'),
(43, 2, 'Mónica', 'Ramírez', 'Castillo', '1234'),
(44, 2, 'Ricardo', 'Rivera', 'Vega', '1234'),
(45, 2, 'Verónica', 'Salazar', 'Herrera', '1234'),
(46, 2, 'Daniel', 'Sánchez', 'Ríos', '1234'),
(47, 2, 'Anahí', 'Mendoza', 'Guerrero', '$2y$10$mKwwp9oM.qftJUFQ2xGc1.14/v/JQp5JZSJobrgI7cW0LUOIyyY5u'),
(48, 2, 'Gabriela', 'Bermúdez', 'Fuentes', '1234'),
(49, 2, 'Javier', 'López', 'Guzmán', '1234'),
(50, 2, 'Iván', 'Rodríguez', 'Morales', '1234'),
(51, 2, 'Lourdes', 'Vargas', 'Castro', '1234'),
(52, 2, 'Carlos', 'Hernández', 'Silva', '1234'),
(53, 2, 'Paola', 'Martínez', 'Paredes', '1234'),
(54, 2, 'Fernando', 'Delgado', 'Torres', '1234'),
(55, 2, 'Antonio', 'Ramírez', 'Jiménez', '1234'),
(56, 2, 'Rosa', 'González', 'Salazar', '1234'),
(57, 2, 'Luis', 'Morales', 'Chavez', '1234'),
(58, 2, 'Margarita', 'Luna', 'Álvarez', '1234'),
(59, 2, 'José', 'González', 'Vázquez', '1234'),
(60, 2, 'Julio', 'Ruiz', 'Soto', '1234'),
(61, 2, 'Leticia', 'Fuentes', 'Díaz', '1234'),
(62, 2, 'Oscar', 'Peña', 'Figueroa', '1234'),
(63, 2, 'Sandra', 'Méndez', 'Rodríguez', '1234'),
(64, 2, 'David', 'Serrano', 'Mora', '1234'),
(65, 2, 'Adriana', 'López', 'Castañeda', '$2y$10$2U7l51XCgD2XRXwRMNUPF.DhjaIiHQNmh05nM9A2JUelfHydcvrP6'),
(66, 2, 'Carlos', 'Martínez', 'López', '1234'),
(67, 2, 'Mónica', 'Serrano', 'Reyes', '1234'),
(68, 2, 'Eduardo', 'Gómez', 'Morales', '1234'),
(69, 2, 'Susana', 'Torres', 'Sánchez', '1234'),
(70, 2, 'Martín', 'López', 'Ramírez', '1234'),
(71, 2, 'Ricardo', 'Fernández', 'Pérez', '1234'),
(72, 2, 'Jessica', 'Sánchez', 'Torres', '1234'),
(73, 2, 'Ramón', 'Torres', 'Cordero', '1234'),
(74, 2, 'Lucía', 'Vega', 'Serrano', '1234'),
(75, 2, 'Pedro', 'Castillo', 'Mendoza', '1234'),
(76, 2, 'Ángel', 'Alvarado', 'Moreno', '1234'),
(77, 2, 'Cristina', 'González', 'Delgado', '1234'),
(78, 2, 'Beatriz', 'Romero', 'López', '1234'),
(79, 2, 'Carlos', 'Pérez', 'González', '1234'),
(80, 2, 'Margarita', 'Serrano', 'Martínez', '1234'),
(81, 2, 'Miguel', 'Fuentes', 'Martín', '1234'),
(82, 2, 'José', 'Torres', 'Gómez', '1234'),
(83, 2, 'Isabel', 'Rodríguez', 'Martínez', '1234'),
(84, 2, 'Ricardo', 'Vargas', 'Hernández', '1234'),
(85, 2, 'Eva', 'Moreno', 'Serrano', '1234'),
(86, 2, 'Felipe', 'Morales', 'Vargas', '1234'),
(87, 2, 'Marina', 'Martínez', 'Fuentes', '1234'),
(88, 2, 'Roberto', 'Castaño', 'Méndez', '1234'),
(89, 2, 'José', 'Martín', 'González', '1234'),
(90, 2, 'Lorena', 'Vázquez', 'González', '1234'),
(91, 2, 'Antonio', 'Hernández', 'Méndez', '1234'),
(92, 2, 'Luisa', 'Bermúdez', 'Alarcón', '1234'),
(93, 2, 'Carlos', 'Torres', 'Gutiérrez', '1234'),
(94, 2, 'Ricardo', 'López', 'Serrano', '1234'),
(95, 2, 'José', 'Gómez', 'Rojas', '1234'),
(96, 2, 'Teresa', 'Luna', 'Mendoza', '1234'),
(97, 2, 'Victor', 'Delgado', 'Peña', '1234'),
(98, 2, 'Cecilia', 'Ruiz', 'Mora', '1234'),
(99, 2, 'Diana', 'López', 'Serrano', '1234'),
(100, 2, 'Óscar', 'Mendoza', 'Álvarez', '1234'),
(101, 2, 'Luis', 'Serrano', 'Martínez', '1234'),
(102, 2, 'Rocío', 'Vázquez', 'Santos', '1234'),
(103, 2, 'Tomás', 'Hernández', 'González', '1234'),
(104, 2, 'Julieta', 'Gómez', 'Muñoz', '1234'),
(105, 2, 'Felipe', 'Martín', 'Vargas', '1234'),
(106, 2, 'Olga', 'Ramírez', 'Sánchez', '1234'),
(107, 2, 'Eduardo', 'Serrano', 'Torres', '1234'),
(108, 2, 'Simón', 'González', 'Peña', '1234'),
(109, 2, 'Raquel', 'López', 'Díaz', '1234'),
(110, 2, 'César', 'Mendoza', 'Vázquez', '1234'),
(111, 2, 'Mercedes', 'Delgado', 'Castillo', '1234'),
(112, 2, 'Gustavo', 'Morales', 'Serrano', '1234'),
(113, 2, 'Yolanda', 'Ramírez', 'Luna', '1234'),
(114, 2, 'Ricardo', 'Cruz', 'Paredes', '1234'),
(115, 2, 'Valeria', 'Torres', 'Vega', '1234'),
(116, 2, 'Juan', 'Sánchez', 'Hernández', '1234'),
(117, 2, 'Susana', 'Méndez', 'Romero', '1234'),
(118, 2, 'Antonio', 'Salazar', 'López', '1234'),
(119, 2, 'Raúl', 'Vega', 'Mora', '1234'),
(120, 2, 'Patricia', 'Martínez', 'Guzmán', '1234'),
(121, 2, 'Miguel', 'Delgado', 'Ramírez', '1234'),
(122, 2, 'Fabiola', 'Torres', 'Pérez', '1234'),
(123, 2, 'Iván', 'Ramírez', 'Cordero', '1234'),
(124, 2, 'Leticia', 'López', 'Martínez', '1234'),
(125, 2, 'Carlos', 'Reyes', 'Morales', '1234'),
(126, 2, 'Santiago', 'Sánchez', 'Méndez', '1234'),
(127, 2, 'Esteban', 'González', 'Cordero', '1234'),
(128, 2, 'Erika', 'Fuentes', 'Luna', '1234'),
(129, 2, 'Claudia', 'Martínez', 'Pérez', '1234'),
(130, 2, 'Fernando', 'Paredes', 'Serrano', '1234'),
(131, 2, 'Renata', 'González', 'Martínez', '1234'),
(132, 2, 'Ramiro', 'Ruíz', 'Torres', '1234'),
(133, 2, 'Julia', 'Morales', 'Hernández', '1234'),
(134, 2, 'Sofía', 'Peña', 'Cordero', '1234'),
(135, 2, 'Emilio', 'Serrano', 'Romero', '1234'),
(136, 2, 'Lorena', 'Delgado', 'Luna', '1234'),
(137, 2, 'José', 'Reyes', 'Serrano', '1234'),
(138, 2, 'Cristina', 'Martínez', 'Hernández', '1234'),
(139, 2, 'Diego', 'Torres', 'Ramírez', '1234'),
(140, 2, 'Pablo', 'Gómez', 'Martínez', '1234'),
(141, 2, 'Marcos', 'Vega', 'López', '1234'),
(142, 2, 'Ángel', 'Serrano', 'Hernández', '1234'),
(143, 2, 'Gloria', 'Ramírez', 'Pérez', '1234'),
(145, 2, 'Juan', 'Martínez', 'Torres', '1234'),
(146, 2, 'Sandra', 'Martínez', 'Ramírez', '1234'),
(147, 2, 'Tomás', 'González', 'Serrano', '1234'),
(148, 2, 'Natividad', 'Torres', 'González', '1234'),
(149, 2, 'Jorge', 'Reyes', 'Sánchez', '1234'),
(150, 2, 'Daniel', 'Mora', 'Méndez', '1234'),
(151, 2, 'Mónica', 'Romero', 'Delgado', '1234'),
(152, 2, 'Pedro', 'Sánchez', 'Gómez', '1234'),
(153, 2, 'Alfredo Alonso', 'Méndez', 'Vega', '1234'),
(154, 2, 'Vanessa', 'Martínez', 'Castillo', '1234'),
(155, 2, 'Santiago', 'Hernández', 'Martínez', '1234'),
(156, 2, 'Raúl', 'González', 'Ramírez', '1234'),
(157, 2, 'Verónica', 'Delgado', 'Vargas', '1234'),
(158, 2, 'Antonio', 'Reyes', 'Serrano', '1234'),
(159, 2, 'Natalia', 'Sánchez', 'Delgado', '1234'),
(160, 2, 'Felipe', 'Torres', 'Romero', '1234'),
(161, 2, 'Rosa', 'Peña', 'Hernández', '1234'),
(162, 2, 'Lucía', 'González', 'Serrano', '1234'),
(163, 2, 'José', 'Méndez', 'Torres', '1234'),
(164, 2, 'Alba', 'González', 'Flores', '1234'),
(165, 2, 'Miguel Ángel', 'Fernández', 'Sánchez', '1234'),
(166, 2, 'Raquel', 'Martínez', 'Hernández', '1234'),
(167, 2, 'José Antonio', 'González', 'Jiménez', '1234'),
(168, 2, 'Mabel', 'Pérez', 'Ruiz', '$2y$10$1cjcvmarFcVTgy3bSYuoee/ry637lq5Tpfb55NZ9nfD.yQecyLieO'),
(169, 2, 'Luis Fernando', 'Martínez', 'Cordero', '1234'),
(170, 2, 'Marta', 'Torres', 'Gómez', '1234'),
(171, 2, 'Isabel', 'Ramírez', 'Vega', '1234'),
(172, 2, 'Antonio', 'Sánchez', 'Torres', '1234'),
(173, 2, 'Patricia', 'Paredes', 'Serrano', '1234'),
(174, 2, 'Carlos Alberto', 'Vargas', 'Morales', '1234'),
(175, 2, 'Laura', 'Gómez', 'Delgado', '$2y$10$gcNvNwiWQE2k2dfKjGrO8u1PGRLBm67rul67U6tjBg7NWy.DwO426'),
(176, 2, 'Miguel', 'Romero', 'Sánchez', '1234'),
(177, 2, 'Susana', 'Rodríguez', 'Martín', '1234'),
(178, 2, 'Javier', 'Delgado', 'Ramírez', '1234'),
(179, 2, 'Verónica', 'Serrano', 'González', '1234'),
(180, 2, 'Cristina', 'Martínez', 'Sánchez', '1234'),
(181, 2, 'José Manuel', 'Ruíz', 'Delgado', '1234'),
(182, 2, 'Beatriz', 'Méndez', 'Vázquez', '1234'),
(183, 2, 'Antonio', 'Torres', 'Martínez', '1234'),
(184, 2, 'Ricardo', 'Vázquez', 'Romero', '1234'),
(185, 2, 'Verónica', 'Cordero', 'Serrano', '1234'),
(186, 2, 'Pedro', 'González', 'Méndez', '1234'),
(187, 2, 'Esteban', 'Sánchez', 'Gómez', '1234'),
(188, 2, 'Eduardo', 'Serrano', 'Vega', '1234'),
(189, 2, 'Isabel', 'Vega', 'Morales', '1234'),
(190, 2, 'Lidia', 'López', 'Martínez', '1234'),
(191, 2, 'Rocío', 'Hernández', 'Reyes', '1234'),
(192, 2, 'Francisco', 'Peña', 'González', '1234'),
(193, 2, 'José Luis', 'Torres', 'Gómez', '1234'),
(194, 2, 'Mario', 'Martínez', 'Hernández', '1234'),
(195, 2, 'Raúl', 'Méndez', 'Torres', '1234'),
(196, 2, 'David', 'Reyes', 'Serrano', '1234'),
(197, 2, 'Claudia', 'Romero', 'Vega', '1234'),
(198, 2, 'Ángel', 'Martínez', 'Pérez', '1234'),
(199, 2, 'Jesús', 'Ruíz', 'Vargas', '1234'),
(200, 2, 'Sara', 'Moreno', 'Reyes', '1234'),
(201, 2, 'Esteban', 'Torres', 'Vega', '1234'),
(202, 2, 'José Antonio', 'González', 'López', '1234'),
(203, 2, 'Carmen', 'Torres', 'Ramírez', '1234'),
(204, 2, 'José Carlos', 'Serrano', 'González', '1234'),
(205, 2, 'Rosa María', 'González', 'Torres', '1234'),
(206, 2, 'Manuel', 'Pérez', 'Hernández', '1234'),
(207, 2, 'Ángel', 'Rodríguez', 'Martínez', '1234'),
(208, 2, 'Eva', 'Gómez', 'Serrano', '1234'),
(209, 2, 'Carlos', 'Reyes', 'Sánchez', '1234'),
(210, 2, 'Lina', 'Martínez', 'Torres', '1234'),
(211, 2, 'Carlos', 'Serrano', 'Ruíz', '1234'),
(212, 2, 'Cristina', 'Ramírez', 'Vázquez', '1234'),
(213, 2, 'José Miguel', 'Torres', 'Paredes', '1234'),
(214, 2, 'Gabriela', 'Méndez', 'Sánchez', '1234'),
(215, 2, 'Antonio', 'Pérez', 'Gómez', '1234'),
(216, 2, 'Pablo', 'Reyes', 'Ramírez', '1234'),
(217, 2, 'Marta', 'González', 'Pérez', '1234'),
(218, 2, 'Carlos', 'Romero', 'Delgado', '1234'),
(219, 2, 'José', 'Serrano', 'Morales', '1234'),
(220, 2, 'José Ramón', 'Vega', 'Méndez', '1234'),
(221, 2, 'Lucía', 'González', 'Vega', '1234'),
(222, 2, 'Raúl', 'Sánchez', 'Reyes', '1234'),
(223, 2, 'Carmen', 'Vázquez', 'Serrano', '1234'),
(224, 2, 'Antonio', 'Ramírez', 'Vargas', '1234'),
(225, 2, 'José Antonio', 'Gómez', 'Cordero', '1234'),
(226, 2, 'Luis', 'Martínez', 'Vega', '1234'),
(227, 2, 'Elena', 'Martínez', 'Ruiz', '1234'),
(228, 2, 'Javier', 'Torres', 'Cordero', '1234'),
(229, 2, 'Patricia', 'González', 'Delgado', '1234'),
(230, 2, 'Álvaro', 'Serrano', 'Vega', '1234'),
(231, 2, 'Sofía', 'Gómez', 'Ramírez', '1234'),
(232, 2, 'Manuel', 'Romero', 'Sánchez', '1234'),
(233, 2, 'Sandra', 'Vázquez', 'Morales', '1234'),
(234, 2, 'Luis', 'Sánchez', 'González', '1234'),
(235, 2, 'Santiago', 'Pérez', 'Hernández', '1234'),
(236, 2, 'Francisco', 'Reyes', 'Torres', '1234'),
(237, 2, 'Beatriz', 'Serrano', 'Sánchez', '1234'),
(238, 2, 'Raúl', 'González', 'Serrano', '1234'),
(239, 2, 'Antonio', 'Torres', 'Ramírez', '1234'),
(240, 2, 'Carmen', 'Delgado', 'Romero', '1234'),
(241, 2, 'Alfredo', 'Vega', 'Vargas', '1234'),
(242, 2, 'Dolores', 'Hernández', 'Gómez', '1234'),
(243, 2, 'Esteban', 'Morales', 'Serrano', '1234'),
(244, 2, 'Juan Carlos', 'Gómez', 'López', '1234'),
(245, 2, 'Isabel', 'Romero', 'Vega', '1234'),
(246, 2, 'José Luis', 'Torres', 'Pérez', '1234'),
(247, 2, 'Juliana', 'Ramírez', 'Vargas', '1234'),
(248, 2, 'Juan', 'Cordero', 'Sánchez', '1234'),
(249, 2, 'José Antonio', 'Hernández', 'Vega', '1234'),
(250, 2, 'Valeria', 'Sánchez', 'Ramírez', '1234'),
(251, 2, 'Ramiro', 'Romero', 'Vega', '1234'),
(252, 2, 'Ana María', 'Moreno', 'Pérez', '1234'),
(253, 2, 'Eva', 'González', 'Pérez', '1234'),
(254, 2, 'José Manuel', 'Méndez', 'Torres', '1234'),
(255, 2, 'Antonio', 'Sánchez', 'Romero', '1234'),
(256, 2, 'Lorena', 'López', 'Vega', '1234'),
(257, 2, 'Manuel', 'Torres', 'Vázquez', '1234'),
(258, 2, 'Andrés', 'Reyes', 'Serrano', '1234'),
(259, 2, 'María Isabel', 'Vega', 'Delgado', '1234'),
(260, 2, 'Santiago', 'Martínez', 'Pérez', '1234'),
(261, 2, 'Julieta', 'Cordero', 'González', '1234'),
(262, 2, 'Jorge', 'Hernández', 'Martínez', '1234'),
(263, 2, 'Gloria', 'Serrano', 'Sánchez', '1234'),
(264, 2, 'David', 'Vázquez', 'López', '1234'),
(265, 2, 'Raúl', 'Reyes', 'Vega', '1234'),
(266, 2, 'Felipe', 'González', 'Martínez', '1234'),
(267, 2, 'Margarita', 'Sánchez', 'Torres', '1234'),
(268, 2, 'Joaquín', 'Delgado', 'Gómez', '1234'),
(269, 2, 'Lucía', 'Vega', 'Pérez', '1234'),
(270, 2, 'Felipe', 'Martínez', 'López', '1234'),
(271, 2, 'Adelita2', 'Romero', 'Méndez', '$2y$10$lsX2y4d5Mo1HiSFPrNppteCZ4RXtA.Pb.SuGPc2m88cRBZWJhTfXq'),
(272, 2, 'Luis', 'Méndez', 'Ramírez', '1234'),
(273, 2, 'Martín', 'Serrano', 'Torres', '1234'),
(274, 2, 'Ricardo', 'Vázquez', 'Gómez', '1234'),
(275, 2, 'Raquel', 'Romero', 'Serrano', '1234'),
(276, 2, 'Eduardo', 'Martínez', 'Vega', '1234'),
(277, 2, 'Ricardo', 'Serrano', 'Vázquez', '1234'),
(278, 2, 'Verónica', 'Torres', 'González', '1234'),
(279, 2, 'Ángel', 'Ramírez', 'Sánchez', '1234'),
(280, 2, 'Olga', 'Martínez', 'Serrano', '1234'),
(281, 2, 'Lina', 'González', 'Vega', '1234'),
(282, 2, 'Fernando', 'López', 'González', '1234'),
(283, 2, 'Julio', 'Serrano', 'Torres', '1234'),
(284, 2, 'Fabiola', 'Cordero', 'Martínez', '1234'),
(285, 2, 'Joaquín', 'Vega', 'Serrano', '1234'),
(286, 2, 'Marina', 'Torres', 'Méndez', '1234'),
(288, 2, 'David', 'Romero', 'Torres', '1234'),
(289, 2, 'Victoria', 'Sánchez', 'Gómez', '1234'),
(290, 2, 'Carlos', 'Romero', 'Vega', '1234'),
(292, 2, 'Joaquín', 'Martínez', 'Reyes', '1234'),
(293, 2, 'Lucía', 'Torres', 'Serrano', '1234'),
(294, 2, 'Ricardo', 'Hernández', 'López', '1234'),
(295, 2, 'Juan', 'Pérez', 'Martínez', '1234'),
(296, 2, 'Patricia', 'Vega', 'Delgado', '1234'),
(297, 2, 'Marta', 'Serrano', 'Romero', '1234'),
(298, 2, 'Santiago', 'Reyes', 'Gómez', '1234'),
(299, 2, 'Javier', 'González', 'Ramírez', '1234'),
(300, 2, 'Mónica', 'Sánchez', 'Pérez', '1234'),
(301, 2, 'Esteban', 'Torres', 'González', '1234'),
(302, 2, 'Verónica', 'Serrano', 'Ramírez', '1234'),
(303, 2, 'Álvaro', 'Vázquez', 'Torres', '1234'),
(304, 2, 'Cristina', 'Martínez', 'Vega', '1234'),
(305, 2, 'Francisco', 'Gómez', 'Reyes', '1234'),
(306, 2, 'Gabriela', 'Delgado', 'Sánchez', '1234'),
(307, 2, 'Julieta', 'Torres', 'Vargas', '1234'),
(308, 2, 'Miguel', 'Pérez', 'González', '1234'),
(309, 2, 'Antonio', 'Hernández', 'Romero', '1234'),
(310, 2, 'Raúl', 'Reyes', 'Méndez', '1234'),
(311, 2, 'Manuel', 'González', 'Romero', '1234'),
(312, 2, 'Jesús', 'Serrano', 'Vega', '1234'),
(313, 2, 'Lucía', 'Reyes', 'Vázquez', '1234'),
(314, 2, 'Ángel', 'Delgado', 'Pérez', '1234'),
(315, 2, 'Verónica', 'Hernández', 'Martínez', '1234'),
(316, 2, 'Margarita', 'Torres', 'González', '1234'),
(317, 2, 'Carlos', 'Vega', 'Reyes', '1234'),
(318, 2, 'Miriam', 'Martínez', 'Torres', '1234'),
(319, 2, 'José Luis', 'Ramírez', 'Serrano', '1234'),
(320, 2, 'Rafael', 'González', 'Pérez', '1234'),
(322, 2, 'Raquel', 'Delgado', 'Torres', '1234'),
(323, 2, 'Carlos', 'Romero', 'Gómez', '1234'),
(324, 2, 'Felipe', 'Serrano', 'Ramírez', '1234'),
(325, 2, 'Alberto', 'Pérez', 'Vega', '1234'),
(326, 2, 'Joaquín', 'González', 'Sánchez', '1234'),
(328, 2, 'Ricardo', 'Ramírez', 'Vargas', '1234'),
(329, 2, 'José Antonio', 'Gómez', 'Reyes', '1234'),
(330, 2, 'Daniela', 'Reyes', 'Romero', '1234'),
(331, 2, 'Patricia', 'Martínez', 'Vázquez', '1234'),
(332, 2, 'Isabel', 'Hernández', 'Sánchez', '1234'),
(333, 2, 'Tomás', 'Vega', 'Delgado', '1234'),
(334, 2, 'Álvaro', 'Romero', 'Serrano', '1234'),
(335, 2, 'Raúl', 'Martínez', 'Romero', '1234'),
(336, 2, 'Juan Carlos', 'Delgado', 'Vega', '1234'),
(339, 2, 'Teresita', 'Palacios', 'Romero', '$2y$10$aoLQyoQRNSDf9NyFfNRLQ.AOkS6yRL9QVPPl.i.BniIxm11l5rvxO'),
(341, 1, 'Rosa María', 'Chávez', 'Camarena', '$2y$10$YyrAbp5ImRwV4.rJ49g1BuAelHsCerhturgvMZ.PTrrEvK8wZ6d5y'),
(343, 2, 'Pedro', 'Velázquez', 'Camarena', '$2y$10$caX8WmxiQk5vwW4jgaq8/emCXFkBHM.wOKAuY0u56HJihdAVSMz4G'),
(345, 1, 'Daniela', 'Gómez', 'Sepúlveda', '$2y$10$LdmBgzk865gqHu6rvfkdieBB5A7arRxr3oBMk0dKkIIAzHyFQM/aG'),
(346, 2, 'Lourdes', 'Salas', 'Puerto', '$2y$10$JNEBuwW.Sq/CCyL0/RM.P.o0K3g7MG18L8XC5v4VDxaf9bGcBZcpu'),
(347, 1, 'Héctor', 'Sepúlveda', 'Gómez', '$2y$10$ykBhwBZLGV7JfSRNwoKio.hHp1I5NMFXzrocHpIeu3oC3mw6Rcwca'),
(348, 1, 'Maiky', 'Alvarez', 'Tabarez', '$2y$10$SA37dp4V/yG46JKHQZIHs.w3taseVA9FxV6.ul0G04/V38Nm6o/wm'),
(349, 1, 'Giovanny', 'Torres', 'Cedillo', '$2y$10$PtlogKqWX9wlDPvTdIM8Lezebh9DhvpBVENoqv9NGknCohJWPlhoi'),
(351, 2, 'Braulio', 'López', ' Zaragoza', '$2y$10$F1xXoGpu8SMSsYoFXn/jXuSc70e0iX5t5be6Nzkl.z.8mP4kIeF1O'),
(352, 2, 'Juan Jose', 'Pérez', 'Sánchez', '$2y$10$H3RjzOJrPt7/7qvI0BPzFeTZTvuue/tSy/7wSx2o4eez4we6v2Qdy'),
(353, 2, 'Federico', 'Chávez', 'León', '$2y$10$dPNyDwCN.3hrbdDgoxMP9OlFjnelXU2dq2NdltHLGZFOFBHMVXQjS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_ventas` int(11) NOT NULL,
  `folio` varchar(45) NOT NULL,
  `total` double DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `fechacancelada` datetime DEFAULT NULL,
  `efectivo` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_ventas`, `folio`, `total`, `fecha`, `id_usuario`, `id_cliente`, `fechacancelada`, `efectivo`) VALUES
(1, '0001', 38, '2025-04-09 23:22:22', 6, 105, NULL, 40),
(2, '0002', 38, '2025-04-09 23:23:02', 6, 105, NULL, 40),
(3, '0003', 80, '2025-04-10 00:14:22', 6, NULL, NULL, 100),
(4, '0004', 110, '2025-04-10 16:04:36', 6, NULL, NULL, 120),
(5, '0005', 34, '2025-04-11 22:29:20', 6, 6, '2025-04-11 22:15:27', 100),
(6, '0006', 20, '2025-04-11 23:18:15', 6, NULL, '2025-04-11 22:18:55', 30),
(7, '0007', 20, '2025-04-12 00:25:37', 6, NULL, '2025-04-11 23:26:24', 50),
(8, '0008', 28, '2025-04-12 00:26:58', 6, NULL, '2025-04-11 23:27:17', 100),
(9, '0009', 15, '2025-04-14 21:26:57', 6, NULL, '2025-04-14 21:29:33', 20),
(10, '0010', 15, '2025-04-14 21:27:50', 6, NULL, '2025-04-14 21:31:24', 50),
(11, '0011', 20, '2025-04-14 22:25:00', 6, NULL, '2025-04-16 13:09:52', 50),
(12, '0012', 90, '2025-04-17 13:57:36', 7, NULL, NULL, 100),
(13, '0013', 18.5, '2025-04-17 14:13:48', 7, NULL, NULL, 0),
(14, '0014', 55.5, '2025-04-17 14:15:51', 7, NULL, NULL, 100),
(15, '0015', 185, '2025-04-17 14:22:34', 7, NULL, NULL, 200),
(16, '0016', 160, '2025-04-17 14:28:21', 7, NULL, NULL, 200),
(17, '0017', 37, '2025-04-17 18:58:06', 7, 111, NULL, 50),
(18, '0018', 66, '2025-04-21 13:28:29', 7, NULL, NULL, 100),
(19, '0019', 160, '2025-04-25 12:46:45', 7, NULL, NULL, 200),
(20, '0020', 34, '2025-04-25 13:48:03', 7, NULL, NULL, 50),
(21, '0021', 170, '2025-04-26 15:11:08', 7, NULL, NULL, 0),
(22, '0022', 170, '2025-04-28 12:03:50', 7, 39, '2025-04-28 12:28:50', 200),
(23, '0023', 148, '2025-04-28 13:15:26', 7, NULL, '2025-04-28 12:28:47', 150),
(24, '0024', 68, '2025-04-28 13:25:19', 7, NULL, '2025-04-29 14:32:11', 100),
(25, '0025', 195, '2025-05-05 10:38:51', 8, NULL, '2025-05-05 14:21:37', 200),
(26, '0026', 225, '2025-05-05 11:13:34', 8, NULL, '2025-05-05 14:21:09', 250),
(27, '0027', 125, '2025-05-05 14:53:31', 8, NULL, NULL, 150),
(28, '0028', 625, '2025-05-06 16:29:50', 348, NULL, NULL, 0),
(29, '0029', 444, '2025-05-10 00:18:36', 8, NULL, NULL, 500),
(30, '0030', 2249, '2025-05-12 17:45:34', 6, NULL, '2025-05-13 18:03:55', 10000000),
(31, '0031', 250, '2025-05-12 17:56:30', 6, NULL, '2025-05-12 16:57:12', 300),
(32, '0032', 450, '2025-05-13 16:26:45', 6, NULL, NULL, 500);

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `trg_devolver_productos` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
  IF OLD.fechacancelada IS NULL AND NEW.fechacancelada IS NOT NULL THEN
    
    UPDATE productos p
    JOIN det_venta dv ON p.id_producto = dv.id_producto
    SET p.cantidad = p.cantidad + dv.cantidad
    WHERE dv.id_venta = NEW.id_ventas;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_compras_detalle`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_compras_detalle` (
`id_comprasprov` int(11)
,`folio` varchar(45)
,`fecha` datetime
,`total_compra` decimal(12,2)
,`estado` enum('activa','cancelada')
,`proveedor` varchar(302)
,`empresa` varchar(150)
,`usuario` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_mas_comprados`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_mas_comprados` (
`id_producto` int(11)
,`nombre` varchar(100)
,`total_comprado` decimal(32,2)
,`total_gastado` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_mas_vendidos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_mas_vendidos` (
`id_producto` int(11)
,`nombre` varchar(100)
,`total_vendido` double
,`total_ingresos` double
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas_detalle`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas_detalle` (
`id_ventas` int(11)
,`folio` varchar(45)
,`total` double
,`fecha` datetime
,`efectivo` double
,`usuario` varchar(152)
,`cliente` varchar(302)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_compras_detalle`
--
DROP TABLE IF EXISTS `vista_compras_detalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_compras_detalle`  AS  select `cp`.`id_comprasprov` AS `id_comprasprov`,`cp`.`folio` AS `folio`,`cp`.`fecha` AS `fecha`,`cp`.`total_compra` AS `total_compra`,`cp`.`estado` AS `estado`,concat(`p`.`nombre`,' ',`p`.`apepat`,' ',`p`.`apemat`) AS `proveedor`,`p`.`empresa` AS `empresa`,`u`.`nombre` AS `usuario` from ((`compras_prov` `cp` left join `proveedores` `p` on((`cp`.`id_proveedor` = `p`.`id_proveedor`))) left join `usuarios` `u` on((`cp`.`id_usuario` = `u`.`id_usuario`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_mas_comprados`
--
DROP TABLE IF EXISTS `vista_productos_mas_comprados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_mas_comprados`  AS  select `dc`.`id_producto` AS `id_producto`,`p`.`nombre` AS `nombre`,sum(`dc`.`cantidad`) AS `total_comprado`,sum(`dc`.`importe`) AS `total_gastado` from (`det_compra` `dc` join `productos` `p` on((`dc`.`id_producto` = `p`.`id_producto`))) group by `dc`.`id_producto`,`p`.`nombre` order by `total_comprado` desc ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_mas_vendidos`
--
DROP TABLE IF EXISTS `vista_productos_mas_vendidos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_mas_vendidos`  AS  select `dv`.`id_producto` AS `id_producto`,`p`.`nombre` AS `nombre`,sum(`dv`.`cantidad`) AS `total_vendido`,sum(`dv`.`importe`) AS `total_ingresos` from (`det_venta` `dv` join `productos` `p` on((`dv`.`id_producto` = `p`.`id_producto`))) group by `dv`.`id_producto`,`p`.`nombre` order by `total_vendido` desc ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas_detalle`
--
DROP TABLE IF EXISTS `vista_ventas_detalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas_detalle`  AS  select `v`.`id_ventas` AS `id_ventas`,`v`.`folio` AS `folio`,`v`.`total` AS `total`,`v`.`fecha` AS `fecha`,`v`.`efectivo` AS `efectivo`,concat(`u`.`nombre`,' ',`u`.`apepat`,' ',`u`.`apemat`) AS `usuario`,concat(`c`.`nombre`,' ',`c`.`apepat`,' ',`c`.`apemat`) AS `cliente` from ((`ventas` `v` left join `usuarios` `u` on((`v`.`id_usuario` = `u`.`id_usuario`))) left join `clientes` `c` on((`v`.`id_cliente` = `c`.`id_cliente`))) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_comprasprov` (`id_comprasprov`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `compras_prov`
--
ALTER TABLE `compras_prov`
  ADD PRIMARY KEY (`id_comprasprov`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `fk_compras_usuario` (`id_usuario`);

--
-- Indices de la tabla `compras_prov_backup`
--
ALTER TABLE `compras_prov_backup`
  ADD PRIMARY KEY (`id_comprasprov`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `fk_compras_usuario` (`id_usuario`);

--
-- Indices de la tabla `det_compra`
--
ALTER TABLE `det_compra`
  ADD PRIMARY KEY (`id_detcompra`),
  ADD KEY `idx_producto` (`id_producto`),
  ADD KEY `idx_compra` (`id_comprasprov`);

--
-- Indices de la tabla `det_compra_backup`
--
ALTER TABLE `det_compra_backup`
  ADD PRIMARY KEY (`id_detcompra`),
  ADD KEY `idx_producto` (`id_producto`),
  ADD KEY `idx_compra` (`id_comprasprov`);

--
-- Indices de la tabla `det_venta`
--
ALTER TABLE `det_venta`
  ADD PRIMARY KEY (`id_detventa`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `det_venta_ibfk_1` (`id_venta`);

--
-- Indices de la tabla `log_trigger`
--
ALTER TABLE `log_trigger`
  ADD PRIMARY KEY (`id_log`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `fk_categoria` (`id_categoria`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_baja_alta` (`baja_alta`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre` (`nombre`,`apepat`,`apemat`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_ventas`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=330;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT de la tabla `compras_prov`
--
ALTER TABLE `compras_prov`
  MODIFY `id_comprasprov` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `compras_prov_backup`
--
ALTER TABLE `compras_prov_backup`
  MODIFY `id_comprasprov` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `det_compra`
--
ALTER TABLE `det_compra`
  MODIFY `id_detcompra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `det_compra_backup`
--
ALTER TABLE `det_compra_backup`
  MODIFY `id_detcompra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `det_venta`
--
ALTER TABLE `det_venta`
  MODIFY `id_detventa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `log_trigger`
--
ALTER TABLE `log_trigger`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=745;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=354;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_ventas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD CONSTRAINT `caja_movimientos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `caja_movimientos_ibfk_2` FOREIGN KEY (`id_comprasprov`) REFERENCES `compras_prov` (`id_comprasprov`);

--
-- Filtros para la tabla `compras_prov`
--
ALTER TABLE `compras_prov`
  ADD CONSTRAINT `compras_prov_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `fk_compras_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `det_compra`
--
ALTER TABLE `det_compra`
  ADD CONSTRAINT `det_compra_ibfk_1` FOREIGN KEY (`id_comprasprov`) REFERENCES `compras_prov` (`id_comprasprov`),
  ADD CONSTRAINT `det_compra_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `det_venta`
--
ALTER TABLE `det_venta`
  ADD CONSTRAINT `det_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_ventas`),
  ADD CONSTRAINT `det_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
