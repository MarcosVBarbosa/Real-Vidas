    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_colaboradoresOS SET fkColaborador = :fkColaborador, fkOrdemServico = :fkOrdemServico, qtdeHora = :qtdeHora, valorHora = :valorHora, ajudaCusto = :ajudaCusto WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkColaborador', isset($json['fkColaborador']) && trim($json['fkColaborador']) !== '' ? trim($json['fkColaborador']) : null);
            $stmt->bindValue(':fkOrdemServico', isset($json['fkOrdemServico']) && trim($json['fkOrdemServico']) !== '' ? trim($json['fkOrdemServico']) : null);
            $stmt->bindValue(':qtdeHora', isset($json['qtdeHora']) && trim($json['qtdeHora']) !== '' ? trim($json['qtdeHora']) : null);
            $stmt->bindValue(':valorHora', isset($json['valorHora']) && trim($json['valorHora']) !== '' ? trim($json['valorHora']) : null);
            $stmt->bindValue(':ajudaCusto', isset($json['ajudaCusto']) && trim($json['ajudaCusto']) !== '' ? trim($json['ajudaCusto']) : null);
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