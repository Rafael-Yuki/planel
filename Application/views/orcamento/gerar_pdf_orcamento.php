<?php
require('Application/libraries/fpdf/fpdf.php'); 
require('Application/models/conexao.php');
require('Application/models/orcamento_dao.php');
require('Application/models/itens_orcamento_dao.php');

if (isset($_GET['id'])) {
    $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
    $orcamento = OrcamentoDAO::buscarOrcamentoPorId($orcamento_id);

    if ($orcamento) {
        $itens = ItensOrcamentoDAO::listarItensPorOrcamento($orcamento_id);

        class PDF extends FPDF
        {
            // Cabeçalho
            function Header()
            {
                $this->SetFont('Arial', 'BI', 16);
                $this->Cell(0, 8, utf8_decode('Planel Planejamento e Instalações Elétricas Ltda.'), 0, 1, 'C');
                $this->Ln(15);
            }

            // Rodapé
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 8, utf8_decode('Rua Visconde de Mauá 165 - fone: (043) 3328 3055 - telefax: (043) 3328 3046 - cep 86 070-540 - Londrina  -  Paraná'), 0, 1, 'C');
            }

            // Função para adicionar materiais com quebras de linha
            function AddMaterial($nome, $quantidade, $preco_unitario, $valor_total)
            {
                $this->SetFont('Arial', '', 10);
                $x = $this->GetX();
                $y = $this->GetY();
                $this->MultiCell(90, 6, utf8_decode($nome), 1);
                $this->SetXY($x + 90, $y);
                $this->Cell(30, 6 * $this->NbLines(90, utf8_decode($nome)), $quantidade, 1);
                $this->Cell(30, 6 * $this->NbLines(90, utf8_decode($nome)), number_format($preco_unitario, 2, ',', '.'), 1);
                $this->Cell(30, 6 * $this->NbLines(90, utf8_decode($nome)), number_format($valor_total, 2, ',', '.'), 1, 1, 'R');
            }

            // Função para adicionar serviços com quebras de linha
            function AddServico($nome, $quantidade, $preco_unitario, $valor_total)
            {
                $this->SetFont('Arial', '', 10);
                $x = $this->GetX();
                $y = $this->GetY();
                $this->MultiCell(90, 6, utf8_decode($nome), 1);
                $this->SetXY($x + 90, $y);
                $this->Cell(30, 6 * $this->NbLines(90, utf8_decode($nome)), $quantidade, 1);
                $this->Cell(30, 6 * $this->NbLines(90, utf8_decode($nome)), number_format($preco_unitario, 2, ',', '.'), 1);
                $this->Cell(30, 6 * $this->NbLines(90, utf8_decode($nome)), number_format($valor_total, 2, ',', '.'), 1, 1, 'R');
            }

            // Função para calcular o número de linhas necessárias em um MultiCell
            function NbLines($width, $text)
            {
                $cw = $this->CurrentFont['cw'];
                if ($width == 0)
                    $width = $this->w - $this->rMargin - $this->x;
                $wmax = ($width - 2 * $this->cMargin) * 1000 / $this->FontSize;
                $s = str_replace("\r", '', $text);
                $nb = strlen($s);
                if ($nb > 0 and $s[$nb - 1] == "\n")
                    $nb--;
                $sep = -1;
                $i = 0;
                $j = 0;
                $l = 0;
                $nl = 1;
                while ($i < $nb) {
                    $c = $s[$i];
                    if ($c == "\n") {
                        $i++;
                        $sep = -1;
                        $j = $i;
                        $l = 0;
                        $nl++;
                        continue;
                    }
                    if ($c == ' ')
                        $sep = $i;
                    $l += $cw[$c];
                    if ($l > $wmax) {
                        if ($sep == -1) {
                            if ($i == $j)
                                $i++;
                        } else
                            $i = $sep + 1;
                        $sep = -1;
                        $j = $i;
                        $l = 0;
                        $nl++;
                    } else
                        $i++;
                }
                return $nl;
            }
        }

        // Instância do PDF
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();

        // Título e informações gerais
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, utf8_decode('Nome do Orçamento: ' . $orcamento['nome_orcamento']), 0, 1);
        $pdf->Cell(0, 8, utf8_decode('Cliente: ' . $orcamento['nome_cliente']), 0, 1);
        $pdf->Cell(0, 8, utf8_decode('Data do Orçamento: ') . date('d/m/Y', strtotime($orcamento['data_orcamento'])), 0, 1);
        $pdf->Cell(0, 8, 'Validade: ' . date('d/m/Y', strtotime($orcamento['validade'])), 0, 1);
        $pdf->Ln(15);

        // Contador para numeração dos itens
        $contadorItens = 1;

        // Adicionando Itens
        foreach ($itens as $item) {
            // Nome do item como texto com numeração
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, utf8_decode($contadorItens . 'º - ' . $item['nome_item']), 0, 1);
            $contadorItens++;

            // Descrição do item como texto separado
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode($item['descricao_item']), 0, 1);
            $pdf->Ln(2);

            // Seção dos Materiais do Item
            if (!empty($item['materiais'])) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 6, utf8_decode('Materiais'), 0, 1);

                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(90, 6, utf8_decode('Nome do Material'), 1);
                $pdf->Cell(30, 6, 'Quantidade', 1);
                $pdf->Cell(30, 6, utf8_decode('Preço Unitário'), 1);
                $pdf->Cell(30, 6, 'Valor Total', 1, 1, 'R');

                $pdf->SetFont('Arial', '', 12);
                foreach ($item['materiais'] as $material) {
                    $valor_total_material = $material['quantidade'] * $material['preco_unitario'];
                    $pdf->AddMaterial($material['nome_material'], $material['quantidade'], $material['preco_unitario'], $valor_total_material);
                }
            }

            // Seção dos Serviços do Item
            if (!empty($item['servicos'])) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 6, utf8_decode('Serviços'), 0, 1);

                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(90, 6, utf8_decode('Nome do Serviço'), 1);
                $pdf->Cell(30, 6, 'Quantidade', 1);
                $pdf->Cell(30, 6, utf8_decode('Preço Unitário'), 1);
                $pdf->Cell(30, 6, 'Valor Total', 1, 1, 'R');

                $pdf->SetFont('Arial', '', 12);
                foreach ($item['servicos'] as $servico) {
                    $valor_total_servico = $servico['quantidade'] * $servico['preco_unitario'];
                    $pdf->AddServico($servico['nome_servico'], $servico['quantidade'], $servico['preco_unitario'], $valor_total_servico);
                }
            }

            // Linha de separação entre itens
            $pdf->Ln(5);
        }

        // Valor Total do Orçamento
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, utf8_decode('Valor Total do Orçamento: R$ ' . number_format($orcamento['valor_total_orcamento'], 2, ',', '.')), 0, 1, 'R');

        // Geração do PDF
        $pdf->Output('D', 'Orcamento_' . $orcamento_id . '.pdf');
    } else {
        echo "Orçamento não encontrado.";
    }
} else {
    echo "ID do orçamento não fornecido.";
}
