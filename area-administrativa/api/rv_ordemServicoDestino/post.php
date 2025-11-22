    <?php
        try {
            if (isset($json['fkOrdemServico']) && isset($json['fkDestino']) && isset($json['distancia']) && isset($json['horaChegada']) && isset($json['horaSaida'])) {

                $sql = "INSERT INTO rv_ordemServicoDestino (fkOrdemServico, fkDestino, distancia, horaChegada, horaSaida) VALUES (:fkOrdemServico, :fkDestino, :distancia, :horaChegada, :horaSaida)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkOrdemServico', trim($json['fkOrdemServico']) === '' ? null : trim($json['fkOrdemServico']));
            $stmt->bindValue(':fkDestino', trim($json['fkDestino']) === '' ? null : trim($json['fkDestino']));
            $stmt->bindValue(':distancia', trim($json['distancia']) === '' ? null : trim($json['distancia']));
            $stmt->bindValue(':horaChegada', trim($json['horaChegada']) === '' ? null : trim($json['horaChegada']));
            $stmt->bindValue(':horaSaida', trim($json['horaSaida']) === '' ? null : trim($json['horaSaida']));
            

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