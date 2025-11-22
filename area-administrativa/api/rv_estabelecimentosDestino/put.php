    <?php
    try {
        if (isset($json['pkId']) && is_numeric($json['pkId'])) {

            $sql = "UPDATE rv_estabelecimentosDestino SET fkEstabelecimento = :fkEstabelecimento, fkDestino = :fkDestino, ida = :ida, idaVolta = :idaVolta WHERE pkId = :pkId";
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':fkEstabelecimento', trim($json['fkEstabelecimento']) === '' ? null : trim($json['fkEstabelecimento']));
            $stmt->bindValue(':fkDestino', trim($json['fkDestino']) === '' ? null : trim($json['fkDestino']));
            $stmt->bindValue(':ida', trim($json['ida']) === '' ? null : trim($json['ida']));
            $stmt->bindValue(':idaVolta', trim($json['idaVolta']) === '' ? null : trim($json['idaVolta']));
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
