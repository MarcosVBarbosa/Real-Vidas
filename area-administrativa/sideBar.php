  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo $urlSystem; ?>" class="brand-link">
      <img src="<?php echo $urlSystem; ?>imagens/favicon.png" alt="RV System" class="brand-image img-circle elevation-3"
        style="opacity: .8">
      <span class="brand-text font-weight-light">RV SYSTEM</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="<?php echo $urlSystem; ?>meuPerfil/" class="d-block"><?php echo $dadosConexao[1]; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview">
            <a href="<?php echo $urlSystem; ?>" class="nav-link <?php echo $pageActive == 'home' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-home"></i>
              <p>
                Home
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="<?php echo $urlSystem; ?>paginaInicial/" class="nav-link <?php echo $pageActive == 'Página Inicial' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Página Inicial
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo $urlSystem; ?>ordemServico/" class="nav-link <?php if ($pageActive == 'Ordem Serviço') {
                                                                                echo 'active';
                                                                              } ?>" disabled>
              <i class="nav-icon fas fa-ambulance"></i>
              <p>
                Ordem de Serviço
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo $urlSystem; ?>faturamento/" class="nav-link <?php if ($pageActive == 'Faturamento') {
                                                                              echo 'active';
                                                                            } ?>">
              <i class="nav-icon fas fa-money-bill-alt"></i>
              <p>
                Faturamento
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo $urlSystem; ?>clientes/" class="nav-link <?php if ($pageActive == 'Clientes') {
                                                                            echo 'active';
                                                                          } ?>">
              <i class="nav-icon fas fa-user-plus"></i>
              <p>
                Clientes
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview <?php if ($pageActive == 'Colaboradores' || $pageActive == 'Estabelecimentos' || $pageActive == 'Serviços' || $pageActive == 'Estoque/Custos' || $pageActive == 'maletas' || $pageActive == 'manutencoes' || $pageActive == 'vtr' || $pageActive == 'Contas'  || $pageActive == 'Controle Contas' || $pageActive == 'Fornecedores' || $pageActive == 'Categorias Fornecedores') {
                                              echo 'menu-open';
                                            } ?>">
            <a href="#" class="nav-link <?php if ($pageActive == 'Colaboradores' || $pageActive == 'Estabelecimentos' || $pageActive == 'Serviços' || $pageActive == 'Estoque/Custos' || $pageActive == 'maletas' || $pageActive == 'manutencoes' || $pageActive == 'vtr' || $pageActive == 'Contas' || $pageActive == 'Controle Contas' || $pageActive == 'Fornecedores' || $pageActive == 'Categorias Fornecedores') {
                                          echo 'active';
                                        } ?>">
              <i class="nav-icon fas fa-info"></i>
              <p>
                Informações Gerais
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>colaboradores/" class="nav-link <?php echo $pageActive == 'Colaboradores' ? 'active' : ''; ?>">
                  <i class="fas fa-briefcase-medical nav-icon"></i>
                  <p>Colaboradores</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>despesas/" class="nav-link <?php echo $pageActive == 'Estoque/Custos' ? 'active' : ''; ?>">
                  <i class="fas fa-minus-circle nav-icon"></i>
                  <p>Estoque/Custos</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>estabelecimentos/" class="nav-link <?php echo $pageActive == 'Estabelecimentos' ? 'active' : ''; ?>">
                  <i class="fas fa-map-marked nav-icon"></i>
                  <p>Estabelecimentos</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>maletas/" class="nav-link <?php echo $pageActive == 'Maletas de Medicação' ? 'active' : ''; ?>">
                  <i class="fas fa-briefcase-medical nav-icon"></i>
                  <p>Maletas de Medicação</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>manutencoes/" class="nav-link <?php echo $pageActive == 'Manutenções' ? 'active' : ''; ?>">
                  <i class="fas fa-tachometer-alt nav-icon"></i>
                  <p>Manutenções</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>servicos/" class="nav-link <?php echo $pageActive == 'Serviços' ? 'active' : ''; ?>">
                  <i class="fas fa-cogs nav-icon"></i>
                  <p>Serviços</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>vtr/" class="nav-link <?php echo $pageActive == 'vtr' ? 'active' : ''; ?>">
                  <i class="fas fa-car nav-icon"></i>
                  <p>VTR</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>contasFixa/" class="nav-link <?php echo $pageActive == 'Contas' ? 'active' : ''; ?>">
                  <i class="fas fa-credit-card nav-icon"></i>
                  <p>Contas</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>controleContas/" class="nav-link <?php echo $pageActive == 'Controle Contas' ? 'active' : ''; ?>">
                  <i class="fas fa-chart-line nav-icon"></i>
                  <p>Controle Contas</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>fornecedores/" class="nav-link <?php echo $pageActive == 'Fornecedores' ? 'active' : ''; ?>">
                  <i class="fas fa-toolbox nav-icon"></i>
                  <p>Fornecedores</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $urlSystem; ?>categoriasFornecedores/" class="nav-link <?php echo $pageActive == 'Categorias Fornecedores' ? 'active' : ''; ?>">
                  <i class="fas fa-rectangle-list nav-icon"></i>
                  <p>Categorias Fornecedores</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="<?php echo $urlSystem; ?>usuarios/" class="nav-link <?php if ($pageActive == 'Usuários') {
                                                                            echo 'active';
                                                                          }
                                                                          ?>">
              <i class="nav-icon fas fa-user-lock"></i>
              <p>
                Usuários
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo $urlSystem; ?>perfil/" class="nav-link <?php if ($pageActive == 'Perfil') {
                                                                          echo 'active';
                                                                        }
                                                                        ?>">
              <i class=" nav-icon  fas fa-unlock"></i>
              <p>
                Perfil
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo $urlSystem; ?>relatorios/" class="nav-link <?php echo $pageActive == 'Relatórios' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Relatórios
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="https://email.realvidas.com.br/" target="_blank" class="nav-link">
              <i class="nav-icon fas fa-envelope"></i>
              <p>
                Webmail
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>