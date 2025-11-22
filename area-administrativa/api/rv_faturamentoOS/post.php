    <?php
        try {
            if (isset($json['fkFaturamento']) && isset($json['fkOrdemServico'])) {

                $sql = "INSERT INTO rv_faturamentoOS (fkFaturamento, fkOrdemServico) VALUES (:fkFaturamento, :fkOrdemServico)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkFaturamento', trim($json['fkFaturamento']) === '' ? null : trim($json['fkFaturamento']));
            $stmt->bindValue(':fkOrdemServico', trim($json['fkOrdemServico']) === '' ? null : trim($json['fkOrdemServico']));
            

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