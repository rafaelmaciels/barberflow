# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
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
- Correção nos requisitos do `composer.json` (`ext-gd` e `php ^8.4`) para permitir o deploy automático com sucesso no ambiente Nixpacks do Railway.
- Correção de erro de sintaxe Javascript causado por comentários minificados em uma única linha (afetou o agendamento público, calendário de edição e TV).
