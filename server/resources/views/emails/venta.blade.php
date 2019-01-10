<!DOCTYPE html>

<?php
	$datetime = new DateTime();
	$hora = $datetime->format('H');
	if ($hora<3)                    $saludo="¡Buenas noches!";
	elseif ($hora>=3 && $hora<12)   $saludo="¡Buenos días!";
	elseif ($hora>=12 && $hora<20)  $saludo="¡Buenas tardes!";
    elseif ($hora>=20)              $saludo="¡Buenas noches!";
 ?>

<html lang="es-MX">
	<head>
		<meta charset="utf-8">

		<style>
			.notas{
				font-size: 11px;
				color: #555;
				font-style: italic;
				text-align: right;
			}
		</style>
	</head>
	<body>
    <table align="center" style="font-family: arial" width="90%">
			<tr style="background-color: #0082cd; color: white;">

				<th colspan="5">
					<h2>COMPROBANTE DE COMPRA</h2>
				</th>
				<th style="background-color: white;" width="150">
					&nbsp;&nbsp;

					&nbsp;&nbsp;
				</th>
			</tr>

      <tr style="background-color: #EBF5FB;">
        <td colspan="6" style="padding-left: 15px; padding-right: 15px;">
          <br>
          	<h3>{{ $saludo }}</h3>
          	<br>
          	Se adjunta comprobante de compra a nombre <b>{{ $datos->customer->name }}</b> registrada el {{ date('Y/m/d H:i:s', $datos->updated_at) }}
          	<br>

			<p>
				Items:
				<br>
				@foreach ($datos->items as $item)
					<span> {{ $item->quantity }} {{ $item->name }} <br> </span>
				@endforeach
			</p>

			<p>
				Email: {{ $datos->email }}
			</p>

			<p>
				Domicilio: {{ $datos->customer->default_address->address1 }}
			</p>

          	Deberá presentar este comprobante en clínica
          	<br><br><br>
          	<div class="notas">
			  {{ $datos->id }}
			  	<br>
          		* Correo automático enviado por MédicaVial.
          	</div>
          	<br>
        </td>
      </tr>
    </table>
	</body>
</html>
