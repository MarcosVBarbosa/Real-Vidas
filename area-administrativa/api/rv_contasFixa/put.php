    <?php
    try {
        if (isset($json['pkId']) && is_numeric($json['pkId'])) {

            $sql = "UPDATE rv_contasFixa 
                    SET descricao = :descricao, 
                        fkFornecedor = :fkFornecedor,
                        nome = :nome
                    WHERE pkId = :pkId";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':descricao', trim($json['descricao']) === '' ? null : trim($json['descricao']));
            $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':fkFornecedor', trim($json['fkFornecedor']) === '' ? null : $json['fkFornecedor'], PDO::PARAM_INT);
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
