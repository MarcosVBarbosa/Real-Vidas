    <?php
        try {
            if (isset($json['pkId']) && is_numeric($json['pkId'])) {

                $sql = "UPDATE rv_fichaAtendimento SET fkOS = :fkOS, checkboxBasico = :checkboxBasico, checkboxExames = :checkboxExames, checkboxUtiAdulto = :checkboxUtiAdulto, checkboxInternacao = :checkboxInternacao, checkboxAph = :checkboxAph, checkboxTrasfHospitalar = :checkboxTrasfHospitalar, checkboxUtiNeo = :checkboxUtiNeo, checkboxEventos = :checkboxEventos, checkboxParticular = :checkboxParticular, checkboxSivInt = :checkboxSivInt, checkboxAltaHospitalar = :checkboxAltaHospitalar, checkboxCate = :checkboxCate, checkboxTomo = :checkboxTomo, checkboxRx = :checkboxRx, checkboxGtt = :checkboxGtt, checkboxCprs = :checkboxCprs, checkboxPetScam = :checkboxPetScam, checkboxTcHip = :checkboxTcHip, checkboxTqtTqm = :checkboxTqtTqm, checkboxUs = :checkboxUs, checkboxRmn = :checkboxRmn, txtOutrosExames = :txtOutrosExames, numberIdade = :numberIdade, txtContato = :txtContato, selectSexo = :selectSexo, selectMembrosSuperior = :selectMembrosSuperior, selectMembrosInferior = :selectMembrosInferior, checkboxLucido = :checkboxLucido, checkboxOrientado = :checkboxOrientado, checkboxConsciente = :checkboxConsciente, checkboxConfuso = :checkboxConfuso, checkboxComunicativo = :checkboxComunicativo, checkboxNaoVerbaliza = :checkboxNaoVerbaliza, checkboxIsocoricas = :checkboxIsocoricas, checkboxAnisocoricas = :checkboxAnisocoricas, checkboxMidriatricas = :checkboxMidriatricas, checkboxMiotica = :checkboxMiotica, checkboxDE = :checkboxDE, checkboxMaior = :checkboxMaior, checkboxEupneico = :checkboxEupneico, checkboxTaquipneico = :checkboxTaquipneico, checkboxBradipneico = :checkboxBradipneico, checkboxDispneico = :checkboxDispneico, checkboxApneia = :checkboxApneia, checkboxNormocardico = :checkboxNormocardico, checkboxTarquicardico = :checkboxTarquicardico, checkboxBradicardico = :checkboxBradicardico, checkboxFiliforme = :checkboxFiliforme, checkboxPlano = :checkboxPlano, checkboxGloboso = :checkboxGloboso, checkboxEscavado = :checkboxEscavado, checkboxFlacido = :checkboxFlacido, checkboxEmAventa = :checkboxEmAventa, checkboxGravidico = :checkboxGravidico, checkboxEspontanea = :checkboxEspontanea, checkboxVoz4 = :checkboxVoz4, checkboxDor2 = :checkboxDor2, checkboxNenhuma1 = :checkboxNenhuma1, checkboxOrientada5 = :checkboxOrientada5, checkboxConfusa4 = :checkboxConfusa4, checkboxPalavras3 = :checkboxPalavras3, checkboxPalavras2 = :checkboxPalavras2, checkboxObdece6 = :checkboxObdece6, checkboxLocaliza5 = :checkboxLocaliza5, checkboxMovimentos4 = :checkboxMovimentos4, checkboxFlexao3 = :checkboxFlexao3, checkboxExtensao2 = :checkboxExtensao2, checkboxNenhuma = :checkboxNenhuma, txtPedagios = :txtPedagios, txtOutros = :txtOutros, selectRefeicaoLanche = :selectRefeicaoLanche, selectGrandeUm = :selectGrandeUm, grandeUmBarValeu = :grandeUmBarValeu, grandeUmBarQuantidade = :grandeUmBarQuantidade, selectGrandeDois = :selectGrandeDois, grandeDoisBarValeu = :grandeDoisBarValeu, grandeDoisBarQuantidade = :grandeDoisBarQuantidade, selectPequenoTres = :selectPequenoTres, pequenoTresBarValeu = :pequenoTresBarValeu, pequenoTresBarQuantidade = :pequenoTresBarQuantidade, selectPequenoQuatro = :selectPequenoQuatro, pequenoQuatroBarValeu = :pequenoQuatroBarValeu, pequenoQuatroBarQuantidade = :pequenoQuatroBarQuantidade, txtObsMedicaEnfermeiros = :txtObsMedicaEnfermeiros, txtMateriaisUtilizados = :txtMateriaisUtilizados, queimadura = :queimadura, trauma = :trauma WHERE pkId = :pkId";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkOS', isset($json['fkOS']) && trim($json['fkOS']) !== '' ? trim($json['fkOS']) : null);
            $stmt->bindValue(':checkboxBasico', isset($json['checkboxBasico']) && trim($json['checkboxBasico']) !== '' ? trim($json['checkboxBasico']) : null);
            $stmt->bindValue(':checkboxExames', isset($json['checkboxExames']) && trim($json['checkboxExames']) !== '' ? trim($json['checkboxExames']) : null);
            $stmt->bindValue(':checkboxUtiAdulto', isset($json['checkboxUtiAdulto']) && trim($json['checkboxUtiAdulto']) !== '' ? trim($json['checkboxUtiAdulto']) : null);
            $stmt->bindValue(':checkboxInternacao', isset($json['checkboxInternacao']) && trim($json['checkboxInternacao']) !== '' ? trim($json['checkboxInternacao']) : null);
            $stmt->bindValue(':checkboxAph', isset($json['checkboxAph']) && trim($json['checkboxAph']) !== '' ? trim($json['checkboxAph']) : null);
            $stmt->bindValue(':checkboxTrasfHospitalar', isset($json['checkboxTrasfHospitalar']) && trim($json['checkboxTrasfHospitalar']) !== '' ? trim($json['checkboxTrasfHospitalar']) : null);
            $stmt->bindValue(':checkboxUtiNeo', isset($json['checkboxUtiNeo']) && trim($json['checkboxUtiNeo']) !== '' ? trim($json['checkboxUtiNeo']) : null);
            $stmt->bindValue(':checkboxEventos', isset($json['checkboxEventos']) && trim($json['checkboxEventos']) !== '' ? trim($json['checkboxEventos']) : null);
            $stmt->bindValue(':checkboxParticular', isset($json['checkboxParticular']) && trim($json['checkboxParticular']) !== '' ? trim($json['checkboxParticular']) : null);
            $stmt->bindValue(':checkboxSivInt', isset($json['checkboxSivInt']) && trim($json['checkboxSivInt']) !== '' ? trim($json['checkboxSivInt']) : null);
            $stmt->bindValue(':checkboxAltaHospitalar', isset($json['checkboxAltaHospitalar']) && trim($json['checkboxAltaHospitalar']) !== '' ? trim($json['checkboxAltaHospitalar']) : null);
            $stmt->bindValue(':checkboxCate', isset($json['checkboxCate']) && trim($json['checkboxCate']) !== '' ? trim($json['checkboxCate']) : null);
            $stmt->bindValue(':checkboxTomo', isset($json['checkboxTomo']) && trim($json['checkboxTomo']) !== '' ? trim($json['checkboxTomo']) : null);
            $stmt->bindValue(':checkboxRx', isset($json['checkboxRx']) && trim($json['checkboxRx']) !== '' ? trim($json['checkboxRx']) : null);
            $stmt->bindValue(':checkboxGtt', isset($json['checkboxGtt']) && trim($json['checkboxGtt']) !== '' ? trim($json['checkboxGtt']) : null);
            $stmt->bindValue(':checkboxCprs', isset($json['checkboxCprs']) && trim($json['checkboxCprs']) !== '' ? trim($json['checkboxCprs']) : null);
            $stmt->bindValue(':checkboxPetScam', isset($json['checkboxPetScam']) && trim($json['checkboxPetScam']) !== '' ? trim($json['checkboxPetScam']) : null);
            $stmt->bindValue(':checkboxTcHip', isset($json['checkboxTcHip']) && trim($json['checkboxTcHip']) !== '' ? trim($json['checkboxTcHip']) : null);
            $stmt->bindValue(':checkboxTqtTqm', isset($json['checkboxTqtTqm']) && trim($json['checkboxTqtTqm']) !== '' ? trim($json['checkboxTqtTqm']) : null);
            $stmt->bindValue(':checkboxUs', isset($json['checkboxUs']) && trim($json['checkboxUs']) !== '' ? trim($json['checkboxUs']) : null);
            $stmt->bindValue(':checkboxRmn', isset($json['checkboxRmn']) && trim($json['checkboxRmn']) !== '' ? trim($json['checkboxRmn']) : null);
            $stmt->bindValue(':txtOutrosExames', isset($json['txtOutrosExames']) && trim($json['txtOutrosExames']) !== '' ? trim($json['txtOutrosExames']) : null);
            $stmt->bindValue(':numberIdade', isset($json['numberIdade']) && trim($json['numberIdade']) !== '' ? trim($json['numberIdade']) : null);
            $stmt->bindValue(':txtContato', isset($json['txtContato']) && trim($json['txtContato']) !== '' ? trim($json['txtContato']) : null);
            $stmt->bindValue(':selectSexo', isset($json['selectSexo']) && trim($json['selectSexo']) !== '' ? trim($json['selectSexo']) : null);
            $stmt->bindValue(':selectMembrosSuperior', isset($json['selectMembrosSuperior']) && trim($json['selectMembrosSuperior']) !== '' ? trim($json['selectMembrosSuperior']) : null);
            $stmt->bindValue(':selectMembrosInferior', isset($json['selectMembrosInferior']) && trim($json['selectMembrosInferior']) !== '' ? trim($json['selectMembrosInferior']) : null);
            $stmt->bindValue(':checkboxLucido', isset($json['checkboxLucido']) && trim($json['checkboxLucido']) !== '' ? trim($json['checkboxLucido']) : null);
            $stmt->bindValue(':checkboxOrientado', isset($json['checkboxOrientado']) && trim($json['checkboxOrientado']) !== '' ? trim($json['checkboxOrientado']) : null);
            $stmt->bindValue(':checkboxConsciente', isset($json['checkboxConsciente']) && trim($json['checkboxConsciente']) !== '' ? trim($json['checkboxConsciente']) : null);
            $stmt->bindValue(':checkboxConfuso', isset($json['checkboxConfuso']) && trim($json['checkboxConfuso']) !== '' ? trim($json['checkboxConfuso']) : null);
            $stmt->bindValue(':checkboxComunicativo', isset($json['checkboxComunicativo']) && trim($json['checkboxComunicativo']) !== '' ? trim($json['checkboxComunicativo']) : null);
            $stmt->bindValue(':checkboxNaoVerbaliza', isset($json['checkboxNaoVerbaliza']) && trim($json['checkboxNaoVerbaliza']) !== '' ? trim($json['checkboxNaoVerbaliza']) : null);
            $stmt->bindValue(':checkboxIsocoricas', isset($json['checkboxIsocoricas']) && trim($json['checkboxIsocoricas']) !== '' ? trim($json['checkboxIsocoricas']) : null);
            $stmt->bindValue(':checkboxAnisocoricas', isset($json['checkboxAnisocoricas']) && trim($json['checkboxAnisocoricas']) !== '' ? trim($json['checkboxAnisocoricas']) : null);
            $stmt->bindValue(':checkboxMidriatricas', isset($json['checkboxMidriatricas']) && trim($json['checkboxMidriatricas']) !== '' ? trim($json['checkboxMidriatricas']) : null);
            $stmt->bindValue(':checkboxMiotica', isset($json['checkboxMiotica']) && trim($json['checkboxMiotica']) !== '' ? trim($json['checkboxMiotica']) : null);
            $stmt->bindValue(':checkboxDE', isset($json['checkboxDE']) && trim($json['checkboxDE']) !== '' ? trim($json['checkboxDE']) : null);
            $stmt->bindValue(':checkboxMaior', isset($json['checkboxMaior']) && trim($json['checkboxMaior']) !== '' ? trim($json['checkboxMaior']) : null);
            $stmt->bindValue(':checkboxEupneico', isset($json['checkboxEupneico']) && trim($json['checkboxEupneico']) !== '' ? trim($json['checkboxEupneico']) : null);
            $stmt->bindValue(':checkboxTaquipneico', isset($json['checkboxTaquipneico']) && trim($json['checkboxTaquipneico']) !== '' ? trim($json['checkboxTaquipneico']) : null);
            $stmt->bindValue(':checkboxBradipneico', isset($json['checkboxBradipneico']) && trim($json['checkboxBradipneico']) !== '' ? trim($json['checkboxBradipneico']) : null);
            $stmt->bindValue(':checkboxDispneico', isset($json['checkboxDispneico']) && trim($json['checkboxDispneico']) !== '' ? trim($json['checkboxDispneico']) : null);
            $stmt->bindValue(':checkboxApneia', isset($json['checkboxApneia']) && trim($json['checkboxApneia']) !== '' ? trim($json['checkboxApneia']) : null);
            $stmt->bindValue(':checkboxNormocardico', isset($json['checkboxNormocardico']) && trim($json['checkboxNormocardico']) !== '' ? trim($json['checkboxNormocardico']) : null);
            $stmt->bindValue(':checkboxTarquicardico', isset($json['checkboxTarquicardico']) && trim($json['checkboxTarquicardico']) !== '' ? trim($json['checkboxTarquicardico']) : null);
            $stmt->bindValue(':checkboxBradicardico', isset($json['checkboxBradicardico']) && trim($json['checkboxBradicardico']) !== '' ? trim($json['checkboxBradicardico']) : null);
            $stmt->bindValue(':checkboxFiliforme', isset($json['checkboxFiliforme']) && trim($json['checkboxFiliforme']) !== '' ? trim($json['checkboxFiliforme']) : null);
            $stmt->bindValue(':checkboxPlano', isset($json['checkboxPlano']) && trim($json['checkboxPlano']) !== '' ? trim($json['checkboxPlano']) : null);
            $stmt->bindValue(':checkboxGloboso', isset($json['checkboxGloboso']) && trim($json['checkboxGloboso']) !== '' ? trim($json['checkboxGloboso']) : null);
            $stmt->bindValue(':checkboxEscavado', isset($json['checkboxEscavado']) && trim($json['checkboxEscavado']) !== '' ? trim($json['checkboxEscavado']) : null);
            $stmt->bindValue(':checkboxFlacido', isset($json['checkboxFlacido']) && trim($json['checkboxFlacido']) !== '' ? trim($json['checkboxFlacido']) : null);
            $stmt->bindValue(':checkboxEmAventa', isset($json['checkboxEmAventa']) && trim($json['checkboxEmAventa']) !== '' ? trim($json['checkboxEmAventa']) : null);
            $stmt->bindValue(':checkboxGravidico', isset($json['checkboxGravidico']) && trim($json['checkboxGravidico']) !== '' ? trim($json['checkboxGravidico']) : null);
            $stmt->bindValue(':checkboxEspontanea', isset($json['checkboxEspontanea']) && trim($json['checkboxEspontanea']) !== '' ? trim($json['checkboxEspontanea']) : null);
            $stmt->bindValue(':checkboxVoz4', isset($json['checkboxVoz4']) && trim($json['checkboxVoz4']) !== '' ? trim($json['checkboxVoz4']) : null);
            $stmt->bindValue(':checkboxDor2', isset($json['checkboxDor2']) && trim($json['checkboxDor2']) !== '' ? trim($json['checkboxDor2']) : null);
            $stmt->bindValue(':checkboxNenhuma1', isset($json['checkboxNenhuma1']) && trim($json['checkboxNenhuma1']) !== '' ? trim($json['checkboxNenhuma1']) : null);
            $stmt->bindValue(':checkboxOrientada5', isset($json['checkboxOrientada5']) && trim($json['checkboxOrientada5']) !== '' ? trim($json['checkboxOrientada5']) : null);
            $stmt->bindValue(':checkboxConfusa4', isset($json['checkboxConfusa4']) && trim($json['checkboxConfusa4']) !== '' ? trim($json['checkboxConfusa4']) : null);
            $stmt->bindValue(':checkboxPalavras3', isset($json['checkboxPalavras3']) && trim($json['checkboxPalavras3']) !== '' ? trim($json['checkboxPalavras3']) : null);
            $stmt->bindValue(':checkboxPalavras2', isset($json['checkboxPalavras2']) && trim($json['checkboxPalavras2']) !== '' ? trim($json['checkboxPalavras2']) : null);
            $stmt->bindValue(':checkboxObdece6', isset($json['checkboxObdece6']) && trim($json['checkboxObdece6']) !== '' ? trim($json['checkboxObdece6']) : null);
            $stmt->bindValue(':checkboxLocaliza5', isset($json['checkboxLocaliza5']) && trim($json['checkboxLocaliza5']) !== '' ? trim($json['checkboxLocaliza5']) : null);
            $stmt->bindValue(':checkboxMovimentos4', isset($json['checkboxMovimentos4']) && trim($json['checkboxMovimentos4']) !== '' ? trim($json['checkboxMovimentos4']) : null);
            $stmt->bindValue(':checkboxFlexao3', isset($json['checkboxFlexao3']) && trim($json['checkboxFlexao3']) !== '' ? trim($json['checkboxFlexao3']) : null);
            $stmt->bindValue(':checkboxExtensao2', isset($json['checkboxExtensao2']) && trim($json['checkboxExtensao2']) !== '' ? trim($json['checkboxExtensao2']) : null);
            $stmt->bindValue(':checkboxNenhuma', isset($json['checkboxNenhuma']) && trim($json['checkboxNenhuma']) !== '' ? trim($json['checkboxNenhuma']) : null);
            $stmt->bindValue(':txtPedagios', isset($json['txtPedagios']) && trim($json['txtPedagios']) !== '' ? trim($json['txtPedagios']) : null);
            $stmt->bindValue(':txtOutros', isset($json['txtOutros']) && trim($json['txtOutros']) !== '' ? trim($json['txtOutros']) : null);
            $stmt->bindValue(':selectRefeicaoLanche', isset($json['selectRefeicaoLanche']) && trim($json['selectRefeicaoLanche']) !== '' ? trim($json['selectRefeicaoLanche']) : null);
            $stmt->bindValue(':selectGrandeUm', isset($json['selectGrandeUm']) && trim($json['selectGrandeUm']) !== '' ? trim($json['selectGrandeUm']) : null);
            $stmt->bindValue(':grandeUmBarValeu', isset($json['grandeUmBarValeu']) && trim($json['grandeUmBarValeu']) !== '' ? trim($json['grandeUmBarValeu']) : null);
            $stmt->bindValue(':grandeUmBarQuantidade', isset($json['grandeUmBarQuantidade']) && trim($json['grandeUmBarQuantidade']) !== '' ? trim($json['grandeUmBarQuantidade']) : null);
            $stmt->bindValue(':selectGrandeDois', isset($json['selectGrandeDois']) && trim($json['selectGrandeDois']) !== '' ? trim($json['selectGrandeDois']) : null);
            $stmt->bindValue(':grandeDoisBarValeu', isset($json['grandeDoisBarValeu']) && trim($json['grandeDoisBarValeu']) !== '' ? trim($json['grandeDoisBarValeu']) : null);
            $stmt->bindValue(':grandeDoisBarQuantidade', isset($json['grandeDoisBarQuantidade']) && trim($json['grandeDoisBarQuantidade']) !== '' ? trim($json['grandeDoisBarQuantidade']) : null);
            $stmt->bindValue(':selectPequenoTres', isset($json['selectPequenoTres']) && trim($json['selectPequenoTres']) !== '' ? trim($json['selectPequenoTres']) : null);
            $stmt->bindValue(':pequenoTresBarValeu', isset($json['pequenoTresBarValeu']) && trim($json['pequenoTresBarValeu']) !== '' ? trim($json['pequenoTresBarValeu']) : null);
            $stmt->bindValue(':pequenoTresBarQuantidade', isset($json['pequenoTresBarQuantidade']) && trim($json['pequenoTresBarQuantidade']) !== '' ? trim($json['pequenoTresBarQuantidade']) : null);
            $stmt->bindValue(':selectPequenoQuatro', isset($json['selectPequenoQuatro']) && trim($json['selectPequenoQuatro']) !== '' ? trim($json['selectPequenoQuatro']) : null);
            $stmt->bindValue(':pequenoQuatroBarValeu', isset($json['pequenoQuatroBarValeu']) && trim($json['pequenoQuatroBarValeu']) !== '' ? trim($json['pequenoQuatroBarValeu']) : null);
            $stmt->bindValue(':pequenoQuatroBarQuantidade', isset($json['pequenoQuatroBarQuantidade']) && trim($json['pequenoQuatroBarQuantidade']) !== '' ? trim($json['pequenoQuatroBarQuantidade']) : null);
            $stmt->bindValue(':txtObsMedicaEnfermeiros', isset($json['txtObsMedicaEnfermeiros']) && trim($json['txtObsMedicaEnfermeiros']) !== '' ? trim($json['txtObsMedicaEnfermeiros']) : null);
            $stmt->bindValue(':txtMateriaisUtilizados', isset($json['txtMateriaisUtilizados']) && trim($json['txtMateriaisUtilizados']) !== '' ? trim($json['txtMateriaisUtilizados']) : null);
            $stmt->bindValue(':queimadura', isset($json['queimadura']) && trim($json['queimadura']) !== '' ? trim($json['queimadura']) : null);
            $stmt->bindValue(':trauma', isset($json['trauma']) && trim($json['trauma']) !== '' ? trim($json['trauma']) : null);
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