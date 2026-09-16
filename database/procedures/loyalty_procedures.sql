-- =========================================================
-- loyalty.fn_generar_token(client_id, barberia_id, segundos)
-- El cliente pide ver su QR: invalida cualquier token previo
-- sin usar y crea uno nuevo con expiración corta.
-- =========================================================
CREATE OR REPLACE FUNCTION loyalty.fn_generar_token(
    p_client_id BIGINT,
    p_barberia_id BIGINT,
    p_segundos INT DEFAULT 60
) RETURNS TABLE (token UUID, expires_at TIMESTAMP) AS $$
DECLARE
    v_token UUID := gen_random_uuid();
    v_expires TIMESTAMP := now() + (p_segundos || ' seconds')::interval;
BEGIN
    -- invalida tokens anteriores no usados de este cliente en esta barbería
    UPDATE loyalty.qr_token
       SET used = true
     WHERE client_id = p_client_id
       AND barberia_id = p_barberia_id
       AND used = false;

    INSERT INTO loyalty.qr_token (client_id, barberia_id, token, expires_at, created_at, updated_at)
    VALUES (p_client_id, p_barberia_id, v_token, v_expires, now(), now());

    RETURN QUERY SELECT v_token, v_expires;
END;
$$ LANGUAGE plpgsql;


-- =========================================================
-- loyalty.fn_escanear(token, empleado_id, sede_id, servicio_id)
-- El barbero escanea el QR del cliente: valida el token,
-- agrega el sello y retorna el estado resultante de la tarjeta.
-- =========================================================
CREATE OR REPLACE FUNCTION loyalty.fn_escanear(
    p_token UUID,
    p_empleado_id BIGINT,
    p_sede_id BIGINT,
    p_servicio_id BIGINT DEFAULT NULL
) RETURNS TABLE (
    ok BOOLEAN,
    mensaje TEXT,
    sellos_actuales INT,
    sellos_requeridos INT,
    corte_gratis BOOLEAN,
    card_id BIGINT
) AS $$
DECLARE
    v_qr loyalty.qr_token%ROWTYPE;
    v_barberia_sede BIGINT;
    v_aplica_sello BOOLEAN := true;
    v_config loyalty.config%ROWTYPE;
    v_card loyalty.card%ROWTYPE;
BEGIN
    -- 1. Validar token
    SELECT * INTO v_qr FROM loyalty.qr_token WHERE token = p_token FOR UPDATE;

    IF NOT FOUND THEN
        RETURN QUERY SELECT false, 'Código no encontrado', 0, 0, false, NULL::BIGINT;
        RETURN;
    END IF;

    IF v_qr.used THEN
        RETURN QUERY SELECT false, 'Código ya utilizado, pide al cliente que actualice su QR', 0, 0, false, NULL::BIGINT;
        RETURN;
    END IF;

    IF v_qr.expires_at < now() THEN
        RETURN QUERY SELECT false, 'Código expirado, pide al cliente que actualice su QR', 0, 0, false, NULL::BIGINT;
        RETURN;
    END IF;

    -- 2. Validar que la sede pertenezca a la misma barbería del token
    SELECT barberia_id INTO v_barberia_sede FROM tenant.sede WHERE id = p_sede_id;
    IF v_barberia_sede IS NULL OR v_barberia_sede <> v_qr.barberia_id THEN
        RETURN QUERY SELECT false, 'La sede no corresponde a la barbería del cliente', 0, 0, false, NULL::BIGINT;
        RETURN;
    END IF;

    -- 3. Si el servicio no aplica sello, no se suma (pero se marca el token como usado)
    IF p_servicio_id IS NOT NULL THEN
        SELECT aplica_sello INTO v_aplica_sello FROM barberia.servicio WHERE id = p_servicio_id;
        v_aplica_sello := COALESCE(v_aplica_sello, true);
    END IF;

    UPDATE loyalty.qr_token SET used = true, updated_at = now() WHERE id = v_qr.id;

    IF NOT v_aplica_sello THEN
        RETURN QUERY SELECT true, 'Servicio registrado (no aplica sello)', 0, 0, false, NULL::BIGINT;
        RETURN;
    END IF;

    -- 4. Config de la barbería (sellos requeridos)
    SELECT * INTO v_config FROM loyalty.config WHERE barberia_id = v_qr.barberia_id;
    IF NOT FOUND THEN
        INSERT INTO loyalty.config (barberia_id, sellos_requeridos, qr_token_segundos, activo, created_at, updated_at)
        VALUES (v_qr.barberia_id, 7, 60, true, now(), now())
        RETURNING * INTO v_config;
    END IF;

    -- 5. Tarjeta activa del cliente en esta barbería (o crear una nueva)
    SELECT * INTO v_card
      FROM loyalty.card
     WHERE client_id = v_qr.client_id
       AND barberia_id = v_qr.barberia_id
       AND estado = 'activa'
     FOR UPDATE;

    IF NOT FOUND THEN
        INSERT INTO loyalty.card (client_id, barberia_id, sellos_actuales, estado, created_at, updated_at)
        VALUES (v_qr.client_id, v_qr.barberia_id, 0, 'activa', now(), now())
        RETURNING * INTO v_card;
    END IF;

    -- 6. Agregar sello
    INSERT INTO loyalty.stamp (card_id, empleado_id, sede_id, servicio_id, created_at, updated_at)
    VALUES (v_card.id, p_empleado_id, p_sede_id, p_servicio_id, now(), now());

    UPDATE loyalty.card
       SET sellos_actuales = sellos_actuales + 1,
           updated_at = now()
     WHERE id = v_card.id
     RETURNING * INTO v_card;

    -- 7. ¿Se completó la tarjeta?
    IF v_card.sellos_actuales >= v_config.sellos_requeridos THEN
        UPDATE loyalty.card
           SET estado = 'completada', completed_at = now(), updated_at = now()
         WHERE id = v_card.id;

        RETURN QUERY SELECT true, '¡Corte gratis disponible!', v_card.sellos_actuales,
                            v_config.sellos_requeridos, true, v_card.id;
        RETURN;
    END IF;

    RETURN QUERY SELECT true, 'Sello agregado', v_card.sellos_actuales,
                        v_config.sellos_requeridos, false, v_card.id;
END;
$$ LANGUAGE plpgsql;


-- =========================================================
-- loyalty.fn_redimir(card_id)
-- Se llama cuando el barbero efectivamente aplica el corte
-- gratis: cierra la tarjeta completada y abre una nueva en 0.
-- =========================================================
CREATE OR REPLACE FUNCTION loyalty.fn_redimir(p_card_id BIGINT)
RETURNS TABLE (ok BOOLEAN, mensaje TEXT, nueva_card_id BIGINT) AS $$
DECLARE
    v_card loyalty.card%ROWTYPE;
    v_nueva loyalty.card%ROWTYPE;
BEGIN
    SELECT * INTO v_card FROM loyalty.card WHERE id = p_card_id FOR UPDATE;

    IF NOT FOUND THEN
        RETURN QUERY SELECT false, 'Tarjeta no encontrada', NULL::BIGINT;
        RETURN;
    END IF;

    IF v_card.estado <> 'completada' THEN
        RETURN QUERY SELECT false, 'La tarjeta aún no está completa', NULL::BIGINT;
        RETURN;
    END IF;

    UPDATE loyalty.card SET estado = 'redimida', updated_at = now() WHERE id = p_card_id;

    INSERT INTO loyalty.card (client_id, barberia_id, sellos_actuales, estado, created_at, updated_at)
    VALUES (v_card.client_id, v_card.barberia_id, 0, 'activa', now(), now())
    RETURNING * INTO v_nueva;

    RETURN QUERY SELECT true, 'Corte gratis redimido', v_nueva.id;
END;
$$ LANGUAGE plpgsql;
