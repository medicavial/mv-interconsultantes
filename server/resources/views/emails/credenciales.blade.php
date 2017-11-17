<?php
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
 			.notas{
 				font-size: 11px;
 				color: #555;
 				font-style: italic;
 				text-align: right;
 			}

      .link{
        color: #0082cd;
        text-decoration: none;
      }

      .link-white{
        color: #f8f8f8;
        text-decoration: none;
      }
 		</style>

 	</head>
 	<body>
         <table align="center" style="font-family: arial" width="90%">
 			<tr style="background-color: #0082ca; color: white;">
 				<th style="background-color: white;" width="150">
 					&nbsp;&nbsp;
 					<img src="{{ $message->embed('http://medicavial.net/mvnuevo/imgs/logos/mv.jpg') }}" width="100" />
 					&nbsp;&nbsp;
 				</th>
 				<th colspan="4">
 					<h2>&nbsp;&nbsp;Acceso Generado&nbsp;&nbsp;</h2>
 				</th>
 				<th>&nbsp;&nbsp;<a href="http://busqueda.medicavial.net" class="link-white" target="_blank">Sistema de Red Qx</a>&nbsp;&nbsp;</th>
 			</tr>

           <tr style="background-color: #DFF4F7;">
             <td colspan="6" style="padding-left: 15px; padding-right: 15px;">
               <br>
               	<h2>{{ $saludo }}</h2>
               	<br><br>
               	Se ha generado nombre de usuario y contraseña para el usuario <b>{{ $usuario }}</b>
                que le dan acceso al <b><a href="http://busqueda.medicavial.net" class="link" target="_blank">Sistema de Red Quirurgica</a></b>
                debido a que ha usted ha sido asignado para la atención de paciente(s).

                <p>
                  Los datos son:
                </p>
               	<ul>
                  <li>Usuario: <b>{{ $username }}</b></li>
               	  <li>Contraseña: <b>{{ $password }}</b></li>
               	</ul>

                <p>Para acceder al sistema ingrese en su navegador a
                  <b><a href="http://busqueda.medicavial.net" class="link" target="_blank">http://busqueda.medicavial.net</a></b>
                </p>

               	<br><br><br>
               	<div class="notas">
               		* Correo automático enviado por Sistema de Red Quirurgica de Médica Vial.
               	</div>
               	<br>
             </td>
           </tr>
         </table>
 	</body>
 </html>
