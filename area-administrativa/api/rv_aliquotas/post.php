    <?php
        try {
            if (isset($json['mesVigente']) && isset($json['taxa'])) {

                $sql = "INSERT INTO rv_aliquotas (mesVigente, taxa) VALUES (:mesVigente, :taxa)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':mesVigente', trim($json['mesVigente']) === '' ? null : trim($json['mesVigente']));
            $stmt->bindValue(':taxa', trim($json['taxa']) === '' ? null : trim($json['taxa']));
            

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