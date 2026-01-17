<?php
// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include("funciones/conectar.php");

// Validación del folio (ahora acepta valores alfanuméricos)
// Validación del ID (usado como folio)
if(!isset($_GET['folio'])) {
    die("Error: El parámetro 'folio' no está presente en la URL. Ejemplo correcto: ticket_compra.php?folio=0020");
}

// Elimina ceros a la izquierda y sanitiza
$folio = ltrim(trim($_GET['folio']), '0');

if(empty($folio) || !is_numeric($folio)) {
    die("Error: El folio debe ser un número válido.");
}

// Consulta usando id_comprasprov
try {
    $stmt = $consulta->prepare("SELECT compras_prov.*, usuarios.nombre, usuarios.apepat, usuarios.apemat 
                                 FROM compras_prov 
                                 LEFT JOIN usuarios ON usuarios.id_usuario = compras_prov.id_usuario 
                                 WHERE id_comprasprov = :id");
    $stmt->bindParam(':id', $folio, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row) {
        die("No se encontró el ticket con el folio: " . str_pad($folio, 6, "0", STR_PAD_LEFT) . ". Verifica que el número sea correcto.");
    }


    // Consulta del proveedor
    $stmt_prov = $consulta->prepare("SELECT * FROM proveedores WHERE id_proveedor = :id_proveedor");
    $stmt_prov->bindParam(':id_proveedor', $row['id_proveedor'], PDO::PARAM_INT);
    $stmt_prov->execute();
    $proveedor = $stmt_prov->fetch(PDO::FETCH_ASSOC);
    
    if(!$proveedor) {
        die("Error: No se encontró información del proveedor asociado a este ticket.");
    }

    $total = 0;
} catch(PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<head>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-3.7.1.min"></script>
</head>
<link rel="icon" type="image/png" href="Graficos/favicon.ico" />
<title>Ticket Compra</title>

<div id="resultados_ticket" style="position:absolute;top:0px;left:0px;margin:0px;padding:0px;width:300px; border:0px solid; font:Arial, Helvetica, sans-serif; font-family:Arial, Helvetica, sans-serif;">
    <div style="position:RELATIVE;top:0px;left:0px;width:<?=$ancho?>px;text-align:center; font-size:12px;">
        <img src="img/tiendita.jpg" height="80" />
    </div>
    <div style="position:RELATIVE;left:0px;width:<?=$ancho?>px;text-align:center; font-size:14px;">
        <label>Domicilio <br>Col.Villafuerte, Ayotlán, Jal. </label><br />
        <label>tel 3481248692</label><br />
        <label><b>Ticket Compra No. : <?=$row["folio"]; ?></b></LABEL>
    </div>
    <div style="position:RELATIVE;left:5px;font-size:12px;width:<?=$ancho?>px;text-align:left;">
        <?php
        $fecha=explode(" ",$row["fecha"]);
        $ano=explode("-",$fecha[0]);
        $mes=$ano[1];
        $dia=$ano[2];
        $horas = explode(":",$fecha[1]);
        $hora = $horas[0];
        if($horas[0]>12) $hora=$horas[0]-12;
        echo "<center>Fecha: ".$dia."/".$mes."/".$ano[0]." ".$hora.":".$horas[1].":".$horas[2]."</center>";
        echo "<center>Proveedor: ".$proveedor['empresa']."</center>";
        echo "<center>Contacto: ".$proveedor['nombre']." ".$proveedor['apepat']." ".$proveedor['apemat']."</center>";
        echo "<center>Tel: ".$proveedor['telefono']."</center>";
        ?>
    </div>
    <div style="position:RELATIVE;left:0px;width:<?=$ancho?>px;text-align:left;">
        <div style="height:16px;font-size:12px;">
            <div style="position:relative; width:<?=$ancho+1?>px; height:30px;">
                <table width="100%" style="font-size:12px;" border="0" cellpadding="0" cellspacing="0">
                    <tr style=" color:#000; font-weight:bold;;">
                        <td width="100">Cantidad</td>
                        <td>Descripcion</td>
                        <td align="center">&nbsp;Precio</td>
                        <td width="100" align="right">Importe</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div style="text-align:center;"><hr></div>
        <table width="100%" class="table" style="font-size:12px;" border="0" cellpadding="0" cellspacing="0">
            <?
            $resulta = $consulta->query("SELECT det_compra.*, productos.nombre, productos.codigo 
                                       FROM det_compra 
                                       LEFT JOIN productos ON productos.id_producto=det_compra.id_producto 
                                       WHERE id_comprasprov=".$row["id_comprasprov"]);
            foreach ($resulta as $reg){
                $Impo=($reg["cantidad"]*($reg["precio_uni"]));
                $precio = $reg["precio_uni"];
                $total+=$Impo;
                ?>
                <tr>
                    <td align="center" colspan="5"><?=$reg["codigo"]." -  ".strtoupper($reg["nombre"])?></td>
                </tr>
                <tr>
                    <td width="40" align="center"><?=$reg["cantidad"]?></td>
                    <td align="center"><b>$<?=number_format($precio,2)?></b></td>
                    <td align="right"><b><?="$".number_format($Impo, 2,'.',',')?></b></td>
                </tr>
            <?
            }
            ?>
        </table>
        <?
        echo '<div style="text-align:left; width:100%; font-size:16px; text-align:right;"><b>Total Compra &nbsp;&nbsp;&nbsp;<span style="float:right;"> $&nbsp;'.number_format($total,2,'.',',').'</span></b></div>';
        if(isset($row["efectivo"]) && $row["efectivo"] > 0){
            $cambio = $row["efectivo"] - $total;
            echo '<div style="text-align:left; width:100%; font-size:16px; text-align:right;"><b>Efectivo &nbsp;&nbsp;&nbsp;<span style="float:right;"> $&nbsp;' . number_format($row["efectivo"],2,'.',',') . '</span></b></div>';
            echo '<div style="text-align:left; width:100%; font-size:16px; text-align:right;"><b>Cambio &nbsp;&nbsp;&nbsp;<span style="float:right;"> $&nbsp;' . number_format($cambio,2,'.',',') . '</span></b></div>';
        }
        
        ?>
        <div style="text-align:left; font-size:12px;">*<?=num2letras($total)?>*</div>
        <div style="text-align:left;font-size:12px; ">
            &nbsp;Registrado por: <?=$row["nombre"]." ".$row["apepat"]." ".$row["apemat"]?>
        </div>
        <label style="text-align:left;font-size:12px; "><center>Documento de compra</center></LABEL>
    </div>
</div>
</HTML>
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script>
$(document).ready(function(e) {
    cerrar() ;
    function cerrar() {
        window.print();
        setTimeout(window.close,3000);
    }
});
</script>
<?php
// Función num2letras se mantiene igual
function num2letras($num, $fem = false, $dec = true) {
   // ... (la función permanece igual)
}
?>
</html>
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>