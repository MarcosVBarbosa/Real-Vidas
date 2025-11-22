    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_aliquotas SET mesVigente = :mesVigente, taxa = :taxa WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':mesVigente', isset($json['mesVigente']) && trim($json['mesVigente']) !== '' ? trim($json['mesVigente']) : null);
            $stmt->bindValue(':taxa', isset($json['taxa']) && trim($json['taxa']) !== '' ? trim($json['taxa']) : null);
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