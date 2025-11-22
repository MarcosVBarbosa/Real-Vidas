    <?php
        try {
            if (isset($json['nome']) && isset($json['email']) && isset($json['senha']) && isset($json['fkPermissao']) && isset($json['ativo']) && isset($json['fkPerfil'])) {

                $sql = "INSERT INTO rv_administradores (nome, email, senha, fkPermissao, ativo, fkPerfil) VALUES (:nome, :email, :senha, :fkPermissao, :ativo, :fkPerfil)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':email', trim($json['email']) === '' ? null : trim($json['email']));
            $stmt->bindValue(':senha', trim($json['senha']) === '' ? null : trim($json['senha']));
            $stmt->bindValue(':fkPermissao', trim($json['fkPermissao']) === '' ? null : trim($json['fkPermissao']));
            $stmt->bindValue(':ativo', trim($json['ativo']) === '' ? null : trim($json['ativo']));
            $stmt->bindValue(':fkPerfil', trim($json['fkPerfil']) === '' ? null : trim($json['fkPerfil']));
            

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