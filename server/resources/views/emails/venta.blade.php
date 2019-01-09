<p>
    ID Evento: {{ $datos->id }}
</p>

@if( $datos->object == 'order' && $datos->financial_status == 'paid' )
<p>
    Orden Pagada
</p>
@endif

@if( $datos->email )
<p>
    Email: {{ $datos->email }}
</p>
@endif

@if($datos->customer)
<p>
    Cliente: {{ $datos->customer->name }}
</p>

<p>
    Domicilio: {{ $datos->customer->default_address->address1 }}
</p>
@endif


<p>
    Items:
    <br>
    @foreach ($datos->items as $item)
        <span> {{ $item->quantity }} {{ $item->name }} <br> </span>
    @endforeach
</p>

{{ date('Y/m/d H:i:s', $datos->updated_at) }}

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
          	<br><br>
          	Se ha registrado una compra a nombre de {{ $datos->customer->name }} con ID:  <b>{{ $datos->id }}</b>
          	<br>
          	Deberá presentar la Clave de la orden al presentarse en la clínica correspondiente.
          	<br><br><br>
          	<div class="notas">
          		* Correo automático enviado por Médica Vial.
          	</div>
          	<br>
        </td>
      </tr>
    </table>
	</body>
</html>
