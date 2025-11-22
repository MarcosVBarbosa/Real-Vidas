    <?php
        try {
            if (isset($json['fkVtr']) && isset($json['nome']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_maletas (fkVtr, nome, ativo) VALUES (:fkVtr, :nome, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkVtr', trim($json['fkVtr']) === '' ? null : trim($json['fkVtr']));
            $stmt->bindValue(':nome', trim($json['nome']) === '' ? null : trim($json['nome']));
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