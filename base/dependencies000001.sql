/************************************I-DEP-JRR-VEF-0-02/05/2015*************************************************/

ALTER TABLE ONLY vef.tformula
    ADD CONSTRAINT fk_tformula__id_medico
    FOREIGN KEY (id_medico) REFERENCES vef.tmedico(id_medico);


ALTER TABLE ONLY vef.tformula_detalle
    ADD CONSTRAINT fk_tformula_detalle__id_item
    FOREIGN KEY (id_item) REFERENCES alm.titem(id_item);

ALTER TABLE ONLY vef.tformula_detalle
    ADD CONSTRAINT fk_tformula_detalle__id_formula
    FOREIGN KEY (id_formula) REFERENCES vef.tformula(id_formula);


ALTER TABLE ONLY vef.tsucursal_usuario
    ADD CONSTRAINT fk_tsucursal_usuario__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tsucursal_usuario
    ADD CONSTRAINT fk_tsucursal_usuario__id_usuario
    FOREIGN KEY (id_usuario) REFERENCES segu.tusuario(id_usuario);

ALTER TABLE ONLY vef.tsucursal_almacen
    ADD CONSTRAINT fk_tsucursal_almacen__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tsucursal_almacen
    ADD CONSTRAINT fk_tsucursal_almacen__id_almacen
    FOREIGN KEY (id_almacen) REFERENCES alm.talmacen(id_almacen);

ALTER TABLE ONLY vef.tsucursal_producto
    ADD CONSTRAINT fk_tsucursal_producto__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tsucursal_producto
    ADD CONSTRAINT fk_tsucursal_producto__id_item
    FOREIGN KEY (id_item) REFERENCES alm.titem(id_item);

ALTER TABLE ONLY vef.tformula
    ADD CONSTRAINT fk_tformula__id_tipo_presentacion
    FOREIGN KEY (id_tipo_presentacion) REFERENCES vef.ttipo_presentacion(id_tipo_presentacion);

ALTER TABLE ONLY vef.tformula
    ADD CONSTRAINT fk_tformula__id_unidad_medida
    FOREIGN KEY (id_unidad_medida) REFERENCES param.tunidad_medida(id_unidad_medida);



ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_cliente
    FOREIGN KEY (id_cliente) REFERENCES vef.tcliente(id_cliente);

ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_proceso_wf
    FOREIGN KEY (id_proceso_wf) REFERENCES wf.tproceso_wf(id_proceso_wf);

ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_estado_wf
    FOREIGN KEY (id_estado_wf) REFERENCES wf.testado_wf(id_estado_wf);



ALTER TABLE ONLY vef.tventa_detalle
    ADD CONSTRAINT fk_tventa_detalle__id_venta
    FOREIGN KEY (id_venta) REFERENCES vef.tventa(id_venta);

ALTER TABLE ONLY vef.tventa_detalle
    ADD CONSTRAINT fk_tventa_detalle__id_item
    FOREIGN KEY (id_item) REFERENCES alm.titem(id_item);

ALTER TABLE ONLY vef.tventa_detalle
    ADD CONSTRAINT fk_tventa_detalle__id_sucursal_producto
    FOREIGN KEY (id_sucursal_producto) REFERENCES vef.tsucursal_producto(id_sucursal_producto);

ALTER TABLE ONLY vef.tventa_detalle
    ADD CONSTRAINT fk_tventa_detalle__id_formula
    FOREIGN KEY (id_formula) REFERENCES vef.tformula(id_formula);





CREATE OR REPLACE VIEW vef.vmedico(
    id_usuario_reg,
    id_usuario_mod,
    fecha_reg,
    fecha_mod,
    estado_reg,
    id_usuario_ai,
    usuario_ai,
    id_medico,
    nombres,
    primer_apellido,
    segundo_apellido,
    telefono_celular,
    telefono_fijo,
    otros_telefonos,
    correo,
    otros_correos,
    porcentaje,
    nombre_completo)
AS
  SELECT m.id_usuario_reg,
         m.id_usuario_mod,
         m.fecha_reg,
         m.fecha_mod,
         m.estado_reg,
         m.id_usuario_ai,
         m.usuario_ai,
         m.id_medico,
         m.nombres,
         m.primer_apellido,
         m.segundo_apellido,
         m.telefono_celular,
         m.telefono_fijo,
         m.otros_telefonos,
         m.correo,
         m.otros_correos,
         m.porcentaje,
         (((m.nombres::text || ' ' ::text) || m.primer_apellido::text) || ' '
          ::text) || COALESCE(m.segundo_apellido, '' ::character varying) ::text
           AS nombre_completo
  FROM vef.tmedico m;

  CREATE OR REPLACE VIEW vef.vcliente(
    id_usuario_reg,
    id_usuario_mod,
    fecha_reg,
    fecha_mod,
    estado_reg,
    id_usuario_ai,
    usuario_ai,
    id_cliente,
    nombres,
    primer_apellido,
    segundo_apellido,
    telefono_celular,
    telefono_fijo,
    otros_telefonos,
    correo,
    otros_correos,
    nombre_factura,
    nit,
    nombre_completo)
AS
  SELECT c.id_usuario_reg,
         c.id_usuario_mod,
         c.fecha_reg,
         c.fecha_mod,
         c.estado_reg,
         c.id_usuario_ai,
         c.usuario_ai,
         c.id_cliente,
         c.nombres,
         c.primer_apellido,
         c.segundo_apellido,
         c.telefono_celular,
         c.telefono_fijo,
         c.otros_telefonos,
         c.correo,
         c.otros_correos,
         c.nombre_factura,
         c.nit,
         (((c.nombres::text || ' ' ::text) || c.primer_apellido::text) || ' '
          ::text) || COALESCE(c.segundo_apellido, '' ::character varying) ::text
           AS nombre_completo
  FROM vef.tcliente c;

/************************************F-DEP-JRR-VEF-0-02/05/2015*************************************************/

/************************************I-DEP-JRR-VEF-0-20/09/2015*************************************************/
ALTER TABLE ONLY vef.tsucursal
    ADD CONSTRAINT fk_tsucursal__id_entidad
    FOREIGN KEY (id_entidad) REFERENCES param.tentidad(id_entidad);

ALTER TABLE ONLY vef.tsucursal_moneda
    ADD CONSTRAINT fk_tsucursal_moneda__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY vef.tsucursal_moneda
    ADD CONSTRAINT fk_tsucursal_moneda__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tsucursal_producto
    ADD CONSTRAINT fk_tsucursal_producto__id_concepto_ingas
    FOREIGN KEY (id_concepto_ingas) REFERENCES param.tconcepto_ingas(id_concepto_ingas);

ALTER TABLE ONLY param.tconcepto_ingas
    ADD CONSTRAINT fk_tconcepto_ingas__id_actividad_economica
    FOREIGN KEY (id_actividad_economica) REFERENCES vef.tactividad_economica(id_actividad_economica);

ALTER TABLE ONLY vef.tdosificacion
    ADD CONSTRAINT fk_tdosificacion__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tsucursal_usuario
    ADD CONSTRAINT fk_tsucursal_usuario__id_punto_venta
    FOREIGN KEY (id_punto_venta) REFERENCES vef.tpunto_venta(id_punto_venta);

ALTER TABLE ONLY vef.tforma_pago
    ADD CONSTRAINT fk_tforma_pago__id_entidad
    FOREIGN KEY (id_entidad) REFERENCES param.tentidad(id_entidad);

ALTER TABLE ONLY vef.tforma_pago
    ADD CONSTRAINT fk_tforma_pago__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_punto_venta
    FOREIGN KEY (id_punto_venta) REFERENCES vef.tpunto_venta(id_punto_venta);

ALTER TABLE ONLY vef.tventa_forma_pago
    ADD CONSTRAINT fk_tventa_forma_pago__id_forma_pago
    FOREIGN KEY (id_forma_pago) REFERENCES vef.tforma_pago(id_forma_pago);

ALTER TABLE ONLY vef.tventa_forma_pago
    ADD CONSTRAINT fk_tventa_forma_pago__id_venta
    FOREIGN KEY (id_venta) REFERENCES vef.tventa(id_venta);

CREATE OR REPLACE VIEW vef.vcliente(
    id_usuario_reg,
    id_usuario_mod,
    fecha_reg,
    fecha_mod,
    estado_reg,
    id_usuario_ai,
    usuario_ai,
    id_cliente,
    nombres,
    primer_apellido,
    segundo_apellido,
    telefono_celular,
    telefono_fijo,
    otros_telefonos,
    correo,
    otros_correos,
    nombre_factura,
    nit,
    nombre_completo)
AS
  SELECT c.id_usuario_reg,
         c.id_usuario_mod,
         c.fecha_reg,
         c.fecha_mod,
         c.estado_reg,
         c.id_usuario_ai,
         c.usuario_ai,
         c.id_cliente,
         c.nombres,
         c.primer_apellido,
         c.segundo_apellido,
         c.telefono_celular,
         c.telefono_fijo,
         c.otros_telefonos,
         c.correo,
         c.otros_correos,
         c.nombre_factura,
         c.nit,
         (((c.nombres::text || ' ' ::text) || c.primer_apellido::text) || ' '
          ::text) || COALESCE(c.segundo_apellido, '' ::character varying) ::text
           AS nombre_completo
  FROM vef.tcliente c;
/************************************F-DEP-JRR-VEF-0-20/09/2015*************************************************/


/************************************I-DEP-JRR-VEF-0-08/11/2015*************************************************/

ALTER TABLE ONLY vef.tventa_detalle
    ADD CONSTRAINT fk_tventa_detalle__id_vendedor
    FOREIGN KEY (id_vendedor) REFERENCES segu.tusuario(id_usuario);

ALTER TABLE ONLY vef.tventa_detalle
    ADD CONSTRAINT fk_tventa_detalle__id_medico
    FOREIGN KEY (id_medico) REFERENCES vef.tmedico;

ALTER TABLE ONLY vef.tformula_detalle
    ADD CONSTRAINT fk_tformula_detalle__id_concepto_ingas
    FOREIGN KEY (id_concepto_ingas) REFERENCES param.tconcepto_ingas;

 ALTER TABLE ONLY vef.tsucursal
    ADD CONSTRAINT fk_tsucursal__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar;


/************************************F-DEP-JRR-VEF-0-08/11/2015*************************************************/

/************************************I-DEP-JRR-VEF-0-25/11/2015*************************************************/

ALTER TABLE ONLY vef.tboleto
    ADD CONSTRAINT fk_tboleto__id_punto_venta
    FOREIGN KEY (id_punto_venta) REFERENCES vef.tpunto_venta(id_punto_venta);

ALTER TABLE ONLY vef.tboleto_fp
    ADD CONSTRAINT fk_tboleto_fp__id_boleto
    FOREIGN KEY (id_boleto) REFERENCES vef.tboleto(id_boleto);

ALTER TABLE ONLY vef.tboleto_fp
    ADD CONSTRAINT fk_tboleto_fp__id_forma_pago
    FOREIGN KEY (id_forma_pago) REFERENCES vef.tforma_pago(id_forma_pago);

/************************************F-DEP-JRR-VEF-0-25/11/2015*************************************************/


/************************************I-DEP-JRR-VEF-0-19/02/2016*************************************************/

ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_dosificacion
    FOREIGN KEY (id_dosificacion) REFERENCES vef.tdosificacion(id_dosificacion);

ALTER TABLE ONLY vef.tpunto_venta_producto
    ADD CONSTRAINT fk_tpunto_venta_producto__id_punto_venta
    FOREIGN KEY (id_punto_venta) REFERENCES vef.tpunto_venta(id_punto_venta);

ALTER TABLE ONLY vef.tpunto_venta_producto
    ADD CONSTRAINT fk_tpunto_venta_producto__id_sucursal_producto
    FOREIGN KEY (id_sucursal_producto) REFERENCES vef.tsucursal_producto(id_sucursal_producto);

/************************************F-DEP-JRR-VEF-0-19/02/2016*************************************************/


/************************************I-DEP-JRR-VEF-0-14/03/2016*************************************************/

ALTER TABLE ONLY vef.tsucursal_producto
    ADD CONSTRAINT fk_tsucursal_producto__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);

/************************************F-DEP-JRR-VEF-0-14/03/2016*************************************************/


/************************************I-DEP-JRR-VEF-0-02/05/2016*************************************************/

ALTER TABLE ONLY vef.ttipo_venta
    ADD CONSTRAINT fk_ttipo_venta__id_plantilla
    FOREIGN KEY (id_plantilla) REFERENCES param.tplantilla(id_plantilla);

ALTER TABLE ONLY vef.tsucursal
    ADD CONSTRAINT fk_tsucursal__id_depto
    FOREIGN KEY (id_depto) REFERENCES param.tdepto(id_depto);
/************************************F-DEP-JRR-VEF-0-02/05/2016*************************************************/

/************************************I-DEP-JRR-VEF-0-08/05/2016*************************************************/

ALTER TABLE ONLY vef.tvalor_descripcion
    ADD CONSTRAINT fk_tvalor_descripcion__id_venta
    FOREIGN KEY (id_venta) REFERENCES vef.tventa(id_venta);

ALTER TABLE ONLY vef.tvalor_descripcion
    ADD CONSTRAINT fk_tvalor_descripcion__id_tipo_descripcion
    FOREIGN KEY (id_tipo_descripcion) REFERENCES vef.ttipo_descripcion(id_tipo_descripcion);
/************************************F-DEP-JRR-VEF-0-08/05/2016*************************************************/


/************************************I-DEP-JRR-VEF-0-07/07/2016*************************************************/


ALTER TABLE ONLY vef.tapertura_cierre_caja
    ADD CONSTRAINT fk_tapertura_cierre_caja__id_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES vef.tsucursal(id_sucursal);

ALTER TABLE ONLY vef.tapertura_cierre_caja
    ADD CONSTRAINT fk_tapertura_cierre_caja__id_punto_venta
    FOREIGN KEY (id_punto_venta) REFERENCES vef.tpunto_venta(id_punto_venta);

ALTER TABLE ONLY vef.tapertura_cierre_caja
    ADD CONSTRAINT fk_tapertura_cierre_caja__id_usuario_cajero
    FOREIGN KEY (id_usuario_cajero) REFERENCES segu.tusuario(id_usuario);

ALTER TABLE ONLY vef.tapertura_cierre_caja
    ADD CONSTRAINT fk_tapertura_cierre_caja__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY vef.tventa
    ADD CONSTRAINT fk_tventa__id_usuario_cajero
    FOREIGN KEY (id_usuario_cajero) REFERENCES segu.tusuario(id_usuario);

/************************************F-DEP-JRR-VEF-0-07/07/2016*************************************************/


/************************************I-DEP-JRR-VEF-0-19/09/2016*************************************************/
DROP TRIGGER IF EXISTS trig_tdosificacion ON vef.tdosificacion;

CREATE TRIGGER trig_tdosificacion
  BEFORE INSERT OR UPDATE
  ON vef.tdosificacion FOR EACH ROW
  EXECUTE PROCEDURE vef.f_trig_tdosificacion();

/************************************F-DEP-JRR-VEF-0-19/09/2016*************************************************/



/************************************I-DEP-JRR-VEF-0-28/10/2016*************************************************/


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD CONSTRAINT tventa__id_cliente_destino_fk FOREIGN KEY (id_cliente_destino)
    REFERENCES vef.tcliente(id_cliente)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;


/************************************F-DEP-JRR-VEF-0-28/10/2016*************************************************/



/************************************I-DEP-RAC-VEF-0-11/11/2016*************************************************/
CREATE TRIGGER trig_tcliente
  AFTER INSERT
  ON vef.tcliente FOR EACH ROW
  EXECUTE PROCEDURE vef.f_trig_cliente();

  --------------- SQL ---------------
-- object recreation
DROP VIEW vef.vcliente;

CREATE OR REPLACE VIEW vef.vcliente
AS
  SELECT c.id_usuario_reg,
         c.id_usuario_mod,
         c.fecha_reg,
         c.fecha_mod,
         c.estado_reg,
         c.id_usuario_ai,
         c.usuario_ai,
         c.id_cliente,
         c.nombres,
         c.primer_apellido,
         c.segundo_apellido,
         c.telefono_celular,
         c.telefono_fijo,
         c.otros_telefonos,
         c.correo,
         c.otros_correos,
         c.nombre_factura,
         c.nit,
         (((c.nombres::text || ' '::text) || c.primer_apellido::text) || ' '::
           text) || COALESCE(c.segundo_apellido, ''::character varying)::text AS
           nombre_completo,
         COALESCE(c.lugar, ''::character varying) AS lugar,
         c.codigo
  FROM vef.tcliente c;


/************************************F-DEP-RAC-VEF-0-11/11/2016*************************************************/


/************************************I-DEP-RCM-VEF-0-13/11/2016*************************************************/
DROP VIEW IF EXISTS vef.vproducto;

CREATE VIEW vef.vproducto
AS

    SELECT sprod.id_sucursal_producto, suc.id_sucursal,suc.nombre, suc.codigo as codigo_suc,
    cing.id_concepto_ingas, cing.desc_ingas as producto, cing.codigo as codigo_producto
    from vef.tsucursal_producto sprod
    inner join vef.tsucursal suc
    on suc.id_sucursal = sprod.id_sucursal
    inner join param.tconcepto_ingas cing
    on cing.id_concepto_ingas = sprod.id_concepto_ingas;

/************************************F-DEP-RCM-VEF-0-13/11/2016*************************************************/

/************************************I-DEP-JRR-VEF-0-14/03/2017*************************************************/
ALTER TABLE vef.tpunto_venta
  ADD CONSTRAINT fk_tpunto_venta__id_sucursal FOREIGN KEY (id_sucursal)
    REFERENCES vef.tsucursal(id_sucursal)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;

/************************************F-DEP-JRR-VEF-0-14/03/2017*************************************************/
/************************************I-DEP-MMV-VEF-0-21/11/2017*************************************************/
CREATE OR REPLACE VIEW vef.vdepositos (
    id_punto_venta,
    id_apertura_cierre_caja,
    id_entrega_brinks,
    id_usuario_cajero,
    codigo_padre,
    estacion,
    nombre,
    codigo,
    cajero,
    fecha_recojo,
    fecha_venta,
    arqueo_moneda_local,
    arqueo_moneda_extranjera,
    deposito_bs,
    deposito_usd,
    tipo_cambio)
AS
 WITH punto_venta AS (
         SELECT p.id_punto_venta,
            l.codigo AS estacion,
            lu.codigo AS codigo_padre,
            p.nombre,
            p.codigo
           FROM param.tlugar l
             JOIN vef.tsucursal s ON s.id_lugar = l.id_lugar
             JOIN vef.tpunto_venta p ON p.id_sucursal = s.id_sucursal
             JOIN param.tlugar lu ON lu.id_lugar = l.id_lugar_fk
        )
 SELECT ap.id_punto_venta,
    ap.id_apertura_cierre_caja,
    ap.id_entrega_brinks,
    ap.id_usuario_cajero,
    pu.codigo_padre,
    pu.estacion,
    pu.nombre,
    pu.codigo,
    pe.nombre_completo1 AS cajero,
    en.fecha_recojo,
    ap.fecha_apertura_cierre AS fecha_venta,
    ap.arqueo_moneda_local,
    ap.arqueo_moneda_extranjera,
    obingresos.f_monto_tipo_cambio('BOB'::character varying, ap.id_apertura_cierre_caja) AS deposito_bs,
    obingresos.f_monto_tipo_cambio('USD'::character varying, ap.id_apertura_cierre_caja) AS deposito_usd,
    ( SELECT c.oficial
           FROM param.ttipo_cambio c
          WHERE c.id_moneda = 2 AND c.fecha = now()::date AND c.fecha_mod IS NULL) AS tipo_cambio
   FROM vef.tapertura_cierre_caja ap
     JOIN punto_venta pu ON pu.id_punto_venta = ap.id_punto_venta
     JOIN segu.tusuario us ON us.id_usuario = ap.id_usuario_cajero
     JOIN segu.vpersona pe ON pe.id_persona = us.id_persona
     JOIN vef.tentrega en ON en.id_entrega_brinks = ap.id_entrega_brinks;
/************************************F-DEP-MMV-VEF-0-21/11/2017*************************************************/

/************************************I-DEP-GSS-VEF-0-22/11/2017*************************************************/

CREATE TRIGGER trig_tapertura_cierre_caja
  BEFORE UPDATE OF fecha_apertura_cierre
  ON vef.tapertura_cierre_caja FOR EACH ROW
  EXECUTE PROCEDURE vef.f_trig_apertura_cierre_caja();

/************************************F-DEP-GSS-VEF-0-22/11/2017*************************************************/

/************************************I-DEP-IRVA-VEF-0-19/07/2019*************************************************/
CREATE OR REPLACE VIEW vef.vdepositos (
    id_punto_venta,
    id_apertura_cierre_caja,
    id_entrega_brinks,
    id_usuario_cajero,
    codigo_padre,
    estacion,
    nombre,
    codigo,
    cajero,
    fecha_recojo,
    fecha_venta,
    arqueo_moneda_local,
    arqueo_moneda_extranjera,
    deposito_bs,
    deposito_usd,
    diferencia_bs,
    diferencia_usd,
    tipo_cambio)
AS
 WITH punto_venta AS (
SELECT p.id_punto_venta,
            l.codigo AS codigo_padre,
            p.nombre,
            p.codigo,
            lu.codigo AS estacion
FROM vef.tsucursal s_1
             JOIN vef.tpunto_venta p ON p.id_sucursal = s_1.id_sucursal
             JOIN param.tlugar l ON l.id_lugar =
                 param.f_obtener_padre_id_lugar(s_1.id_lugar, 'pais'::character varying)
             JOIN param.tlugar lu ON lu.id_lugar = s_1.id_lugar
        )
    SELECT ap.id_punto_venta,
    ap.id_apertura_cierre_caja,
    ap.id_entrega_brinks,
    ap.id_usuario_cajero,
    pu.codigo_padre,
    pu.estacion,
    pu.nombre,
    pu.codigo,
    pe.nombre_completo1 AS cajero,
    en.fecha_recojo,
    ap.fecha_apertura_cierre AS fecha_venta,
    ap.arqueo_moneda_local,
    ap.arqueo_moneda_extranjera,
    COALESCE(s.monto_total, 0::numeric) AS deposito_bs,
    COALESCE(ss.monto_total, 0::numeric) AS deposito_usd,
        CASE
            WHEN round(ap.arqueo_moneda_local - COALESCE(s.monto_total,
                0::numeric)) <> 0::numeric THEN round(ap.arqueo_moneda_local - COALESCE(s.monto_total, 0::numeric))
            WHEN round(ap.arqueo_moneda_local - COALESCE(s.monto_total,
                0::numeric)) = 0::numeric THEN 0::numeric
            ELSE round(ap.arqueo_moneda_local - COALESCE(s.monto_total, 0::numeric))
        END::numeric(18,2) AS diferencia_bs,
        CASE
            WHEN round(ap.arqueo_moneda_extranjera - COALESCE(ss.monto_total,
                0::numeric)) <> 0::numeric THEN round(ap.arqueo_moneda_extranjera - COALESCE(ss.monto_total, 0::numeric))
            WHEN round(ap.arqueo_moneda_extranjera - COALESCE(ss.monto_total,
                0::numeric)) = 0::numeric THEN 0::numeric
            ELSE round(ap.arqueo_moneda_extranjera - COALESCE(ss.monto_total,
                0::numeric))
        END::numeric(18,2) AS diferencia_usd,
    (
        SELECT c.oficial
        FROM param.ttipo_cambio c
        WHERE c.id_moneda = 2 AND c.fecha = now()::date AND c.fecha_mod IS NULL
        ) AS tipo_cambio
    FROM vef.tapertura_cierre_caja ap
     JOIN punto_venta pu ON pu.id_punto_venta = ap.id_punto_venta
     JOIN segu.tusuario us ON us.id_usuario = ap.id_usuario_cajero
     JOIN segu.vpersona pe ON pe.id_persona = us.id_persona
     LEFT JOIN vef.vdepositos_suma s ON s.id_apertura_cierre_caja =
         ap.id_apertura_cierre_caja AND s.tipo = (((
        SELECT mo.codigo_internacional
        FROM param.tmoneda mo
        WHERE mo.tipo_moneda::text = 'base'::text
        ))::text)
     LEFT JOIN vef.vdepositos_suma ss ON ss.id_apertura_cierre_caja =
         ap.id_apertura_cierre_caja AND ss.tipo = 'US'::text
     JOIN vef.tentrega en ON en.id_entrega_brinks = ap.id_entrega_brinks;
     ALTER VIEW vef.vdepositos_suma
  OWNER TO postgres;

/************************************F-DEP-IRVA-VEF-0-19/07/2019*************************************************/

/************************************I-DEP-IRVA-VEF-1-19/07/2019*************************************************/
CREATE OR REPLACE VIEW vef.vdepositos_suma (
      id_apertura_cierre_caja,
      monto_total,
      tipo)
  AS
  SELECT d.id_apertura_cierre_caja,
      sum(COALESCE(d.monto_total, 0::numeric)) AS monto_total,
      ((
      SELECT mo.codigo_internacional
      FROM param.tmoneda mo
      WHERE mo.tipo_moneda::text = 'base'::text
      ))::text AS tipo
  FROM obingresos.tdeposito d
  WHERE d.id_moneda_deposito = ((
      SELECT mo.id_moneda
      FROM param.tmoneda mo
      WHERE mo.tipo_moneda::text = 'base'::text
      )) AND d.id_apertura_cierre_caja IS NOT NULL
  GROUP BY d.id_apertura_cierre_caja
  UNION
  SELECT d.id_apertura_cierre_caja,
      sum(COALESCE(d.monto_total, 0::numeric)) AS monto_total,
      'US'::text AS tipo
  FROM obingresos.tdeposito d
  WHERE d.id_moneda_deposito = 2 AND d.id_apertura_cierre_caja IS NOT NULL
  GROUP BY d.id_apertura_cierre_caja;
  ALTER VIEW vef.vdepositos_suma
  OWNER TO postgres;
/************************************F-DEP-IRVA-VEF-1-19/07/2019*************************************************/

/************************************I-DEP-IRVA-VEF-0-26/08/2019*************************************************/
CREATE OR REPLACE VIEW vef.vdepositos_suma (
    id_apertura_cierre_caja,
    monto_total,
    tipo)
AS
SELECT
        CASE
            WHEN d.id_apertura_cierre_caja IS NOT NULL THEN d.id_apertura_cierre_caja
            ELSE aper.id_apertura_cierre_caja
        END AS id_apertura_cierre_caja,
    sum(COALESCE(d.monto_total, 0::numeric)) AS monto_total,
    ((
    SELECT mo.codigo_internacional
    FROM param.tmoneda mo
    WHERE mo.tipo_moneda::text = 'base'::text
    ))::text AS tipo
FROM obingresos.tdeposito d
     LEFT JOIN vef.tapertura_cierre_caja_asociada aper ON aper.id_deposito =
         d.id_deposito
WHERE d.id_moneda_deposito = ((
    SELECT mo.id_moneda
    FROM param.tmoneda mo
    WHERE mo.tipo_moneda::text = 'base'::text
    )) AND (d.tipo::text = 'venta_propia'::text OR d.tipo::text =
        'venta_propia_agrupada'::text)
GROUP BY d.id_apertura_cierre_caja, aper.id_apertura_cierre_caja
UNION
SELECT
        CASE
            WHEN d.id_apertura_cierre_caja IS NOT NULL THEN d.id_apertura_cierre_caja
            ELSE aper.id_apertura_cierre_caja
        END AS id_apertura_cierre_caja,
    sum(COALESCE(d.monto_total, 0::numeric)) AS monto_total,
    'US'::text AS tipo
FROM obingresos.tdeposito d
     LEFT JOIN vef.tapertura_cierre_caja_asociada aper ON aper.id_deposito =
         d.id_deposito
WHERE d.id_moneda_deposito = 2 AND (d.tipo::text = 'venta_propia'::text OR
    d.tipo::text = 'venta_propia_agrupada'::text)
GROUP BY d.id_apertura_cierre_caja, aper.id_apertura_cierre_caja;

ALTER VIEW vef.vdepositos_suma
  OWNER TO postgres;
/************************************F-DEP-IRVA-VEF-0-26/08/2019*************************************************/

/************************************I-DEP-IRVA-VEF-0-26/08/2019*************************************************/
CREATE VIEW vef.vdepositos_agrupados (
    id_deposito,
    estado_reg,
    nro_deposito,
    nro_deposito_boa,
    monto_deposito,
    id_moneda_deposito,
    id_agencia,
    fecha,
    saldo,
    id_usuario_reg,
    fecha_reg,
    id_usuario_ai,
    usuario_ai,
    id_usuario_mod,
    fecha_mod,
    usr_reg,
    usr_mod,
    desc_moneda,
    agt,
    fecha_venta,
    monto_total,
    nombre_agencia,
    desc_periodo,
    estado,
    id_apertura_cierre_caja,
    nro_cuenta,
    monto_total_ml,
    monto_total_me,
    diferencia_ml,
    diferencia_me)
AS
SELECT dep.id_deposito,
    dep.estado_reg,
    dep.nro_deposito,
    dep.nro_deposito_boa,
    dep.monto_deposito,
    dep.id_moneda_deposito,
    dep.id_agencia,
    dep.fecha,
    dep.saldo,
    dep.id_usuario_reg,
    dep.fecha_reg,
    dep.id_usuario_ai,
    dep.usuario_ai,
    dep.id_usuario_mod,
    dep.fecha_mod,
    us.cuenta AS usr_reg,
    usu2.cuenta AS usr_mod,
    mon.codigo_internacional AS desc_moneda,
    dep.agt,
    dep.fecha_venta,
    dep.monto_total,
    ((age.codigo_int::text || ' - '::text) || age.nombre::text)::character
        varying AS nombre_agencia,
    (((to_char(pv.fecha_ini::timestamp with time zone, 'DD/MM/YYYY'::text) ||
        '-'::text) || to_char(pv.fecha_fin::timestamp with time zone, 'DD/MM/YYYY'::text)) || ' '::text) || tp.tipo_cc::text AS desc_periodo,
    dep.estado,
    dep.id_apertura_cierre_caja,
    cuen.nro_cuenta,
        CASE
            WHEN dep.id_moneda_deposito <> 2 THEN sum(caja.arqueo_moneda_local)
            ELSE 0.00
        END AS monto_total_ml,
        CASE
            WHEN dep.id_moneda_deposito = 2 THEN sum(caja.arqueo_moneda_extranjera)
            ELSE 0.00
        END AS monto_total_me,
        CASE
            WHEN dep.id_moneda_deposito <> 2 THEN dep.monto_deposito -
                sum(caja.arqueo_moneda_local)
            ELSE 0.00
        END AS diferencia_ml,
        CASE
            WHEN dep.id_moneda_deposito = 2 THEN dep.monto_deposito -
                sum(caja.arqueo_moneda_extranjera)
            ELSE 0.00
        END AS diferencia_me
FROM obingresos.tdeposito dep
     JOIN segu.tusuario us ON us.id_usuario = dep.id_usuario_reg
     LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = dep.id_usuario_mod
     JOIN param.tmoneda mon ON mon.id_moneda = dep.id_moneda_deposito
     LEFT JOIN obingresos.tagencia age ON age.id_agencia = dep.id_agencia
     LEFT JOIN obingresos.tperiodo_venta pv ON pv.id_periodo_venta =
         dep.id_periodo_venta
     LEFT JOIN obingresos.ttipo_periodo tp ON tp.id_tipo_periodo = pv.id_tipo_periodo
     LEFT JOIN vef.tapertura_cierre_caja_asociada aper ON aper.id_deposito =
         dep.id_deposito
     LEFT JOIN vef.tapertura_cierre_caja caja ON caja.id_apertura_cierre_caja =
         aper.id_apertura_cierre_caja
     JOIN tes.tts_libro_bancos lib ON lib.id_deposito = dep.id_deposito
     JOIN tes.tcuenta_bancaria cuen ON cuen.id_cuenta_bancaria = lib.id_cuenta_bancaria
WHERE dep.tipo::text = 'venta_propia_agrupada'::text
GROUP BY dep.id_deposito, us.cuenta, usu2.cuenta, mon.codigo_internacional,
    age.codigo_int, age.nombre, pv.fecha_ini, pv.fecha_fin, tp.tipo_cc, cuen.nro_cuenta;

ALTER VIEW vef.vdepositos_agrupados
  OWNER TO postgres;
/************************************F-DEP-IRVA-VEF-0-26/08/2019*************************************************/
