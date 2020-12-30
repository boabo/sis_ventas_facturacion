CREATE OR REPLACE FUNCTION vef.ft_venta_detalle_facturacion_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_detalle_facturacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa_detalle_facturacion'
 AUTOR: 		 (ivaldivia)
 FECHA:	        10-05-2019 19:33:22
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				10-05-2019 19:33:22								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa_detalle_facturacion'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_venta_detalle	integer;

    venta_total				numeric;

    v_id_formula				integer;
    v_id_sucursal_producto		integer;
    v_id_item					integer;
    v_porcentaje_descuento		integer;
    v_id_vendedor				integer;
    v_id_medico					integer;
    v_descripcion				varchar;
    v_bruto						varchar;

    v_ley						varchar;
    v_kg_fino					varchar;
    v_total						numeric;
    v_id_unidad_medida			integer;
    v_id_boleto					integer;
    v_registros					record;
    v_tmp						record;
    v_concepto_ingas			integer;
    v_total_venta				numeric;
    v_id_venta					integer;
	v_id_concepto_ingas			integer;

    v_excento					numeric;
    v_tipo_cambio				numeric;
    v_precio					numeric;
    v_existe_excento			integer;
    v_tiene_excento				varchar;
    v_cantidad					integer;
    v_cantidad_exe				integer;
    v_cantidad_ace				integer;
BEGIN

    v_nombre_funcion = 'vef.ft_venta_detalle_facturacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FACTDETIND_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	if(p_transaccion='VF_FACTDETIND_INS')then

        begin


        	--Sentencia de la insercion
        	insert into vef.tventa_detalle(
			id_venta,
			descripcion,
			cantidad,
			tipo,
			estado_reg,
			id_producto,
			--id_sucursal_producto,
			precio,
            id_usuario_reg,
            fecha_reg

          	) values(
			v_parametros.id_venta,
			v_parametros.descripcion,
			v_parametros.cantidad_det,
			v_parametros.tipo,
			'activo',
            v_parametros.id_producto,
			--v_parametros.id_producto,
			v_parametros.precio,
            p_id_usuario,
            now()

			)RETURNING id_venta_detalle into v_id_venta_detalle;

            /*Verificamos si el concepto es contabilizable para no mezclar*/

       	select count(distinct inga.contabilizable),
          count(distinct inga.excento),
          count(distinct inga.id_actividad_economica)
         into v_cantidad,v_cantidad_exe, v_cantidad_ace
        from vef.tventa_detalle det
        inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_producto
        where det.id_venta = v_parametros.id_venta;

        if (v_cantidad_exe > 1) then
          raise exception 'No puede utilizar conceptos con exentos si y exentos no, en la misma venta';
        end if;

        if(v_cantidad_ace > 1)then
          raise exception 'No puede utilizar conceptos de diferente actividad economica en la misma venta';
        end if;

        if (v_cantidad > 1) then
          raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
        else

          update vef.tventa set contabilizable =
           (
                          select distinct(sp.contabilizable)
                          from vef.tventa_detalle vd
                            inner join param.tconcepto_ingas sp on sp.id_concepto_ingas = vd.id_producto
                          where vd.id_venta = v_parametros.id_venta)

          where id_venta = v_parametros.id_venta;

        end if;

        /******************************************************************/

            select sum(ven.precio * ven.cantidad) into v_total_venta
              from vef.tventa_detalle ven
              where  ven.id_venta = v_parametros.id_venta;

            /*if (v_parametros.excento is null) then
            	v_excento = 0;
            else
            	v_excento = v_parametros.excento;
            end if;  */

              update vef.tventa set
                total_venta = v_total_venta,
                total_venta_msuc = v_total_venta
              where id_venta = v_parametros.id_venta;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion almacenado(a) con exito (id_venta_detalle'||v_id_venta_detalle||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_id_venta_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
     #TRANSACCION:  'VF_FACTDET_INS'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		admin
     #FECHA:		01-06-2015 09:21:07
    ***********************************/

    elsif(p_transaccion='VF_FACTDET_INS')then

      begin

        if (v_parametros.tipo = 'formula') then
          v_id_formula = v_parametros.id_formula;
          v_id_concepto_ingas = v_parametros.id_producto;
        elsif (v_parametros.tipo = 'servicio' or
               (v_parametros.tipo = 'producto_terminado' and pxp.f_get_variable_global('vef_integracion_almacenes') = 'false'))then

              /* if (pxp.f_existe_parametro(p_tabla,'id_sucursal_producto')) then
                  v_id_sucursal_producto = v_parametros.id_sucursal_producto;

                  select suc.id_concepto_ingas into v_id_concepto_ingas
                  from vef.tsucursal_producto suc
                  where suc.id_sucursal_producto = v_parametros.id_sucursal_producto;

               else*/
                  v_id_concepto_ingas = v_parametros.id_producto;

                /*select suc.id_concepto_ingas into v_id_concepto_ingas
                from vef.tsucursal_producto suc
                where suc.id_sucursal_producto = v_parametros.id_producto;

               end if;                */


        else
          v_id_item =  v_parametros.id_producto;
        end if;

        v_porcentaje_descuento = 0;
        --verificar si existe porcentaje de descuento
        if (pxp.f_existe_parametro(p_tabla,'porcentaje_descuento')) then
          v_porcentaje_descuento = v_parametros.porcentaje_descuento;
        end if;

        --verificar si existe vendedor o medico
        v_id_vendedor = NULL;
        v_id_medico = NULL;

        if (pxp.f_existe_parametro(p_tabla,'id_vendedor_medico')) then
          if (split_part(v_parametros.id_vendedor_medico,'_',2) = 'usuario') then
            v_id_vendedor =  split_part(v_parametros.id_vendedor_medico::text,'_'::text,1)::integer;
          else
            v_id_medico =  split_part(v_parametros.id_vendedor_medico::text,'_'::text,1)::integer;
          end if;
        end if;


        if (pxp.f_existe_parametro(p_tabla,'descripcion')) then
          v_descripcion =  v_parametros.descripcion;
        else
          v_descripcion = '';
        end if;

        v_bruto = 0;
        v_ley = 0;
        v_kg_fino = 0;
        if (pxp.f_existe_parametro(p_tabla,'bruto')) then
          v_bruto = v_parametros.bruto;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'ley')) then
          v_ley = v_parametros.ley;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'kg_fino')) then
          v_kg_fino = v_parametros.kg_fino;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'id_unidad_medida')) then
          v_id_unidad_medida = v_parametros.id_unidad_medida;
        end if;

        --Si el total a pagar debe estar redondeado a entero
        if (pxp.f_get_variable_global('vef_redondeo_detalle') = 'true') then
          --si el total no es entero
          if (trunc((v_parametros.precio * v_parametros.cantidad_det) - (v_parametros.precio * v_parametros.cantidad_det * v_porcentaje_descuento / 100)) != ((v_parametros.precio * v_parametros.cantidad_det) - (v_parametros.precio * v_parametros.cantidad_det * v_porcentaje_descuento / 100))) then
            v_total = ceil((v_parametros.precio * v_parametros.cantidad_det) - (v_parametros.precio * v_parametros.cantidad_det * v_porcentaje_descuento / 100));
            v_parametros.precio = v_total / v_parametros.cantidad_det*100/(100 - v_porcentaje_descuento);
          end if;
        end if;

        --Sentencia de la insercion
        insert into vef.tventa_detalle(
          id_venta,
          id_item,
          --id_sucursal_producto,
          id_formula,
          tipo,
          estado_reg,
          cantidad,
          precio,
          fecha_reg,
          id_usuario_reg,
          id_usuario_mod,
          fecha_mod,
          precio_sin_descuento,
          porcentaje_descuento,
          id_vendedor,
          id_medico,
          descripcion,
          bruto,
          ley,
          kg_fino,
          id_unidad_medida,
          id_producto
        ) values(
          v_parametros.id_venta,
          v_parametros.id_producto,--v_id_item,
          --v_id_sucursal_producto,
          v_id_formula,
          v_parametros.tipo,
          'activo',
          v_parametros.cantidad_det,
          round(v_parametros.precio - (v_parametros.precio * v_porcentaje_descuento / 100),6),
          now(),
          p_id_usuario,
          null,
          null,
          v_parametros.precio,
          v_porcentaje_descuento,
          v_id_vendedor,
          v_id_medico,
          v_descripcion,
          v_bruto,
          v_ley,
          v_kg_fino,
          v_id_unidad_medida,
          v_parametros.id_producto
        )RETURNING id_venta_detalle into v_id_venta_detalle;

        --recupera datos de la venta
        select
          *
        into
          v_registros
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;


        select precio, cantidad into  v_tmp
        from vef.tventa_detalle
        where id_venta = v_parametros.id_venta;

        IF v_parametros.tipo_factura != 'computarizadaexpo' THEN
          v_total = COALESCE(v_registros.transporte_fob ,0)  + COALESCE(v_registros.seguros_fob ,0)+ COALESCE(v_registros.otros_fob ,0) + COALESCE(v_registros.transporte_cif ,0) +  COALESCE(v_registros.seguros_cif ,0) + COALESCE(v_registros.otros_cif ,0);
        ELSE
          v_total = 0; --en la factura comun de exportacion el detalle ya incluye los precios fob y cif
        END IF;

        update vef.tventa
        set total_venta = round((select sum(round(precio * cantidad,2)) from vef.tventa_detalle where id_venta = v_parametros.id_venta) + v_total,2)
        where id_venta = v_parametros.id_venta;




        --raise exception 'llega auqi tipo %',v_parametros.tipo_factura;
        --verificar si existe el sistema obingresos, si existe actualizar el ib_boleto
        /**************************Comentamos este codigo para revisar facturas**************/
        /*if ( (v_descripcion != '' and v_descripcion is not null and pxp.f_is_positive_integer(v_descripcion)) and
             exists (
                 select 1
                 from segu.tsubsistema s
                 where s.codigo like 'OBINGRESOS')) then

          if (exists (select 1
                      from vef.tventa_detalle
                      where id_venta_detalle != v_parametros.id_venta_detalle and
                            descripcion = v_descripcion))then
            raise exception 'El boleto %, ya fue relacionado con otra venta',v_parametros.descripcion;
          end if;

          select b.id_boleto into v_id_boleto
          from obingresos.tboleto b
          where b.nro_boleto = v_descripcion;

          if (v_id_boleto is not null) then
            update vef.tventa_detalle
            set id_boleto = v_id_boleto
            where id_venta_detalle = v_id_venta_detalle;
          end if;
        end if;*/

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de Venta almacenado(a) con exito (id_venta_detalle'||v_id_venta_detalle||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_id_venta_detalle::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

	/*********************************
 	#TRANSACCION:  'VF_FACTDET_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_FACTDET_MOD')then

		begin
        	--raise exception 'llega auqi el preio del serv %',v_parametros.precio;
			--Sentencia de la modificacion

            /*if (v_parametros.id_moneda = 2) then
                    select tc.oficial
                    into v_tipo_cambio
                    from param.ttipo_cambio tc
                    inner join param.tmoneda tm on tm.id_moneda = tc.id_moneda
                    where tm.codigo_internacional = 'USD' and tc.fecha = now()::date;

		            v_precio = v_parametros.precio * v_tipo_cambio;
            else  */
            		v_precio = v_parametros.precio;

            --end if;
            select count(distinct inga.contabilizable),
                   count(distinct inga.excento),
                   count(distinct inga.id_actividad_economica)
             into v_cantidad, v_cantidad_exe, v_cantidad_ace
            from vef.tventa_detalle det
            inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_producto
            where det.id_venta = v_parametros.id_venta;

            if (v_cantidad_exe > 1) then
              raise exception 'No puede utilizar conceptos con exentos si y exentos no, en la misma venta';
            end if;
            if(v_cantidad_ace > 1)then
              raise exception 'No puede utilizar conceptos de diferente actividad economica en la misma venta';
            end if;
            if (v_cantidad > 1) then
              raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
            end if;

			update vef.tventa_detalle set
            tipo = v_parametros.tipo,
            id_producto = v_parametros.id_producto,
            cantidad = v_parametros.cantidad_det,
            precio = v_precio
			where id_venta_detalle=v_parametros.id_venta_detalle;

            /*Obtenemos el total de la venta*/
            select sum((ven.precio * ven.cantidad)) into venta_total
            from vef.tventa_detalle ven
            where ven.id_venta = v_parametros.id_venta;

            /***Actualizamos el total de la venta***/
            update vef.tventa set
            total_venta = venta_total,
            total_venta_msuc = venta_total
            --excento = v_parametros.excento
            where id_venta = v_parametros.id_venta;
            /*****************************************/

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_parametros.id_venta_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FACTDET_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_FACTDET_ELI')then

		begin

            select vedet.id_venta into v_id_venta
            from vef.tventa_detalle vedet
            WHERE vedet.id_venta_detalle = v_parametros.id_venta_detalle;

			--Sentencia de la eliminacion
			delete from vef.tventa_detalle
            where id_venta_detalle=v_parametros.id_venta_detalle;


            select sum(ven.precio * ven.cantidad) into v_total_venta
              from vef.tventa_detalle ven
              where  ven.id_venta = v_id_venta;

            if (v_total_venta is null ) then
            	v_total_venta = 0;
            else
            	v_total_venta = v_total_venta;
            end if;

              update vef.tventa set
                total_venta = v_total_venta,
                total_venta_msuc = v_total_venta
              where id_venta = v_id_venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_parametros.id_venta_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACTEXCEN_INS'
 	#DESCRIPCION:	Verificacion si algun concepto tiene excento
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_FACTEXCEN_INS')then

		begin


            /*select count (ingas.excento) into v_existe_excento
            from vef.tformula form
            inner join vef.tformula_detalle det on det.id_formula = form.id_formula
            inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_concepto_ingas
            where ingas.excento = 'si' and form.id_formula = v_parametros.id_formula;*/


            select DISTINCT (ingas.excento) into v_tiene_excento
            from vef.tventa_detalle det
            inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
            where ingas.excento = 'si' and det.id_venta = v_parametros.id_venta;

        	/*select count (ing.excento) into v_existe_excento
            from vef.tventa_detalle det
            inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = det.id_producto
            where det.id_venta = v_parametros.id_venta and ing.excento = 'si';*/

            if (v_tiene_excento = '' or v_tiene_excento = null) then
            	v_tiene_excento = 'no';
            end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'v_tiene_excento',v_tiene_excento::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

        /*********************************
 	#TRANSACCION:  'VF_EXCENFOR_INS'
 	#DESCRIPCION:	Verificacion si algun concepto tiene excento
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_EXCENFOR_INS')then

		begin


            select DISTINCT (ingas.excento) into v_tiene_excento
            from vef.tformula form
            inner join vef.tformula_detalle det on det.id_formula = form.id_formula
            inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_concepto_ingas
            where ingas.excento = 'si' and form.id_formula = v_parametros.id_formula;


            /*select DISTINCT (ingas.excento) into v_tiene_excento
            from vef.tventa_detalle det
            inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
            where ingas.excento = 'si' and det.id_venta = v_parametros.id_venta;
        	*/
        	/*select count (ing.excento) into v_existe_excento
            from vef.tventa_detalle det
            inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = det.id_producto
            where det.id_venta = v_parametros.id_venta and ing.excento = 'si';*/

            if (v_tiene_excento = '' or v_tiene_excento = null) then
            	v_tiene_excento = 'no';
            end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'v_tiene_excento',v_tiene_excento::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACTUDT_MOD'
 	#DESCRIPCION:	Actualizamos el Valor excento
 	#AUTOR:		ivaldivia
 	#FECHA:		27-09-2019 17:30:22
	***********************************/

	elsif(p_transaccion='VF_FACTUDT_MOD')then

		begin

        	update vef.tventa set
            excento = v_parametros.valor_excento,
            excento_verificado = 'si'
			where id_venta=v_parametros.id_venta;

			--Definicion de la respuesta
             v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_parametros.id_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
     #TRANSACCION:  'VF_FPDET_INS'
     #DESCRIPCION:	Insercion de registros detalle de las n formas de pago
     #AUTOR:		Ismael Valdivia
     #FECHA:		8-12-2020 8:45:07
    ***********************************/

    elsif(p_transaccion='VF_FPDET_INS')then

      begin

        if (v_parametros.tipo = 'formula') then
          v_id_formula = v_parametros.id_formula;
          v_id_concepto_ingas = v_parametros.id_producto;
        elsif (v_parametros.tipo = 'servicio' or
               (v_parametros.tipo = 'producto_terminado' and pxp.f_get_variable_global('vef_integracion_almacenes') = 'false'))then
                  v_id_concepto_ingas = v_parametros.id_producto;
        else
          v_id_item =  v_parametros.id_producto;
        end if;

        v_porcentaje_descuento = 0;

        --verificar si existe vendedor o medico
        v_id_vendedor = NULL;
        v_id_medico = NULL;


        if (pxp.f_existe_parametro(p_tabla,'descripcion')) then
          v_descripcion =  v_parametros.descripcion;
        else
          v_descripcion = '';
        end if;

        v_bruto = 0;
        v_ley = 0;
        v_kg_fino = 0;

        --Si el total a pagar debe estar redondeado a entero
        if (pxp.f_get_variable_global('vef_redondeo_detalle') = 'true') then
          --si el total no es entero
          if (trunc((v_parametros.precio * v_parametros.cantidad_det) - (v_parametros.precio * v_parametros.cantidad_det * v_porcentaje_descuento / 100)) != ((v_parametros.precio * v_parametros.cantidad_det) - (v_parametros.precio * v_parametros.cantidad_det * v_porcentaje_descuento / 100))) then
            v_total = ceil((v_parametros.precio * v_parametros.cantidad_det) - (v_parametros.precio * v_parametros.cantidad_det * v_porcentaje_descuento / 100));
            v_parametros.precio = v_total / v_parametros.cantidad_det*100/(100 - v_porcentaje_descuento);
          end if;
        end if;

        --Sentencia de la insercion
        insert into vef.tventa_detalle(
          id_venta,
          id_item,
          --id_sucursal_producto,
          id_formula,
          tipo,
          estado_reg,
          cantidad,
          precio,
          fecha_reg,
          id_usuario_reg,
          id_usuario_mod,
          fecha_mod,
          precio_sin_descuento,
          porcentaje_descuento,
          id_vendedor,
          id_medico,
          descripcion,
          bruto,
          ley,
          kg_fino,
          id_unidad_medida,
          id_producto
        ) values(
          v_parametros.id_venta,
          v_parametros.id_producto,
          v_id_formula,
          v_parametros.tipo,
          'activo',
          v_parametros.cantidad_det,
          round(v_parametros.precio - (v_parametros.precio * v_porcentaje_descuento / 100),6),
          now(),
          p_id_usuario,
          null,
          null,
          v_parametros.precio,
          v_porcentaje_descuento,
          v_id_vendedor,
          v_id_medico,
          v_descripcion,
          v_bruto,
          v_ley,
          v_kg_fino,
          v_id_unidad_medida,
          v_parametros.id_producto
        )RETURNING id_venta_detalle into v_id_venta_detalle;


        --recupera datos de la venta
        select
          *
        into
          v_registros
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;


        select precio, cantidad into  v_tmp
        from vef.tventa_detalle
        where id_venta = v_parametros.id_venta;

        IF v_parametros.tipo_factura != 'computarizadaexpo' THEN
          v_total = COALESCE(v_registros.transporte_fob ,0)  + COALESCE(v_registros.seguros_fob ,0)+ COALESCE(v_registros.otros_fob ,0) + COALESCE(v_registros.transporte_cif ,0) +  COALESCE(v_registros.seguros_cif ,0) + COALESCE(v_registros.otros_cif ,0);
        ELSE
          v_total = 0;
        END IF;

        update vef.tventa
        set total_venta = round((select sum(round(precio * cantidad,2)) from vef.tventa_detalle where id_venta = v_parametros.id_venta) + v_total,2)
        where id_venta = v_parametros.id_venta;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de Venta almacenado(a) con exito (id_venta_detalle'||v_id_venta_detalle||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_id_venta_detalle::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_venta_detalle_facturacion_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
