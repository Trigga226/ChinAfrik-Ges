<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu de Paiement #{{ $paiement->reference }}</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #fff; /* White background for the page */
        }
        body {
            padding: 20px; /* Spacing around the receipt */
        }
        .receipt-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.07);
            font-size: 14px;
            line-height: 1.6;
            border-top: 8px solid #871414;
            page-break-inside: avoid; /* Avoid breaking the box across pages */
        }
        .receipt-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .receipt-box table td {
            padding: 8px 5px;
            vertical-align: top;
        }
        .receipt-box table tr.top table td {
            padding-bottom: 20px;
        }
        .receipt-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .receipt-box table tr.information table td {
            padding-bottom: 30px;
        }
        .receipt-box table tr.heading td {
            background: #871414;
            border-bottom: 1px solid #871414;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            font-size: 12px;
            padding: 10px 5px;
        }
        .receipt-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .receipt-box table tr.item.last td {
            border-bottom: none;
        }
        .receipt-box table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 0px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-bottom: 1px dotted #333;
            height: 70px;
            margin-bottom: 10px;
        }
        .payment-confirmation {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f8f8;
            border-left: 4px solid #4CAF50;
        }
        @media print {
            body {
                padding: 0;
                background-color: #fff;
            }
            .receipt-box {
                box-shadow: none;
                border: 0;
                width: 100%;
                border-top: 8px solid #871414;
            }
        }
    </style>
</head>
<body>
<div class="receipt-box">
    <table>
        <tr class="top">
            <td colspan="8">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{ public_path('logo.png') }}" style="max-width: 200px; max-height: 80px;" alt="{{ config('app.name') }} Logo">
                        </td>
                        <td class="text-right">
                            <strong>REÇU DE PAIEMENT</strong><br>
                            Reçu #: {{ $paiement->reference }}<br>
                            Date: {{ $paiement->date_versement }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="8">
                <table>
                    <tr>
                        <td>
                            <strong>Émetteur:</strong><br>
                            ChinAfrik Groupe<br>
                            01BP 4589 Ouagadougou,Burkina Faso<br>
                            4589 Ouagadougou,Burkina Faso<br>
                        </td>
                        <td class="text-right">
                            <strong>Client:</strong><br>
                            {{ $dossier->nom_complet }}<br>
                            {{ $dossier->pays }} {{ $dossier->ville }}, Secteur: {{ $dossier->secteur }}<br>
                            {{ $dossier->phone }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="8">
                <div class="payment-confirmation">
                    <p><strong>Nous confirmons avoir reçu votre paiement.</strong></p>
                    <p>
                        <strong>Méthode de paiement:</strong> {{ $paiement->moyen_versement }},
                        <strong>Date de paiement:</strong> {{ $paiement->date_versement }},
                        @if($paiement->reference)
                            <strong>Référence de paiement:</strong> {{ $paiement->reference }}.<br>
                        @endif
                    </p>
                </div>
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">Description</td>
            <td colspan="2">Montant versé</td>
            @if(is_null($bourse->type))
                <td class="text-left" colspan="2">Solde cas totale</td>
                <td class="text-left" colspan="2">Solde cas partielle</td>
            @else
                <td class="text-left" colspan="4">Solde</td>
            @endif
        </tr>
        <tr class="item last">
            <td colspan="2">{{ $paiement->motif }}</td>
            <td colspan="2">{{ number_format($paiement->montant, 2, ',', ' ') }} F CFA</td>
            @if(is_null($bourse->type))
                <td class="text-left" colspan="2">{{ number_format($soldet, 2, ',', ' ') }} F CFA</td>
                <td class="text-left" colspan="2">{{ number_format($soldep, 2, ',', ' ') }} F CFA</td>
            @else
                @if($bourse->type=='totale')
                    <td class="text-left" colspan="4">{{ number_format($soldet, 2, ',', ' ') }} F CFA</td>
                @endif
                @if($bourse->type=='partielle')
                    <td class="text-left" colspan="4">{{ number_format($soldep, 2, ',', ' ') }} F CFA</td>
                @endif
            @endif
        </tr>

        <tr class="total">
            <td colspan="2"><strong>Total payé</strong></td>
            <td colspan="6" class="text-left"><strong>{{ number_format($paiement->montant, 2, ',', ' ') }} F CFA</strong></td>
        </tr>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Signature du caissier</strong></p>
            <div class="signature-line"></div>
        </div>
    </div>


</div>
</body>
</html>
