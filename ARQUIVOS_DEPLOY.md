# 📝 Arquivos Prontos para Upload - BarberFlow

## 📦 Frontend Build (Pronto para Upload)
**Localização local:** `/home/rafael/barberflow/frontend/build/`  
**Destino no servidor:** `private_html/sistemas/barberflow/`

### Arquivos do Build (VERIFICADOS ✅):
```
build/
├── index.html ⭐ (existe)
├── .htaccess ⭐ (existe - para SPA)
├── asset-manifest.json ⭐ (existe)
├── static/ ⭐ (existe)
│   ├── css/
│   │   └── main.c32417dc.css ⭐ (existe)
│   └── js/
│       ├── main.af36d496.js ⭐ (existe)
│       └── main.af36d496.js.LICENSE.txt ⭐ (existe)
└── robots.txt (opcional)
```

**🔍 Como verificar se o build existe:**
```bash
# No terminal, execute:
ls -la /home/rafael/barberflow/frontend/build/

# Deve mostrar algo como:
# -rw-r--r-- 1 user user  387 Apr  2 20:36 index.html
# -rw-r--r-- 1 user user  303 Apr  2 20:36 asset-manifest.json
# drwxr-xr-x 2 user user 4096 Apr  2 20:36 static/
```

**⚠️ Se os arquivos não existirem:**
```bash
# Execute o build novamente:
cd /home/rafael/barberflow/frontend
npm run build
```

## 🔧 Backend (Pronto para Upload)
**Localização local:** `/home/rafael/barberflow/backend/`  
**Destino no servidor:** `private_html/sistemas/barberflow/backend/`

### Arquivos do Backend:
```
backend/
├── index.php ⭐
├── .htaccess ⭐ (ATUALIZADO - LiteSpeed simplificado)
├── .htaccess.minimal ⭐ (NOVO - versão mínima para teste 403)
├── test_db.php ⭐
├── test_php.php ⭐ (NOVO - diagnóstico)
├── config/
│   └── database.php ✅ (credenciais configuradas)
├── controllers/
│   ├── AdminController.php ⭐
│   ├── AppointmentController.php ⭐
│   ├── ServiceController.php ⭐
│   └── TimeSlotController.php ⭐
├── models/
│   ├── Appointment.php ⭐
│   └── Service.php
└── routes/
    └── api.php ⭐
```

## ✅ Alterações Implementadas

- [x] URLs atualizadas para `https://rafaelmaciel.net/sistemas/barberflow`
- [x] CORS configurado para aceitar o domínio
- [x] Banco de dados com credenciais reais
- [x] Build frontend otimizado
- [x] Documentação de deploy (DEPLOY_FTP.md)

## 🚀 Próximo Passo
Fazer upload dos arquivos via FileZilla conforme instruções em `DEPLOY_FTP.md`
