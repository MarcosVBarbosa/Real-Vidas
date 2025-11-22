<?php

// VALIDA SE FOI LIBERADO O ACESSO
try {
    if (isset($json["id"]) && is_numeric($json["id"])) {
        $sql = "DELETE FROM rv_contasFixa WHERE pkId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $json["id"], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            $result = [
                'status' => 'success',
                'result' => 'Registro excluído com sucesso!'
            ];
        } else {
            http_response_code(404);
            $result = [
                'status' => 'fail',
                'result' => 'Registro não encontrado!'
            ];
        }
    } else {
        http_response_code(400);
        $result = [
            'status' => 'fail',
            'result' => 'ID não informado ou inválido!'
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