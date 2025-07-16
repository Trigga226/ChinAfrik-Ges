<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Suivi de Toutes les Machines</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .logo {
            width: 120px;
            height: auto;
        }
        .title-cell {
            text-align: center;
        }
        .title-cell h1 {
            margin: 0;
            font-size: 18px;
        }
        .title-cell h2 {
            margin: 0;
            font-size: 14px;
        }
        .page-break {
            page-break-after: always;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        .data-table th {
            background-color: #CC0000;
            color: white;
        }
        .machine-header {
            background-color: #e0e0e0;
            font-size: 14px;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 130px;">
                <img src="{{ public_path('logo2.png') }}" alt="Logo" class="logo">
            </td>
            <td class="title-cell">
                <h1>FICHE DE SUIVI DE TOUTES LES MACHINES</h1>
                <h2>Période du {{ $startDate }} au {{ $endDate }}</h2>
            </td>
            <td style="width: 130px;"></td>
        </tr>
    </table>

    @forelse($suivisGroupes as $machineDesignation => $suivis)

        <div class="machine-header">
            Machine: @foreach($machines as $mach){{$mach}} /  @endforeach
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Chauffeur</th>
                    <th>Type d'Intervention</th>
                    <th>Pièce(s) Changée(s)</th>
                    <th>Description de la Panne / Entretien</th>
                    <th>Kilométrage</th>
                    <th>Durée d'Immobilisation</th>
                    <th>Atelier / Mécanicien</th>
                    <th>Remarques</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suivis as $suivi)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($suivi->date)->format('d/m/Y') }}</td>
                        <td>{{ $suivi->chauffeurs->nom.' '.$suivi->chauffeurs->prenom ?? 'N/A' }}</td>
                        <td>{{ $suivi->type_entretient }}</td>
                        <td>{{ $suivi->piece_change }}</td>
                        <td>{{ $suivi->decription_panne }}</td>
                        <td>{{ $suivi->kilometrage }}</td>
                        <td>{{ $suivi->duree_immobilisation }}</td>
                        <td>{{ $suivi->atelier }}</td>
                        <td>{{ $suivi->observation }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">Aucun suivi trouvé pour cette machine dans cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p style="text-align: center;">Aucun suivi de machine trouvé pour la période sélectionnée.</p>
    @endforelse

    <div style="margin-top: 20px; text-align: right;">
        <p>Généré le {{ $date }}</p>
    </div>
</body>
</html>
