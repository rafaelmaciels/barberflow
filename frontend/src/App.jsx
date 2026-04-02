import { useEffect, useState } from "react";
import Home from "./pages/Home";
import Admin from "./pages/Admin";
import Login from "./pages/Login";
import api from "./services/api";
import "./styles/theme.css";

function App() {
  const [page, setPage] = useState("home");
  const [authenticated, setAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);

  // 🔐 Verifica sessão ao iniciar (sem quebrar app)
  useEffect(() => {
    checkAuth();
  }, []);

  async function checkAuth() {
    try {
      const res = await fetch(`${api.baseURL}/auth`, {
        credentials: "include"
      });

      if (!res.ok) {
        setAuthenticated(false);
      } else {
        const data = await res.json();
        setAuthenticated(!!data.authenticated);
      }
    } catch (error) {
      console.log("Backend offline (ok por enquanto)");
      setAuthenticated(false);
    } finally {
      setLoading(false);
    }
  }

  // 🔥 Loading evita bug de render
  if (loading) {
    return (
      <div className="min-vh-100 d-flex align-items-center justify-content-center app-bg">
        <div className="text-center">
          <div className="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
          <p className="mb-0 text-muted">Carregando...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="app-bg min-vh-100">
      <nav className="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div className="container py-1">
          <span className="navbar-brand fw-semibold">💈 BarberFlow</span>

          <div className="d-flex gap-2 ms-auto">
            <button
              onClick={() => setPage("home")}
              className={`btn btn-sm ${page === "home" ? "btn-primary" : "btn-outline-light"}`}
            >
              Cliente
            </button>
            <button
              onClick={() => setPage("admin")}
              className={`btn btn-sm ${page === "admin" ? "btn-primary" : "btn-outline-light"}`}
            >
              Admin
            </button>
          </div>
        </div>
      </nav>

      <main>
        {/* CLIENTE */}
        {page === "home" && <Home />}

        {/* ADMIN */}
        {page === "admin" && (
          authenticated ? (
            <Admin />
          ) : (
            <Login
              onLogin={() => {
                setAuthenticated(true);
                setPage("admin");
              }}
            />
          )
        )}
      </main>
    </div>
  );
}

export default App;
