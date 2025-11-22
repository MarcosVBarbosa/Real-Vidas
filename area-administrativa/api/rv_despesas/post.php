    <?php
        try {
            if (isset($json['nome']) && isset($json['valor']) && isset($json['qtde']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_despesas (nome, valor, qtde, ativo) VALUES (:nome, :valor, :qtde, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
            $stmt->bindValue(':valor', trim($json['valor']) === '' ? null : trim($json['valor']));
            $stmt->bindValue(':qtde', trim($json['qtde']) === '' ? null : trim($json['qtde']));
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