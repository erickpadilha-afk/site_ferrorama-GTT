# Sistema de Monitoramento Ferroviario — Golden Train Track (GTT)

## Equipe
- **Bruno Mendes Abreu**
- **Erick Correia Padilha**
- **Lucas Mickus Boeing**
- **Victor Schulz**

## O que o sistema faz
O Golden Train Track (GTT) é um sistema web de gestão e monitoramento ferroviário desenvolvido para centralizar o acompanhamento da frota de trens, leituras de sensores em tempo real e controle de rotas. O sistema resolve o problema da descentralização de dados operacionais, evitando falhas mecânicas não detectadas e atrasos na escala de viagens.

A plataforma divide o acesso em três perfis distintos: o **Administrador**, que monitora indicadores gerais, gerencia trens, sensores, rotas e clientes; o **Maquinista**, que acompanha a escala diária de viagens e emite relatórios operacionais; e o **Cliente**, que consulta informações e horários disponíveis.

## Tecnologias
- **Linguagens e Marcação:** PHP 8+, HTML5, CSS3, JavaScript
- **Banco de Dados:** MySQL
- **Ambiente de Execução:** XAMPP (Apache + MySQL)

## Como instalar
1. Copiar a pasta do projeto para o diretório `htdocs` do XAMPP.
2. Iniciar os serviços **Apache** e **MySQL** no painel de controle do XAMPP.
3. Acessar `http://localhost/phpmyadmin`, ir na aba **Importar**, selecionar o arquivo `banco/database.sql` e executar (o script cria o banco `ferrovias`, todas as tabelas e os dados de exemplo automaticamente).
4. Verificar se o arquivo `config/conexao.php` está configurado com servidor `localhost`, usuário `root`, senha vazia `""` e banco `ferrovias`.
5. Acessar `http://localhost/site_ferrorama-main/pagina-inicial/index.php` no navegador.

## Usuarios de teste
Credenciais pré-cadastradas no script inicial do banco de dados:

| Login | Senha | Perfil |
| :--- | :--- | :--- |
| `admin@gmail.com` | `808080` | administrador |
| `maquinista@gmail.com` | `909090` | maquinista |
| `cliente@gmail.com` | `7070` | cliente |

## Telas do sistema
- `pagina-inicial/index.php` — Tela de autenticação e login com redirecionamento por perfil.
- `admin/admin.php` — Painel principal do administrador com resumo da frota, velocidade média, alertas ativos e sensores críticos.
- `admin/controle.php` — Central de navegação para os módulos administrativos.
- `admin/admin_trem/gestao_trem.php` — Cadastro, listagem, edição e exclusão de trens.
- `admin/admin_sensor/gestao_sensor.php` — Cadastro e gerenciamento de sensores vinculados aos trens.
- `admin/admin_rota/gestao_rota.php` — Planejamento, filtro e gerenciamento de rotas e viagens.
- `admin/admin_cliente/gestao_cliente.php` — Gerenciamento de contas, perfis e permissões de usuários.
- `admin/admin_usuario.php` — Edição de perfil e senha do administrador.
- `notificacoes_alertas.php` — Central de visualização e filtro de alertas gerados pelos sensores.
- `simulador.php` — Simulador de telemetria para geração de leituras normais e críticas.
- `relatorio/gera_relatorio.php` — Filtro de emissão de relatórios operacionais e histórico de auditoria.
- `relatorio/visualiza_relatorio.php` — Exibição tabular dos relatórios com exportação em CSV e impressão.
- `relatorio/viagens.php` — Escala diária de viagens do maquinista com atualização de status (`programada`, `em andamento`, `concluida`).
- `relatorio/maquinista_usuario.php` — Edição de perfil e senha do maquinista.
- `dashboard/dashboard.php` — Tela principal de consulta para o perfil cliente.

## Decisoes tomadas
- **Conexão centralizada (`config/conexao.php`):** Isolar as credenciais do banco em um único arquivo evita repetição de código e facilita a manutenção caso o servidor ou senha mudem.
- **Uso de Prepared Statements (`prepare` e `bind_param`):** Adotado nas consultas e formulários para impedir ataques de SQL Injection, tratando entradas do usuário sempre como texto.
- **Senhas criptografadas com `password_hash()`:** Garante a segurança dos dados armazenados no banco, tornando as senhas ilegíveis mesmo com acesso direto ao phpMyAdmin.
- **Controle de sessão (`$_SESSION`) por perfil:** Protege todas as páginas internas contra acesso não autenticado e restringe rotas administrativas apenas ao papel `administrador`.
- **Script SQL único (`banco/database.sql`):** Consolida a criação do banco, chaves estrangeiras e dados iniciais em um único arquivo para garantir que o avaliador consiga subir o ambiente sem erros de dependência.
- **Auditoria de relatórios (`relatorios_emitidos`):** Registra no banco cada relatório gerado para rastreabilidade e reabertura rápida sem duplicar registros.

## Limitacoes conhecidas
- **Sensores físicos (IoT):** O sistema não recebe dados de hardware real; a alimentação de telemetria é simulada via banco de dados e pelo módulo `simulador.php`.
- **Recuperação de senha por e-mail:** Não foi implementado envio automático de e-mail para redefinição de senha; alterações são feitas apenas pelo próprio usuário logado ou pelo administrador.
- **Gráficos dinâmicos avançados:** Os indicadores da tela de análise são exibidos em cards numéricos e listas estruturadas, sem bibliotecas externas de gráficos interativos.

## Uso de inteligencia artificial
| Ferramenta | Etapa / Onde foi usada | O que foi gerado / ajustado |
| :--- | :--- | :--- |
| Gemini | Padronização visual (CSS) e refatoração PHP | Ajuste do tema escuro (*Golden Dark*), correção de rotas de navegação e criação do histórico de relatórios. |
| Gemini | Consolidação do Banco de Dados | Unificação dos scripts `.sql` em um único arquivo (`database.sql`) com dados de exemplo. |
| Gemini | Documentação | Estruturação inicial do `README.md` conforme o modelo da Situação de Aprendizagem. |