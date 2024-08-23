<?php
session_start();
require_once 'Application/models/fornecedor_dao.php';
require_once 'Application/models/material_dao.php';
require_once 'Application/models/xml_dao.php';
require_once 'Application/models/conexao.php';

function normalizarNome($nome) {
    $nome = utf8_decode($nome); 
    $nome = strtr($nome, 
        'ÁÀÂÃÄÉÈÊËÍÌÎÏÓÒÔÕÖÚÙÛÜÇÑáàâãäéèêëíìîïóòôõöúùûüçñ', 
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
        $xmlContent = file_get_contents($xmlFile);

        if ($xmlContent) {
            $xml = simplexml_load_string($xmlContent);
            $namespaces = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('nfe', $namespaces['']);

            // Extrair dados da Nota Fiscal
            $numero_nota = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:ide/nfe:nNF')[0]);
            $data_emissao = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:ide/nfe:dhEmi')[0]);
            $valor_total = mysqli_real_escape_string($conexao, (float)$xml->xpath('//nfe:ICMSTot/nfe:vNF')[0]);
            $caminho_xml = 'uploads/xml/' . basename($_FILES['xmlFile']['name']);
            $parcelas = 1; // Supondo que a nota tem uma parcela, isso pode ser ajustado conforme necessário

            // Extrair dados do fornecedor
            $cnpj_emit = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:CNPJ')[0]);
            $nome_emit = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:xNome')[0]);
            $telefone_emit = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:fone')[0]);
            $endereco_emit = mysqli_real_escape_string($conexao, (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:xLgr')[0] . ', ' . (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:nro')[0]);
            $cidade_nome_original = (string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:xMun')[0];
            $cidade_nome_normalizado = normalizarNome($cidade_nome_original);
            $estado_sigla = normalizarNome((string)$xml->xpath('//nfe:emit/nfe:enderEmit/nfe:UF')[0]);

            $cnpj_emit = formatarCNPJ($cnpj_emit);
            $telefone_emit = formatarTelefone($telefone_emit);

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
            $query_fornecedor_existente = "
                SELECT id_fornecedor 
                FROM fornecedores 
                WHERE cnpj = '$cnpj_emit'
            ";
            $result_fornecedor_existente = mysqli_query($conexao, $query_fornecedor_existente);

            if ($result_fornecedor_existente && mysqli_num_rows($result_fornecedor_existente) > 0) {
                $fornecedor = mysqli_fetch_assoc($result_fornecedor_existente);
                $fornecedor_id = $fornecedor['id_fornecedor'];
            } else {
                // Cadastrar o fornecedor no banco de dados
                $fornecedor_id = FornecedorDAO::criarFornecedor($nome_emit, $cnpj_emit, $telefone_emit, '', $endereco_emit, $cidade_id, $estado_id);

                if ($fornecedor_id <= 0) {
                    $_SESSION['mensagem'] = 'Erro ao criar fornecedor.';
                    $_SESSION['mensagem_tipo'] = 'error';
                    header('Location: /planel/xml');
                    exit;
                }
            }

            // Verificar se o XML já foi importado
            $query_xml_existente = "
                SELECT id_nota_fiscal 
                FROM notas_fiscais 
                WHERE caminho_xml = '$caminho_xml'
            ";
            $result_xml_existente = mysqli_query($conexao, $query_xml_existente);

            if ($result_xml_existente && mysqli_num_rows($result_xml_existente) > 0) {
                $_SESSION['mensagem'] = 'Este XML já foi importado anteriormente.';
                $_SESSION['mensagem_tipo'] = 'warning';
                header('Location: /planel/xml');
                exit;
            }

            // Cadastrar a nota fiscal no banco de dados
            $nota_fiscal_id = XMLDAO::cadastrarNotaFiscal([
                'numero' => $numero_nota,
                'data_emissao' => $data_emissao,
                'valor_total' => $valor_total,
                'parcelas' => $parcelas,
                'caminho_xml' => $caminho_xml,
                'fornecedor_id' => $fornecedor_id
            ]);

            if ($nota_fiscal_id <= 0) {
                $_SESSION['mensagem'] = 'Erro ao cadastrar a nota fiscal.';
                $_SESSION['mensagem_tipo'] = 'error';
                header('Location: /planel/xml');
                exit;
            }

            // Extrair e cadastrar materiais do XML
            $items = $xml->xpath('//nfe:det');
            foreach ($items as $item) {
                $ncm_produto = mysqli_real_escape_string($conexao, (string)$item->prod->NCM);
                $descricao_produto = mysqli_real_escape_string($conexao, (string)$item->prod->xProd);
                $quantidade = mysqli_real_escape_string($conexao, (float)$item->prod->qCom);
                $valor_unitario = mysqli_real_escape_string($conexao, (float)$item->prod->vUnTrib);
                $valor_compra = $valor_unitario;
                $valor_venda = $valor_compra * 1.4;
                $data_compra = date('Y-m-d');
                $unidade_medida = mysqli_real_escape_string($conexao, (string)$item->prod->uCom);

                // Verificar se o material já existe para o mesmo fornecedor
                $query_material_existente = "
                    SELECT id_material, quantidade 
                    FROM materiais 
                    WHERE ncm = '$ncm_produto' 
                    AND nome_material = '$descricao_produto' 
                    AND fk_fornecedores_id_fornecedor = $fornecedor_id
                ";
                $result_material_existente = mysqli_query($conexao, $query_material_existente);

                if ($result_material_existente && mysqli_num_rows($result_material_existente) > 0) {
                    $material_existente = mysqli_fetch_assoc($result_material_existente);
                    $nova_quantidade = $material_existente['quantidade'] + $quantidade;

                    // Atualizar material existente
                    $result_update_material = MaterialDAO::atualizarMaterial($material_existente['id_material'], $descricao_produto, $valor_compra, $valor_venda, $data_compra, $nova_quantidade, $unidade_medida);

                    if ($result_update_material <= 0) {
                        $_SESSION['mensagem'] = 'Erro ao atualizar material: ' . $descricao_produto;
                        $_SESSION['mensagem_tipo'] = 'error';
                        header('Location: /planel/xml');
                        exit;
                    }
                } else {
                    // Inserir novo material
                    $result_material = MaterialDAO::criarMaterial($descricao_produto, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fornecedor_id, $ncm_produto);

                    if ($result_material <= 0) {
                        $_SESSION['mensagem'] = 'Erro ao cadastrar material: ' . $descricao_produto;
                        $_SESSION['mensagem_tipo'] = 'error';
                        header('Location: /planel/xml');
                        exit;
                    }
                }
            }

            // Mover o arquivo XML para o diretório apropriado
            move_uploaded_file($_FILES['xmlFile']['tmp_name'], $caminho_xml);

            $_SESSION['mensagem'] = 'XML importado e processado com sucesso!';
            $_SESSION['mensagem_tipo'] = 'success';
        } else {
            $_SESSION['mensagem'] = 'Erro ao ler o conteúdo do arquivo XML.';
            $_SESSION['mensagem_tipo'] = 'error';
        }
    } else {
        $_SESSION['mensagem'] = 'Erro ao carregar o arquivo XML.';
        $_SESSION['mensagem_tipo'] = 'error';
    }

    header('Location: /planel/xml');
    exit;
}
