
const formularioCEP = document.getElementById("form-cep");
const campoCEP = document.getElementById("cep");
const resultadoCEP = document.getElementById("resultado-cep");

// Formata o CEP enquanto o usuário digita
campoCEP.addEventListener("input", function () {
    let valor = campoCEP.value.replace(/\D/g, "");

    if (valor.length > 5) {
        valor = valor.replace(/^(\d{5})(\d)/, "$1-$2");
    }

    campoCEP.value = valor;
});

// Consulta o endereço na API ViaCEP
formularioCEP.addEventListener("submit", async function (evento) {
    evento.preventDefault();

    const cep = campoCEP.value.replace(/\D/g, "");

    if (cep.length !== 8) {
        resultadoCEP.innerHTML = "<p>Digite um CEP válido com 8 números.</p>";
        return;
    }

    resultadoCEP.innerHTML = "<p>Consultando endereço...</p>";

    try {
        const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

        if (!resposta.ok) {
            throw new Error("Não foi possível consultar o CEP.");
        }

        const dados = await resposta.json();

        if (dados.erro) {
            resultadoCEP.innerHTML = "<p>CEP não encontrado.</p>";
            return;
        }

        resultadoCEP.innerHTML = `
            <p><strong>Logradouro:</strong> ${dados.logradouro || "Não informado"}</p>
            <p><strong>Bairro:</strong> ${dados.bairro || "Não informado"}</p>
            <p><strong>Cidade:</strong> ${dados.localidade} - ${dados.uf}</p>
        `;

    } catch (erro) {
        resultadoCEP.innerHTML = "<p>Erro ao consultar o CEP. Tente novamente.</p>";
    }
});
