    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_perfil SET nome = :nome, ativo = :ativo, permissao_valores = :permissao_valores WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':nome', isset($json['nome']) && trim($json['nome']) !== '' ? trim($json['nome']) : null);
            $stmt->bindValue(':ativo', isset($json['ativo']) && trim($json['ativo']) !== '' ? trim($json['ativo']) : null);
            $stmt->bindValue(':permissao_valores', isset($json['permissao_valores']) && trim($json['permissao_valores']) !== '' ? trim($json['permissao_valores']) : null);
            $stmt->bindValue(':pkId', $json['pkId'], PDO::PARAM_INT);
            

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