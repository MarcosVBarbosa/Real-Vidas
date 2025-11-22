    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_ordemServicoDestino SET fkOrdemServico = :fkOrdemServico, fkDestino = :fkDestino, distancia = :distancia, horaChegada = :horaChegada, horaSaida = :horaSaida WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkOrdemServico', isset($json['fkOrdemServico']) && trim($json['fkOrdemServico']) !== '' ? trim($json['fkOrdemServico']) : null);
            $stmt->bindValue(':fkDestino', isset($json['fkDestino']) && trim($json['fkDestino']) !== '' ? trim($json['fkDestino']) : null);
            $stmt->bindValue(':distancia', isset($json['distancia']) && trim($json['distancia']) !== '' ? trim($json['distancia']) : null);
            $stmt->bindValue(':horaChegada', isset($json['horaChegada']) && trim($json['horaChegada']) !== '' ? trim($json['horaChegada']) : null);
            $stmt->bindValue(':horaSaida', isset($json['horaSaida']) && trim($json['horaSaida']) !== '' ? trim($json['horaSaida']) : null);
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