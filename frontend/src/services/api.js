// Importa a biblioteca fetch nativa do navegador (ou axios se preferir instalar)
// Aqui criamos uma função auxiliar para padronizar as requisições
const BASE_URL = "http://localhost:8000";

const api = {
    baseURL: BASE_URL,
    // Função assíncrona para fazer requisições POST
    post: async (endpoint, data) => {
        // Usa o fetch para chamar a URL completa (baseURL + endpoint específico)
        const response = await fetch(`${api.baseURL}${endpoint}`, {
            // Define o método da requisição como POST
            method: 'POST',
            // Define os cabeçalhos avisando que estamos enviando JSON
            headers: {
                'Content-Type': 'application/json',
            },
            // Converte o objeto JavaScript (data) para uma string JSON
            body: JSON.stringify(data),
        });
        // Converte a resposta do servidor de volta para um objeto JavaScript
        return await response.json();
    }
};

// Exporta o objeto api para ser usado em outros componentes
export default api;