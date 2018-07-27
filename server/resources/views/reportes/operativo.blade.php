<!DOCTYPE html>

<?php
// return $datos;
	$datetime = new DateTime();
	$hora = $datetime->format('H');
	if ($hora<3) {
	  $saludo="¡Muy buenas noches!";
	} elseif ($hora>=3 && $hora<12) {
	  $saludo="¡Muy buenos días!";
	} elseif ($hora>=12 && $hora<20) {
	  $saludo="¡Muy buenas tardes!";
	} elseif ($hora>=20) {
	  $saludo="¡Muy buenas noches!";
	};
 ?>

<html lang="es-MX">
	<head>
		<meta charset="utf-8">

	</head>
	<body>
		<h1>hola</h1>
    <table>
			<tr style="background-color: #0082cd; color: #FFFFFF; font-size: 16px; border: 1px solid #000000">
				@foreach ($variables as $var)
					@if ( $var != 'CantidadCliente')
						<th align="center">{{ $var }}</th>
					@endif
				@endforeach
			</tr>
        {{ $contador = 0 }}

        @foreach ($datos as $dato)
          @if( $contador % 2 == 0 )
          <tr style="background-color: #FFFFFF">
          @else
          <tr style="background-color: #E0E0E0">
          @endif
            @foreach ($variables as $var)
							@if ( $var != 'CantidadCliente')
								<td>{{ $dato->$var }}</td>
							@endif
            @endforeach
          </tr>
          {{ $contador++ }}
        @endforeach
    </table>
	</body>
</html>
