    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_administradores SET nome = :nome, email = :email, senha = :senha, fkPermissao = :fkPermissao, ativo = :ativo, fkPerfil = :fkPerfil WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':nome', isset($json['nome']) && trim($json['nome']) !== '' ? trim($json['nome']) : null);
            $stmt->bindValue(':email', isset($json['email']) && trim($json['email']) !== '' ? trim($json['email']) : null);
            $stmt->bindValue(':senha', isset($json['senha']) && trim($json['senha']) !== '' ? trim($json['senha']) : null);
            $stmt->bindValue(':fkPermissao', isset($json['fkPermissao']) && trim($json['fkPermissao']) !== '' ? trim($json['fkPermissao']) : null);
            $stmt->bindValue(':ativo', isset($json['ativo']) && trim($json['ativo']) !== '' ? trim($json['ativo']) : null);
            $stmt->bindValue(':fkPerfil', isset($json['fkPerfil']) && trim($json['fkPerfil']) !== '' ? trim($json['fkPerfil']) : null);
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