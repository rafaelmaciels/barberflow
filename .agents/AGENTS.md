# Regras de Documentação Contínua

Sempre que concluir o desenvolvimento de uma nova funcionalidade (feature) ou melhoria e for enviá-la para o GitHub (commit/push), siga obrigatoriamente os passos abaixo antes de commitar:

1. **Atualizar `README.md`**: Vá até a seção apropriada do README (ex: Roadmap, Funcionalidades) e adicione a nova feature lá, com checkboxes `[x]` ou a descrição exata do que foi feito.
2. **Atualizar `CHANGELOG.md`**: Adicione a versão ou a tag `[Unreleased]` documentando tecnicamente a mudança que foi feita (ex: `Added`, `Fixed`, `Changed`).
3. **Commit e Push**: Só então execute `git add .`, `git commit -m "..."` e `git push origin main`.
