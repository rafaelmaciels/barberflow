const mysql = require('mysql2');
const host = process.env.MYSQLHOST;
const port = process.env.MYSQLPORT;
const user = process.env.MYSQLUSER;
const pass = process.env.MYSQLPASSWORD;

if (!host || !user || !pass) {
    console.log("Variáveis nativas do Railway não encontradas. Ignorando fix.");
    process.exit(0);
}

const connection = mysql.createConnection({
  host: host,
  port: port,
  user: user,
  password: pass
});

const query = `ALTER USER '${user}'@'%' IDENTIFIED WITH mysql_native_password BY '${pass}'`;

connection.query(query, function(err, results) {
  if (err) {
    console.error("Erro ao alterar senha:", err.message);
    process.exit(0); 
  }
  console.log("Banco de dados alterado com sucesso para mysql_native_password!");
  process.exit(0);
});
