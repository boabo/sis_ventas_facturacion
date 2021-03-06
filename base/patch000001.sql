/************************************I-SCP-JRR-VEF-0-02/05/2015*************************************************/
CREATE TABLE vef.tmedico (
    id_medico serial NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    primer_apellido VARCHAR(100) NOT NULL,
    segundo_apellido VARCHAR(100),
    telefono_celular VARCHAR(20) NOT NULL,
    telefono_fijo VARCHAR(20),
    otros_telefonos VARCHAR(100),
    correo VARCHAR(150) NOT NULL,
    otros_correos VARCHAR(255),
    porcentaje INTEGER NOT NULL,
    CONSTRAINT pk_tmedico__id_medico
    PRIMARY KEY (id_medico))
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE vef.tcliente (
    id_cliente serial NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    primer_apellido VARCHAR(100) NOT NULL,
    segundo_apellido VARCHAR(100),
    telefono_celular VARCHAR(20),
    telefono_fijo VARCHAR(20),
    otros_telefonos VARCHAR(100),
    correo VARCHAR(150),
    otros_correos VARCHAR(255),
    nombre_factura VARCHAR(100),
    nit VARCHAR(25),
    CONSTRAINT pk_tcliente__id_cliente
    PRIMARY KEY (id_cliente))
INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE vef.tsucursal (
    id_sucursal serial NOT NULL,
    codigo VARCHAR(20),
    nombre VARCHAR(200),
    telefono VARCHAR(50),
    correo VARCHAR(200),
    tiene_precios_x_sucursal VARCHAR(2),
    clasificaciones_para_venta INTEGER[],
    clasificaciones_para_formula INTEGER[],
    CONSTRAINT pk_tsucursal__id_sucursal
    PRIMARY KEY (id_sucursal))
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE vef.tsucursal_usuario (
    id_sucursal_usuario serial NOT NULL,
    tipo_usuario VARCHAR(20) NOT NULL,
    id_sucursal INTEGER NOT NULL,
    id_usuario INTEGER NOT NULL,
    CONSTRAINT pk_tsucursal_usuario__id_sucursal_usuario
    PRIMARY KEY (id_sucursal_usuario))
INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE vef.tsucursal_almacen (
    id_sucursal_almacen serial NOT NULL,
    tipo_almacen VARCHAR(20) NOT NULL,
    id_sucursal INTEGER NOT NULL,
    id_almacen INTEGER NOT NULL,
    CONSTRAINT pk_tsucursal_almacen__id_sucursal_almacen
    PRIMARY KEY (id_sucursal_almacen))
INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE vef.tsucursal_producto (
    id_sucursal_producto serial NOT NULL,
    precio NUMERIC(18,2) NOT NULL,
    id_sucursal INTEGER NOT NULL,
    id_item INTEGER,
    tipo_producto VARCHAR(30) NOT NULL,
    nombre_producto VARCHAR(150) NOT NULL,
    descripcion_producto TEXT NOT NULL,
    CONSTRAINT pk_tsucursal_item__id_sucursal_producto
    PRIMARY KEY (id_sucursal_producto))
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE vef.tformula (
    id_formula serial NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_medico INTEGER NOT NULL,
    id_tipo_presentacion INTEGER,
    id_unidad_medida INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    CONSTRAINT pk_tformula__id_formula
    PRIMARY KEY (id_formula))
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE vef.tformula_detalle (
    id_formula_detalle serial NOT NULL,
    cantidad NUMERIC(18,2) NOT NULL,
    id_item INTEGER NOT NULL,
    id_formula INTEGER NOT NULL,
    CONSTRAINT pk_tformula_detalle__id_formula_detalle
    PRIMARY KEY (id_formula_detalle))
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE vef.ttipo_presentacion (
    id_tipo_presentacion serial NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    CONSTRAINT pk_ttipo_presentacion__id_tipo_presentacion
    PRIMARY KEY (id_tipo_presentacion))
INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE vef.tventa (
    id_venta serial NOT NULL,
    id_cliente INTEGER NOT NULL,
    id_sucursal INTEGER NOT NULL,
    id_proceso_wf INTEGER NOT NULL,
    id_estado_wf INTEGER NOT NULL,
    nro_tramite VARCHAR NOT NULL,
    total_venta NUMERIC(18,2) NOT NULL DEFAULT 0,
    a_cuenta NUMERIC(18,2) NOT NULL,
    fecha_estimada_entrega DATE NOT NULL,
    estado VARCHAR(100) NOT NULL,
    CONSTRAINT pk_tventa__id_venta
    PRIMARY KEY (id_venta))
INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE vef.tventa_detalle (
    id_venta_detalle serial NOT NULL,
    id_venta INTEGER NOT NULL,
    id_item INTEGER,
    id_sucursal_producto INTEGER,
    id_formula INTEGER,
    tipo VARCHAR NOT NULL,
    precio NUMERIC(18,2) NOT NULL,
    cantidad INTEGER NOT NULL,
    sw_porcentaje_formula VARCHAR(2) NOT NULL,
    CONSTRAINT pk_tventa_detalle__id_venta_detalle
    PRIMARY KEY (id_venta_detalle))
INHERITS (pxp.tbase) WITHOUT OIDS;


/************************************F-SCP-JRR-VEF-0-02/05/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-17/06/2015*************************************************/

ALTER TABLE vef.tventa
  ADD COLUMN tiene_formula VARCHAR(2) DEFAULT 'no' NOT NULL;

/************************************F-SCP-JRR-VEF-0-17/06/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-05/07/2015*************************************************/
ALTER TABLE vef.tsucursal
  ADD COLUMN direccion VARCHAR(255);

ALTER TABLE vef.tventa
  ADD COLUMN id_movimiento INTEGER;

/************************************F-SCP-JRR-VEF-0-05/07/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-20/09/2015*************************************************/
ALTER TABLE vef.tsucursal
  ADD COLUMN id_entidad INTEGER NOT NULL;

ALTER TABLE vef.tsucursal
  ADD COLUMN plantilla_documento_factura VARCHAR (50);

ALTER TABLE vef.tsucursal
  ADD COLUMN plantilla_documento_recibo VARCHAR (50);

ALTER TABLE vef.tsucursal
  ADD COLUMN formato_comprobante VARCHAR (50);

CREATE TABLE vef.tsucursal_moneda (
    id_sucursal_moneda serial NOT NULL,
    tipo_moneda VARCHAR(20) NOT NULL,
    id_sucursal INTEGER NOT NULL,
    id_moneda INTEGER NOT NULL,
    CONSTRAINT pk_tsucursal_moneda__id_sucursal_moneda
    PRIMARY KEY (id_sucursal_moneda))
INHERITS (pxp.tbase) WITHOUT OIDS;

ALTER TABLE vef.tsucursal
  ADD COLUMN lugar VARCHAR (150);

ALTER TABLE vef.tsucursal_producto
  ADD COLUMN id_concepto_ingas INTEGER;


ALTER TABLE vef.tsucursal_producto
  DROP COLUMN nombre_producto;

ALTER TABLE vef.tsucursal_producto
  DROP COLUMN descripcion_producto;

CREATE TABLE vef.tactividad_economica (
    id_actividad_economica serial NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    CONSTRAINT pk_tactividad_economica__id_actividad_economica
    PRIMARY KEY (id_actividad_economica))
INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE vef.tdosificacion (
  id_dosificacion SERIAL,
  tipo VARCHAR(50) NOT NULL,
  id_sucursal INTEGER NOT NULL,
  nroaut VARCHAR(150) NOT NULL,
  tipo_generacion VARCHAR(50) NOT NULL,
  inicial INTEGER,
  final INTEGER,
  llave VARCHAR(150),
  fecha_dosificacion DATE NOT NULL,
  fecha_inicio_emi DATE,
  fecha_limite DATE,
  id_activida_economica INTEGER[] NOT NULL,
  glosa_impuestos VARCHAR(150),
  glosa_empresa VARCHAR(150),
  nro_siguiente INTEGER,
  CONSTRAINT pk_tdosificacion__id_dosificacion PRIMARY KEY(id_dosificacion)
) INHERITS (pxp.tbase);

COMMENT ON COLUMN vef.tdosificacion.tipo
IS 'F Factura, Notas de Credito y Debito todavia no se tiene ';

COMMENT ON COLUMN vef.tdosificacion.tipo_generacion
IS 'manual|computarizada';

CREATE TABLE vef.tpunto_venta (
  id_punto_venta SERIAL,
  id_sucursal INTEGER NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  CONSTRAINT pk_tpunto_venta__id_punto_venta PRIMARY KEY(id_punto_venta)
) INHERITS (pxp.tbase);

ALTER TABLE vef.tsucursal_usuario
  ADD COLUMN id_punto_venta INTEGER;

ALTER TABLE vef.tmedico
  ADD COLUMN fecha_nacimiento date;


CREATE TABLE vef.tforma_pago (
  id_forma_pago SERIAL,
  codigo VARCHAR NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  id_entidad INTEGER NOT NULL,
  id_moneda INTEGER NOT NULL,
  CONSTRAINT pk_tforma_pago__id_forma_pago PRIMARY KEY(id_forma_pago)
) INHERITS (pxp.tbase);

DROP VIEW IF EXISTS vef.vcliente;

ALTER TABLE vef.tcliente
  ALTER COLUMN nombres DROP NOT NULL;

ALTER TABLE vef.tcliente
  ALTER COLUMN primer_apellido DROP NOT NULL;

ALTER TABLE vef.tcliente
  ALTER COLUMN nombre_factura TYPE VARCHAR(200) COLLATE pg_catalog."default";

ALTER TABLE vef.tcliente
  ALTER COLUMN nombre_factura SET NOT NULL;

ALTER TABLE vef.tventa
  ADD COLUMN id_punto_venta INTEGER;


ALTER TABLE vef.tventa
  ADD COLUMN correlativo_venta VARCHAR(20)  DEFAULT '' NOT NULL;


CREATE TABLE vef.tventa_forma_pago (
  id_venta_forma_pago SERIAL,
  id_forma_pago INTEGER NOT NULL,
  id_venta INTEGER NOT NULL,
  monto NUMERIC(18,2) NOT NULL,
  CONSTRAINT pk_tventa_forma_pago__id_venta_forma_pago PRIMARY KEY(id_venta_forma_pago)
) INHERITS (pxp.tbase);

ALTER TABLE vef.tforma_pago
  ADD COLUMN defecto VARCHAR(2);

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN monto_transaccion NUMERIC(18,2) NOT NULL;

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN cambio NUMERIC(18,2) NOT NULL;

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN monto_mb_efectivo NUMERIC(18,2) NOT NULL;

ALTER TABLE vef.tventa_detalle
  DROP COLUMN sw_porcentaje_formula;

ALTER TABLE vef.tforma_pago
  ADD COLUMN registrar_tarjeta VARCHAR(2);

ALTER TABLE vef.tforma_pago
  ADD COLUMN registrar_cc VARCHAR(2);

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN numero_tarjeta VARCHAR(25);

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN codigo_tarjeta VARCHAR(25);

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN tipo_tarjeta VARCHAR(10);

/************************************F-SCP-JRR-VEF-0-20/09/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-08/11/2015*************************************************/

ALTER TABLE vef.tventa_detalle
  ADD COLUMN precio_sin_descuento NUMERIC(18,2);

ALTER TABLE vef.tventa_detalle
  ADD COLUMN porcentaje_descuento NUMERIC(5);

ALTER TABLE vef.tventa_detalle
  ADD COLUMN id_vendedor INTEGER;

ALTER TABLE vef.tventa_detalle
  ADD COLUMN id_medico INTEGER;

ALTER TABLE vef.tventa
  ADD COLUMN porcentaje_descuento NUMERIC(5);

ALTER TABLE vef.tventa
  ADD COLUMN id_vendedor_medico VARCHAR(30);

ALTER TABLE vef.tsucursal_producto
  ADD COLUMN requiere_descripcion VARCHAR(2);

ALTER TABLE vef.tsucursal
  ADD COLUMN habilitar_comisiones VARCHAR(2);

ALTER TABLE vef.tpunto_venta
  ADD COLUMN habilitar_comisiones VARCHAR(2);

ALTER TABLE vef.tpunto_venta
  ADD COLUMN codigo VARCHAR(20);

ALTER TABLE vef.tventa
  ADD COLUMN comision NUMERIC(18,2);

ALTER TABLE vef.tventa_detalle
  ADD COLUMN descripcion TEXT;

ALTER TABLE vef.tformula_detalle
  ADD COLUMN id_concepto_ingas INTEGER;

ALTER TABLE vef.tformula_detalle
  ALTER COLUMN id_item DROP NOT NULL;

ALTER TABLE vef.tsucursal
  ADD COLUMN id_lugar INTEGER;

/************************************F-SCP-JRR-VEF-0-08/11/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-19/11/2015*************************************************/

ALTER TABLE vef.tformula
  ALTER COLUMN cantidad DROP NOT NULL;

ALTER TABLE vef.tformula
  ALTER COLUMN id_unidad_medida DROP NOT NULL;

ALTER TABLE vef.tformula
  ALTER COLUMN id_medico DROP NOT NULL;

ALTER TABLE vef.tventa
  ADD COLUMN observaciones TEXT;
/************************************F-SCP-JRR-VEF-0-19/11/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-25/11/2015*************************************************/

CREATE TABLE vef.tboleto (
  id_boleto SERIAL,
  fecha DATE NOT NULL,
  id_punto_venta INTEGER NOT NULL,
  numero VARCHAR (30) NOT NULL,
  ruta VARCHAR (50) NOT NULL,
  CONSTRAINT pk_tboleto__id_boleto PRIMARY KEY(id_boleto)
) INHERITS (pxp.tbase);

CREATE TABLE vef.tboleto_fp (
  id_boleto_fp SERIAL,
  id_forma_pago INTEGER NOT NULL ,
  id_boleto INTEGER NOT NULL,
  monto NUMERIC(18,2) NOT NULL,
  CONSTRAINT pk_tboleto_fp__id_boleto_fp PRIMARY KEY(id_boleto_fp)
) INHERITS (pxp.tbase);

/************************************F-SCP-JRR-VEF-0-25/11/2015*************************************************/

/************************************I-SCP-JRR-VEF-0-19/02/2016*************************************************/

ALTER TABLE vef.tventa
  ADD COLUMN id_dosificacion INTEGER;

ALTER TABLE vef.tventa
  ADD COLUMN nro_factura INTEGER;

ALTER TABLE vef.tventa
  ADD COLUMN fecha DATE NOT NULL;

ALTER TABLE vef.tventa
  ADD COLUMN excento NUMERIC(18,2) DEFAULT 0 NOT NULL;

ALTER TABLE vef.tventa
  ADD COLUMN tipo_factura VARCHAR(20) DEFAULT 'recibo' NOT NULL;

ALTER TABLE vef.tventa
  ADD COLUMN cod_control VARCHAR(15);

CREATE TABLE vef.tpunto_venta_producto (
  id_punto_venta_producto SERIAL,
  id_punto_venta INTEGER NOT NULL,
  id_sucursal_producto INTEGER NOT NULL,
  CONSTRAINT pk_tpunto_venta_producto PRIMARY KEY(id_punto_venta_producto)
) INHERITS (pxp.tbase);

 /************************************F-SCP-JRR-VEF-0-19/02/2016*************************************************/


/************************************I-SCP-JRR-VEF-0-11/03/2016*************************************************/

ALTER TABLE vef.tpunto_venta
  ADD COLUMN tipo VARCHAR ;

ALTER TABLE vef.tsucursal_producto
  ADD COLUMN id_moneda INTEGER ;

/************************************F-SCP-JRR-VEF-0-11/03/2016*************************************************/


/************************************I-SCP-JRR-VEF-0-22/03/2016*************************************************/

CREATE TABLE vef.ttipo_venta (
  id_tipo_venta SERIAL,
  codigo VARCHAR(80) NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  codigo_relacion_contable VARCHAR(100),
  tipo_base VARCHAR(40),
  CONSTRAINT pk_ttipo_venta PRIMARY KEY(id_tipo_venta)
) INHERITS (pxp.tbase);

CREATE TABLE vef.tproceso_venta (
  id_proceso_venta SERIAL,
  estado VARCHAR(20) NOT NULL,
  fecha_desde DATE NOT NULL,
  fecha_hasta DATE NOT NULL,
  id_int_comprobante INTEGER,
  tipos VARCHAR[],
  CONSTRAINT pk_tproceso_venta PRIMARY KEY(id_proceso_venta)
) INHERITS (pxp.tbase);

/************************************F-SCP-JRR-VEF-0-22/03/2016*************************************************/

/************************************I-SCP-JRR-VEF-0-29/03/2016*************************************************/

ALTER TABLE vef.tsucursal_producto
  ADD COLUMN contabilizable VARCHAR(2) DEFAULT 'no' NOT NULL;

ALTER TABLE vef.tsucursal_producto
  ADD COLUMN excento VARCHAR(2) DEFAULT 'no' NOT NULL;

CREATE TABLE vef.tventa_boleto (
  id_venta_boleto SERIAL,
  id_venta INTEGER NOT NULL,
  id_boleto INTEGER,
  nro_boleto VARCHAR(20) NOT NULL,
  monto_moneda_susursal NUMERIC(18,2),
  CONSTRAINT pk_tventa_boleto PRIMARY KEY(id_venta_boleto)
) INHERITS (pxp.tbase);

/************************************F-SCP-JRR-VEF-0-29/03/2016*************************************************/


/************************************I-SCP-RAC-VEF-0-16/04/2016*************************************************/

--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN id_moneda INTEGER;

COMMENT ON COLUMN vef.tventa.id_moneda
IS 'moneda de la venta';


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN total_venta_msuc NUMERIC(18,2);

COMMENT ON COLUMN vef.tventa.total_venta_msuc
IS 'total venta en la moneda de la sucursal';



--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN transporte_fob NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa.transporte_fob
IS 'transporte fob para exportacion';


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN seguros_fob NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa.seguros_fob
IS 'seguros fob para exportacion';


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN otros_fob NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa.otros_fob
IS 'otros fob para exportacion';


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN transporte_cif NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa.transporte_cif
IS 'trasporte cif para exportacion';


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN seguros_cif NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa.seguros_cif
IS 'seguros cif para exportacion';



--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN otros_cif NUMERIC(18,2) DEFAULT 0 NOT NULL;


--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN cantidad TYPE NUMERIC;

--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN tipo_cambio_venta NUMERIC DEFAULT 1 NOT NULL;

COMMENT ON COLUMN vef.tventa.tipo_cambio_venta
IS 'solo si la trasaccion se define en una moneda diferetne de la base';


-------------- SQL ---------------

ALTER TABLE vef.tcliente
  ADD COLUMN direccion VARCHAR DEFAULT '' NOT NULL;

COMMENT ON COLUMN vef.tcliente.direccion
IS 'direccion del cliente';

/************************************F-SCP-RAC-VEF-0-16/04/2016*************************************************/


/************************************I-SCP-RAC-VEF-0-22/04/2016*************************************************/


--------------- SQL ---------------

CREATE TABLE vef.ttipo_descripcion (
  id_tipo_descripcion SERIAL NOT NULL,
  codigo VARCHAR(100),
  nombre VARCHAR(300),
  obs VARCHAR,
  PRIMARY KEY(id_tipo_descripcion)
) INHERITS (pxp.tbase)

WITH (oids = false);

--------------- SQL ---------------

ALTER TABLE vef.ttipo_descripcion
  ADD COLUMN columna NUMERIC(10,2) DEFAULT 1 NOT NULL;

COMMENT ON COLUMN vef.ttipo_descripcion.columna
IS 'posicion en reporte';


--------------- SQL ---------------

ALTER TABLE vef.ttipo_descripcion
  ADD COLUMN fila NUMERIC(10,2) DEFAULT 1 NOT NULL;

COMMENT ON COLUMN vef.ttipo_descripcion.fila
IS 'numeros de fila';


--------------- SQL ---------------

ALTER TABLE vef.ttipo_descripcion
  ADD COLUMN id_sucursal INTEGER;


 --------------- SQL ---------------

CREATE TABLE vef.tvalor_descripcion (
  id_valor_descripcion SERIAL NOT NULL,
  id_venta INTEGER NOT NULL,
  id_tipo_descripcion INTEGER NOT NULL,
  valor VARCHAR(300) DEFAULT '' NOT NULL,
  obs VARCHAR,
  PRIMARY KEY(id_valor_descripcion)
) INHERITS (pxp.tbase)

WITH (oids = false);


--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN bruto NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa_detalle.bruto
IS 'esto es para facturas de mineria';


--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN ley NUMERIC(18,4) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa_detalle.ley
IS 'atributo para venta de mineria';


--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN kg_fino NUMERIC(18,4) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa_detalle.kg_fino
IS 'atributo para mineria';

/************************************F-SCP-RAC-VEF-0-22/04/2016*************************************************/


/************************************I-SCP-RAC-VEF-0-29/04/2016*************************************************/



--------------- SQL ---------------

ALTER TABLE vef.tsucursal
  ADD COLUMN tipo_interfaz VARCHAR(100)[];

COMMENT ON COLUMN vef.tsucursal.tipo_interfaz
IS 'interfaces con las que puede trabajr una sucursal,  son los nombre de clase';

--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN kg_fino DROP DEFAULT;

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN kg_fino TYPE VARCHAR;

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN kg_fino SET DEFAULT 0;

  --------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN ley DROP DEFAULT;

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN ley TYPE VARCHAR;

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN ley SET DEFAULT 0;

--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN bruto DROP DEFAULT;

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN bruto TYPE VARCHAR;

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN bruto SET DEFAULT 0;

--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN id_unidad_medida INTEGER;

ALTER TABLE vef.ttipo_venta
  ADD COLUMN id_plantilla INTEGER;

ALTER TABLE vef.tsucursal
  ADD COLUMN id_depto INTEGER;


/************************************F-SCP-RAC-VEF-0-29/04/2016*************************************************/




/************************************I-SCP-RAC-VEF-0-12/05/2016*************************************************/

--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN precio TYPE NUMERIC(18,6);

--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ALTER COLUMN precio_sin_descuento TYPE NUMERIC(18,6);

ALTER TABLE vef.tsucursal
  ADD COLUMN nombre_comprobante VARCHAR ;

COMMENT ON COLUMN vef.tsucursal.nombre_comprobante
IS 'El nombre de la sucursal tal como se mostrara en el comprobante de venta. Debe incluir el nombre de la empresa';

/************************************F-SCP-JRR-VEF-0-12/05/2016*************************************************/


CREATE INDEX tdosificacion_idx ON vef.tdosificacion
  USING btree (nroaut)
  WHERE estado_reg = 'activo';



/************************************I-SCP-RAC-VEF-0-16/05/2016*************************************************/


ALTER TABLE vef.tventa
  ADD COLUMN valor_bruto NUMERIC(18,2) DEFAULT 0 NOT NULL;

COMMENT ON COLUMN vef.tventa.valor_bruto
IS 'valor de los materiales';

--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN descripcion_bulto VARCHAR(1000) DEFAULT '' NOT NULL;

COMMENT ON COLUMN vef.tventa.descripcion_bulto
IS 'descripon de bultos en exportacion';


/************************************F-SCP-RAC-VEF-0-16/05/2016*************************************************/


/************************************I-SCP-RAC-VEF-0-06/06/2016*************************************************/


--------------- SQL ---------------

ALTER TABLE vef.tvalor_descripcion
  ADD COLUMN valor_label VARCHAR(300);

/************************************F-SCP-RAC-VEF-0-06/06/2016*************************************************/

/************************************I-SCP-JRR-VEF-0-16/06/2016*************************************************/
ALTER TABLE vef.tforma_pago
  ADD COLUMN registrar_tipo_tarjeta VARCHAR(2);

ALTER TABLE vef.tforma_pago
  ALTER COLUMN registrar_tipo_tarjeta SET DEFAULT 'no';

/************************************F-SCP-JRR-VEF-0-16/06/2016*************************************************/


/************************************I-SCP-JRR-VEF-0-07/07/2016*************************************************/

CREATE TABLE vef.tapertura_cierre_caja (
  id_apertura_cierre_caja SERIAL NOT NULL,
  id_sucursal INTEGER,
  id_punto_venta INTEGER,
  id_usuario_cajero INTEGER NOT NULL,
  id_moneda INTEGER NOT NULL,
  monto_inicial INTEGER DEFAULT 0 NOT NULL,
  monto_inicial_moneda_extranjera INTEGER DEFAULT 0 NOT NULL,
  obs_cierre TEXT,
  obs_apertura TEXT,
  estado VARCHAR(50) NOT NULL,
  fecha_hora_cierre TIMESTAMP,
  fecha_apertura_cierre DATE NOT NULL,
  arqueo_moneda_local NUMERIC(18,2),
  arqueo_moneda_extranjera NUMERIC(18,2) DEFAULT 0,
  PRIMARY KEY(id_apertura_cierre_caja)
) INHERITS (pxp.tbase);


ALTER TABLE vef.tventa
  ADD COLUMN id_usuario_cajero INTEGER;

/************************************F-SCP-JRR-VEF-0-07/07/2016*************************************************/

/************************************I-SCP-JRR-VEF-0-30/09/2016*************************************************/
ALTER TABLE vef.tapertura_cierre_caja
  ALTER COLUMN monto_inicial DROP DEFAULT;

ALTER TABLE vef.tapertura_cierre_caja
  ALTER COLUMN monto_inicial TYPE NUMERIC(18,2);

ALTER TABLE vef.tapertura_cierre_caja
  ALTER COLUMN monto_inicial SET DEFAULT 0;

ALTER TABLE vef.tapertura_cierre_caja
  ALTER COLUMN monto_inicial_moneda_extranjera DROP DEFAULT;

ALTER TABLE vef.tapertura_cierre_caja
  ALTER COLUMN monto_inicial_moneda_extranjera TYPE NUMERIC(18,2);

ALTER TABLE vef.tapertura_cierre_caja
  ALTER COLUMN monto_inicial_moneda_extranjera SET DEFAULT 0;


/************************************F-SCP-JRR-VEF-0-30/09/2016*************************************************/

/************************************I-SCP-RAC-VEF-0-18/06/2016*************************************************/


ALTER TABLE vef.tcliente
  ADD COLUMN observaciones VARCHAR(255);

/************************************F-SCP-RAC-VEF-0-18/06/2016*************************************************/

/************************************I-SCP-JRR-VEF-0-16/07/2016*************************************************/

ALTER TABLE vef.tventa
  ADD COLUMN hora_estimada_entrega TIME(0) WITHOUT TIME ZONE;

ALTER TABLE vef.tformula_detalle
  ALTER COLUMN cantidad TYPE NUMERIC(18,6);

/************************************F-SCP-JRR-VEF-0-16/07/2016*************************************************/

/************************************I-SCP-JRR-VEF-0-14/08/2016*************************************************/
ALTER TABLE vef.tmedico
  ADD COLUMN especialidad VARCHAR(200);

ALTER TABLE vef.tventa
  ADD COLUMN forma_pedido VARCHAR(200);

/************************************F-SCP-JRR-VEF-0-14/08/2016*************************************************/


/************************************I-SCP-JRR-VEF-0-27/10/2016*************************************************/

ALTER TABLE vef.tsucursal_usuario
ALTER COLUMN id_sucursal DROP NOT NULL;

ALTER TABLE vef.tventa
ADD COLUMN contabilizable VARCHAR(2) DEFAULT 'si' NOT NULL;

/************************************F-SCP-JRR-VEF-0-27/10/2016*************************************************/



/************************************I-SCP-JRR-VEF-0-14/09/2016*************************************************/

ALTER TABLE vef.tventa
  ADD COLUMN nombre_factura VARCHAR(100);

ALTER TABLE vef.tventa
  ADD COLUMN nit VARCHAR(25);
/************************************F-SCP-JRR-VEF-0-14/09/2016*************************************************/

/************************************I-SCP-JRR-VEF-0-18/09/2016*************************************************/

CREATE INDEX tdosificacion_idx ON vef.tdosificacion
  USING btree (nroaut)
  WHERE estado_reg = 'activo';

/************************************F-SCP-JRR-VEF-0-18/09/2016*************************************************/




/************************************I-SCP-RAC-VEF-0-28/10/2016*************************************************/

--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN id_cliente_destino INTEGER;

COMMENT ON COLUMN vef.tventa.id_cliente_destino
IS 'identifica el cliente destino';


/************************************F-SCP-RAC-VEF-0-28/10/2016*************************************************/


/************************************I-SCP-RAC-VEF-0-31/10/2016*************************************************/

--------------- SQL ---------------

ALTER TABLE vef.tcliente
  ADD COLUMN lugar VARCHAR(500);

/************************************F-SCP-RAC-VEF-0-31/10/2016*************************************************/



/************************************I-SCP-RAC-VEF-0-11/11/2016*************************************************/



--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN estado VARCHAR(100) DEFAULT 'registrado' NOT NULL;

COMMENT ON COLUMN vef.tventa_detalle.estado
IS 'registrado, validado';

--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN obs VARCHAR;


--------------- SQL ---------------

ALTER TABLE vef.tventa_detalle
  ADD COLUMN serie VARCHAR(400) DEFAULT '' NOT NULL;

  --------------- SQL ---------------

ALTER TABLE vef.tcliente
  ADD COLUMN codigo VARCHAR(20);


/************************************F-SCP-RAC-VEF-0-11/11/2016*************************************************/



/************************************I-SCP-RAC-VEF-0-20/09/2017*************************************************/

--------------- SQL ---------------

ALTER TABLE vef.tforma_pago
  ADD COLUMN gen_cuentas_por_cobrar VARCHAR(5) DEFAULT 'no' NOT NULL;

COMMENT ON COLUMN vef.tforma_pago.gen_cuentas_por_cobrar
IS 'Si esta bandera esta habilitada la formas de pago genera cuentas por cobrar en la tabla conta.tcobro_pago';


/************************************F-SCP-RAC-VEF-0-20/09/2017*************************************************/



/***********************************I-SCP-RAC-VEF-1-19/09/2017****************************************/

--------------- SQL ---------------

COMMENT ON COLUMN vef.tventa_forma_pago.monto
IS 'importe entregado en la  moneda de la venta (puede tener cambios)';

--------------- SQL ---------------

COMMENT ON COLUMN vef.tventa_forma_pago.monto_transaccion
IS 'monto entregado en la moneda de pago, correponde al id_moneda de la forma de pago (puede tener cambios)';


--------------- SQL ---------------

COMMENT ON COLUMN vef.tventa_forma_pago.cambio
IS 'cambio a devolver en moneda de venta';


--------------- SQL ---------------

COMMENT ON COLUMN vef.tventa_forma_pago.monto_mb_efectivo
IS 'monto realmente cobrado en la moneda de la venta, sirve para generar el cbte de la venta y determinar si el total cobrado cuadra con el total vendido';


/***********************************F-SCP-RAC-VEF-1-19/09/2017****************************************/



/***********************************I-SCP-RAC-VEF-1-26/09/2017****************************************/

--------------- SQL ---------------

ALTER TABLE vef.tsucursal
  ADD COLUMN permite_externo VARCHAR(4) DEFAULT 'no' NOT NULL;

COMMENT ON COLUMN vef.tsucursal.permite_externo
IS 'si o no, si permite eterno, las facturas se inten mediante servicio, y las dosificacioens solo son refenreciales, no sirve para emitir facturas desde este sistema';


--------------- SQL ---------------

CREATE TABLE vef.tgrupo_factura (
  id_grupo_factura SERIAL NOT NULL,
  fecha_ini DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  id_depto_conta INTEGER NOT NULL,
  id_moneda INTEGER,
  id_gestion INTEGER NOT NULL,
  id_int_comprobante INTEGER,
  PRIMARY KEY(id_grupo_factura)
) INHERITS (pxp.tbase)

WITH (oids = false);


--------------- SQL ---------------

ALTER TABLE vef.tventa
  ADD COLUMN id_grupo_factura INTEGER;

COMMENT ON COLUMN vef.tventa.id_grupo_factura
IS 'indetifica el grupo usado para la generacion del comprobante';


/***********************************F-SCP-RAC-VEF-1-26/09/2017****************************************/

/***********************************I-SCP-GSS-VEF-1-10/10/2017****************************************/

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN id_auxiliar INTEGER;

ALTER TABLE vef.tforma_pago
  ADD COLUMN sw_tipo_venta VARCHAR(50) [];

ALTER TABLE vef.tforma_pago
  ADD COLUMN orden NUMERIC(8,2);

/***********************************F-SCP-RAC-VEF-1-10/10/2017****************************************/

/***********************************I-SCP-IRVA-VEF-1-09/05/2019****************************************/
ALTER TABLE vef.tventa_detalle
  ADD COLUMN id_producto INTEGER;
/***********************************F-SCP-IRVA-VEF-1-09/05/2019****************************************/

/***********************************I-SCP-IRVA-VEF-0-26/08/2019****************************************/
CREATE TABLE vef.tapertura_cierre_caja_asociada (
  id_apertura_asociada SERIAL,
  id_apertura_cierre_caja INTEGER,
  id_deposito INTEGER,
  CONSTRAINT tapertura_cierre_caja_asociada_pkey PRIMARY KEY(id_apertura_asociada),
  CONSTRAINT tapertura_cierre_caja_asociada_fk FOREIGN KEY (id_apertura_cierre_caja)
    REFERENCES vef.tapertura_cierre_caja(id_apertura_cierre_caja)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tapertura_cierre_caja_asociada_fk1 FOREIGN KEY (id_deposito)
    REFERENCES obingresos.tdeposito(id_deposito)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE vef.tapertura_cierre_caja_asociada
  OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-0-26/08/2019****************************************/

/***********************************I-SCP-IRVA-VEF-1-26/08/2019****************************************/
ALTER TABLE vef.tventa
  ADD COLUMN informe TEXT;

ALTER TABLE vef.tventa
  ADD COLUMN anulado VARCHAR(5);
/***********************************F-SCP-IRVA-VEF-1-26/08/2019****************************************/

/***********************************I-SCP-IRVA-VEF-1-31/10/2019****************************************/
ALTER TABLE vef.tventa_forma_pago
  ALTER COLUMN tipo_tarjeta TYPE VARCHAR(100) COLLATE pg_catalog."default";


ALTER TABLE vef.tformula
  ADD COLUMN punto_venta_asociado INTEGER [];

COMMENT ON COLUMN vef.tformula.punto_venta_asociado
IS 'Lista de los puntos de venta que seran asociados';


ALTER TABLE vef.tformula
  ADD COLUMN tipo_punto_venta VARCHAR(200) [];

COMMENT ON COLUMN vef.tformula.tipo_punto_venta
IS 'el tipo de punto de venta (ato,cto)';

ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN id_instancia_pago INTEGER;

ALTER TABLE vef.tventa_forma_pago
  ALTER COLUMN id_forma_pago DROP NOT NULL;

ALTER TABLE vef.tventa_forma_pago
DROP CONSTRAINT fk_tventa_forma_pago__id_forma_pago RESTRICT;

ALTER TABLE vef.tventa_forma_pago
ADD COLUMN id_moneda INTEGER;


CREATE TABLE vef.tboletos_asociados_fact (
  id_boleto_asociado SERIAL,
  id_boleto INTEGER NOT NULL,
  nro_boleto VARCHAR(200),
  fecha_emision DATE,
  pasajero VARCHAR(200),
  nit VARCHAR(200),
  ruta VARCHAR(200),
  razon VARCHAR(200),
  id_venta INTEGER NOT NULL,
  CONSTRAINT tboletos_asociados_fact_pkey PRIMARY KEY(id_boleto_asociado)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN vef.tboletos_asociados_fact.id_boleto
IS 'Boleto de la tabla obingresos.tboleto';

COMMENT ON COLUMN vef.tboletos_asociados_fact.id_venta
IS 'Factura de la tabla vef.tventa';

ALTER TABLE vef.tboletos_asociados_fact
  OWNER TO postgres;

/***********************************F-SCP-IRVA-VEF-1-31/10/2019****************************************/

/***********************************I-SCP-IRVA-VEF-1-7/11/2019****************************************/
CREATE TABLE vef.trespaldo_facturas_anuladas (
  id_factura_anulada SERIAL,
  id_venta INTEGER NOT NULL,
  nombre_factura VARCHAR(200),
  nit VARCHAR(200),
  cod_control VARCHAR(200),
  id_dosificacion INTEGER,
  nro_autorizacion VARCHAR(200),
  num_factura INTEGER,
  total_venta NUMERIC(20,2),
  total_venta_msuc NUMERIC(20,2),
  id_sucursal INTEGER,
  id_cliente INTEGER,
  id_punto_venta INTEGER,
  observaciones TEXT,
  id_moneda INTEGER,
  excento NUMERIC(20,2),
  fecha DATE,
  id_sucursal_producto INTEGER,
  id_formula INTEGER,
  id_producto INTEGER,
  cantidad INTEGER,
  precio NUMERIC(20,2),
  tipo VARCHAR(200),
  descripcion VARCHAR(400),
  id_forma_pago INTEGER,
  monto NUMERIC(20,2),
  monto_transaccion NUMERIC(20,2),
  monto_mb_efectivo NUMERIC(20,2),
  numero_tarjeta VARCHAR(30),
  codigo_tarjeta VARCHAR(50),
  tipo_tarjeta VARCHAR(200),
  id_auxiliar INTEGER,
  CONSTRAINT trespaldo_facturas_anuladas_pkey PRIMARY KEY(id_factura_anulada)
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE vef.trespaldo_facturas_anuladas
  OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-1-7/11/2019****************************************/

/***********************************I-SCP-IRVA-VEF-1-8/11/2019****************************************/
CREATE TABLE vef.tventa_forma_pago_log (
  id_venta_forma_pago INTEGER,
  id_forma_pago INTEGER,
  id_venta INTEGER NOT NULL,
  monto NUMERIC(18,2) NOT NULL,
  monto_transaccion NUMERIC(18,2) NOT NULL,
  cambio NUMERIC(18,2) NOT NULL,
  monto_mb_efectivo NUMERIC(18,2) NOT NULL,
  numero_tarjeta VARCHAR(25),
  codigo_tarjeta VARCHAR(25),
  tipo_tarjeta VARCHAR(100),
  id_auxiliar INTEGER,
  id_instancia_pago INTEGER,
  id_moneda INTEGER,
  "current_user" VARCHAR(200) DEFAULT "current_user"(),
  accion VARCHAR(200) NOT NULL
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE vef.tventa_forma_pago_log
  OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-1-8/11/2019****************************************/

/***********************************I-SCP-IRVA-VEF-0-11/12/2019****************************************/
ALTER TABLE vef.tsucursal
  ADD COLUMN estado_sucursal VARCHAR(50);

ALTER TABLE vef.tsucursal
  ALTER COLUMN estado_sucursal SET DEFAULT 'abierto';
/***********************************F-SCP-IRVA-VEF-0-11/12/2019****************************************/

/***********************************I-SCP-IRVA-VEF-0-23/05/2020****************************************/
CREATE TYPE vef.detalle_venta AS (
  id_concepto INTEGER,
  precio NUMERIC(19,2)
);
/***********************************F-SCP-IRVA-VEF-0-23/05/2020****************************************/

/***********************************I-SCP-IRVA-VEF-0-24/08/2020****************************************/
ALTER TABLE vef.tventa
  ADD COLUMN id_deposito INTEGER;

COMMENT ON COLUMN vef.tventa.id_deposito
IS 'Este campo para obtener la relacion con los depositos';

ALTER TABLE vef.tventa
  ADD COLUMN formato_factura_emitida VARCHAR(100);

COMMENT ON COLUMN vef.tventa.formato_factura_emitida
IS 'Almacenamos el formato de la factura con la que se emitio al pasajero';

ALTER TABLE vef.tventa
  ADD COLUMN enviar_correo VARCHAR(5);

COMMENT ON COLUMN vef.tventa.enviar_correo
IS 'Si se envio el correo';

ALTER TABLE vef.tventa
  ADD COLUMN correo_electronico VARCHAR(200);

COMMENT ON COLUMN vef.tventa.correo_electronico
IS 'Campo para registrar el correo donde se enviara la factura';

ALTER TABLE vef.tventa
  ADD COLUMN id_moneda_venta_recibo INTEGER;

ALTER TABLE vef.tventa
  ADD COLUMN id_auxiliar_anticipo INTEGER;

COMMENT ON COLUMN vef.tventa.id_auxiliar_anticipo
IS 'Auxiliar relacionado al deposito para los conceptos del tipo anticipo';
/***********************************F-SCP-IRVA-VEF-0-24/08/2020****************************************/
/***********************************I-SCP-IRVA-VEF-0-25/08/2020****************************************/
ALTER TABLE vef.tventa
  ADD COLUMN id_dosificacion_ro INTEGER;

  CREATE TABLE vef.tdosificacion_ro (
    id_dosificacion_ro SERIAL,
    tipo VARCHAR(50) NOT NULL,
    id_sucursal INTEGER NOT NULL,
    tipo_generacion VARCHAR(50) NOT NULL,
    inicial INTEGER,
    final INTEGER,
    fecha_dosificacion DATE NOT NULL,
    fecha_inicio_emi DATE,
    fecha_limite DATE,
    nro_siguiente INTEGER,
    CONSTRAINT pk_tdosificacion__id_dosificacion_ro PRIMARY KEY(id_dosificacion_ro),
    CONSTRAINT fk_tdosificacion__id_sucursal FOREIGN KEY (id_sucursal)
      REFERENCES vef.tsucursal(id_sucursal)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
      NOT DEFERRABLE
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  COMMENT ON COLUMN vef.tdosificacion_ro.tipo
  IS 'recibo';

  COMMENT ON COLUMN vef.tdosificacion_ro.tipo_generacion
  IS 'manual|computarizada';

  ALTER TABLE vef.tdosificacion_ro
    OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-0-25/08/2020****************************************/
/***********************************I-SCP-IRVA-VEF-0-15/09/2020****************************************/
ALTER TABLE vef.tventa
  ALTER COLUMN anulado TYPE VARCHAR(20) COLLATE pg_catalog."default";
/***********************************F-SCP-IRVA-VEF-0-15/09/2020****************************************/

/***********************************I-SCP-IRVA-VEF-0-15/11/2020****************************************/
ALTER TABLE vef.tsucursal
  ADD COLUMN enviar_correo VARCHAR(5);

ALTER TABLE vef.tsucursal
  ALTER COLUMN enviar_correo SET DEFAULT 'no'::CHARACTER VARYING;
/***********************************F-SCP-IRVA-VEF-0-15/11/2020****************************************/

/***********************************I-SCP-IRVA-VEF-0-16/11/2020****************************************/
ALTER TABLE vef.tventa_forma_pago
  RENAME COLUMN id_instancia_pago TO id_medio_pago;

ALTER TABLE vef.tformula
  DROP COLUMN punto_venta_asociado;

ALTER TABLE vef.tformula
  DROP COLUMN tipo_punto_venta;

ALTER TABLE vef.tformula
  ADD COLUMN sw_autorizacion VARCHAR(200) [];

COMMENT ON COLUMN vef.tformula.sw_autorizacion
IS 'Permisos para RO, Facturas, etc';

ALTER TABLE vef.tformula
  ADD COLUMN regionales VARCHAR(200) [];

COMMENT ON COLUMN vef.tformula.regionales
IS 'Permisos Nivel Regional MIA,BOL';

/***********************************F-SCP-IRVA-VEF-0-16/11/2020****************************************/
/***********************************I-SCP-IRVA-VEF-0-26/11/2020****************************************/
COMMENT ON COLUMN vef.tventa_forma_pago.numero_tarjeta
IS 'Numero de la tarjeta de Credito';

COMMENT ON COLUMN vef.tventa_forma_pago.codigo_tarjeta
IS 'Codigo de Autorizacion de la Tarjeta de Credito';

COMMENT ON COLUMN vef.tventa_forma_pago.id_auxiliar
IS 'Id de la cuenta corriente';

COMMENT ON COLUMN vef.tventa_forma_pago.id_medio_pago
IS 'Remplazo del campo id_forma_pago ahora se usan los medios de pago';

COMMENT ON COLUMN vef.tventa_forma_pago.id_moneda
IS 'Tipo de moneda con la que se realizo el pago ejemplo (BOB(1),USD(2),etc)';

COMMENT ON COLUMN vef.tventa_detalle.id_formula
IS 'Almacena el Id de los paquetes en caso que se seleccione un paquete (Este campo va relacionado a la tabla vef.tformula)';

COMMENT ON COLUMN vef.tventa_detalle.tipo
IS 'Este campo define que tipo de registro se usa para los conceptos ejemplo (formula, servicio, o producto)';

COMMENT ON COLUMN vef.tventa_detalle.descripcion
IS 'Almacena la descripcion del concepto si este lo requiere';

COMMENT ON COLUMN vef.tventa_detalle.id_producto
IS 'Aqui almacenamos el id_concepto_ingas';

COMMENT ON COLUMN vef.tventa_detalle.monto_descuento
IS 'Si el concepto tiene un descuento';

COMMENT ON COLUMN vef.tventa_detalle.llave_unica
IS 'Llave unica para no repetir conceptos mediante servicio';
/***********************************F-SCP-IRVA-VEF-0-26/11/2020****************************************/
/***********************************I-SCP-IRVA-VEF-0-08/12/2020****************************************/
ALTER TABLE vef.tdosificacion
  ALTER COLUMN llave TYPE VARCHAR(700) COLLATE pg_catalog."default";

ALTER TABLE vef.tdosificacion
  ALTER COLUMN glosa_impuestos TYPE VARCHAR(700) COLLATE pg_catalog."default";

ALTER TABLE vef.tdosificacion
  ALTER COLUMN glosa_empresa TYPE VARCHAR(700) COLLATE pg_catalog."default";

ALTER TABLE vef.tdosificacion
  ALTER COLUMN nroaut TYPE VARCHAR(300) COLLATE pg_catalog."default";

ALTER TABLE vef.tdosificacion
  ALTER COLUMN nro_tramite TYPE VARCHAR(300) COLLATE pg_catalog."default";
/***********************************F-SCP-IRVA-VEF-0-08/12/2020****************************************/

/***********************************I-SCP-IRVA-VEF-0-10/12/2020****************************************/
  CREATE TABLE vef.tpermiso_sucursales (
    id_autorizacion SERIAL,
    id_funcionario INTEGER,
    CONSTRAINT tpermiso_gerencias_id_funcionario_key UNIQUE(id_funcionario),
    CONSTRAINT tpermiso_gerencias_pkey PRIMARY KEY(id_autorizacion)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE vef.tpermiso_sucursales
    OWNER TO postgres;

  ALTER TABLE vef.trespaldo_facturas_anuladas
    RENAME COLUMN id_forma_pago TO id_medio_pago;
/***********************************F-SCP-IRVA-VEF-0-10/12/2020****************************************/
/***********************************I-SCP-IRVA-VEF-0-18/12/2020****************************************/
ALTER TABLE vef.tformula
  ADD COLUMN nivel_permiso VARCHAR(200) [];
/***********************************F-SCP-IRVA-VEF-0-18/12/2020****************************************/
/***********************************I-SCP-IRVA-VEF-0-21/12/2020****************************************/
ALTER TABLE vef.tventa
  ADD COLUMN excento_verificado VARCHAR(2);
/***********************************F-SCP-IRVA-VEF-0-21/12/2020****************************************/

/***********************************I-SCP-MAY-VEF-0-22/12/2020****************************************/
CREATE TABLE vef.tnits_no_considerados (
  id_nits_no_considerados SERIAL,
  nit_ci VARCHAR(800),
  razon_social VARCHAR(800),
  t_contr VARCHAR(500),
  incl_rep VARCHAR(500),
  observaciones VARCHAR(800),
  CONSTRAINT tnits_no_considerados_pkey PRIMARY KEY(id_nits_no_considerados)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN vef.tnits_no_considerados.id_nits_no_considerados
IS 'identificador tabla';

COMMENT ON COLUMN vef.tnits_no_considerados.nit_ci
IS 'NIT/CI/CUIT';

COMMENT ON COLUMN vef.tnits_no_considerados.razon_social
IS 'Nombre/Razon Social';

COMMENT ON COLUMN vef.tnits_no_considerados.t_contr
IS 'N/S/G';

COMMENT ON COLUMN vef.tnits_no_considerados.incl_rep
IS 'S/M';

ALTER TABLE vef.tnits_no_considerados
  OWNER TO postgres;
/***********************************F-SCP-MAY-VEF-0-22/12/2020****************************************/

/***********************************I-SCP-BVP-VEF-0-23/12/2020****************************************/
ALTER TABLE vef.tventa
  ALTER COLUMN excento_verificado SET DEFAULT 'no';
/***********************************F-SCP-BVP-VEF-0-23/12/2020****************************************/
/***********************************I-SCP-BVP-VEF-0-28/12/2020****************************************/
ALTER TABLE vef.tventa
  ADD COLUMN id_formula INTEGER;

COMMENT ON COLUMN vef.tventa.id_formula
IS 'Id de Formula de paquete';
/***********************************F-SCP-BVP-VEF-0-28/12/2020****************************************/
/***********************************I-SCP-BVP-VEF-0-13/01/2021****************************************/
CREATE TABLE vef.tcomision (
  id_venta_comision SERIAL,
  id_venta INTEGER,
  comision VARCHAR(10),
  importe NUMERIC(18,2),
  porcomis NUMERIC(18,2),
  renglon INTEGER,
  CONSTRAINT tcomision_pkey PRIMARY KEY(id_venta_comision)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN vef.tcomision.comision
IS 'Codigo comision';

COMMENT ON COLUMN vef.tcomision.importe
IS 'Importe por calculo';

COMMENT ON COLUMN vef.tcomision.porcomis
IS 'Porcentaje comision';

COMMENT ON COLUMN vef.tcomision.renglon
IS 'Orden de registro';

ALTER TABLE vef.tcomision
  OWNER TO postgres;
/***********************************F-SCP-BVP-VEF-0-13/01/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-18/01/2021****************************************/
ALTER TABLE vef.tboletos_asociados_fact
  ALTER COLUMN id_boleto DROP NOT NULL;
/***********************************F-SCP-IRVA-VEF-0-18/01/2021****************************************/

/***********************************I-SCP-MAY-VEF-0-28/01/2021****************************************/
ALTER TABLE vef.tapertura_cierre_caja
  ADD COLUMN id_apertura_cierre_admin INTEGER;

COMMENT ON COLUMN vef.tapertura_cierre_caja.id_apertura_cierre_admin
IS 'registra el id_apertura_cierre_admin (administrativo)';


/***********************************F-SCP-MAY-VEF-0-28/01/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-04/02/2021****************************************/
CREATE TABLE vef.tacumulacion_comisionistas (
  nit VARCHAR(100),
  razon_social TEXT,
  id_periodo INTEGER,
  fecha_ini DATE,
  fecha_fin DATE,
  total_acumulado NUMERIC(18,2),
  natural_simplificado VARCHAR(2),
  id_gestion INTEGER,
  estado VARCHAR(100)
)
WITH (oids = false);

COMMENT ON COLUMN vef.tacumulacion_comisionistas.nit
IS 'Almacenamos el nit del Cliente';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.razon_social
IS 'Razon Social del Cliente';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.id_periodo
IS 'id del periodo al que corresponde la venta';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.fecha_ini
IS 'Fecha Inicio del periodo';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.fecha_fin
IS 'fecha fin del periodo';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.total_acumulado
IS 'Total acumulado por nit desde inicio del periodo hasta la fecha';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.natural_simplificado
IS 'N Natural, S simplificado';

COMMENT ON COLUMN vef.tacumulacion_comisionistas.estado
IS 'Estado abierto, cerrado';

CREATE INDEX tacumulacion_comisionistas_id_periodo ON vef.tacumulacion_comisionistas
  USING btree (id_periodo);

CREATE INDEX tacumulacion_comisionistas_idx ON vef.tacumulacion_comisionistas
  USING btree (id_gestion);

CREATE INDEX tacumulacion_comisionistas_nit ON vef.tacumulacion_comisionistas
  USING btree (nit COLLATE pg_catalog."default");

ALTER TABLE vef.tacumulacion_comisionistas
  OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-0-04/02/2021****************************************/
/***********************************I-SCP-BVP-VEF-0-08/02/2021****************************************/
ALTER TABLE vef.tpunto_venta
  ADD COLUMN nombre_amadeus VARCHAR;

COMMENT ON COLUMN vef.tpunto_venta.nombre_amadeus
IS 'Nombre punto de venta segun registro Amadeus.';

ALTER TABLE vef.tpunto_venta
  ADD COLUMN office_id VARCHAR;

COMMENT ON COLUMN vef.tpunto_venta.office_id
IS 'Codigo Office ID.';

ALTER TABLE vef.tpunto_venta
  ADD COLUMN iata_status VARCHAR(5);

ALTER TABLE vef.tpunto_venta
  ADD COLUMN id_catalogo INTEGER;

COMMENT ON COLUMN vef.tpunto_venta.id_catalogo
IS 'osd agencia';

ALTER TABLE vef.tpunto_venta
  ADD COLUMN id_catalogo_canal INTEGER;

COMMENT ON COLUMN vef.tpunto_venta.id_catalogo_canal
IS 'Catalogo canal de venta.';



CREATE TABLE vef.tfiltro (
  id_filtro SERIAL NOT NULL,
  codigo VARCHAR,
  descripcion VARCHAR,
  tipo VARCHAR,
  PRIMARY KEY(id_filtro)
)
WITH (oids = false);

ALTER TABLE vef.tfiltro
  OWNER TO postgres;
/***********************************F-SCP-BVP-VEF-0-08/02/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-17/03/2021****************************************/
ALTER TABLE vef.tventa_forma_pago
  DROP CONSTRAINT fk_tventa_forma_pago__id_venta RESTRICT;

ALTER TABLE vef.tventa_forma_pago
ADD CONSTRAINT fk_tventa_forma_pago__id_venta FOREIGN KEY (id_venta)
REFERENCES vef.tventa(id_venta)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE vef.tventa_detalle
ADD CONSTRAINT tventa_detalle_id_venta_fk FOREIGN KEY (id_venta)
REFERENCES vef.tventa(id_venta)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE vef.tboletos_asociados_fact
ADD CONSTRAINT tboletos_asociados_fact_fk FOREIGN KEY (id_venta)
REFERENCES vef.tventa(id_venta)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

CREATE TABLE vef.tdata_carga (
  nro_factura VARCHAR(200),
  num_autorizacion VARCHAR(200)
)
WITH (oids = false);

ALTER TABLE vef.tdata_carga
OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-0-17/03/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-18/03/2021****************************************/
CREATE TABLE vef.tdatos_carga_recibido (
  id_sistema_origen INTEGER,
  fecha VARCHAR(100),
  nro_factura VARCHAR(200),
  nro_autorizacion VARCHAR(200),
  nit VARCHAR(200),
  razon_social VARCHAR(200),
  importe_total NUMERIC(18,2),
  codigo_control VARCHAR(200),
  tipo_factura VARCHAR(200),
  moneda VARCHAR(5),
  codigo_punto_venta VARCHAR(200),
  id_funcionario INTEGER,
  observaciones TEXT,
  json_venta_forma_pago TEXT
)
WITH (oids = false);

COMMENT ON TABLE vef.tdatos_carga_recibido
IS 'Tabla que almacenara los datos que nos llega del servicio para tener respaldo';

ALTER TABLE vef.tdatos_carga_recibido
  OWNER TO postgres;

CREATE TYPE vef.medio_pago_venta AS (
  importe NUMERIC(18,2),
  moneda VARCHAR(10),
  numero_tarjeta VARCHAR(25),
  codigo_tarjeta VARCHAR(10),
  cod_auxiliar VARCHAR(50),
  cod_medio_pago VARCHAR(30)
);

ALTER TYPE vef.medio_pago_venta
  OWNER TO postgres;


ALTER TABLE vef.tventa
ADD COLUMN id_sistema_origen INTEGER;

COMMENT ON COLUMN vef.tventa.id_sistema_origen
IS 'id del sistema de origen de donde replica al erp';



CREATE TABLE vef.tfacturas_carga_observadas (
  id_factura_pendiente SERIAL,
  id_venta INTEGER,
  id_funcionario INTEGER,
  estado VARCHAR(20) DEFAULT 'observado'::character varying,
  observacion TEXT,
  CONSTRAINT tfacturas_carga_observadas_pkey PRIMARY KEY(id_factura_pendiente)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON TABLE vef.tfacturas_carga_observadas
IS 'Tabla donde se ira almacenando las facturas de Carga que no tienen el id_usuario para regularizar
';

COMMENT ON COLUMN vef.tfacturas_carga_observadas.id_venta
IS 'Campo para relacionar a que Venta pertenece';

COMMENT ON COLUMN vef.tfacturas_carga_observadas.id_funcionario
IS 'Campo para almacenar el id_funcionario que nos llega mediante el servicio';

COMMENT ON COLUMN vef.tfacturas_carga_observadas.estado
IS 'Nos sirve para saber que factruas han sido regularizadas o cuales estan pendientes los estados a usar son (observado, replicado)';

COMMENT ON COLUMN vef.tfacturas_carga_observadas.observacion
IS 'Descripcion del posible incidente';

ALTER TABLE vef.tfacturas_carga_observadas
  OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-0-18/03/2021****************************************/
/***********************************I-SCP-BVP-VEF-0-19/03/2021****************************************/
CREATE TABLE vef.tstage_punto_venta (
  stage_id_pv INTEGER,
  iata_area VARCHAR,
  iata_zone VARCHAR,
  iata_zone_name VARCHAR,
  country_code VARCHAR,
  country_name VARCHAR,
  city_code VARCHAR,
  city_name VARCHAR,
  accounting_station VARCHAR,
  sale_type VARCHAR,
  sale_channel VARCHAR,
  tipo_pos VARCHAR,
  iata_code VARCHAR,
  iata_status VARCHAR,
  osd VARCHAR,
  office_id VARCHAR,
  gds VARCHAR,
  nit VARCHAR,
  name_pv VARCHAR,
  address VARCHAR,
  phone_number VARCHAR,
  id_stage_pv SERIAL,
  id_punto_venta_erp INTEGER,
  CONSTRAINT tstage_punto_venta_pkey PRIMARY KEY(id_stage_pv)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN vef.tstage_punto_venta.stage_id_pv
IS 'id de registro de punto de venta stage';

COMMENT ON COLUMN vef.tstage_punto_venta.id_punto_venta_erp
IS 'id de registro del punto de venta del ERP';

ALTER TABLE vef.tstage_punto_venta
  OWNER TO postgres;
/***********************************F-SCP-BVP-VEF-0-19/03/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-25/03/2021****************************************/
ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN nro_mco VARCHAR(20);

  ALTER TABLE vef.tventa_forma_pago_log
    ADD COLUMN nro_mco VARCHAR(20);

/***********************************F-SCP-IRVA-VEF-0-25/03/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-31/03/2021****************************************/
ALTER TABLE vef.trespaldo_facturas_anuladas
  ADD COLUMN nro_mco VARCHAR(20);
/***********************************F-SCP-IRVA-VEF-0-31/03/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-01/04/2021****************************************/
CREATE TABLE vef.tfacturas_pendientes_carga_anuladas (
  fecha DATE,
  nro_factura VARCHAR(200),
  estado VARCHAR(100),
  nit VARCHAR(200),
  razon_social VARCHAR(200),
  importe_total NUMERIC(18,2),
  id_funcionario INTEGER,
  id_origen INTEGER,
  codigo_control VARCHAR(200),
  nro_autorizacion VARCHAR(500),
  moneda VARCHAR(20),
  cod_medio_pago VARCHAR(200),
  cod_auxiliar VARCHAR(200),
  cod_punto_venta VARCHAR(200)
)
WITH (oids = false);

ALTER TABLE vef.tfacturas_pendientes_carga_anuladas
  OWNER TO postgres;


CREATE TABLE vef.tfacturas_pendientes_carga_validas (
  fecha DATE,
  nro_factura VARCHAR(200),
  estado VARCHAR(100),
  nit VARCHAR(200),
  razon_social VARCHAR(200),
  importe_total NUMERIC(18,2),
  id_funcionario INTEGER,
  id_origen INTEGER,
  codigo_control VARCHAR(200),
  nro_autorizacion VARCHAR(500),
  moneda VARCHAR(20),
  cod_medio_pago VARCHAR(200),
  cod_auxiliar VARCHAR(200),
  cod_punto_venta VARCHAR(200)
)
WITH (oids = false);

ALTER TABLE vef.tfacturas_pendientes_carga_validas
  OWNER TO postgres;
/***********************************F-SCP-IRVA-VEF-0-01/04/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-17/04/2021****************************************/
ALTER TABLE vef.tdosificacion
  ADD COLUMN caracteristica VARCHAR(200);
COMMENT ON COLUMN vef.tdosificacion.caracteristica
IS 'En las dosificaciones para exportacion se utiliza este campo';

ALTER TABLE vef.tdosificacion
  ADD COLUMN titulo VARCHAR(200);
COMMENT ON COLUMN vef.tdosificacion.titulo
IS 'Se utiliza en facturacion de exportacion y en Factura Normal';

ALTER TABLE vef.tdosificacion
  ADD COLUMN subtitulo VARCHAR(200);
COMMENT ON COLUMN vef.tdosificacion.subtitulo
IS 'Se utiliza en Facturacion de Exportacion';


ALTER TABLE vef.tdosificacion
  ALTER COLUMN titulo SET NOT NULL;
/***********************************F-SCP-IRVA-VEF-0-17/04/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-18/04/2021****************************************/
ALTER TABLE vef.tventa_forma_pago
  ADD CONSTRAINT tventa_forma_pago_fk FOREIGN KEY (id_medio_pago)
    REFERENCES obingresos.tmedio_pago_pw(id_medio_pago_pw)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;


ALTER TABLE vef.tventa_forma_pago
  ADD CONSTRAINT tventa_forma_pago_fk1 FOREIGN KEY (id_moneda)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;


ALTER TABLE vef.tventa_forma_pago
	ADD CONSTRAINT tventa_forma_pago_fk1 FOREIGN KEY (id_auxiliar)
	REFERENCES conta.tauxiliar(id_auxiliar)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;



ALTER TABLE vef.tventa_detalle
  ADD CONSTRAINT tventa_detalle_fk FOREIGN KEY (id_producto)
    REFERENCES param.tconcepto_ingas(id_concepto_ingas)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;
/***********************************F-SCP-IRVA-VEF-0-18/04/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-19/04/2021****************************************/
ALTER TABLE vef.tventa_forma_pago_log
  RENAME COLUMN id_medio_pago TO id_instancia_pago;
/***********************************F-SCP-IRVA-VEF-0-19/04/2021****************************************/
/***********************************I-SCP-BVP-VEF-0-28/04/2021****************************************/
ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN id_venta_recibo INTEGER;

ALTER TABLE vef.tventa_forma_pago
  ADD CONSTRAINT tventa_forma_pago_fk3 FOREIGN KEY (id_venta_recibo)
    REFERENCES vef.tventa(id_venta)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;
/***********************************F-SCP-BVP-VEF-0-28/04/2021****************************************/
/***********************************I-SCP-IRVA-VEF-0-30/04/2021****************************************/
ALTER TABLE vef.trespaldo_facturas_anuladas
  ADD COLUMN id_venta_recibo INTEGER;
/***********************************F-SCP-IRVA-VEF-0-30/04/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-01/05/2021****************************************/
ALTER TABLE vef.tventa
  ALTER COLUMN correlativo_venta DROP DEFAULT;

ALTER TABLE vef.tventa
  ALTER COLUMN correlativo_venta TYPE VARCHAR(200) COLLATE pg_catalog."default";

ALTER TABLE vef.tventa
  ALTER COLUMN correlativo_venta SET DEFAULT ''::character varying;
/***********************************F-SCP-IRVA-VEF-0-01/05/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-03/05/2021****************************************/
ALTER TABLE vef.tdetalle_apertura_cc
  ADD COLUMN monto_otro_boleto_bs NUMERIC(18,2);

ALTER TABLE vef.tdetalle_apertura_cc
  ADD COLUMN monto_otro_boleto_usd NUMERIC(18,2);

 ALTER TABLE vef.tdetalle_apertura_cc
  ADD COLUMN monto_otro_recibo_ml NUMERIC(18,2);

 ALTER TABLE vef.tdetalle_apertura_cc
  ADD COLUMN monto_otro_recibo_me NUMERIC(18,2);

  ALTER TABLE vef.tdetalle_apertura_cc
  ADD COLUMN monto_otro_facturacion_bs NUMERIC(18,2);

 ALTER TABLE vef.tdetalle_apertura_cc
  ADD COLUMN monto_otro_facturacion_usd NUMERIC(18,2);
  /***********************************F-SCP-IRVA-VEF-0-03/05/2021****************************************/

/***********************************I-SCP-BVP-VEF-0-04/05/2021****************************************/
ALTER TABLE vef.tventa_forma_pago_log
  ADD COLUMN id_venta_recibo INTEGER;
/***********************************F-SCP-BVP-VEF-0-04/05/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-07/05/2021****************************************/

ALTER TABLE vef.tventa
  ADD COLUMN direccion_cliente TEXT;

COMMENT ON COLUMN vef.tventa.direccion_cliente
IS 'Almacenamos la direccion del cliente';


ALTER TABLE vef.tventa_forma_pago
  ADD COLUMN monto_dolar_efectivo NUMERIC(18,2);

COMMENT ON COLUMN vef.tventa_forma_pago.monto_dolar_efectivo
IS 'Monto donde se almacena el total de la transacccion en dolar';
/***********************************F-SCP-IRVA-VEF-0-07/05/2021****************************************/

/***********************************I-SCP-BVP-VEF-0-02/06/2021****************************************/
ALTER TABLE vef.tventa
  ADD COLUMN nro_pnr VARCHAR(20);

COMMENT ON COLUMN vef.tventa.nro_pnr
IS 'PNR de reserva de vuelo, recibo oficial inicial.';
/***********************************F-SCP-BVP-VEF-0-02/06/2021****************************************/

/***********************************I-SCP-IRVA-VEF-0-14/06/2021****************************************/
ALTER TYPE vef.detalle_venta
  RENAME ATTRIBUTE precio TO precio_unitario;

ALTER TYPE vef.detalle_venta
  ADD ATTRIBUTE cantidad INTEGER,
  ADD ATTRIBUTE desc_ingas TEXT,
  ADD ATTRIBUTE id_liquidacion INTEGER;
/***********************************F-SCP-IRVA-VEF-0-14/06/2021****************************************/


/***********************************I-SCP-IRVA-VEF-0-05/07/2021****************************************/
CREATE TYPE vef.detalle_venta_carga AS (
 cantidad NUMERIC,
 precio_unitario NUMERIC(18,2),
 concepto VARCHAR(200)
);
/***********************************F-SCP-IRVA-VEF-0-05/07/2021****************************************/
