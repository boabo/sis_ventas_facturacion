CREATE OR REPLACE FUNCTION vef.ft_punto_venta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Sistema de Ventas
   FUNCION: 		vef.ft_punto_venta_sel
   DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tpunto_venta'
   AUTOR: 		 (jrivera)
   FECHA:	        07-10-2015 21:02:00
   COMENTARIOS:
  ***************************************************************************
   HISTORIAL DE MODIFICACIONES:

   DESCRIPCION:
   AUTOR:
   FECHA:
  ***************************************************************************/

  DECLARE

    v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;

  BEGIN

    v_nombre_funcion = 'vef.ft_punto_venta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'VF_PUVE_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		jrivera
     #FECHA:		07-10-2015 21:02:00
    ***********************************/

    if(p_transaccion='VF_PUVE_SEL')then

      begin
        --Sentencia de la consulta
        v_consulta:='select
						puve.id_punto_venta,
						puve.estado_reg,
						puve.id_sucursal,
						puve.nombre,
						puve.descripcion,
						puve.id_usuario_reg,
						puve.fecha_reg,
						puve.id_usuario_ai,
						puve.usuario_ai,
						puve.id_usuario_mod,
						puve.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
            puve.codigo,
            puve.habilitar_comisiones,
            suc.formato_comprobante,
            puve.tipo,
            suc.enviar_correo,
            puve.office_id,
            puve.id_catalogo,
            cat.codigo as cod_osd,
            puve.iata_status,
            puve.id_catalogo_canal,
            cat1.codigo as cod_canal,
            puve.nombre_amadeus
						from vef.tpunto_venta puve
						inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
				    inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
            left join param.tcatalogo cat on cat.id_catalogo = puve.id_catalogo
            left join param.tcatalogo cat1 on cat1.id_catalogo = puve.id_catalogo_canal
                        where  puve.estado_reg = ''activo'' and ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'VF_OFFID_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		24-07-2017
	***********************************/

    elsif(p_transaccion='VF_OFFID_SEL')then

		begin
        --prueba para consumo de servicio
        if (v_parametros.id_punto_venta = 3404 )then

         --Sentencia de la consulta de conteo de registros
            v_consulta:='select ''CBBOB04TE''::varchar as officeID,
                         4919::integer,
                        COALESCE(pvr.identificador_reporte,''0'') AS identificador_reporte
						from vef.tpunto_venta pv
                        left join vef.tpunto_venta_reporte pvr on pvr.id_punto_venta=pv.id_punto_venta
     					and pvr.fecha='''||v_parametros.fecha||''' and pvr.moneda='''||v_parametros.moneda||'''';
        else



			--Sentencia de la consulta de conteo de registros
            v_consulta:='select ag.codigo_int as officeID,
                         ag.id_agencia,
                        COALESCE(pvr.identificador_reporte,''0'') AS identificador_reporte
						from vef.tpunto_venta pv
						inner join obingresos.tagencia ag on pv.codigo=ag.codigo
						and pv.id_punto_venta='||v_parametros.id_punto_venta||'
                        left join vef.tpunto_venta_reporte pvr on pvr.id_punto_venta=pv.id_punto_venta
     					and pvr.fecha='''||v_parametros.fecha||''' and pvr.moneda='''||v_parametros.moneda||'''';
            raise notice 'v_consulta %', v_consulta;
           end if;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
     #TRANSACCION:  'VF_PUVE_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		jrivera
     #FECHA:		07-10-2015 21:02:00
    ***********************************/

    elsif(p_transaccion='VF_PUVE_CONT')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta:='select count(puve.id_punto_venta)
					    from vef.tpunto_venta puve
					    inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
						  left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
					    inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
              left join param.tcatalogo cat on cat.id_catalogo = puve.id_catalogo
              left join param.tcatalogo cat1 on cat1.id_catalogo = puve.id_catalogo_canal
              where  puve.estado_reg = ''activo'' and ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;

     /*********************************
     #TRANSACCION:  'VF_PVUSU_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		maylee.perez
     #FECHA:		28-01-2020 21:02:00
     ***********************************/

    elsif(p_transaccion='VF_PVUSU_SEL')then

      begin

        v_consulta:='select
                        puve.id_punto_venta,
                        puve.estado_reg,
                        puve.id_sucursal,
                        puve.nombre,
                        puve.descripcion,
                        puve.id_usuario_reg,
                        puve.fecha_reg,
                        puve.id_usuario_ai,
                        puve.usuario_ai,
                        puve.id_usuario_mod,
                        puve.fecha_mod,
                        usu1.cuenta as usr_reg,
                        usu2.cuenta as usr_mod,
                        puve.codigo,
                        puve.habilitar_comisiones,
                        suc.formato_comprobante,
                        puve.tipo,
                        suc.enviar_correo,

                        sucusu.tipo_usuario as tipo_suc_usuario

                        from vef.tpunto_venta puve
                        inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
                        inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal

                        left join vef.tsucursal_usuario sucusu on puve.id_punto_venta = sucusu.id_punto_venta  and sucusu.id_usuario = '||p_id_usuario||'

                        where  ';

        --Definicion de la respuesta
          v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

      /*********************************
      #TRANSACCION:  'VF_PVUSU_CONT'
      #DESCRIPCION:	Conteo de registros
      #AUTOR:		maylee.perez
      #FECHA:		28-01-2020 21:02:00
      ***********************************/

    elsif(p_transaccion='VF_PVUSU_CONT')then

      begin

        --Sentencia de la consulta de conteo de registros
        v_consulta:='select count(puve.id_punto_venta)
                        from vef.tpunto_venta puve
                        inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
                        inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal

                        left join vef.tsucursal_usuario sucusu on puve.id_punto_venta = sucusu.id_punto_venta  and sucusu.id_usuario = '||p_id_usuario||'

                        where ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;


    else

      raise exception 'Transaccion inexistente';

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

ALTER FUNCTION vef.ft_punto_venta_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
