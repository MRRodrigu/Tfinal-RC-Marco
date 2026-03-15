# Tfinal-RC-Marco

# Projeto GeoDados - Mapa Interativo

## Descrição do Projeto
Este projeto consiste numa aplicação web que disponibiliza um mapa interativo utilizando **Leaflet**. A plataforma permite aos utilizadores (normais e administradores) visualizar locais no mapa com clustering, adicionar novos locais e pesquisar por localização ou categoria.
O design e interface do projeto foram refatorados com base no layout (PCoded template/EMFMP), incorporando um menu lateral dinâmico (sidebar) e um cabeçalho fixo (navbar) para uma experiência visual e de navegação intuitiva.

## Funcionalidades Implementadas
- **Autenticação e Autorização**: Sistema de login (`login.php`) com suporte a sessões, redirecionamento e permissões divididas entre Administradores e Utilizadores Normais.
- **Gestão de Locais (CRUD)**: Possibilidade de visualizar, adicionar e gerir pontos turísticos do mundo no mapa interativo.
- **Mapa Interativo com Clustering**: Integração do Leaflet.js acompanhado de Leaflet.markercluster, permitindo agrupar visualmente múltiplos pontos.
- **Partilha por Email**: Funcionalidade acionada através de um botão no detalhe de cada ponto, enviando as coordenadas e informações sobre o local.
- **Interface Baseada num Menu Lateral (EMFMP)**: Utilização de um template profissional focado na usabilidade de painéis de administração.

## Tecnologias Utilizadas
- **Frontend**:
  - HTML5, CSS3, Vanilla JavaScript
  - Bootstrap (para estrutura da grelha de elementos do template) e componentes pré-feitos do PCoded Template.
  - [Leaflet.js](https://leafletjs.com/) e Plugin MarkerCluster.
- **Backend / Intermediação**:
  - PHP utilizando PDO (PHP Data Objects) para operações seguras de base de dados.
- **Base de Dados**:
  - MySQL / MariaDB 
  
## Instruções para Executar o Projeto Localmente

1. **Requisitos Prévios**: Deverá ter um ambiente de simulação de servidor (MAMP, XAMPP, Laragon, etc.) ativo que suporte o MySQL e o PHP.
2. **Importação da Base de Dados**: 
   - Aceda a uma ferramenta de gestão de base de dados (ex: phpMyAdmin).
   - Crie a base de dados em branco e importe os ficheiros SQL (`Script_SQL...`) fornecidos em conjunto com a aplicação principal.
3. **Configuração da Ligação**:
   - Analise o documento `db.php` e assegure-se de que os dados (Host, DB Name, User e Password) refletem o estado da base de dados local.
4. **Execução**:
   - Mova os ficheiros desta diretoria para a pasta de acesso público web (ex: `htdocs` no XAMPP).
   - Aceda pelo seu navegador (ex: `http://localhost/sua-pasta/exemplo_map/login.php`).
   - Autentique-se utilizando as credenciais listadas na base de dados para aceder à aplicação.
