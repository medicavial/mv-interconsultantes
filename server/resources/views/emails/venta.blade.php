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