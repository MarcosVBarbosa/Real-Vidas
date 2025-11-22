<?php
try {
    if (isset($json['nome'])) {

        $sql = "INSERT INTO rv_categoriasFornecedores (nome)  VALUES (:nome)";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));

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
            'result' => 'Campo obrigatório nome ausente!'
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
