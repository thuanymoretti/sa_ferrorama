const campos = {
    sensores: document.getElementById("total-sensores"),
    ativos: document.getElementById("trens-ativos"),
    manutencao: document.getElementById("trens-manutencao"),
    disponibilidade: document.getElementById("disponibilidade"),
    barra: document.getElementById("barra-disponibilidade"),
    atualizacao: document.getElementById("ultima-atualizacao"),
    usuarios: document.getElementById("total-usuarios"),
    administradores: document.getElementById("total-administradores"),
    funcionarios: document.getElementById("total-funcionarios"),
};

function formatarDisponibilidade(valor) {
    return `${Number(valor).toLocaleString("pt-BR", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    })}%`;
}

async function atualizarDashboard() {
    try {
        const resposta = await fetch("dashboard_status.php", { cache: "no-store" });
        if (resposta.status === 401) {
            window.location.href = "login.php";
            return;
        }
        if (!resposta.ok) throw new Error("Falha ao consultar o painel");

        const dados = await resposta.json();
        campos.sensores.textContent = dados.sensores;
        campos.ativos.textContent = dados.trens_ativos;
        campos.manutencao.textContent = dados.trens_manutencao;
        campos.disponibilidade.textContent = formatarDisponibilidade(dados.disponibilidade);
        campos.barra.style.width = `${dados.disponibilidade}%`;
        campos.usuarios.textContent = dados.usuarios;
        campos.administradores.textContent = dados.administradores;
        campos.funcionarios.textContent = dados.funcionarios;
        campos.atualizacao.textContent = `Atualizado às ${new Date().toLocaleTimeString("pt-BR", { hour: "2-digit", minute: "2-digit" })}`;
    } catch (erro) {
        campos.atualizacao.textContent = "Atualização indisponível";
    }
}

atualizarDashboard();
window.setInterval(atualizarDashboard, 15000);
