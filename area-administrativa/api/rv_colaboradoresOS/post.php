    <?php
        try {
            if (isset($json['fkColaborador']) && isset($json['fkOrdemServico']) && isset($json['qtdeHora']) && isset($json['valorHora']) && isset($json['ajudaCusto'])) {

                $sql = "INSERT INTO rv_colaboradoresOS (fkColaborador, fkOrdemServico, qtdeHora, valorHora, ajudaCusto) VALUES (:fkColaborador, :fkOrdemServico, :qtdeHora, :valorHora, :ajudaCusto)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkColaborador', trim($json['fkColaborador']) === '' ? null : trim($json['fkColaborador']));
            $stmt->bindValue(':fkOrdemServico', trim($json['fkOrdemServico']) === '' ? null : trim($json['fkOrdemServico']));
            $stmt->bindValue(':qtdeHora', trim($json['qtdeHora']) === '' ? null : trim($json['qtdeHora']));
            $stmt->bindValue(':valorHora', trim($json['valorHora']) === '' ? null : trim($json['valorHora']));
            $stmt->bindValue(':ajudaCusto', trim($json['ajudaCusto']) === '' ? null : trim($json['ajudaCusto']));
            

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