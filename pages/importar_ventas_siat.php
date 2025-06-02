<?php
$page_security = 'SA_CREATEMODULES';
$path_to_root = "../../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Libro de ventas externo - V2.0 CSV"));

include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/includes/ui/items_cart.inc");

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/functions.inc.php");

include_once($path_to_root . "/gl/includes/gl_db.inc");




function parseCSV($rutaCSV){
    $handle = fopen($rutaCSV, "r");
    $filas = array(); //explode(chr(13).chr(10),$file);
    $cntTmp = 0;
    while (($line = fgets($handle)) !== false) {
        if($cntTmp>0){
            $csvReg = explode(",", $line);
            $csvIn_data_row = array();
            $csvIn_data_row["NRO"] = str_replace('"', '', $csvReg[0] ) ;     
			$csvIn_data_row["FECHA_DE_LA_FACTURA"] = str_replace('"', '', $csvReg[1] ) ;     
			$csvIn_data_row["NRO_DE_LA_FACTURA"] = str_replace('"', '', $csvReg[2] ) ;     
			$csvIn_data_row["CODIGO_DE_AUTORIZACION"] = str_replace('"', '', $csvReg[3] ) ;     
			$csvIn_data_row["NIT___CI_CLIENTE"] = str_replace('"', '', $csvReg[4] ) ;     
			$csvIn_data_row["COMPLEMENTO"] = str_replace('"', '', $csvReg[5] ) ;     
			$csvIn_data_row["NOMBRE_O_RAZON_SOCIAL"] = str_replace('"', '', $csvReg[6] ) ;     
			$csvIn_data_row["IMPORTE_TOTAL_DE_LA_VENTA"] = str_replace('"', '', $csvReg[7] ) ;     
			$csvIn_data_row["IMPORTE_ICE"] = str_replace('"', '', $csvReg[8] ) ;     
			$csvIn_data_row["IMPORTE_IEHD"] = str_replace('"', '', $csvReg[9] ) ;     
			$csvIn_data_row["IMPORTE_IPJ"] = str_replace('"', '', $csvReg[10] ) ;     
			$csvIn_data_row["TASAS"] = str_replace('"', '', $csvReg[11] ) ;     
			$csvIn_data_row["OTROS_NO_SUJETOS_AL_IVA"] = str_replace('"', '', $csvReg[12] ) ;     
			$csvIn_data_row["EXPORTACIONES_Y_OPERACIONES_EXENTAS"] = str_replace('"', '', $csvReg[13] ) ;     
			$csvIn_data_row["VENTAS_GRAVADAS_A_TASA_CERO"] = str_replace('"', '', $csvReg[14] ) ;     
			$csvIn_data_row["SUBTOTAL"] = str_replace('"', '', $csvReg[15] ) ;     
			$csvIn_data_row["DESCUENTOS_BONIFICACIONES_Y_REBAJAS_SUJETAS_AL_IVA"] = str_replace('"', '', $csvReg[16] ) ;     
			$csvIn_data_row["IMPORTE_GIFT_CARD"] = str_replace('"', '', $csvReg[17] ) ;     
			$csvIn_data_row["IMPORTE_BASE_PARA_DEBITO_FISCAL"] = str_replace('"', '', $csvReg[18] ) ;     
			$csvIn_data_row["DEBITO_FISCAL"] = str_replace('"', '', $csvReg[19] ) ;     
			$csvIn_data_row["ESTADO"] = str_replace('"', '', $csvReg[20] ) ;     
			$csvIn_data_row["CODIGO_DE_CONTROL"] = str_replace('"', '', $csvReg[21] ) ;     
			$csvIn_data_row["TIPO_DE_VENTA"] = str_replace('"', '', $csvReg[22] ) ;     
			$csvIn_data_row["CON_DERECHO_A_CREDITO_FISCAL"] = str_replace('"', '', $csvReg[23] ) ;     
			$csvIn_data_row["ESTADO_CONSOLIDACION"] = str_replace('"', '', $csvReg[24] ) ;  
			
            $filas[] = $csvIn_data_row;
        }
        $cntTmp++;
    }
    
    //echo "cntTmp: $cntTmp<br>";
    //echo "fila 0 :".json_encode($filas[0])." <br>";
    //echo "filasCSV: ".count($filas)."<br>";
    return $filas;
}


function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  set_focus('_code_id_edit');
}

extract($_POST);

if (isset($deletetable) && $deletetable) 
{
	$sqlDelete = "DELETE FROM `0_facturas_venta`";
	db_query($sqlDelete, "No se pudo eliminar");

}

check_db_has_gl_account_groups(_("There are no account groups defined. Please define at least one account group before entering accounts."));

$empresa = get_company_prefs();


?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">

<script type="text/javascript" src="../js/jquery.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../js/datatableinput.js"></script>
<script type="text/javascript" src="../js/books_ventas_externoCSV.js?v=1"></script>
<script type="text/javascript" src="../js/bulk_selection_ventas.js"></script>

<form style="margin-left:10px" method="post" action="#" enctype="multipart/form-data">
	<div style="border:1px solid black; margin-left: 40px; width:300px; padding:10px">
		<label>Importar archivo CSV</label></br></br>
		<input type="hidden" name="accion" value="importar">
		<input id="importarTXT" name="archivo" type="file" value="Importar TXT"></br></br>
		<input type="submit" value="Importar">
	</div>
</form>
<form style="margin-left:10px" method="post" action="" enctype="multipart/form-data" onsubmit="return confirm('Esta seguro de eliminar las registros guardados?Esto reseteara las ventas, sin eliminar los asientos ya generados.');">
	<div style="border:1px solid black; margin-left: 40px; width:300px; padding:10px">
		<input type="hidden" name="deletetable" value="1">	
		<input type="submit" value="Limpiar datos">
	</div>
</form>
<div id="contenedor">
	<center>
		<h1>Ventas</h1>
	</center>
	<h4>Filtrar por fecha</h4>
<table>
<tr>
	<td>
		Fecha de inicio:
	</td>
	<td>
		<input type="date" id="fechaIni">
	</td>
</tr>
<tr>
	<td>
		Fecha fin:
	</td>
	<td>
		<input type="date" id="fechaFin">
	</td>
</tr>
<tr>
	<td colspan="2">
		<input id="filtrar" type="button" value="Filtrar">
	</td>
</tr>
</table>
</br></br>
<table id="tablaArticulos" class="bulk-selection-enabled">
		<thead>
		<tr>
			
			<!-- COLS HTML TODO-->
			<!--<td>
			NRO
			</td>-->
			<td>
			FECHA DE LA FACTURA
			</td>
			<td>
			NRO DE LA FACTURA
			</td>
			<td>
			CODIGO DE AUTORIZACIÓN
			</td>
			<td>
			NIT / CI CLIENTE
			</td>
			<!--<td>
			COMPLEMENTO
			</td>-->
			<td>
			NOMBRE O RAZON SOCIAL
			</td>
			<td>
			IMPORTE TOTAL DE LA VENTA
			</td>
			<!--<td>
			IMPORTE ICE
			</td>
			-->
			<!--<td>
			IMPORTE IEHD
			</td>
			-->
			<td>
			IMPORTE IPJ
			</td>
			<!--<td>
			TASAS
			</td>
			-->
			<!--<td>
			OTROS NO SUJETOS AL IVA
			</td>
			-->
			<td>
			EXPORTACIONES Y OPERACIONES EXENTAS
			</td>
			<td>
			VENTAS GRAVADAS A TASA CERO
			</td>
			<!--<td>
			SUBTOTAL
			</td>
			-->
			<td>
			DESCUENTOS BONIFICACIONES Y REBAJAS SUJETAS AL IVA
			</td>
			<!--<td>
			IMPORTE GIFT CARD
			</td>
			-->
			<td>
			IMPORTE BASE PARA DEBITO FISCAL
			</td>
			<td>
			DEBITO FISCAL
			</td>
			<td>
			ESTADO
			</td>
			<td>
			CODIGO DE CONTROL
			</td>
			<!--<td>
			TIPO DE VENTA
			</td>
			-->
			<!--<td>
			CON DERECHO A CREDITO FISCAL
			</td>
			-->
			<!--<td>
			ESTADO CONSOLIDACION
			</td>
			-->
			<td class="dropdown">
				Cuenta uno del DEBE
			</td>

			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 1
			</td>

			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 2
			</td>

			<td class="dropdown">
				Cuenta dos del DEBE - Imp. Trans. Gasto
			</td>

			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 1
			</td>

			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 2
			</td>

			<td class="dropdown" >
				Cuenta uno del HABER - Ventas
			</td>

			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 1
			</td>

			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 2
			</td>

			<td class="dropdown">
				Cuenta dos del HABER - D&eacute;bito fiscal
			</td>

			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 1
			</td>

			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 2
			</td>

			<td class="dropdown">
				Cuenta tres del HABER - Impto. Trans. Pasivo
			</td>

			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 1
			</td>

			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				Dimensi&oacute;n 2
			</td>

			<td>
				Glosa
			</td>

			<td>
				Contabilizar
			</td>
		</tr>
		</thead>
		<tbody>
			<?php
				$indice = 0;
				$sql = "SELECT * FROM `0_facturas_venta`";
				$result = db_query($sql, "Error en obtener las facturas de compra");
				$ind = 1;

				while ($row = mysql_fetch_array($result)) {
					$indice++;
					echo "<tr id='fila".$indice."'>";
					
					///
					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  NRO 
					//echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["fecha"]."' disabled >";  //  FECHA_DE_LA_FACTURA 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["nro_fact"]."' disabled >";  //  NRO_DE_LA_FACTURA 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["nro_auth"]."' disabled >";  //  CODIGO_DE_AUTORIZACION 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["nit"]."' disabled >";  //  NIT___CI_CLIENTE 
					echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  COMPLEMENTO 
					//echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["razon_social"]."' disabled >";  //  NOMBRE_O_RAZON_SOCIAL 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["imp"]."' disabled >";  //  IMPORTE_TOTAL_DE_LA_VENTA 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["imp_ice"]."' disabled >";  //  IMPORTE_ICE 
					echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  IMPORTE_IEHD 
					//echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  IMPORTE_IPJ 
					//echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  TASAS 
					//echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  OTROS_NO_SUJETOS_AL_IVA 
					//echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["imp_exc"]."' disabled >";  //  EXPORTACIONES_Y_OPERACIONES_EXENTAS 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["tasa_cero"]."' disabled >";  //  VENTAS_GRAVADAS_A_TASA_CERO 
					echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  SUBTOTAL 
					//echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["dbr"]."' disabled >";  //  DESCUENTOS_BONIFICACIONES_Y_REBAJAS_SUJETAS_AL_IVA 
					echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  IMPORTE_GIFT_CARD 
					//echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["imp_deb_fiscal"]."' disabled >";  //  IMPORTE_BASE_PARA_DEBITO_FISCAL 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["deb_fiscal"]."' disabled >";  //  DEBITO_FISCAL 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["estado"]."' disabled >";  //  ESTADO 
					echo "</td>"; 


					echo "<td>";
					echo "<input type='text' value='".$row["cod_control"]."' disabled >";  //  CODIGO_DE_CONTROL 
					echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  TIPO_DE_VENTA 
					//echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  CON_DERECHO_A_CREDITO_FISCAL 
					//echo "</td>"; 


					//echo "<td>";
					//echo "<input type='text' value='".$row["nulo"]."' disabled >";  //  ESTADO_CONSOLIDACION 
					//echo "</td>"; 



					switch ($empresa['use_dimension']) {
						case 1:
							$display1 = true;
							$display2 = false;
							break;
						case 2:
							$display1 = true;
							$display2 = true;
							break;
						case 0:
							$display1 = false;
							$display2 = false;
							break;
					}

					echo generarCuentas($row['debe1'], false);

					echo generarDimensiones($row['debe1_dimension1'], false, $display1);

					echo generarDimensiones($row['debe1_dimension2'], false, $display2);

					echo generarCuentas($row['debe2'], false);

					echo generarDimensiones($row['debe2_dimension1'], false, $display1);

					echo generarDimensiones($row['debe2_dimension2'], false, $display2);

					echo generarCuentas($row['haber1'], false);

					echo generarDimensiones($row['haber1_dimension1'], false, $display1);

					echo generarDimensiones($row['haber1_dimension2'], false, $display2);

					echo generarCuentas($row['haber2'], false);

					echo generarDimensiones($row['haber2_dimension1'], false, $display1);

					echo generarDimensiones($row['haber2_dimension2'], false, $display2);

					echo generarCuentas($row['haber3'], false);

					echo generarDimensiones($row['haber3_dimension1'], false, $display1);

					echo generarDimensiones($row['haber3_dimension2'], false, $display2);

					echo "<td>";
					echo "<input type='text' value='";
					echo $row['memo'];
					echo "' disabled></td>";

					echo "<td>";
					echo '<a target="_blank" href="../gl/view/gl_trans_view.php?type_id=0&trans_no='.$row['nro_trans'].'"><input type="button" value="Ver Asiento #'.$row['nro_trans'].'"></a>';
					echo "</td>";

					echo "</tr>";
				}


				if (isset($accion))
				{
					if ($accion == 'importar')
					{
						//copy(,);
						move_uploaded_file($_FILES['archivo']['tmp_name'], "importados/ventas/" .$_FILES['archivo']['name']);
						$file = file_get_contents("importados/ventas/" .$_FILES['archivo']['name']);
						//$filas = explode(chr(13).chr(10),$file);
						//echo "archivo0000000 "."importados/ventas/" .$_FILES['archivo']['name']." <br>";
						$filas = parseCSV("importados/ventas/" .$_FILES['archivo']['name']);
						
						for ($i=0; $i < count($filas); $i++)
						{
						$columnas = $filas[$i];  //$columnas = explode("|",$filas[$i]);
						if (isset($columnas["FECHA_DE_LA_FACTURA"]) && $columnas["FECHA_DE_LA_FACTURA"]!=null) {
							//echo ">>>> ".json_encode($columnas)." <<<<<<br>";
							//$fecha_transaccion = DateTime::createFromFormat('d/m/Y', $columnas[2]);
							//$columnas[2] =  $fecha_transaccion->format('Y-m-d');
							$fecha_transaccion = DateTime::createFromFormat('d/m/Y', $columnas["FECHA_DE_LA_FACTURA"] );
							$columnas["FECHA_DE_LA_FACTURA"] = $fecha_transaccion->format('Y-m-d');

							$indice++;
							echo '<tr id="fila'.$indice.'" class="bulk-control-enabled">';
							?>

								<!--<td>
								<input type="text" value="<?php echo $columnas["NRO"];?>" id="cel_reg_<?php echo $indice;?>_NRO"  >
								</td>-->
								<td>
								<input type="date" value="<?php echo $columnas["FECHA_DE_LA_FACTURA"];?>" id="cel_reg_<?php echo $indice;?>_FECHA_DE_LA_FACTURA"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["NRO_DE_LA_FACTURA"];?>" id="cel_reg_<?php echo $indice;?>_NRO_DE_LA_FACTURA"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["CODIGO_DE_AUTORIZACION"];?>" id="cel_reg_<?php echo $indice;?>_CODIGO_DE_AUTORIZACION"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["NIT___CI_CLIENTE"];?>" id="cel_reg_<?php echo $indice;?>_NIT___CI_CLIENTE"  >
								</td>
								<!--<td>
								<input type="text" value="<?php echo $columnas["COMPLEMENTO"];?>" id="cel_reg_<?php echo $indice;?>_COMPLEMENTO"  >
								</td>
								-->
								<td>
								<input type="text" value="<?php echo $columnas["NOMBRE_O_RAZON_SOCIAL"];?>" id="cel_reg_<?php echo $indice;?>_NOMBRE_O_RAZON_SOCIAL"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["IMPORTE_TOTAL_DE_LA_VENTA"];?>" id="cel_reg_<?php echo $indice;?>_IMPORTE_TOTAL_DE_LA_VENTA"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["IMPORTE_ICE"];?>" id="cel_reg_<?php echo $indice;?>_IMPORTE_ICE"  >
								</td>
								<!--<td>
								<input type="text" value="<?php echo $columnas["IMPORTE_IEHD"];?>" id="cel_reg_<?php echo $indice;?>_IMPORTE_IEHD"  >
								</td>
								-->
								<!--<td>
								<input type="text" value="<?php echo $columnas["IMPORTE_IPJ"];?>" id="cel_reg_<?php echo $indice;?>_IMPORTE_IPJ"  >
								</td>
								-->
								<!--<td>
								<input type="text" value="<?php echo $columnas["TASAS"];?>" id="cel_reg_<?php echo $indice;?>_TASAS"  >
								</td>
								-->
								<!--<td>
								<input type="text" value="<?php echo $columnas["OTROS_NO_SUJETOS_AL_IVA"];?>" id="cel_reg_<?php echo $indice;?>_OTROS_NO_SUJETOS_AL_IVA"  >
								</td>-->
								<td>
								<input type="text" value="<?php echo $columnas["EXPORTACIONES_Y_OPERACIONES_EXENTAS"];?>" id="cel_reg_<?php echo $indice;?>_EXPORTACIONES_Y_OPERACIONES_EXENTAS"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["VENTAS_GRAVADAS_A_TASA_CERO"];?>" id="cel_reg_<?php echo $indice;?>_VENTAS_GRAVADAS_A_TASA_CERO"  >
								</td>
								<!--<td>
								<input type="text" value="<?php echo $columnas["SUBTOTAL"];?>" id="cel_reg_<?php echo $indice;?>_SUBTOTAL"  >
								</td>
								-->
								<td>
								<input type="text" value="<?php echo $columnas["DESCUENTOS_BONIFICACIONES_Y_REBAJAS_SUJETAS_AL_IVA"];?>" id="cel_reg_<?php echo $indice;?>_DESCUENTOS_BONIFICACIONES_Y_REBAJAS_SUJETAS_AL_IVA"  >
								</td>
								<!--<td>
								<input type="text" value="<?php echo $columnas["IMPORTE_GIFT_CARD"];?>" id="cel_reg_<?php echo $indice;?>_IMPORTE_GIFT_CARD"  >
								</td>
								-->
								<td>
								<input type="text" value="<?php echo $columnas["IMPORTE_BASE_PARA_DEBITO_FISCAL"];?>" id="cel_reg_<?php echo $indice;?>_IMPORTE_BASE_PARA_DEBITO_FISCAL"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["DEBITO_FISCAL"];?>" id="cel_reg_<?php echo $indice;?>_DEBITO_FISCAL"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["ESTADO"];?>" id="cel_reg_<?php echo $indice;?>_ESTADO"  >
								</td>
								<td>
								<input type="text" value="<?php echo $columnas["CODIGO_DE_CONTROL"];?>" id="cel_reg_<?php echo $indice;?>_CODIGO_DE_CONTROL"  >
								</td>
								<!--<td>
								<input type="text" value="<?php echo $columnas["TIPO_DE_VENTA"];?>" id="cel_reg_<?php echo $indice;?>_TIPO_DE_VENTA"  >
								</td>-->
								<!--<td>
								<input type="text" value="<?php echo $columnas["CON_DERECHO_A_CREDITO_FISCAL"];?>" id="cel_reg_<?php echo $indice;?>_CON_DERECHO_A_CREDITO_FISCAL"  >
								</td>
								-->
								<!--<td>
								<input type="text" value="<?php echo $columnas["ESTADO_CONSOLIDACION"];?>" id="cel_reg_<?php echo $indice;?>_ESTADO_CONSOLIDACION"  >
								</td>
								-->


								<td style="width: 200px;">
								    <input type="checkbox" class="cuenta-uno-del-debe" style="float: left; display: inline; width: auto;" />
									<select style="width: 130px" class="cuenta-uno-del-debe">
									<?php
									$select = "";
									$cuentas = get_gl_accounts(null,null,null);
									while ($row = mysql_fetch_array($cuentas))
									{
										$select .= "<option value='".$row['account_code']."'";
										$select .= ">".$row['account_name']."";
										$select .= "</option>";
									}
									echo $select;
									?>

									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 1)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
									$dimension = "";
									$dimensiones = get_the_dimensions();
									while ($row = mysql_fetch_array($dimensiones))
									{
										$dimension .= "<option value='".$row['id']."'";
										$dimension .= ">".$row['name']."";
										$dimension .= "</option>";
									}
									if ($empresa['use_dimension'] < 1)
									{
										echo "<option value='0'>Sin dimension</option>";
									}
									echo $dimension;
									?>
									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 2)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 2)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td style="width: 200px;">
								    <input type="checkbox" class="cuenta-dos-del-debe" style="float: left; display: inline; width: auto;" />
									<select style="width: 130px" class="cuenta-dos-del-debe" >
									<?php
									echo $select;
									?>

									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 1)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 1)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 2)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 2)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td style="width: 200px;">
								    <input type="checkbox" class="cuenta-uno-del-haber" style="float: left; display: inline; width: auto;" />
									<select style="width: 130px" class="cuenta-uno-del-haber">
									<?php
									echo $select;
									?>

									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 1)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 1)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 2)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 2)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td style="width: 200px;">
								    <input type="checkbox" class="cuenta-dos-del-haber" style="float: left; display: inline; width: auto;" />
									<select style="width: 130px" class="cuenta-dos-del-haber">
									<?php
									echo $select;
									?>

									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 1)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 1)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 2)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 2)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td style="width: 200px;">
								    
								    <input type="checkbox" class="cuenta-tres-del-haber" style="float: left; display: inline; width: auto;" />
									<select style="width: 130px" class="cuenta-tres-del-haber">
									<?php
									echo $select;
									?>

									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 1)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 1)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td <?php if ($empresa['use_dimension'] < 2)
									{
										echo "style='display:none;'";
									}
									?>>
									<select>
									<?php
										if ($empresa['use_dimension'] < 2)
										{
											echo "<option value='0'>Sin dimension</option>";
										}
										echo $dimension;
									?>
									</select>
								</td>
								<td>
									<input type="text" value="Por el registro de la venta a <?php echo $columnas["NOMBRE_O_RAZON_SOCIAL"]; ?> con factura Nro. <?php echo $columnas["NRO_DE_LA_FACTURA"]; ?> en fecha <?php echo $columnas["FECHA_DE_LA_FACTURA"]; ?> por un importe de Bs <?php echo $columnas["IMPORTE_TOTAL_DE_LA_VENTA"]; ?>">
								</td>
								<td>
									<input type="button" class="contaCSV" value="Contabilizar" indiceReg="<?php echo $indice;?>" >
								</td>
					</tr>
						<?php
				}
						}
					}
				}
			?>
		</tbody>
	</table></br></br>

</div>
<input type='hidden' id='indice' value='<?php echo $indice;?>'>

</input>
<div style='margin-left: 70px;'>
	</br></br>
		<a href="#" id="otroPedido"><img src="../img/plus.png" width="25" height="25"></a>
		<a href="#" id="restarPedido"><img src="../img/minus.png" width="25" height="25"></a>
	</br></br>
		<input id="altaPedidoTxt" type="button" value="">
		<!-- <input id="altaPedidoPdf" type="button" value="Generar PDF"> -->
</div>
	</div>
</br></br>
<style type="text/css">
#tablaArticulos tr td input{
	width: 100px;
}
#contenedor{
	margin: 50px;
}
</style>
<table>
<tr id="fila" style="display:none">
			<td>
				<input value="3" type="text" disabled>
			</td>
			<td>
				<input value="<?php echo $ind++; ?>" disabled>
			</td>
			<td>
				<input type="date" style="width: 120px !important">
			</td>
			<td>
				<input class="autocompletar" name="nro_fact" type="text" onkeypress="return Solo_Numeros(event);">
			</td>
			<td>
				<input class="autocompletar" name="nro_auth" type="text" onkeypress="return Solo_Numeros(event);">
			</td>
			<td>
				<select>
					<option value="A">A</option>
					<option value="C">C</option>
					<option value="E">E</option>
					<option value="L">L</option>
					<option value="N">N</option>
					<option value="V">V</option>
				</select>
			</td>
			<td>
				<input class="autocompletar" name="nit" type="text" onkeypress="return Solo_Numeros(event);" >
			</td>
			<td>
				<input class="autocompletar" name="razon_social" type="text">
			</td>
			<td>
				<input class="imptot decimal memo" value="0.00" onkeypress="return solo_decimales(event);" type="text">
			</td>
			<td>
				<input type="text" class="impice decimal" value="0.00" onkeypress="return solo_decimales(event);">
			</td>
			<td>
				<input type="text" class="opexc decimal" value="0.00" onkeypress="return solo_decimales(event);">
			</td>
			<td>
				<input type="text" class="tasac decimal" value="0.00" onkeypress="return solo_decimales(event);">
			</td>
			<td>
				<input type="text" class="decimal" value="0.00" onkeypress="return solo_decimales(event);" disabled>
			</td>
			<td>
				<input type="text" class="dbr decimal" value="0.00" onkeypress="return solo_decimales(event);">
			</td>
			<td>
				<input type="text" class="decimal" value="0.00" onkeypress="return solo_decimales(event);" disabled>
			</td>
			<td>
				<input type="text" class="decimal" value="0.00" onkeypress="return solo_decimales(event);" disabled>
			</td>
			<td>
				<input class="autocompletar" name="cod_control" type="text" value="0">
			</td>
			<td>
				<select style="width: 150px">
				<?php
				$select = "";
				$cuentas = get_gl_accounts(null,null,null);
				while ($row = mysql_fetch_array($cuentas))
				{
					$select .= "<option value='".$row['account_code']."'";
					$select .= ">".$row['account_name']."";
					$select .= "</option>";
				}
				echo $select;
				?>

				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
				$dimension = "";
				$dimensiones = get_the_dimensions();
				while ($row = mysql_fetch_array($dimensiones))
				{
					$dimension .= "<option value='".$row['id']."'";
					$dimension .= ">".$row['name']."";
					$dimension .= "</option>";
				}
				if ($empresa['use_dimension'] < 1)
				{
					echo "<option value='0'>Sin dimension</option>";
				}
				echo $dimension;
				?>
				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 2)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td>
				<select style="width: 150px">
				<?php
				echo $select;
				?>

				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 1)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 2)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td>
				<select style="width: 150px">
				<?php
				echo $select;
				?>

				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 1)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 2)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td>
				<select style="width: 150px">
				<?php
				echo $select;
				?>

				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 1)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 2)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td>
				<select style="width: 150px">
				<?php
				echo $select;
				?>

				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 1)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 1)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td <?php if ($empresa['use_dimension'] < 2)
				{
					echo "style='display:none;'";
				}
				?>>
				<select>
				<?php
					if ($empresa['use_dimension'] < 2)
					{
						echo "<option value='0'>Sin dimension</option>";
					}
					echo $dimension;
				?>
				</select>
			</td>
			<td>
				<input type="text">
			</td>
			<td>
				<input type="button" class="cont" value="Contabilizar">
			</td>
</tr>
<table>
<script type="text/javascript">
	var editors = "";
</script>
