    <?php
    try {
        if (isset($json['nome']) && isset($json['ativo'])) {

            $sql = "INSERT INTO rv_servicos (nome, ativo) VALUES (:nome, :ativo)";
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':ativo', trim($json['ativo']) === '' ? null : trim($json['ativo']));


            $stmt->execute();

            // Pega o último ID inserido pelo PDO
            $lastId = $conn->lastInsertId();

            http_response_code(200);
            $result = [
                'status' => 'success',
                'result' => 'Registro inserido com sucesso!',
                "pkId" => $lastId
            ];
        } else {
            http_response_code(400);
            $result = [
                'status' => 'fail',
                'result' => 'Campos obrigatórios ausentes!'
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
