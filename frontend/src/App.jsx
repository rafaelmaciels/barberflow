import { useEffect, useState } from "react";
import Home from "./pages/Home";
import Admin from "./pages/Admin";
import Login from "./pages/Login";
import api from "./services/api";

function App() {
  const [page, setPage] = useState("home");
  const [authenticated, setAuthenticated] = useState(false);

  // 🔥 REMOVIDO BLOQUEIO INICIAL
  useEffect(() => {
    // checkAuth(); ← vamos testar sem isso primeiro
  }, []);

  async function checkAuth() {
    try {
      const res = await fetch(`${api.baseURL}/auth`, {
        credentials: "include"
      });

      if (!res.ok) {
        setAuthenticated(false);
        return;
      }

      const data = await res.json();

      if (data.authenticated) {
        setAuthenticated(true);
      }
    } catch (error) {
      console.log("Erro:", error.message);
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
        authenticated ? (
          <Admin />
        ) : (
          <Login onLogin={() => {
            setAuthenticated(true);
            setPage("admin");
          }} />
        )
      )}
    </>
  );
}

export default App;