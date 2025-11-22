    <?php
    try {
        if (isset($json['nome']) && isset($json['cidade']) && isset($json['estado'])) {

            $sql = "INSERT INTO rv_estabelecimentos (nome, cidade, estado) VALUES (:nome, :cidade, :estado)";
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':cidade', trim($json['cidade']) === '' ? null : trim($json['cidade']));
            $stmt->bindValue(':estado', trim($json['estado']) === '' ? null : trim($json['estado']));


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
