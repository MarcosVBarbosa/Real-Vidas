<?php
try {
    if (isset($json['pkId']) && is_numeric($json['pkId'])) {

        $sql = "UPDATE rv_fornecedores SET
                    razaoSocial = :razaoSocial,
                    nomeFantasia = :nomeFantasia,
                    endereco = :endereco,
                    numero = :numero,
                    pontoReferencia = :pontoReferencia,
                    categoriaFornecedor = :categoriaFornecedor,
                    cidade = :cidade,
                    estado = :estado,
                    telefoneFixo = :telefoneFixo,
                    celular = :celular,
                    whatsapp = :whatsapp,
                    email = :email
                WHERE pkId = :pkId";

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
        $stmt->bindValue(':pkId', $json['pkId'], PDO::PARAM_INT);

        $stmt->execute();

        http_response_code(200);
        $result = [
            'status' => 'success',
            'result' => 'Registro atualizado com sucesso!',
            'code' => http_response_code(200)
        ];
    } else {
        http_response_code(400);
        $result = [
            'status' => 'fail',
            'result' => 'Chave primária ausente ou inválida!'
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
