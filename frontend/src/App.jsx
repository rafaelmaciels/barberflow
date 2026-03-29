import { useEffect, useState } from "react";
import Home from "./pages/Home";
import Admin from "./pages/Admin";
import Login from "./pages/Login";
import api from "./services/api";

function App() {
  const [page, setPage] = useState("home");
  const [authenticated, setAuthenticated] = useState(false);

  // Verifica sessão ao abrir app
  useEffect(() => {
    checkAuth();
  }, []);

  async function checkAuth() {
    try {
      const res = await fetch(`${api.baseURL}/auth`, {
        credentials: "include"
      });

      const data = await res.json();

      if (data.authenticated) {
        setAuthenticated(true);
      }
    } catch {
      setAuthenticated(false);
    }
  }

  return (
    <>
      <div style={{ padding: 10 }}>
        <button onClick={() => setPage("home")}>Cliente</button>
        <button onClick={() => setPage("admin")}>Admin</button>
      </div>

      {page === "home" && <Home />}

      {page === "admin" && (
        authenticated
          ? <Admin />
          : <Login onLogin={() => {
              setAuthenticated(true);
              setPage("admin");
            }} />
      )}
    </>
  );
}

export default App;