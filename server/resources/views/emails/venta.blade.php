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
					<img src="{{ $message->embed('http://medicavial.net/mvnuevo/imgs/logos/mv.jpg') }}" width="100" />
					&nbsp;&nbsp;
				</th>
			</tr>

      <tr style="background-color: #EBF5FB;">
        <td colspan="6" style="padding-left: 15px; padding-right: 15px;">
          <br>
          	<h3>{{ $saludo }}</h3>
			<b>ESTE ES UN CORREO DE PRUEBAS DE DESARROLLO. SE INCLUIRÁ TAMBIEN UN DOCUMENTO PDF ADJUNTO CON MÁS DETALLES DE LAS COMPRAS.</b>

          	<br><br>
          	Se adjunta comprobante de compra a nombre <b>{{ $datos->customer->name }}</b> registrada el {{ date('Y/m/d H:i:s', $datos->updated_at) }}
          	<br>

			<p>
				Items:
				<br>
				@foreach ($datos->items as $item)
					<b> {{ $item->quantity }} {{ $item->name }} </b><br>
				@endforeach
			</p>

			<p>
				Teléfono: <b>{{ $datos->shipping->phone }}</b>
			</p>

			<p>
				Email: <b>{{ $datos->email }}</b>
			</p>

			<p>
				Domicilio: 
				<b>{{ $datos->shipping->address->address1 }}</b>

				@if( $datos->shipping->address->address2 )
					<b>, {{ $datos->shipping->address->address2 }} </b>
				@endif

				@if( $datos->shipping->address->city )
					<b>, {{ $datos->shipping->address->city }} </b>
				@endif

				@if( $datos->shipping->address->state )
					<b>, {{ $datos->shipping->address->state }} </b>
				@endif

				@if( $datos->shipping->address->country )
					<b>, {{ $datos->shipping->address->country }} </b>
				@endif

				@if( $datos->shipping->address->postcode )
					<b>, CP {{ $datos->shipping->address->postcode }} </b>
				@endif

				@if( $datos->shipping->address->references )
					<b>| Referencia: {{ $datos->shipping->address->references }} </b>
				@endif
				.
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
