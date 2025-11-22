    <?php
        try {
            if (isset($json['status']) && isset($json['dataHora']) && isset($json['dataVencimento']) && isset($json['notaFiscal']) && isset($json['formaPgto']) && isset($json['prazoPgto']) && isset($json['taxaNF']) && isset($json['caminhoBoleto']) && isset($json['caminhoNF']) && isset($json['enviado']) && isset($json['ativo'])) {

                $sql = "INSERT INTO rv_faturamento (status, dataHora, dataVencimento, notaFiscal, formaPgto, prazoPgto, taxaNF, caminhoBoleto, caminhoNF, enviado, ativo) VALUES (:status, :dataHora, :dataVencimento, :notaFiscal, :formaPgto, :prazoPgto, :taxaNF, :caminhoBoleto, :caminhoNF, :enviado, :ativo)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':status', trim($json['status']) === '' ? null : trim($json['status']));
            $stmt->bindValue(':dataHora', trim($json['dataHora']) === '' ? null : trim($json['dataHora']));
            $stmt->bindValue(':dataVencimento', trim($json['dataVencimento']) === '' ? null : trim($json['dataVencimento']));
            $stmt->bindValue(':notaFiscal', trim($json['notaFiscal']) === '' ? null : trim($json['notaFiscal']));
            $stmt->bindValue(':formaPgto', trim($json['formaPgto']) === '' ? null : trim($json['formaPgto']));
            $stmt->bindValue(':prazoPgto', trim($json['prazoPgto']) === '' ? null : trim($json['prazoPgto']));
            $stmt->bindValue(':taxaNF', trim($json['taxaNF']) === '' ? null : trim($json['taxaNF']));
            $stmt->bindValue(':caminhoBoleto', trim($json['caminhoBoleto']) === '' ? null : trim($json['caminhoBoleto']));
            $stmt->bindValue(':caminhoNF', trim($json['caminhoNF']) === '' ? null : trim($json['caminhoNF']));
            $stmt->bindValue(':enviado', trim($json['enviado']) === '' ? null : trim($json['enviado']));
            $stmt->bindValue(':ativo', trim($json['ativo']) === '' ? null : trim($json['ativo']));
            

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