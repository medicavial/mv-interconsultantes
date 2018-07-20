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

		<style>
      table{
        border-collapse: collapse;
      }
      table, th, td {
          border: 1px solid #000000;
      }
		</style>
	</head>
	<body>
    <table>
      <thead>
        <tr style="background-color: #0082cd; color: #FFFFFF; font-size: 16px;">
          @foreach ($variables as $var)
            <th>{{ $var }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        {{ $contador = 0 }}

        @foreach ($datos as $dato)
          @if( $contador % 2 == 0 )
          <tr style="background-color: #FFFFFF">
          @else
          <tr style="background-color: #E0E0E0">
          @endif
            @foreach ($variables as $var)
              <td>{{ $dato->$var }}</td>
            @endforeach
          </tr>
          {{ $contador++ }}
        @endforeach
      </tbody>
    </table>
	</body>
</html>
