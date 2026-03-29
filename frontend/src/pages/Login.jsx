import { useState } from "react";
import api from "../services/api";

export default function Login({ onLogin }) {
  const [user, setUser] = useState("");
  const [pass, setPass] = useState("");

  async function handleLogin() {
    const res = await fetch(`${api.baseURL}/login`, {
      method: "POST",
      credentials: "include", // 🔥 ESSENCIAL (cookie da sessão)
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        username: user,
        password: pass
      })
    });

    const data = await res.json();

    if (data.success) {
      onLogin();
    } else {
      alert(data.error);
    }
  }

  return (
    <div>
      <h2>Login Admin</h2>
      <input placeholder="Usuário" onChange={e => setUser(e.target.value)} />
      <input type="password" placeholder="Senha" onChange={e => setPass(e.target.value)} />
      <button onClick={handleLogin}>Entrar</button>
    </div>
  );
}