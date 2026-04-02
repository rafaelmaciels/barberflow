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

## 🐛 Troubleshooting

| Problema | Solução |
|----------|---------|
| **Erro 404 na raiz** | Verifique se `index.html` está em `public_html/sistemas/barberflow/` |
| **API retorna erro de conexão** | Verifique `backend/config/database.php` e credenciais no phpMyAdmin |
| **CORS error no console** | Certifique-se de que o backend está em `backend/index.php` |
| **Arquivo .htaccess não funciona** | Peça ao seu provedor para habilitar `mod_rewrite` |
| **Permissão negada ao enviar** | Ajuste permissões das pastas (CHMOD 755 para pastas, 644 para arquivos) |

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
