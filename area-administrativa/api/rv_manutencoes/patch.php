<?php

try {
     if (isset($json["id"]) && is_numeric($json["id"])) {
        $sql = "UPDATE rv_manutencoes SET ";
        foreach ($json as $key => $value) {
            if ($key !== 'id') {
                $sql .= "$key = :$key,";
            }
        }
        $sql = rtrim($sql, ',') . " WHERE pkId = :pkId";

        $stmt = $conn->prepare($sql);
        
        foreach ($json as $key => $value) {
            if ($key !== 'pkId') {
                $val = trim($value);
                if ($val === '') {
                    $val = null;
                }
                $stmt->bindValue(":$key", $val);
            } else {
                $stmt->bindValue(':pkId', $value, PDO::PARAM_INT);
            }
        }

        $stmt->execute();

        http_response_code(200);
        $result = [
            'status' => 'success',
            'result' => 'Registro atualizado com sucesso!'
        ];
    } else {
        http_response_code(400);
        $result = [
            'status' => 'fail',
            'result' => 'ID não informado ou inválido!'
        ];
    }

} catch (Throwable $th) {
    http_response_code(500);
    if ($th->getCode() == 23000) {
        $result = [
            'status' => 'fail',
            'result' => 'Registro já existente (violação de chave única).'
        ];
    } else {
        $result = [
            'status' => 'fail',
            'result' => $th->getMessage()
        ];
    }
} finally {
    $conn = null;
    echo json_encode($result);
}