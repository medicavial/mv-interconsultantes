<?php
date_default_timezone_set('America/Mexico_City');
$fecha = date('Y/m/d H:i:s', $datos->updated_at)
?>

<html lang="es-MX">
<!-- <base href="/"> -->
<head>
<meta charset="utf-8">
<link href="https://fonts.googleapis.com/css?family=Alegreya+Sans" rel="stylesheet">
<style media="screen">
		@page { margin: 100px 25px; }
		header { position: fixed; top: -50px; left: 0px; right: 0px; height: 50px; }
		footer { position: fixed; bottom: -50px; left: 0px; right: 0px; height: 50px; }
		body{ font-family: 'Alegreya Sans', sans-serif; font-size: 1em; }
		.titulo{ font-size: 1.5em; font-weight: bold; }
		.subtitulo{ font-size: 1.2em; font-weight: bold; }
		.titulo > .subtitulo{ font-size: 0.6em; font-weight: bold; }
		.bg-mv{ background-color: #0082cd; }
		.bg-white{ background-color: #fff; }
		.bg-yellow{ background: #FCF3CF; }
		.tbl{ border-spacing: 0; width: 100%; }
		.tbl-container{ padding-left: 1em; padding-right: 1em; }
		.tbl-datos{ background-color: #EBF5FB; }
		.tbl-title{ font-size: 1.2em; font-weight: bold; }
		td > .logomv{ height: auto; max-width: 130px; }
		td { padding: 0.5em, 0, 0.5em, 0; }
		.img-map{ height: auto; max-width: 530px; }
		.img-qr{ height: auto; max-width: 150px; }
		.logomv{ height: auto; max-width: 20%;}
		.text-mv{ color: #0082cd; }
		.text-white{ color: #fff; }
		.notas{ font-size: 0.8em; color: #555; font-weight: normal; }
		.enlace{ color: #0082cd; text-decoration: none; }
		.espacio{ margin-top: 2em; }
		.saludo{ font-size: 1.2em; font-weight: bold; }
		.datos{ font-weight: bold; }
		.encabezado{ width: auto;}
</style>
</head>
<body>
	<header>
		<table class="tbl">
			<tr>
				<td width="25%">
					<img src="img/logomv.png" class="logomv"/>
				</td>
				<td class="titulo" width="50%" align="center">
					COMPROBANTE DE COMPRA
				</td>
				<td width="25%" class="notas" align="right">
					Fecha de compra:<br>
					{{ date('d/m/Y H:i:s', $datos->updated_at) }}
				</td>
			</tr>
		</table>
	</header>

	<!-- mensaje -->
	<br>
	<table class="tbl">
		<tr>
			<td width="100%">
				<p align="justify">
					<b>
						Estimad@ {{ $datos->customer->name }}:
					</b>
					<br>
					¡Agradecemos mucho tu preferencia!
				</p>
			</td>
		</tr>
	</table>

	<table class="tbl tbl-datos">
		<tr class="bg-mv text-white">
			<td colspan="2" align="center" class="tbl-title">
				DETALLE DE LA COMPRA
			</td>
		</tr>

		<tr>
			<td  width="140" class="tbl-container encabezado">
				ID de la orden: <span class="datos">{{ $datos->number }}</span>
				<span class="notas">{{ $datos->id }}</span>
			</td>

			<td  class="tbl-container">
				Entregar a
				<span class="datos">
					{{ strtoupper( $datos->shipping->name ) }}
					<br><br>
					{{ strtoupper( $datos->shipping->address->address1 ) }}

					@if( $datos->shipping->address->address2 )
						, {{ strtoupper( $datos->shipping->address->address2 ) }}
					@endif

					@if( $datos->shipping->address->city )
						, {{ strtoupper( $datos->shipping->address->city ) }}
					@endif

					@if( $datos->shipping->address->state )
						, {{ strtoupper( $datos->shipping->address->state ) }}
					@endif

					@if( $datos->shipping->address->country )
						, {{ strtoupper( $datos->shipping->address->country ) }}
					@endif

					@if( $datos->shipping->address->postcode )
						, CP {{ strtoupper( $datos->shipping->address->postcode ) }}
					@endif

					@if( $datos->shipping->address->references )
						<br>Referencias: {{ strtoupper( $datos->shipping->address->references ) }}.
					@endif
				</span>
				<br><br>
				Teléfono: <b>{{ $datos->shipping->phone }}</b>
				<br>
				Email: <b>{{ $datos->email }}</b>
			</td>
		</tr>

		<tr>
			<td colspan="2" width="100%" class="tbl-container">
				Items:
				<br>
				@foreach ($datos->items as $item)
					<b> {{ $item->quantity }} {{ $item->name }} </b><br>
				@endforeach
			</td>
		</tr>

		<tr>
			<td  width="25%" class="tbl-container">
				Direccionado por:
			</td>
			<td  width="75%" class="tbl-container datos">
				Dato de usuario
				<br>
				<span class="notas">
				Aqui van las cedulas y las especialidades
				</span>
			</td>
		</tr>

		<tr>
			<td width="25%" class="tbl-container">
				Servicio:
			</td>

			<td width="75%" class="tbl-container datos">
					Cantidad de sesiones de rehabilitación.
			</td>
		</tr>

		<tr>
			<td  width="25%" class="tbl-container">
				Lugar de atención:
			</td>
			<td  width="75%" class="tbl-container datos">
				Cualquier Clínica Médica Vial
				<br>
				<span class="notas">
					Consulte <span class="text-mv">www.medicavial.com/clinicas</span>
					o llame al <span class="text-mv">01 - 800 - 3 MEDICA(633422)</span>
					para conocer la ubicación de las clínicas Médica Vial.
				</span>
				<br>
				<span class="notas"><b>* POR FAVOR LLAME UN DÍA ANTES DE ACUDIR A CLÍNICA</b></span>
			</td>
		</tr>
	</table>

	<table class="tbl">
		<td width="50%" class="notas">
			<b>Recomendaciones para cita:</b>
			<br>* Llegar 10 minutos antes de la hora de la hora acordada.
			<br>* Usar ropa cómoda.
		</td>

		<td width="50%" class="notas">
			<br>* Cuidar higiene personal.
			<br>
			* En caso de que necesite reagendar la cita llame un día antes.
		</td>
		</tr>
	</table>

	<footer>
		<table class="tbl">
		<tr class="bg-yellow">
		<td width="100%" align="center" class="notas">
			Consulta nuestro aviso de privacidad y cualquier cambio sobre el mismo en nuestro sitio web <b>www.medicavial.com</b>.
			<br>
			Para cualquier duda o aclaración contactanos al <b>01 - 800 - 3 MEDICA(633422)</b> o escribenos a <b>info@medicavial.com.mx</b>.
		</td>
		</tr>
	</table>
	</footer>
</body>
</html>
