    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_acesso SET fkPerfil = :fkPerfil, acesso = :acesso, permissoes = :permissoes, ativo = :ativo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkPerfil', isset($json['fkPerfil']) && trim($json['fkPerfil']) !== '' ? trim($json['fkPerfil']) : null);
            $stmt->bindValue(':acesso', isset($json['acesso']) && trim($json['acesso']) !== '' ? trim($json['acesso']) : null);
            $stmt->bindValue(':permissoes', isset($json['permissoes']) && trim($json['permissoes']) !== '' ? trim($json['permissoes']) : null);
            $stmt->bindValue(':ativo', isset($json['ativo']) && trim($json['ativo']) !== '' ? trim($json['ativo']) : null);
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