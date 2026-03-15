<!--
/**
 * @file form_local.php
 * @brief Glassmorphism Popup form for adding/editing a location.
 * @author Marco
 * @date 2026-03-15
 */
-->
<div id="form-popup">
    <div class="form-header">
        <h2><i class="fa-solid fa-location-dot"></i> <span id="form-title">Novo Local</span></h2>
    </div>

    <form id="formLocal">
        <input type="hidden" name="acao" id="form-acao" value="inserir">
        <input type="hidden" name="id" id="local-id" value="">

        <div class="form-group">
            <label for="nome">Nome do Local</label>
            <input type="text" id="nome" name="nome" placeholder="Ex: Câmara Municipal" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
                <!-- Estas opções podem ser lidas da DB e montadas pelo JS, mas por agora estáticas ou carregadas via PHP -->
                <option value="" disabled selected>Selecione uma categoria...</option>
                <option value="Câmara Municipal">Câmara Municipal</option>
                <option value="Bombeiros">Bombeiros</option>
                <option value="Hospital">Hospital</option>
                <option value="Aeroporto">Aeroporto</option>
                <option value="Hotel">Hotel</option>
                <option value="Restaurante">Restaurante</option>
                <option value="Museu">Museu</option>
            </select>
        </div>

        <div style="display:flex; gap:16px;">
            <div class="form-group" style="flex:1;">
                <label for="paisInput">País</label>
                <input type="text" id="paisInput" name="pais" placeholder="Ex: Portugal">
            </div>
            
            <div class="form-group" style="flex:1;">
                <label for="cidadeInput">Cidade</label>
                <input type="text" id="cidadeInput" name="cidade" placeholder="Ex: Lisboa">
            </div>
        </div>

        <div class="form-group">
            <label for="morada">Morada</label>
            <input type="text" id="morada" name="morada" placeholder="Ex: Rua Central, 10">
        </div>

        <div style="display:flex; gap:16px;">
            <div class="form-group" style="flex:1;">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" placeholder="Ex: 210000000">
            </div>
            
            <div class="form-group" style="flex:1;">
                <label for="email_local">Email</label>
                <input type="email" id="email_local" name="email" placeholder="contacto@local.pt">
            </div>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição Expandida</label>
            <textarea id="descricao" name="descricao" rows="3" placeholder="Insira detalhes adicionais sobre o local..."></textarea>
        </div>

        <div class="form-group">
            <label for="foto">Fotografia do Local (Opcional)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg, image/png, image/webp" style="background: rgba(0,0,0,0.2);">
        </div>

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="fechar()">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Guardar Local</button>
        </div>
    </form>
</div>
