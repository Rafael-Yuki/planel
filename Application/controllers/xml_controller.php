<?php
session_start();
require_once 'Application/models/fornecedor_dao.php';
require_once 'Application/models/material_dao.php';
require_once 'Application/models/nota_fiscal_dao.php';
require_once 'Application/models/contas_pagar_dao.php';
require_once 'Application/models/parcelas_pagar_dao.php';
require_once 'Application/models/conexao.php';

function normalizarNome($nome) {
    $nome = utf8_decode($nome); 
    $nome = strtr($nome, 'ÁÀÂÃÄÉÈÊËÍÌÎÏÓÒÔÕÖÚÙÛÜÇÑáàâãäéèêëíìîïóòôõöúùûüçñ', 
                  'AAAAAEEEEIIIIOOOOOUUUUCNaaaaaeeeeiiiiooooouuuucn');
    $nome = strtolower($nome); 
    $nome = trim($nome); 
    return $nome;
}

function formatarCNPJ($cnpj) {
    return preg_replace("/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/", "$1.$2.$3/$4-$5", $cnpj);
}

function formatarTelefone($telefone) {
    return preg_replace("/^(\d{2})(\d{4,5})(\d{4})$/", "($1) $2-$3", $telefone);
}

if (isset($_POST['importar_xml'])) {
    if (isset($_FILES['xmlFile']) && $_FILES['xmlFile']['error'] == UPLOAD_ERR_OK) {
        $xmlFile = $_FILES['xmlFile']['tmp_name'];
        $nome_arquivo_xml = basename($_FILES['xmlFile']['name']);
        $caminho_xml = 'uploads/xml/' . $nome_arquivo_xml;

        // Verificar se a pasta 'uploads/xml' existe e criar caso não exista
        if (!is_dir('uploads/xml')) {
            mkdir('uploads/xml', 0777, true);
        }

        // Mover o arquivo XML para a pasta 'uploads/xml'
        if (move_uploaded_file($xmlFile, $caminho_xml)) {
            $xmlContent = file_get_contents($caminho_xml);

            if ($xmlContent) {
                $xml = simplexml_load_string($xmlContent);
                $namespaces = $xml->getNamespaces(true);
                $xml->registerXPathNamespace('nfe', $namespaces['']);

                // Extrair dados da Nota Fiscal
                $numero_nota = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:ide/nfe:nNF')[0]);

                // Verificar se a nota fiscal já foi importada
                $query_verifica_nota = "SELECT id_nota_fiscal FROM notas_fiscais WHERE numero = '$numero_nota'";
                $result_verifica_nota = mysqli_query($conexao, $query_verifica_nota);

                if (mysqli_num_rows($result_verifica_nota) > 0) {
                    $_SESSION['mensagem'] = 'Essa nota fiscal já foi importada anteriormente.';
                    $_SESSION['mensagem_tipo'] = 'error';
                    header('Location: /planel/xml');
                    exit;
                }

                $data_emissao = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:ide/nfe:dhEmi')[0]);
                $valor_total = mysqli_real_escape_string($conexao, (float)$xml->xpath('//nfe:ICMSTot/nfe:vNF')[0]);

                // Extrair dados do fornecedor
                $cnpj_emit = formatarCNPJ(mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:CNPJ')[0]));
                $nome_emit = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:xNome')[0]);
                $telefone_emit = formatarTelefone(mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:fone')[0]));
                $endereco_emit = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:xLgr')[0] . ', ' . (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:nro')[0]);
                $cidade_nome_original = (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:xMun')[0];
                $cidade_nome_normalizado = normalizarNome($cidade_nome_original);
                $estado_sigla = normalizarNome((string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:UF')[0]);

                // Buscar o estado no banco de dados
                $query_estado = "
                    SELECT id_estado 
                    FROM estados 
                    WHERE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(sigla_estado, 'ã', 'a'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o')) = '$estado_sigla'
                ";
                $result_estado = mysqli_query($conexao, $query_estado);
                $estado_id = 0;

                if ($result_estado && mysqli_num_rows($result_estado) > 0) {
                    $estado = mysqli_fetch_assoc($result_estado);
                    $estado_id = $estado['id_estado'];
                } else {
                    $_SESSION['mensagem'] = 'Estado não encontrado no banco de dados.';
                    $_SESSION['mensagem_tipo'] = 'error';
                    header('Location: /planel/xml');
                    exit;
                }

                // Buscar a cidade no banco de dados
                $query_cidade = "
                    SELECT id_cidade, nome_cidade 
                    FROM cidades 
                    WHERE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(nome_cidade, 'ã', 'a'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o')) = '$cidade_nome_normalizado' 
                    AND fk_estados_id_estado = $estado_id
                ";
                $result_cidade = mysqli_query($conexao, $query_cidade);
                $cidade_id = 0;

                if ($result_cidade && mysqli_num_rows($result_cidade) > 0) {
                    $cidade = mysqli_fetch_assoc($result_cidade);
                    $cidade_id = $cidade['id_cidade'];
                } else {
                    $_SESSION['mensagem'] = "Cidade '{$cidade_nome_original}' (normalizada como '{$cidade_nome_normalizado}') não encontrada no banco de dados.";
                    $_SESSION['mensagem_tipo'] = 'error';
                    header('Location: /planel/xml');
                    exit;
                }

                // Verificar se o fornecedor já existe
                $fornecedor_existente = FornecedorDAO::buscarFornecedorPorCNPJ($cnpj_emit);
                if (!$fornecedor_existente) {
                    // Se não existir, criar novo fornecedor
                    FornecedorDAO::criarFornecedor($nome_emit, $cnpj_emit, $telefone_emit, '', $endereco_emit, $cidade_id, $estado_id);
                    $fornecedor_id = mysqli_insert_id($conexao);
                } else {
                    $fornecedor_id = $fornecedor_existente['id_fornecedor'];
                }

                // Extrair duplicatas e parcelas da Nota Fiscal
                $duplicatas = $xml->xpath('//nfe:dup');
                $numero_parcelas = count($duplicatas);

                // Criar Nota Fiscal e armazenar o caminho do arquivo XML, associando o número de parcelas
                NotaFiscalDAO::criarNotaFiscal($numero_nota, $data_emissao, $valor_total, $numero_parcelas, $fornecedor_id, $caminho_xml);

                // Obter o ID da nota fiscal recém-criada
                $id_nota_fiscal = mysqli_insert_id($conexao);

                if (!$id_nota_fiscal) {
                    $_SESSION['mensagem'] = 'Erro ao criar a nota fiscal.';
                    $_SESSION['mensagem_tipo'] = 'error';
                    header('Location: /planel/xml');
                    exit;
                }

                if ($numero_parcelas > 0) {
                    // Criar Conta a Pagar associada à Nota Fiscal com o número correto de parcelas
                    $data_vencimento_primeira = mysqli_real_escape_string($conexao, (string)$duplicatas[0]->dVenc);
                    $conta_pagar_id = ContasPagarDAO::criarContaPagar($valor_total, $data_vencimento_primeira, $numero_parcelas, 0, $id_nota_fiscal, $fornecedor_id);

                    // Criar parcelas da conta a pagar com base nas duplicatas
                    foreach ($duplicatas as $dup) {
                        $valor_parcela = (float)$dup->vDup;
                        $vencimento_parcela = mysqli_real_escape_string($conexao, (string)$dup->dVenc);

                        ParcelasPagarDAO::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, null, $conta_pagar_id, $id_nota_fiscal, $fornecedor_id, null);
                    }
                }

                // Criar materiais associados à Nota Fiscal
                $items = $xml->xpath('//nfe:det');
                foreach ($items as $item) {
                    $ncm_produto = mysqli_real_escape_string($conexao, (string)$item->prod->NCM);
                    $descricao_produto = mysqli_real_escape_string($conexao, (string)$item->prod->xProd);
                    $quantidade = mysqli_real_escape_string($conexao, (float)$item->prod->qCom);
                    $valor_unitario = mysqli_real_escape_string($conexao, (float)$item->prod->vUnTrib);
                    
                    // Atribuir valores para compra e venda
                    $valor_compra = $valor_unitario;
                    $valor_venda = $valor_compra * 1.4;  // Aplicar margem de 40% no valor de venda
                    
                    // Definir data de compra como a data atual
                    $data_compra = date('Y-m-d');
                    
                    // Unidade de medida do produto
                    $unidade_medida = mysqli_real_escape_string($conexao, (string)$item->prod->uCom);
                
                    // Verificar se os dados são válidos
                    if ($quantidade == 0 || $valor_compra == 0 || empty($descricao_produto)) {
                        $_SESSION['mensagem'] = 'Erro na extração dos dados do material. Quantidade, valor de compra ou descrição estão incorretos.';
                        $_SESSION['mensagem_tipo'] = 'error';
                        header('Location: /planel/xml');
                        exit;
                    }
                
                    // Inserir material no banco de dados
                    MaterialDAO::criarMaterial($descricao_produto, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fornecedor_id, $ncm_produto);
                }

                $_SESSION['mensagem'] = 'Importação de XML e criação de contas a pagar, parcelas e materiais concluídas com sucesso.';
                $_SESSION['mensagem_tipo'] = 'success';
                header('Location: /planel/xml');  // Redirecionar após o sucesso
                exit;
            } else {
                $_SESSION['mensagem'] = 'Falha ao carregar o conteúdo do arquivo XML.';
                $_SESSION['mensagem_tipo'] = 'error';
            }
        } else {
            $_SESSION['mensagem'] = 'Erro ao fazer upload do arquivo XML.';
            $_SESSION['mensagem_tipo'] = 'error';
        }
    }

    header('Location: /planel/xml'); // Redireciona em caso de falha
    exit;
}
