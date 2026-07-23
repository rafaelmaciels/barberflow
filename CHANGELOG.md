# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- **Cancelamento Automático (Tolerância):** Nova regra no Model de Agendamentos que converte automaticamente para "cancelado" clientes aguardando há mais de 1 hora do horário marcado. A limpeza é disparada silenciosamente através da atualização da TV ou acessos ao Painel.
- **Pipeline CI/CD (GitHub Actions):** Deploy automatizado (`deploy-demo.yml`) configurado para atualizar a instância de Demonstração na AWS usando AWS Systems Manager (SSM) a cada push na branch principal.
- **Serviço de Cortesia (Fidelidade):** Novo atributo `is_admin_only` (Exclusivo para Administração) em Serviços. Permite criar serviços invisíveis para clientes no site e para funcionários no painel. Somente Administradores podem aplicar esses serviços nos agendamentos.
- **Deploy AWS EC2:** Adição de scripts de provisionamento de infraestrutura (`user-data.sh`) para AWS (LEMP stack) e `.env.producao` atualizado para uso do novo servidor e Certificado SSL Let's Encrypt.
- **Módulo de Gestão de Usuários (RBAC):** Criação de níveis de acesso diferenciados (Administrador vs Funcionário/Barbeiro).
- **Vínculo de Usuário e Barbeiro:** Ao criar um usuário do tipo "Funcionário", ele é vinculado a um barbeiro específico, restringindo o acesso dele à sua própria agenda e impedindo visualização da agenda de outros.
- **Proteção contra Exclusão:** Remoção do botão e do acesso para exclusão de agendamentos e transações financeiras para usuários de nível "Funcionário" (somente Administrador pode excluir).
- Refatoração visual global focada em UI/UX Premium (Design mais limpo e profissional, utilizando o tema Azul).
- Sistema nativo de Modo Escuro (Dark Mode) acionado por botões ☀️ / 🌙 e persistido via `localStorage`.
- Limpeza global de classes Bootstrap hardcoded (migração para o sistema de variáveis e esquema de cores automático).
- Módulo Instalador SaaS (Wizard web) contendo setup de banco de dados, SMTP, empresa e administrador inicial.
- Módulo de Relatórios completo com filtros dinâmicos e exportação automática nativa para PDF e Excel (Agendamentos, Financeiro e Desempenho).
- Painel "Fila Ao Vivo" na página pública de agendamento (gatilho de escassez/prova social).
- Módulo de Bloqueios de Agenda (Férias/Folgas) com validação dinâmica em tempo real no agendamento público e backend.
- Integração de player do YouTube na tela da TV da Recepção (`/fila`), com player sem controles e em loop automático.
- Painel no Dashboard para gerenciar e salvar a URL do vídeo do YouTube.
- Layout aprimorado da Tela da TV, priorizando espaço lateral para exibição de vídeo e lista compacta (máximo de 3) de clientes aguardando.
- Arquitetura inicial baseada no padrão SaaS.
- Configurações do Git e arquivos de documentação (README, CHANGELOG, etc).

### Fixed
- **Upload de Imagens:** Atualização no `SettingsController` e `BarberService` para explicitamente utilizar o disco `public` padrão de uploads no Laravel 11, corrigindo o problema de imagens que não carregavam.
- **Exclusão de Barbeiros:** Correção no botão de excluir da página de Barbeiros para utilizar o evento `onsubmit` padrão, resolvendo a falha no funcionamento que existia devido à lógica do SweetAlert.
- **Filtros de Relatório Financeiro:** Correção no backend e front-end para usar os enumeradores corretos do banco de dados (`entrada`/`saida`) no lugar de `receita`/`despesa`, resolvendo o problema de resultados em branco no filtro.
- **Ordenação de Relatórios e Financeiro:** Forçada a ordenação combinada por `data`, `hora` e `id` (descendente) no backend (e ajuste na renderização do DataTables) para garantir que os lançamentos mais recentes fiquem de fato no topo da lista, superando a ambiguidade quando existem várias transações na mesma data.
- Alteração do builder no `railway.json` de NIXPACKS para RAILPACK para forçar o uso do Node.js 22 e resolver incompatibilidades com o Vite 8.
- Adição do arquivo `railway.json` para executar as migrations automaticamente no processo de Release (contorno para a limitação de terminal no plano gratuito do Railway).
- Correção nos requisitos do `composer.json` (`ext-gd`, `ext-zip` e `php ^8.4`) para permitir o deploy automático com sucesso no ambiente Nixpacks do Railway.
- Correção de erro de sintaxe Javascript causado por comentários minificados em uma única linha (afetou o agendamento público, calendário de edição e TV).
