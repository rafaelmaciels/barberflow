# 🚀 BarberFlow - Guia de Deploy via FTP

**Data:** 2 de abril de 2026  
**Servidor:** https://rafaelmaciel.net/sistemas/barberflow  
**Credenciais do Banco:** ✅ Já configuradas  

---

## 📋 Checklist de Deploy

### ✅ Fase 1: Preparação (CONCLUÍDO)
- [x] Database configurado com credenciais corretas
- [x] Frontend build otimizado gerado
- [x] URLs ajustadas para produção
- [x] CORS configurado

### 📦 Fase 2: Upload via FTP (PRÓXIMO PASSO)

#### Servidor FTP
- **Host:** (fornecido pelo seu provedor)
- **Usuário:** (seu usuário FTP)
- **Senha:** (sua senha FTP)
- **Porta:** 21 (padrão)

#### Estrutura no Servidor
```
public_html/
└── sistemas/
    └── barberflow/
        ├── index.html
        ├── favicon.ico (opcional)
        ├── static/
        │   ├── css/
        │   │   └── main.c32417dc.css
        │   └── js/
        │       ├── main.af36d496.js
        │       └── main.af36d496.js.LICENSE.txt
        ├── asset-manifest.json
        └── backend/
            ├── index.php
            ├── .htaccess
            ├── test_db.php
            ├── config/
            │   └── database.php ✅
            ├── controllers/
            │   ├── AdminController.php
            │   ├── AppointmentController.php
            │   ├── ServiceController.php
            │   └── TimeSlotController.php
            ├── models/
            │   ├── Appointment.php
            │   └── Service.php
            └── routes/
                └── api.php
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
1. No painel direito (servidor), navegue até: `public_html/sistemas/barberflow`
2. Se a pasta não existir, crie-a:
   - Clique direito → Criar diretório
   - Nome: `barberflow`

### Passo 3: Upload dos arquivos Frontend (Build)
**Origem (local):** `/home/rafael/barberflow/frontend/build/`  
**Destino (servidor):** `public_html/sistemas/barberflow/`

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
**Destino (servidor):** `public_html/sistemas/barberflow/backend/`

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

#### Possíveis Causas:
1. **Permissões incorretas** nos arquivos/pastas
2. **.htaccess** muito restritivo
3. **Servidor não suporta** certas configurações
4. **Pasta backend** bloqueada

#### ✅ Soluções (faça na ordem):

##### 1. Ajustar Permissões via FTP
**No FileZilla:**
1. Clique direito na pasta `barberflow` → **Permissões de arquivo**
2. Marque:
   - **Proprietário:** Leitura, Escrita, Execução
   - **Grupo:** Leitura, Execução
   - **Público:** Leitura, Execução
3. **Valor numérico:** 755
4. Aplique recursivamente

**Para arquivos PHP:**
- Permissões: 644 (Leitura + Escrita para proprietário, Leitura para outros)

##### 2. Testar PHP Básico
1. Acesse: `https://rafaelmaciel.net/sistemas/barberflow/backend/test_php.php`
2. Deve retornar JSON com informações do servidor
3. Se funcionar → problema é no index.php ou rotas
4. Se não funcionar → problema de permissões ou PHP desabilitado

##### 3. Verificar .htaccess
Se o teste PHP não funcionar:
1. **Renomeie** `.htaccess` para `.htaccess.bak` via FTP
2. Teste novamente o `test_php.php`
3. Se funcionar → problema no .htaccess
4. Se não → problema de permissões ou configuração do servidor

##### 4. Testar Index.php Direto
1. Acesse: `https://rafaelmaciel.net/sistemas/barberflow/backend/index.php`
2. Deve retornar erro JSON (não 403)
3. Se retornar 403 → arquivo index.php com permissões incorretas

##### 5. Verificar Estrutura de Pastas
Certifique-se de que:
```
public_html/
└── sistemas/
    └── barberflow/        ← Permissões 755
        ├── index.html     ← Permissões 644
        ├── backend/       ← Permissões 755
        │   ├── index.php  ← Permissões 644
        │   └── .htaccess  ← Permissões 644
        └── static/        ← Permissões 755
```

##### 6. Contatar Suporte (se necessário)
Se nada funcionar, contate o suporte do seu provedor com:
- "Erro 403 ao acessar arquivos PHP em hospedagem compartilhada"
- Mencione que outros sites funcionam normalmente

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
