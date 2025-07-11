<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport de Pointage des Machines</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .info-container {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-column {
            width: 100%;
            margin-top: 5px;
        }
        .info-column h3 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #0000FF;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .info-column p {
            margin: 2px 0;
            font-size: 10px;
        }
        .info-column ul {
            margin: 2px 0;
            padding-left: 15px;
            font-size: 10px;
        }
        .info-jours {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f0f0f0;
        }
        .info-jours h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        .signature-box {
            width: 200px;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }
        .date-box {
            text-align: right;
        }
        .status-travail {
            font-weight: bold;
        }
        .status-travail.oui {
            color: green;
        }
        .status-travail.non {
            color: red;
        }
        .note {
            font-style: italic;
            color: #666;
            margin-top: 5px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rapport de Pointage des Machines</h2>
    </div>

    <div class="info-container">
        <div class="info-row">
            <div class="info-column">
                <h3>Informations Client</h3>
                <p><strong>Client:</strong> {{ $client->designation ?? 'N/A' }}</p>
                <p><strong>Téléphone:</strong> {{ $client->phone ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</p>
            </div>

            <div class="info-column">
                <h3>Informations Location</h3>
                <p><strong>Date de début:</strong> {{ $location->date_debut }}</p>
                <p><strong>Durée totale:</strong> {{ $location->duree }} jours</p>
                <p><strong>Camions concernés:</strong></p>
                <ul>
                    @foreach($location->machines as $machine)
                        <li>{{ $machine->designation }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="info-jours">
        <h3>Résumé des Heures de Travail</h3>
        <table>
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Heures Travaillées</th>
                    <th>Heures Restantes</th>
                    <th>Total Ravitaillement (L)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statistiques as $machine => $stats)
                    <tr>
                        <td>{{ $machine }}</td>
                        <td>
                            @php
                                $heures = floor($stats['heure_travailles']);
                                $minutes = round(($stats['heure_travailles'] - $heures) * 60);
                            @endphp
                            {{ $heures }}h {{ $minutes }}m
                        </td>
                        <td>
                            @php
                                $heures_restantes = $stats['heure_restantes'];
                                $heures = floor($heures_restantes);
                                $minutes = round(($heures_restantes - $heures) * 60);
                            @endphp
                            {{ $heures }}h {{ $minutes }}m
                        </td>
                        <td>{{ $stats['total_ravitailler'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="info-container">
        <h3>Détails des Pointages</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Machine</th>
                    <th>Chauffeur</th>
                    <th>Heure Sortie</th>
                    <th>Heure Retour</th>
                    <th>Heures Travaillées</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    <tr>
                        <td>{{ $record->date }}</td>
                        <td>{{ $record->machine }}</td>
                        <td>{{ $record->chauffeurs->nom ?? 'N/A' }} {{ $record->chauffeurs->prenom ?? '' }}</td>
                        <td>{{ $record->heure_sortie }}</td>
                        <td>{{ $record->heure_retour }}</td>
                        <td>
                            @php
                                if ($record->heure_sortie && $record->heure_retour) {
                                    $sortie = \Carbon\Carbon::parse($record->heure_sortie);
                                    $retour = \Carbon\Carbon::parse($record->heure_retour);

                                    if ($retour->lt($sortie)) {
                                        $retour->addDay();
                                    }

                                    $heures = $sortie->diffInHours($retour);
                                    $minutes = $sortie->diffInMinutes($retour) % 60;
                                    echo round($heures, 0) . 'h ' . $minutes . 'm';
                                } else {
                                    echo 'N/A';
                                }
                            @endphp
                        </td>
                        <td>{{ $record->a_travailler ? 'A travaillé' : 'N\'a pas travaillé' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>Signature du Superviseur</p>
        </div>
        <div class="date-box">
            <p>Généré le {{ $date }}</p>
        </div>
    </div>
</body>
</html>

