<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; color: #1E293B; margin: 18px; }
        .header { background: #0F2744; color: #FFFFFF; padding: 14px 18px; border-radius: 6px; margin-bottom: 14px; }
        .header h1 { margin: 0; font-size: 15px; letter-spacing: 0.3px; }
        .header .sub { margin-top: 4px; font-size: 11px; color: #BAE6FD; }
        .header .inst { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #CBD5E1; margin-bottom: 4px; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; background: #F8FAFC; border: 1px solid #CBD5E1; }
        .meta td { padding: 6px 10px; border: 1px solid #E2E8F0; font-size: 10px; }
        .meta .label { font-weight: bold; color: #0F2744; width: 22%; background: #EFF6FF; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data th { background: #1E3A5F; color: #FFFFFF; padding: 7px 6px; text-align: left; font-size: 9.5px; border: 1px solid #1E3A5F; }
        table.data td { padding: 6px; border: 1px solid #CBD5E1; font-size: 9.5px; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #F8FAFC; }
        .summary { margin-top: 14px; padding: 10px 14px; background: #EFF6FF; border-left: 4px solid #1E3A5F; }
        .summary-item { margin-bottom: 3px; font-size: 10.5px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: right; font-size: 8.5px; color: #64748B; border-top: 1px solid #CBD5E1; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="inst">Universidad Privada Domingo Savio — Sistema Web de Gestión Académica (SIGA)</div>
        <h1>{{ $report['title'] }}</h1>
        <div class="sub">{{ $report['subtitle'] }}</div>
    </div>

    <table class="meta">
        @foreach($report['meta'] as $label => $value)
            <tr>
                <td class="label">{{ $label }}</td>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="label">Fecha y hora de emisión</td>
            <td>{{ $generatedAt }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                @foreach($report['columns'] as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($report['rows'] as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($report['columns']) }}" style="text-align:center; padding: 14px; color: #64748B;">
                        No se encontraron registros para los criterios seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($report['summary']))
        <div class="summary">
            @foreach($report['summary'] as $label => $val)
                <div class="summary-item"><strong>{{ $label }}:</strong> {{ $val }}</div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        Documento oficial generado por SIGA Académico (UPDS) · {{ $generatedAt }}
    </div>
</body>
</html>
