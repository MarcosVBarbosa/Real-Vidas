    <?php
        try {
            if (isset($json['fkPerfil']) && isset($json['acesso']) && isset($json['permissoes']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_acesso (fkPerfil, acesso, permissoes, ativo) VALUES (:fkPerfil, :acesso, :permissoes, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkPerfil', trim($json['fkPerfil']) === '' ? null : trim($json['fkPerfil']));
            $stmt->bindValue(':acesso', trim($json['acesso']) === '' ? null : trim($json['acesso']));
            $stmt->bindValue(':permissoes', trim($json['permissoes']) === '' ? null : trim($json['permissoes']));
            $stmt->bindValue(':ativo', trim($json['ativo']) === '' ? null : trim($json['ativo']));
            

                $stmt->execute();

                http_response_code(201);
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