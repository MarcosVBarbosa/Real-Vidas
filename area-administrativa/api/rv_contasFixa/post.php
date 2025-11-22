    <?php
    try {
        if (isset($json['descricao'], $json['nome'])) {

            $sql = "INSERT INTO rv_contasFixa (descricao, nome,fkFornecedor) 
                    VALUES (:descricao, :nome,:fkFornecedor)";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':descricao', trim($json['descricao']) === '' ? null : trim($json['descricao']));
            $stmt->bindValue(':fkFornecedor', trim($json['fkFornecedor']) === '' ? null : $json['fkFornecedor'], PDO::PARAM_INT);
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
