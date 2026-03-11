<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4;
            margin: 15mm;
            /* Margem padrão de impressão */
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            color: #333;
        }

        /* Força o contêiner a ocupar 100% da área útil entre as margens */
        .wrapper {
            width: 100%;
        }

        /* Cabeçalho Moderno */
        .header {
            width: 100%;
            border-bottom: 3px solid #1a1a1a;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .header table {
            width: 100%;
            border: none;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }

        /* Tabela de Itens Full Width */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* O segredo para o w-100% real */
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 12px 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            word-wrap: break-word;
        }

        /* Seção de Totais alinhada à direita */
        .summary-wrapper {
            width: 100%;
            text-align: right;
        }

        .summary-table {
            width: 300px;
            /* Largura fixa para o bloco de total no canto */
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 8px 10px;
        }

        .total-highlight {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #1a1a1a;
        }

        /* Observações e Assinaturas */
        .notes {
            margin-top: 50px;
            padding: 15px;
            background-color: #fdfdfd;
            border: 1px solid #eee;
            font-size: 12px;
        }

        .signature-container {
            margin-top: 80px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            border-top: 1px solid #333;
            text-align: center;
            padding-top: 8px;
            font-size: 12px;
            display: inline-block;
        }

        /* Helpers de alinhamento */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <table>
                <tr>
                    <td width="50%">
                        <div class="title">Orçamento</div>
                        <div style="margin-top: 10px;">
                            <strong>Nº:</strong> #{{ str_pad($venda->id, 5, '0', STR_PAD_LEFT) }}<br>
                            <strong>Data:</strong> {{ $venda->data->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td width="50%" class="text-right">
                        <strong>Cliente:</strong> {{ $venda->cliente->nome }}<br>
                        <strong>Vendedor:</strong> {{ $venda->vendedor?->user?->name ?? $venda->admin?->name ?? '—' }}
                    </td>
                </tr>
            </table>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="50%">Descrição do Produto</th>
                    <th width="15%" class="text-center">Qtd</th>
                    <th width="20%" class="text-right">Unitário</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venda->itens as $item)
                <tr>
                    <td>{{ $item->produto->nome }}</td>
                    <td class="text-center">{{ $item->quantidade }}</td>
                    <td class="text-right">R$ {{ number_format($item->valor, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($item->valor * $item->quantidade, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @php
        $subtotal = $venda->itens->sum(fn($i) => $i->valor * $i->quantidade);
        $comissao = $venda->vendedor?->comissao ?? 0;
        $valorComissao = ($subtotal * $comissao) / 100;
        $lucroEmpresa = $subtotal - $valorComissao;
        @endphp

        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td class="text-right">Subtotal:</td>
                    <td class="text-right">R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
                @if(!auth()->user()->parent_user_id)
                <tr>
                    <td class="text-right">Comissão ({{ $comissao }}%):</td>
                    <td class="text-right">R$ {{ number_format($valorComissao, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-right">Lucro Empresa:</td>
                    <td class="text-right">R$ {{ number_format($lucroEmpresa, 2, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="notes">
            <strong>OBSERVAÇÕES:</strong><br>
            1. Validade desta proposta: 07 dias corridos.<br>
            2. Pagamento facilitado conforme política interna.<br>
            3. A entrega será realizada após a confirmação do pagamento.
        </div>

        <div class="signature-container">
            <table width="100%">
                <tr>
                    <td width="45%" align="center">
                        <div style="border-top: 1px solid #000; width: 80%; padding-top: 5px;">
                            Assinatura do Vendedor
                        </div>
                    </td>
                    <td width="10%"></td>
                    <td width="45%" align="center">
                        <div style="border-top: 1px solid #000; width: 80%; padding-top: 5px;">
                            Aceite do Cliente
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>