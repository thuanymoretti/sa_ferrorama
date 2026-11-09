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

function atualizarRelogio() {
    if (!campos.atualizacao) return;

    campos.atualizacao.textContent = new Intl.DateTimeFormat("pt-BR", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    }).format(new Date());
}

const camposClima = {
    temperatura: document.getElementById("clima-temperatura"),
    sensacao: document.getElementById("clima-sensacao"),
    condicao: document.getElementById("clima-condicao"),
    umidade: document.getElementById("clima-umidade"),
    vento: document.getElementById("clima-vento"),
    atualizado: document.getElementById("clima-atualizado"),
};

function descreverClima(codigo) {
    if (codigo === 0) return "Céu limpo";
    if ([1, 2].includes(codigo)) return "Parcialmente nublado";
    if (codigo === 3) return "Nublado";
    if ([45, 48].includes(codigo)) return "Neblina";
    if ([51, 53, 55, 56, 57].includes(codigo)) return "Garoa";
    if ([61, 63, 65, 66, 67, 80, 81, 82].includes(codigo)) return "Chuva";
    if ([71, 73, 75, 77, 85, 86].includes(codigo)) return "Neve";
    if ([95, 96, 99].includes(codigo)) return "Trovoada";
    return "Condição indisponível";
}

async function atualizarClima() {
    if (!camposClima.temperatura) return;

    const parametros = new URLSearchParams({
        latitude: "-26.3044",
        longitude: "-48.8487",
        current: "temperature_2m,relative_humidity_2m,apparent_temperature,is_day,weather_code,wind_speed_10m",
        timezone: "America/Sao_Paulo",
    });

    try {
        const resposta = await fetch(`https://api.open-meteo.com/v1/forecast?${parametros}`, { cache: "no-store" });
        if (!resposta.ok) throw new Error("Falha ao consultar o serviço de clima");

        const dados = await resposta.json();
        const atual = dados.current;
        const unidades = dados.current_units;
        if (!atual) throw new Error("Dados meteorológicos indisponíveis");

        const condicao = descreverClima(atual.weather_code);
        const formatar = (valor) => Math.round(valor);
        camposClima.temperatura.textContent = `${formatar(atual.temperature_2m)}${unidades.temperature_2m}`;
        camposClima.sensacao.textContent = `${formatar(atual.apparent_temperature)}${unidades.apparent_temperature}`;
        camposClima.condicao.textContent = condicao;
        camposClima.umidade.textContent = `${atual.relative_humidity_2m}${unidades.relative_humidity_2m}`;
        camposClima.vento.textContent = `${formatar(atual.wind_speed_10m)} ${unidades.wind_speed_10m}`;
        camposClima.atualizado.textContent = `Atualizado às ${new Intl.DateTimeFormat("pt-BR", {
            hour: "2-digit",
            minute: "2-digit",
            timeZone: "America/Sao_Paulo",
        }).format(new Date())}`;
    } catch (erro) {
        camposClima.condicao.textContent = "Não foi possível carregar o clima agora";
        camposClima.atualizado.textContent = "Tente novamente em alguns minutos";
    }
}

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
    } catch (erro) {
        // O relógio continua funcionando mesmo se a consulta do painel falhar.
    }
}

atualizarRelogio();
window.setInterval(atualizarRelogio, 1000);
atualizarDashboard();
window.setInterval(atualizarDashboard, 15000);
atualizarClima();
window.setInterval(atualizarClima, 10 * 60 * 1000);
