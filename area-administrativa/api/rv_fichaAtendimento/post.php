    <?php
        try {
            if (isset($json['fkOS']) && isset($json['checkboxBasico']) && isset($json['checkboxExames']) && isset($json['checkboxUtiAdulto']) && isset($json['checkboxInternacao']) && isset($json['checkboxAph']) && isset($json['checkboxTrasfHospitalar']) && isset($json['checkboxUtiNeo']) && isset($json['checkboxEventos']) && isset($json['checkboxParticular']) && isset($json['checkboxSivInt']) && isset($json['checkboxAltaHospitalar']) && isset($json['checkboxCate']) && isset($json['checkboxTomo']) && isset($json['checkboxRx']) && isset($json['checkboxGtt']) && isset($json['checkboxCprs']) && isset($json['checkboxPetScam']) && isset($json['checkboxTcHip']) && isset($json['checkboxTqtTqm']) && isset($json['checkboxUs']) && isset($json['checkboxRmn']) && isset($json['txtOutrosExames']) && isset($json['numberIdade']) && isset($json['txtContato']) && isset($json['selectSexo']) && isset($json['selectMembrosSuperior']) && isset($json['selectMembrosInferior']) && isset($json['checkboxLucido']) && isset($json['checkboxOrientado']) && isset($json['checkboxConsciente']) && isset($json['checkboxConfuso']) && isset($json['checkboxComunicativo']) && isset($json['checkboxNaoVerbaliza']) && isset($json['checkboxIsocoricas']) && isset($json['checkboxAnisocoricas']) && isset($json['checkboxMidriatricas']) && isset($json['checkboxMiotica']) && isset($json['checkboxDE']) && isset($json['checkboxMaior']) && isset($json['checkboxEupneico']) && isset($json['checkboxTaquipneico']) && isset($json['checkboxBradipneico']) && isset($json['checkboxDispneico']) && isset($json['checkboxApneia']) && isset($json['checkboxNormocardico']) && isset($json['checkboxTarquicardico']) && isset($json['checkboxBradicardico']) && isset($json['checkboxFiliforme']) && isset($json['checkboxPlano']) && isset($json['checkboxGloboso']) && isset($json['checkboxEscavado']) && isset($json['checkboxFlacido']) && isset($json['checkboxEmAventa']) && isset($json['checkboxGravidico']) && isset($json['checkboxEspontanea']) && isset($json['checkboxVoz4']) && isset($json['checkboxDor2']) && isset($json['checkboxNenhuma1']) && isset($json['checkboxOrientada5']) && isset($json['checkboxConfusa4']) && isset($json['checkboxPalavras3']) && isset($json['checkboxPalavras2']) && isset($json['checkboxObdece6']) && isset($json['checkboxLocaliza5']) && isset($json['checkboxMovimentos4']) && isset($json['checkboxFlexao3']) && isset($json['checkboxExtensao2']) && isset($json['checkboxNenhuma']) && isset($json['txtPedagios']) && isset($json['txtOutros']) && isset($json['selectRefeicaoLanche']) && isset($json['selectGrandeUm']) && isset($json['grandeUmBarValeu']) && isset($json['grandeUmBarQuantidade']) && isset($json['selectGrandeDois']) && isset($json['grandeDoisBarValeu']) && isset($json['grandeDoisBarQuantidade']) && isset($json['selectPequenoTres']) && isset($json['pequenoTresBarValeu']) && isset($json['pequenoTresBarQuantidade']) && isset($json['selectPequenoQuatro']) && isset($json['pequenoQuatroBarValeu']) && isset($json['pequenoQuatroBarQuantidade']) && isset($json['txtObsMedicaEnfermeiros']) && isset($json['txtMateriaisUtilizados']) && isset($json['queimadura']) && isset($json['trauma'])) {

                $sql = "INSERT INTO rv_fichaAtendimento (fkOS, checkboxBasico, checkboxExames, checkboxUtiAdulto, checkboxInternacao, checkboxAph, checkboxTrasfHospitalar, checkboxUtiNeo, checkboxEventos, checkboxParticular, checkboxSivInt, checkboxAltaHospitalar, checkboxCate, checkboxTomo, checkboxRx, checkboxGtt, checkboxCprs, checkboxPetScam, checkboxTcHip, checkboxTqtTqm, checkboxUs, checkboxRmn, txtOutrosExames, numberIdade, txtContato, selectSexo, selectMembrosSuperior, selectMembrosInferior, checkboxLucido, checkboxOrientado, checkboxConsciente, checkboxConfuso, checkboxComunicativo, checkboxNaoVerbaliza, checkboxIsocoricas, checkboxAnisocoricas, checkboxMidriatricas, checkboxMiotica, checkboxDE, checkboxMaior, checkboxEupneico, checkboxTaquipneico, checkboxBradipneico, checkboxDispneico, checkboxApneia, checkboxNormocardico, checkboxTarquicardico, checkboxBradicardico, checkboxFiliforme, checkboxPlano, checkboxGloboso, checkboxEscavado, checkboxFlacido, checkboxEmAventa, checkboxGravidico, checkboxEspontanea, checkboxVoz4, checkboxDor2, checkboxNenhuma1, checkboxOrientada5, checkboxConfusa4, checkboxPalavras3, checkboxPalavras2, checkboxObdece6, checkboxLocaliza5, checkboxMovimentos4, checkboxFlexao3, checkboxExtensao2, checkboxNenhuma, txtPedagios, txtOutros, selectRefeicaoLanche, selectGrandeUm, grandeUmBarValeu, grandeUmBarQuantidade, selectGrandeDois, grandeDoisBarValeu, grandeDoisBarQuantidade, selectPequenoTres, pequenoTresBarValeu, pequenoTresBarQuantidade, selectPequenoQuatro, pequenoQuatroBarValeu, pequenoQuatroBarQuantidade, txtObsMedicaEnfermeiros, txtMateriaisUtilizados, queimadura, trauma) VALUES (:fkOS, :checkboxBasico, :checkboxExames, :checkboxUtiAdulto, :checkboxInternacao, :checkboxAph, :checkboxTrasfHospitalar, :checkboxUtiNeo, :checkboxEventos, :checkboxParticular, :checkboxSivInt, :checkboxAltaHospitalar, :checkboxCate, :checkboxTomo, :checkboxRx, :checkboxGtt, :checkboxCprs, :checkboxPetScam, :checkboxTcHip, :checkboxTqtTqm, :checkboxUs, :checkboxRmn, :txtOutrosExames, :numberIdade, :txtContato, :selectSexo, :selectMembrosSuperior, :selectMembrosInferior, :checkboxLucido, :checkboxOrientado, :checkboxConsciente, :checkboxConfuso, :checkboxComunicativo, :checkboxNaoVerbaliza, :checkboxIsocoricas, :checkboxAnisocoricas, :checkboxMidriatricas, :checkboxMiotica, :checkboxDE, :checkboxMaior, :checkboxEupneico, :checkboxTaquipneico, :checkboxBradipneico, :checkboxDispneico, :checkboxApneia, :checkboxNormocardico, :checkboxTarquicardico, :checkboxBradicardico, :checkboxFiliforme, :checkboxPlano, :checkboxGloboso, :checkboxEscavado, :checkboxFlacido, :checkboxEmAventa, :checkboxGravidico, :checkboxEspontanea, :checkboxVoz4, :checkboxDor2, :checkboxNenhuma1, :checkboxOrientada5, :checkboxConfusa4, :checkboxPalavras3, :checkboxPalavras2, :checkboxObdece6, :checkboxLocaliza5, :checkboxMovimentos4, :checkboxFlexao3, :checkboxExtensao2, :checkboxNenhuma, :txtPedagios, :txtOutros, :selectRefeicaoLanche, :selectGrandeUm, :grandeUmBarValeu, :grandeUmBarQuantidade, :selectGrandeDois, :grandeDoisBarValeu, :grandeDoisBarQuantidade, :selectPequenoTres, :pequenoTresBarValeu, :pequenoTresBarQuantidade, :selectPequenoQuatro, :pequenoQuatroBarValeu, :pequenoQuatroBarQuantidade, :txtObsMedicaEnfermeiros, :txtMateriaisUtilizados, :queimadura, :trauma)";
                $stmt = $conn->prepare($sql);

                $stmt->bindValue(':fkOS', trim($json['fkOS']) === '' ? null : trim($json['fkOS']));
            $stmt->bindValue(':checkboxBasico', trim($json['checkboxBasico']) === '' ? null : trim($json['checkboxBasico']));
            $stmt->bindValue(':checkboxExames', trim($json['checkboxExames']) === '' ? null : trim($json['checkboxExames']));
            $stmt->bindValue(':checkboxUtiAdulto', trim($json['checkboxUtiAdulto']) === '' ? null : trim($json['checkboxUtiAdulto']));
            $stmt->bindValue(':checkboxInternacao', trim($json['checkboxInternacao']) === '' ? null : trim($json['checkboxInternacao']));
            $stmt->bindValue(':checkboxAph', trim($json['checkboxAph']) === '' ? null : trim($json['checkboxAph']));
            $stmt->bindValue(':checkboxTrasfHospitalar', trim($json['checkboxTrasfHospitalar']) === '' ? null : trim($json['checkboxTrasfHospitalar']));
            $stmt->bindValue(':checkboxUtiNeo', trim($json['checkboxUtiNeo']) === '' ? null : trim($json['checkboxUtiNeo']));
            $stmt->bindValue(':checkboxEventos', trim($json['checkboxEventos']) === '' ? null : trim($json['checkboxEventos']));
            $stmt->bindValue(':checkboxParticular', trim($json['checkboxParticular']) === '' ? null : trim($json['checkboxParticular']));
            $stmt->bindValue(':checkboxSivInt', trim($json['checkboxSivInt']) === '' ? null : trim($json['checkboxSivInt']));
            $stmt->bindValue(':checkboxAltaHospitalar', trim($json['checkboxAltaHospitalar']) === '' ? null : trim($json['checkboxAltaHospitalar']));
            $stmt->bindValue(':checkboxCate', trim($json['checkboxCate']) === '' ? null : trim($json['checkboxCate']));
            $stmt->bindValue(':checkboxTomo', trim($json['checkboxTomo']) === '' ? null : trim($json['checkboxTomo']));
            $stmt->bindValue(':checkboxRx', trim($json['checkboxRx']) === '' ? null : trim($json['checkboxRx']));
            $stmt->bindValue(':checkboxGtt', trim($json['checkboxGtt']) === '' ? null : trim($json['checkboxGtt']));
            $stmt->bindValue(':checkboxCprs', trim($json['checkboxCprs']) === '' ? null : trim($json['checkboxCprs']));
            $stmt->bindValue(':checkboxPetScam', trim($json['checkboxPetScam']) === '' ? null : trim($json['checkboxPetScam']));
            $stmt->bindValue(':checkboxTcHip', trim($json['checkboxTcHip']) === '' ? null : trim($json['checkboxTcHip']));
            $stmt->bindValue(':checkboxTqtTqm', trim($json['checkboxTqtTqm']) === '' ? null : trim($json['checkboxTqtTqm']));
            $stmt->bindValue(':checkboxUs', trim($json['checkboxUs']) === '' ? null : trim($json['checkboxUs']));
            $stmt->bindValue(':checkboxRmn', trim($json['checkboxRmn']) === '' ? null : trim($json['checkboxRmn']));
            $stmt->bindValue(':txtOutrosExames', trim($json['txtOutrosExames']) === '' ? null : trim($json['txtOutrosExames']));
            $stmt->bindValue(':numberIdade', trim($json['numberIdade']) === '' ? null : trim($json['numberIdade']));
            $stmt->bindValue(':txtContato', trim($json['txtContato']) === '' ? null : trim($json['txtContato']));
            $stmt->bindValue(':selectSexo', trim($json['selectSexo']) === '' ? null : trim($json['selectSexo']));
            $stmt->bindValue(':selectMembrosSuperior', trim($json['selectMembrosSuperior']) === '' ? null : trim($json['selectMembrosSuperior']));
            $stmt->bindValue(':selectMembrosInferior', trim($json['selectMembrosInferior']) === '' ? null : trim($json['selectMembrosInferior']));
            $stmt->bindValue(':checkboxLucido', trim($json['checkboxLucido']) === '' ? null : trim($json['checkboxLucido']));
            $stmt->bindValue(':checkboxOrientado', trim($json['checkboxOrientado']) === '' ? null : trim($json['checkboxOrientado']));
            $stmt->bindValue(':checkboxConsciente', trim($json['checkboxConsciente']) === '' ? null : trim($json['checkboxConsciente']));
            $stmt->bindValue(':checkboxConfuso', trim($json['checkboxConfuso']) === '' ? null : trim($json['checkboxConfuso']));
            $stmt->bindValue(':checkboxComunicativo', trim($json['checkboxComunicativo']) === '' ? null : trim($json['checkboxComunicativo']));
            $stmt->bindValue(':checkboxNaoVerbaliza', trim($json['checkboxNaoVerbaliza']) === '' ? null : trim($json['checkboxNaoVerbaliza']));
            $stmt->bindValue(':checkboxIsocoricas', trim($json['checkboxIsocoricas']) === '' ? null : trim($json['checkboxIsocoricas']));
            $stmt->bindValue(':checkboxAnisocoricas', trim($json['checkboxAnisocoricas']) === '' ? null : trim($json['checkboxAnisocoricas']));
            $stmt->bindValue(':checkboxMidriatricas', trim($json['checkboxMidriatricas']) === '' ? null : trim($json['checkboxMidriatricas']));
            $stmt->bindValue(':checkboxMiotica', trim($json['checkboxMiotica']) === '' ? null : trim($json['checkboxMiotica']));
            $stmt->bindValue(':checkboxDE', trim($json['checkboxDE']) === '' ? null : trim($json['checkboxDE']));
            $stmt->bindValue(':checkboxMaior', trim($json['checkboxMaior']) === '' ? null : trim($json['checkboxMaior']));
            $stmt->bindValue(':checkboxEupneico', trim($json['checkboxEupneico']) === '' ? null : trim($json['checkboxEupneico']));
            $stmt->bindValue(':checkboxTaquipneico', trim($json['checkboxTaquipneico']) === '' ? null : trim($json['checkboxTaquipneico']));
            $stmt->bindValue(':checkboxBradipneico', trim($json['checkboxBradipneico']) === '' ? null : trim($json['checkboxBradipneico']));
            $stmt->bindValue(':checkboxDispneico', trim($json['checkboxDispneico']) === '' ? null : trim($json['checkboxDispneico']));
            $stmt->bindValue(':checkboxApneia', trim($json['checkboxApneia']) === '' ? null : trim($json['checkboxApneia']));
            $stmt->bindValue(':checkboxNormocardico', trim($json['checkboxNormocardico']) === '' ? null : trim($json['checkboxNormocardico']));
            $stmt->bindValue(':checkboxTarquicardico', trim($json['checkboxTarquicardico']) === '' ? null : trim($json['checkboxTarquicardico']));
            $stmt->bindValue(':checkboxBradicardico', trim($json['checkboxBradicardico']) === '' ? null : trim($json['checkboxBradicardico']));
            $stmt->bindValue(':checkboxFiliforme', trim($json['checkboxFiliforme']) === '' ? null : trim($json['checkboxFiliforme']));
            $stmt->bindValue(':checkboxPlano', trim($json['checkboxPlano']) === '' ? null : trim($json['checkboxPlano']));
            $stmt->bindValue(':checkboxGloboso', trim($json['checkboxGloboso']) === '' ? null : trim($json['checkboxGloboso']));
            $stmt->bindValue(':checkboxEscavado', trim($json['checkboxEscavado']) === '' ? null : trim($json['checkboxEscavado']));
            $stmt->bindValue(':checkboxFlacido', trim($json['checkboxFlacido']) === '' ? null : trim($json['checkboxFlacido']));
            $stmt->bindValue(':checkboxEmAventa', trim($json['checkboxEmAventa']) === '' ? null : trim($json['checkboxEmAventa']));
            $stmt->bindValue(':checkboxGravidico', trim($json['checkboxGravidico']) === '' ? null : trim($json['checkboxGravidico']));
            $stmt->bindValue(':checkboxEspontanea', trim($json['checkboxEspontanea']) === '' ? null : trim($json['checkboxEspontanea']));
            $stmt->bindValue(':checkboxVoz4', trim($json['checkboxVoz4']) === '' ? null : trim($json['checkboxVoz4']));
            $stmt->bindValue(':checkboxDor2', trim($json['checkboxDor2']) === '' ? null : trim($json['checkboxDor2']));
            $stmt->bindValue(':checkboxNenhuma1', trim($json['checkboxNenhuma1']) === '' ? null : trim($json['checkboxNenhuma1']));
            $stmt->bindValue(':checkboxOrientada5', trim($json['checkboxOrientada5']) === '' ? null : trim($json['checkboxOrientada5']));
            $stmt->bindValue(':checkboxConfusa4', trim($json['checkboxConfusa4']) === '' ? null : trim($json['checkboxConfusa4']));
            $stmt->bindValue(':checkboxPalavras3', trim($json['checkboxPalavras3']) === '' ? null : trim($json['checkboxPalavras3']));
            $stmt->bindValue(':checkboxPalavras2', trim($json['checkboxPalavras2']) === '' ? null : trim($json['checkboxPalavras2']));
            $stmt->bindValue(':checkboxObdece6', trim($json['checkboxObdece6']) === '' ? null : trim($json['checkboxObdece6']));
            $stmt->bindValue(':checkboxLocaliza5', trim($json['checkboxLocaliza5']) === '' ? null : trim($json['checkboxLocaliza5']));
            $stmt->bindValue(':checkboxMovimentos4', trim($json['checkboxMovimentos4']) === '' ? null : trim($json['checkboxMovimentos4']));
            $stmt->bindValue(':checkboxFlexao3', trim($json['checkboxFlexao3']) === '' ? null : trim($json['checkboxFlexao3']));
            $stmt->bindValue(':checkboxExtensao2', trim($json['checkboxExtensao2']) === '' ? null : trim($json['checkboxExtensao2']));
            $stmt->bindValue(':checkboxNenhuma', trim($json['checkboxNenhuma']) === '' ? null : trim($json['checkboxNenhuma']));
            $stmt->bindValue(':txtPedagios', trim($json['txtPedagios']) === '' ? null : trim($json['txtPedagios']));
            $stmt->bindValue(':txtOutros', trim($json['txtOutros']) === '' ? null : trim($json['txtOutros']));
            $stmt->bindValue(':selectRefeicaoLanche', trim($json['selectRefeicaoLanche']) === '' ? null : trim($json['selectRefeicaoLanche']));
            $stmt->bindValue(':selectGrandeUm', trim($json['selectGrandeUm']) === '' ? null : trim($json['selectGrandeUm']));
            $stmt->bindValue(':grandeUmBarValeu', trim($json['grandeUmBarValeu']) === '' ? null : trim($json['grandeUmBarValeu']));
            $stmt->bindValue(':grandeUmBarQuantidade', trim($json['grandeUmBarQuantidade']) === '' ? null : trim($json['grandeUmBarQuantidade']));
            $stmt->bindValue(':selectGrandeDois', trim($json['selectGrandeDois']) === '' ? null : trim($json['selectGrandeDois']));
            $stmt->bindValue(':grandeDoisBarValeu', trim($json['grandeDoisBarValeu']) === '' ? null : trim($json['grandeDoisBarValeu']));
            $stmt->bindValue(':grandeDoisBarQuantidade', trim($json['grandeDoisBarQuantidade']) === '' ? null : trim($json['grandeDoisBarQuantidade']));
            $stmt->bindValue(':selectPequenoTres', trim($json['selectPequenoTres']) === '' ? null : trim($json['selectPequenoTres']));
            $stmt->bindValue(':pequenoTresBarValeu', trim($json['pequenoTresBarValeu']) === '' ? null : trim($json['pequenoTresBarValeu']));
            $stmt->bindValue(':pequenoTresBarQuantidade', trim($json['pequenoTresBarQuantidade']) === '' ? null : trim($json['pequenoTresBarQuantidade']));
            $stmt->bindValue(':selectPequenoQuatro', trim($json['selectPequenoQuatro']) === '' ? null : trim($json['selectPequenoQuatro']));
            $stmt->bindValue(':pequenoQuatroBarValeu', trim($json['pequenoQuatroBarValeu']) === '' ? null : trim($json['pequenoQuatroBarValeu']));
            $stmt->bindValue(':pequenoQuatroBarQuantidade', trim($json['pequenoQuatroBarQuantidade']) === '' ? null : trim($json['pequenoQuatroBarQuantidade']));
            $stmt->bindValue(':txtObsMedicaEnfermeiros', trim($json['txtObsMedicaEnfermeiros']) === '' ? null : trim($json['txtObsMedicaEnfermeiros']));
            $stmt->bindValue(':txtMateriaisUtilizados', trim($json['txtMateriaisUtilizados']) === '' ? null : trim($json['txtMateriaisUtilizados']));
            $stmt->bindValue(':queimadura', trim($json['queimadura']) === '' ? null : trim($json['queimadura']));
            $stmt->bindValue(':trauma', trim($json['trauma']) === '' ? null : trim($json['trauma']));
            

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