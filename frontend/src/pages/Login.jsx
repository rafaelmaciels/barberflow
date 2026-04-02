import { useState } from "react";
import api from "../services/api";

export default function Login({ onLogin }) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  async function handleLogin() {
    try {
      const res = await api.login({ username, password });
      const data = await res.json();

      if (data.success) {
        onLogin();
      } else {
        alert("Credenciais inválidas");
      }
    } catch (error) {
      alert("Erro ao conectar com servidor");
    }
  }

  return (
    <section className="py-5 login-section">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-md-8 col-lg-5">
            <div className="card border-0 shadow modern-card">
              <div className="card-body p-4 p-md-5">
                <h2 className="h4 fw-bold mb-1">Login Admin</h2>
                <p className="text-secondary small mb-4">Acesse o painel da barbearia</p>

                <div className="mb-3">
                  <label className="form-label">Usuário</label>
                  <input
                    placeholder="Usuário"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    className="form-control"
                  />
                </div>

                <div className="mb-4">
                  <label className="form-label">Senha</label>
                  <input
                    type="password"
                    placeholder="Senha"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="form-control"
                  />
                </div>

                <button onClick={handleLogin} className="btn btn-primary w-100">
                  Entrar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
