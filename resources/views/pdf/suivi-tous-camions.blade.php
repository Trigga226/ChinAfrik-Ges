<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Suivi de Tous les Camions</title>
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
        .camion-header {
            background-color: #e0e0e0;
            font-size: 14px;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @include('pdf.partials.pdf-header', ['startDate' => $startDate, 'endDate' => $endDate])

    <div style="margin-top: 80px;"> <!-- Ajustez cette marge selon la hauteur de votre en-tête -->
    @forelse($suivisGroupes as $camionDesignation => $suivis)
        <div class="camion-header">
            Camion: @foreach($camions as $cam){{$cam}} /  @endforeach
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
                        <td>{{ $suivi->chauffeur ? $suivi->chauffeurs->nom.' '.$suivi->chauffeurs->prenom : 'N/A' }}</td>
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
                        <td colspan="9" style="text-align: center;">Aucun suivi trouvé pour ce camion dans cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p style="text-align: center;">Aucun suivi de camion trouvé pour la période sélectionnée.</p>
    @endforelse
    </div>

    <div style="margin-top: 20px; text-align: right;">
        <p>Généré le {{ $date }}</p>
    </div>
</body>
</html>
