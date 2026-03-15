/**
 * @file mapa.js
 * @brief Script para mapa com pesquisa, filtros, e CRUD de locais com validação de utilizadores.
 */

let mapInstance = null;
let markerCluster = null;
let currentMarkers = [];
let categoriasMap = {};

document.addEventListener("DOMContentLoaded", init);

function init() {
    mapInstance = criarMapa();
    markerCluster = L.markerClusterGroup();
    mapInstance.addLayer(markerCluster);

    carregarCategoriasInputs().then(() => {
        carregarLocais();
    });

    configurarFiltros();
    configurarFormulario();
}

/**
 * Cria o mapa Leaflet
 */
function criarMapa() {
    const osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { attribution: "&copy; OpenStreetMap" });
    const satellite = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}");

    const map = L.map("map", {
        center: [39.5, -8],
        zoom: 6,
        layers: [osm],
        zoomControl: false // Oculto para UI mais limpa, ou podemos adicionar em outra posição
    });

    L.control.zoom({ position: "bottomright" }).addTo(map);

    L.control.layers({
        "Ruas": osm,
        "Satélite": satellite
    }, null, { position: "bottomleft" }).addTo(map);

    return map;
}

/**
 * Carrega Categorias para popular os <select> de filtro e inserção
 */
function carregarCategoriasInputs() {
    return fetch('api_categorias.php')
        .then(r => r.json())
        .then(cats => {
            const filterCat = document.getElementById('filter-category');
            const formCat = document.getElementById('categoria');
            
            // Limpar formCat exceto o placeholder
            formCat.innerHTML = '<option value="" disabled selected>Selecione uma categoria...</option>';

            cats.forEach(c => {
                categoriasMap[c.id] = c.nome; // Para uso futuro se necessário
                
                // Opções pro Filtro
                const optFilter = document.createElement('option');
                optFilter.value = c.id;
                optFilter.textContent = c.nome;
                filterCat.appendChild(optFilter);

                // Opções pro Formulário
                const optForm = document.createElement('option');
                optForm.value = c.nome; // O guardar_local.php espera o nome da categoria
                optForm.textContent = c.nome;
                formCat.appendChild(optForm);
            });
        }).catch(err => console.error("Erro a carregar categorias:", err));
}

/**
 * Filtros de Pesquisa (Cidade e Select Categoria)
 */
function configurarFiltros() {
    const searchCountry = document.getElementById('search-country');
    const searchCity = document.getElementById('search-city');
    const filterCat = document.getElementById('filter-category');

    const triggerSearch = () => {
        carregarLocais(searchCity.value, searchCountry.value, filterCat.value);
    };

    searchCountry.addEventListener('keyup', (e) => {
        if(e.key === 'Enter') triggerSearch();
    });
    
    searchCity.addEventListener('keyup', (e) => {
        if(e.key === 'Enter') triggerSearch();
    });
    
    // Auto-search after 700ms typing pause
    let typingTimer;
    searchCountry.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(triggerSearch, 700);
    });
    
    searchCity.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(triggerSearch, 700);
    });

    filterCat.addEventListener('change', triggerSearch);
}

/**
 * Carrega Locais da BD baseados em filtros
 */
function carregarLocais(cidade = '', pais = '', categoriaId = '') {
    const url = `api_locais.php?cidade=${encodeURIComponent(cidade)}&pais=${encodeURIComponent(pais)}&categoria_id=${encodeURIComponent(categoriaId)}`;
    
    fetch(url)
        .then(r => r.json())
        .then(locais => {
            markerCluster.clearLayers();
            currentMarkers = [];

            locais.forEach(l => {
                const marker = L.marker([l.latitude, l.longitude], {
                    icon: L.divIcon({
                        className: "",
                        html: `
                            <div class="leaflet-marker-drop" style="position:relative;width:30px;height:42px;">
                                <svg width="30" height="42" viewBox="0 0 30 42">
                                    <path d="M15 0 C23 0 30 7 30 15 C30 23 15 42 15 42 C15 42 0 23 0 15 C0 7 7 0 15 0 Z" fill="${l.cor}"/>
                                </svg>
                                <div style="position:absolute;top:6px;left:6px;width:18px;height:18px;background:white;border-radius:50%;text-align:center;line-height:18px;font-size:12px;font-weight:bold;color:#333;">
                                    ${l.letras}
                                </div>
                            </div>`,
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                });

                // Tratar fotos
                let fotosHtml = '';
                if (l.fotos) {
                    const fotosArr = l.fotos.split(',');
                    // Mostrar apenas a primeira por simplicidade, ou todas se o popup for grande
                    fotosHtml = `<img src="uploads/${fotosArr[0]}" style="width:100%; height:140px; object-fit:cover; border-radius:8px; margin-top:8px;">`;
                }

                // Construção do Popup
                const content = document.createElement('div');
                content.innerHTML = `
                    <div style="font-family:'Outfit'; min-width: 200px;">
                        ${fotosHtml}
                        <h3 style="margin:8px 0 0 0; color:${l.cor};">${l.nome}</h3>
                        <p style="margin:4px 0;font-size:0.9em;color:#94a3b8;">${l.categoria} - ${l.cidade}, ${l.pais}</p>
                        ${l.telefone ? `<div style="font-size:0.85em; margin-bottom: 2px;"><i class="fa-solid fa-phone"></i> ${l.telefone}</div>` : ''}
                        ${l.descricao ? `<p style="font-size:0.85em; margin-top:8px;">${l.descricao}</p>` : ''}
                    </div>
                `;

                // Verificar Permissões para mostrar botões Editar e Apagar
                // loggedUserId e isAdmin definidos na index.php script inline
                if (isAdmin || Number(l.criado_por) === Number(loggedUserId)) {
                    const btnActions = document.createElement('div');
                    btnActions.style.marginTop = '12px';
                    btnActions.style.display = 'flex';
                    btnActions.style.gap = '8px';
                    
                    btnActions.innerHTML = `
                        <button class="btn-edit" style="background:#4F46E5; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:0.8em; cursor:pointer;"><i class="fa-solid fa-pen"></i> Editar</button>
                        <button class="btn-delete" style="background:#ef4444; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:0.8em; cursor:pointer;"><i class="fa-solid fa-trash"></i> Apagar</button>
                        <button class="btn-share" style="background:#10b981; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:0.8em; cursor:pointer;"><i class="fa-solid fa-envelope"></i> Partilhar</button>
                    `;
                    
                    // Ligar Eventos
                    const actContainer = btnActions; // if isAdmin it is populated
                    actContainer.querySelector('.btn-edit').onclick = () => abrirEdicao(l);
                    actContainer.querySelector('.btn-delete').onclick = () => apagarLocal(l.id);
                    actContainer.querySelector('.btn-share').onclick = () => partilharEmail(l.id);
                    content.appendChild(actContainer);
                } else {
                    // Even if not admin, show share button
                    const btnActions = document.createElement('div');
                    btnActions.style.marginTop = '12px';
                    btnActions.innerHTML = `<button class="btn-share" style="background:#10b981; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:0.8em; cursor:pointer;"><i class="fa-solid fa-envelope"></i> Partilhar</button>`;
                    btnActions.querySelector('.btn-share').onclick = () => partilharEmail(l.id);
                    content.appendChild(btnActions);
                }

                marker.bindPopup(content);
                markerCluster.addLayer(marker);
                currentMarkers.push(marker);
            });

            // Se for pesquisa específica e tem resultados, ajusta o zoom
            if ((cidade !== '' || pais !== '' || categoriaId !== '') && currentMarkers.length > 0) {
                mapInstance.fitBounds(markerCluster.getBounds(), { padding: [50, 50], maxZoom: 12 });
            }
        });
}

function partilharEmail(id) {
    const email = prompt("Insira o email de destino:");
    if (!email) return;

    const data = new FormData();
    data.append('id', id);
    data.append('email_destino', email);

    fetch('enviar_email.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            alert(res.mensagem);
        })
        .catch(err => alert("Erro ao contactar o servidor: " + err));
}

function apagarLocal(id) {
    if(!confirm("Tem a certeza que deseja apagar este local?")) return;

    const data = new FormData();
    data.append('id', id);

    fetch('apagar_local.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if(res.status === 'ok') {
                carregarLocais(); // Recarregar
            } else {
                alert(res.mensagem);
            }
        });
}

/**
 * Lógica do Formulário Popup e Nominatim
 */
function configurarFormulario() {
    const formBox = document.getElementById('form-popup');
    const form = document.getElementById('formLocal');

    mapInstance.on("click", (e) => {
        // Se já estava algo a ser editado, limpar form e resetar acao = inserir
        resetarFormulario();
        
        const { lat, lng } = e.latlng;
        form.latitude.value = lat;
        form.longitude.value = lng;
        
        consultarNominatim(lat, lng).then(addr => {
            form.pais.value = addr.country || "";
            form.cidade.value = addr.city || addr.town || addr.village || "";
            form.morada.value = [addr.road, addr.house_number].filter(Boolean).join(", ");
        });

        formBox.style.display = 'block';
    });

    form.onsubmit = (e) => {
        e.preventDefault();
        
        const doSubmit = () => {
            fetch('guardar_local.php', { method: 'POST', body: new FormData(form) })
                .then(r => r.json())
                .then(res => {
                    if(res.status === 'ok') {
                        fechar();
                        carregarLocais();
                    } else {
                        alert(res.mensagem);
                    }
                });
        };

        // Se lat/lng estiver vazio (inserção via botão Sem clique no mapa), tenta geocodar
        if (!form.latitude.value || !form.longitude.value) {
            const query = [form.morada.value, form.cidade.value, form.pais.value].filter(Boolean).join(", ");
            if(!query) {
                alert("Por favor insira um País e Cidade, ou clique no mapa para definir a localização.");
                return;
            }
            
            fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`)
                .then(r => r.json())
                .then(data => {
                    if (data && data.length > 0) {
                        form.latitude.value = data[0].lat;
                        form.longitude.value = data[0].lon;
                        doSubmit();
                    } else {
                        alert("Não foi possível encontrar as coordenadas para a morada fornecida. Por favor preencha uma morada mais detalhada ou feche e clique no mapa.");
                    }
                }).catch(err => {
                    alert("Erro ao tentar obter coordenas: " + err);
                });
        } else {
            doSubmit();
        }
    };
}

function abrirEdicao(l) {
    const formBox = document.getElementById('form-popup');
    const form = document.getElementById('formLocal');
    
    document.getElementById('form-title').innerText = "Editar Local";
    document.getElementById('form-acao').value = "editar";
    document.getElementById('local-id').value = l.id;
    
    form.nome.value = l.nome;
    form.categoria.value = l.categoria;
    form.pais.value = l.pais;
    form.cidade.value = l.cidade;
    form.morada.value = l.morada;
    form.telefone.value = l.telefone || '';
    form.email.value = l.email || '';
    form.descricao.value = l.descricao || '';
    form.latitude.value = l.latitude;
    form.longitude.value = l.longitude;
    
    formBox.style.display = 'block';
    mapInstance.closePopup();
}

function resetarFormulario() {
    const form = document.getElementById('formLocal');
    form.reset();
    document.getElementById('form-title').innerText = "Novo Local";
    document.getElementById('form-acao').value = "inserir";
    document.getElementById('local-id').value = "";
}

function fechar() {
    document.getElementById('form-popup').style.display = 'none';
    resetarFormulario();
}

function consultarNominatim(lat, lng) {
    return fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=pt`)
        .then(r => {
            if (!r.ok) throw new Error(r.status);
            return r.json();
        })
        .then(d => d.address || {})
        .catch(e => {
            console.error("Nominatim falhou:", e);
            return {};
        });
}
