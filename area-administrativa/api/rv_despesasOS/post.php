    <?php
        try {
            if (isset($json['fkOrdemServico']) && isset($json['fkDespesa']) && isset($json['qtde']) && isset($json['valor']) && isset($json['confirmado'])) {

                $sql = "INSERT INTO rv_despesasOS (fkOrdemServico, fkDespesa, qtde, valor, confirmado) VALUES (:fkOrdemServico, :fkDespesa, :qtde, :valor, :confirmado)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkOrdemServico', trim($json['fkOrdemServico']) === '' ? null : trim($json['fkOrdemServico']));
            $stmt->bindValue(':fkDespesa', trim($json['fkDespesa']) === '' ? null : trim($json['fkDespesa']));
            $stmt->bindValue(':qtde', trim($json['qtde']) === '' ? null : trim($json['qtde']));
            $stmt->bindValue(':valor', trim($json['valor']) === '' ? null : trim($json['valor']));
            $stmt->bindValue(':confirmado', trim($json['confirmado']) === '' ? null : trim($json['confirmado']));
            

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