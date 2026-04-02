# 🚀 BarberFlow - Guia de Deploy via FTP

**Servidor:** https://rafaelmaciel.net/sistemas/barberflow  
**Credenciais do Banco:** ✅ Já configuradas  
**Servidor Web:** LiteSpeed ✅  
**Estrutura:** private_html ✅  

---

## 📋 Checklist de Deploy

### ✅ Fase 1: Preparação (CONCLUÍDO)
- [x] Database configurado com credenciais corretas
- [x] Frontend build otimizado gerado
- [x] URLs ajustadas para produção
- [x] CORS configurado
- [x] **Compatibilidade LiteSpeed implementada**

### 🔧 Configurações Específicas para LiteSpeed

**O servidor usa LiteSpeed (não Apache), então:**
- ✅ .htaccess compatível com LiteSpeed
- ✅ Headers CORS configurados no .htaccess
- ✅ Estrutura private_html correta
- ✅ Rewrite rules otimizadas

### 📦 Fase 2: Upload via FTP (PRÓXIMO PASSO)

#### ✅ Verificação Pré-Upload
**Antes de fazer o upload, confirme que tem todos os arquivos:**

```bash
# Verificar frontend build
ls -la /home/rafael/barberflow/frontend/build/
# ✅ Deve conter: index.html, .htaccess, asset-manifest.json, static/

# Verificar backend
ls -la /home/rafael/barberflow/backend/
# ✅ Deve conter: index.php, .htaccess, config/, controllers/, etc.
```

**Se faltar algum arquivo:**
```bash
# Refazer o build do frontend
cd /home/rafael/barberflow/frontend && npm run build

# Verificar novamente
ls -la /home/rafael/barberflow/frontend/build/
```

#### Servidor FTP
- **Host:** (fornecido pelo seu provedor)
- **Usuário:** (seu usuário FTP)
- **Senha:** (sua senha FTP)
- **Porta:** 21 (padrão)

#### Estrutura no Servidor (ATUALIZADO - private_html)
```
private_html/
└── sistemas/
    └── barberflow/
        ├── index.html ⭐
        ├── .htaccess ⭐ (para SPA)
        ├── asset-manifest.json ⭐
        ├── static/ ⭐
        │   ├── css/
        │   │   └── main.*.css ⭐
        │   └── js/
        │       ├── main.*.js ⭐
        │       └── main.*.js.LICENSE.txt
        └── backend/
            ├── index.php ⭐
            ├── .htaccess ⭐
            ├── test_db.php ⭐
            ├── test_php.php ⭐
            ├── config/
            │   └── database.php ✅
            ├── controllers/ ⭐
            ├── models/ ⭐
            └── routes/ ⭐
```

**📍 Localização dos arquivos no seu PC:**
- Frontend build: `/home/rafael/barberflow/frontend/build/`
- Backend: `/home/rafael/barberflow/backend/`

**✅ Verificar se o build existe:**
```bash
ls -la /home/rafael/barberflow/frontend/build/
# Deve mostrar: index.html, static/, asset-manifest.json, .htaccess
```

---

## 🔧 Passos de Upload via FileZilla

### Passo 1: Conectar ao FTP
1. Abra o FileZilla
2. Menu → Arquivo → Gerenciador de Sites
3. Clique em "Novo Site"
4. Configure:
   - **Protocolo:** FTP
   - **Host:** [Seu host FTP]
   - **Usuário:** [Seu usuário FTP]
   - **Senha:** [Sua senha FTP]
   - **Porta:** 21
5. Conecte

### Passo 2: Navegar até a pasta de destino
1. No painel direito (servidor), navegue até: `private_html/sistemas/barberflow`
2. Se a pasta não existir, crie-a:
   - Clique direito → Criar diretório
   - Nome: `barberflow`

### Passo 3: Upload dos arquivos Frontend (Build)
**Origem (local):** `/home/rafael/barberflow/frontend/build/`  
**Destino (servidor):** `private_html/sistemas/barberflow/`

**Arquivos a transferir:**
- `index.html`
- `favicon.ico` (se houver)
- `static/` (pasta inteira com CSS e JS)
- `asset-manifest.json`

**Instruções:**
1. No painel esquerdo, abra: `/home/rafael/barberflow/frontend/build/`
2. Selecione TODOS os arquivos e pastas
3. Clique direito → Enviar
4. Verifique se aparecem no painel direito

### Passo 4: Upload da pasta Backend
**Origem (local):** `/home/rafael/barberflow/backend/`  
**Destino (servidor):** `private_html/sistemas/barberflow/backend/`

**Instruções:**
1. No painel direito, crie uma pasta `backend` (se não existir)
   - Clique direito → Criar diretório
   - Nome: `backend`

2. Entre na pasta `backend` (duplo clique)

3. No painel esquerdo, abra `/home/rafael/barberflow/backend/`

4. Selecione:
   - `index.php`
   - `.htaccess`
   - `test_db.php`
   - Pastas: `config/`, `controllers/`, `models/`, `routes/`

5. Clique direito → Enviar

6. Aguarde a conclusão (verifique a aba "Fila de transferência")

---

## ✅ Verificação de Upload

### Teste 1: Frontend
- Acesse: `https://rafaelmaciel.net/sistemas/barberflow/`
- Você deve ver a página de agendamento
- Teste agendar um horário

### Teste 2: Backend
- Acesse: `https://rafaelmaciel.net/sistemas/barberflow/backend/index.php/services`
- Deve retornar um JSON com os serviços

### Teste 3: Conexão com Banco
1. Via FTP, abra `backend/test_db.php`
2. Ou acesse: `https://rafaelmaciel.net/sistemas/barberflow/backend/test_db.php`
3. Deve exibir: "Conectado com sucesso!"

### Teste 4: Admin
- Acesse: `https://rafaelmaciel.net/sistemas/barberflow/`
- Clique em "Admin" (se disponível no menu)
- Login: `admin` / `admin123`

---

## 🐛 Troubleshooting - Erro 403 Forbidden

### ❌ Problema: "403 Forbidden - Access to this resource on the server is denied!"

#### ✅ SOLUÇÕES DEFINITIVAS (faça nesta ordem):

##### **PASSO 1: Testar SEM .htaccess**
1. **Via FTP:** Renomeie `.htaccess` para `.htaccess.bak` na pasta `backend/`
2. **Teste:** Acesse `https://rafaelmaciel.net/sistemas/barberflow/backend/test_php.php`
3. **Resultado esperado:** Deve funcionar e mostrar JSON
4. **Se funcionar:** O problema é o `.htaccess` - use a versão minimal

##### **PASSO 2: Usar .htaccess Minimal**
1. **Via FTP:** Delete o `.htaccess` atual
2. **Upload:** Envie o arquivo `.htaccess.minimal` como `.htaccess`
3. **Teste novamente**

##### **PASSO 3: Verificar Permissões (CRÍTICO)**
**No FileZilla - para TODAS as pastas:**
1. Clique direito na pasta → **Permissões de arquivo**
2. **Configuração correta:**
   - **Proprietário:** ✅ Leitura, ✅ Escrita, ✅ Execução
   - **Grupo:** ✅ Leitura, ❌ Escrita, ✅ Execução
   - **Público:** ✅ Leitura, ❌ Escrita, ✅ Execução
3. **Valor numérico:** `755`
4. ✅ **Aplicar recursivamente** (importante!)

**Para arquivos PHP (.php):**
- **Permissões:** `644`
- **Proprietário:** ✅ Leitura, ✅ Escrita
- **Grupo/Público:** ✅ Leitura

##### **PASSO 4: Teste Passo-a-Passo**
1. **Teste PHP básico:** `https://rafaelmaciel.net/sistemas/barberflow/backend/test_php.php`
2. **Teste index.php:** `https://rafaelmaciel.net/sistemas/barberflow/backend/index.php`
3. **Teste API:** `https://rafaelmaciel.net/sistemas/barberflow/backend/index.php/services`

##### **PASSO 5: Contatar Suporte (se necessário)**
Se nada funcionar, contate o suporte com:
```
"Erro 403 Forbidden em arquivos PHP - Hospedagem LiteSpeed
- PHP 8.3.21 funcionando
- Estrutura: private_html/sistemas/barberflow/
- Permissões: pastas 755, arquivos 644
- Mesmo sem .htaccess dá erro
```

---

## 🔧 Arquivos Atualizados para Compatibilidade

Os arquivos foram atualizados para melhor compatibilidade com hospedagens compartilhadas:

- **.htaccess do backend:** Mais permissivo e com fallbacks
- **.htaccess do frontend:** Configurado para SPA
- **test_php.php:** Arquivo de diagnóstico

**Faça upload novamente dos arquivos atualizados!**

---

## 📞 Próximas Etapas

1. ✅ Database configurado
2. ⏳ **Upload via FTP (SUA RESPONSABILIDADE)**
3. ⏳ Testes de funcionamento
4. ⏳ Monitoramento em produção

---

## 📝 Notas Importantes

- **Banco de dados:** Já criado com usuário `admin` / `admin123`
- **Credenciais MySQL:** Configuradas em `backend/config/database.php`
- **Horários de funcionamento:** 08:00-12:00 e 13:30-18:00
- **Zona horária:** America/Sao_Paulo

---

**Status:** ✅ Pronto para Deploy
