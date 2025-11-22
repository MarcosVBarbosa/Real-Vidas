<?php
try {
    if (isset($json['razaoSocial'])) {  // Campo obrigatório

        $sql = "INSERT INTO rv_fornecedores (razaoSocial, nomeFantasia, endereco, numero, pontoReferencia, categoriaFornecedor,cidade, estado, telefoneFixo, celular, whatsapp, email) 
                VALUES (:razaoSocial, :nomeFantasia, :endereco, :numero, :pontoReferencia,:categoriaFornecedor,:cidade, :estado, :telefoneFixo, :celular, :whatsapp, :email)";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':razaoSocial', trim($json['razaoSocial']) === '' ? null : trim($json['razaoSocial']));
        $stmt->bindValue(':nomeFantasia', isset($json['nomeFantasia']) && trim($json['nomeFantasia']) !== '' ? trim($json['nomeFantasia']) : null);
        $stmt->bindValue(':endereco', isset($json['endereco']) && trim($json['endereco']) !== '' ? trim($json['endereco']) : null);
        $stmt->bindValue(':numero', isset($json['numero']) && trim($json['numero']) !== '' ? trim($json['numero']) : null);
        $stmt->bindValue(':pontoReferencia', isset($json['pontoReferencia']) && trim($json['pontoReferencia']) !== '' ? trim($json['pontoReferencia']) : null);
        $stmt->bindValue(':categoriaFornecedor', isset($json['categoriaFornecedor']) && trim($json['categoriaFornecedor']) !== '' ? trim($json['categoriaFornecedor']) : null);
        $stmt->bindValue(':cidade', isset($json['cidade']) && trim($json['cidade']) !== '' ? trim($json['cidade']) : null);
        $stmt->bindValue(':estado', isset($json['estado']) && trim($json['estado']) !== '' ? trim($json['estado']) : null);
        $stmt->bindValue(':telefoneFixo', isset($json['telefoneFixo']) && trim($json['telefoneFixo']) !== '' ? trim($json['telefoneFixo']) : null);
        $stmt->bindValue(':celular', isset($json['celular']) && trim($json['celular']) !== '' ? trim($json['celular']) : null);
        $stmt->bindValue(':whatsapp', isset($json['whatsapp']) && trim($json['whatsapp']) !== '' ? trim($json['whatsapp']) : null);
        $stmt->bindValue(':email', isset($json['email']) && trim($json['email']) !== '' ? trim($json['email']) : null);

        $stmt->execute();

        http_response_code(200);
        $result = [
            'status' => 'success',
            'result' => 'Registro inserido com sucesso!'
        ];
    } else {
        http_response_code(400);
        $result = [
            'status' => 'fail',
            'result' => 'Campo obrigatório "razaoSocial" ausente!'
        ];
    }
} catch (Throwable $th) {
    http_response_code(500);
    $result = [
        'status' => 'fail',
        'result' => $th->getMessage()
    ];
} finally {
    $conn = null;
    echo json_encode($result);
}
