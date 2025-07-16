<div style="width: 100%; position: running(header); top: 0;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 130px;">
                <img src="{{ public_path('logo2.png') }}" alt="Logo" style="width: 120px; height: auto;">
            </td>
            <td style="text-align: center;">
                <h1>FICHE DE SUIVI</h1>
                @isset($camion)
                    <h2>{{ $camion->designation }} ({{ $camion->immatriculation }})</h2>
                @endisset
                @isset($machine)
                    <h2>{{ $machine->designation }}</h2>
                @endisset
                @isset($startDate)
                    <h2>Période du {{ $startDate }} au {{ $endDate }}</h2>
                @endisset
            </td>
            <td style="width: 130px;"></td>
        </tr>
    </table>
    <hr style="border: 0; border-top: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
</div>
