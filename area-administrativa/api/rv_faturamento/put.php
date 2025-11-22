    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_faturamento SET status = :status, dataHora = :dataHora, dataVencimento = :dataVencimento, notaFiscal = :notaFiscal, formaPgto = :formaPgto, prazoPgto = :prazoPgto, taxaNF = :taxaNF, caminhoBoleto = :caminhoBoleto, caminhoNF = :caminhoNF, enviado = :enviado, ativo = :ativo WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':status', isset($json['status']) && trim($json['status']) !== '' ? trim($json['status']) : null);
            $stmt->bindValue(':dataHora', isset($json['dataHora']) && trim($json['dataHora']) !== '' ? trim($json['dataHora']) : null);
            $stmt->bindValue(':dataVencimento', isset($json['dataVencimento']) && trim($json['dataVencimento']) !== '' ? trim($json['dataVencimento']) : null);
            $stmt->bindValue(':notaFiscal', isset($json['notaFiscal']) && trim($json['notaFiscal']) !== '' ? trim($json['notaFiscal']) : null);
            $stmt->bindValue(':formaPgto', isset($json['formaPgto']) && trim($json['formaPgto']) !== '' ? trim($json['formaPgto']) : null);
            $stmt->bindValue(':prazoPgto', isset($json['prazoPgto']) && trim($json['prazoPgto']) !== '' ? trim($json['prazoPgto']) : null);
            $stmt->bindValue(':taxaNF', isset($json['taxaNF']) && trim($json['taxaNF']) !== '' ? trim($json['taxaNF']) : null);
            $stmt->bindValue(':caminhoBoleto', isset($json['caminhoBoleto']) && trim($json['caminhoBoleto']) !== '' ? trim($json['caminhoBoleto']) : null);
            $stmt->bindValue(':caminhoNF', isset($json['caminhoNF']) && trim($json['caminhoNF']) !== '' ? trim($json['caminhoNF']) : null);
            $stmt->bindValue(':enviado', isset($json['enviado']) && trim($json['enviado']) !== '' ? trim($json['enviado']) : null);
            $stmt->bindValue(':ativo', isset($json['ativo']) && trim($json['ativo']) !== '' ? trim($json['ativo']) : null);
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