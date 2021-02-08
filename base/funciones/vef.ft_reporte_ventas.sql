CREATE OR REPLACE FUNCTION vef.ft_reporte_ventas (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_reporte_ventas
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas
 AUTOR: 		 (breydi.vasquez)
 FECHA:	        29-01-2021
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
    v_filtro			varchar=' 0=0 ';

BEGIN

	v_nombre_funcion = 'vef.ft_reporte_ventas';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_RREVBOL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		breydi.vasquez
 	#FECHA:		29-01-2021
	***********************************/

	if(p_transaccion='VF_RREVBOL_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select cat.id_catalogo,
            					cat.codigo,
                                cat.descripcion
            			 from param.tcatalogo cat
                         where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'resp %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_RREVBOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-01-2021
	***********************************/

	elsif(p_transaccion='VF_RREVBOL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(cat.id_catalogo)
					    from param.tcatalogo cat
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
     #TRANSACCION:  'VF_PUVERB_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		breydi.vasquez
     #FECHA:		28-01-2021
    ***********************************/

    elsif(p_transaccion='VF_PUVERB_SEL')then

      begin


      	IF p_administrador != 1 THEN
        	IF (NOT EXISTS (select 1
                      from vef.tpermiso_sucursales
                      where id_funcionario = (
                      select id_funcionario
                      from orga.vfuncionario
                      where id_persona = (select id_persona from segu.vusuario where id_usuario= p_id_usuario)
                      ))) THEN

            v_filtro= '(1 in
                      (select id_rol
                      from segu.tusuario_rol ur
                      where ur.id_usuario = '||p_id_usuario||'
                      and ur.estado_reg = ''activo'') or
                      ( '||p_id_usuario||' in (select id_usuario
                      from vef.tsucursal_usuario
                      sucusu where
                      puve.id_punto_venta = sucusu.id_punto_venta and sucusu.tipo_usuario = ''cajero'')))';
            END IF;

        END IF;

        v_consulta:='select
                        puve.id_punto_venta,
                        puve.nombre,
                        puve.descripcion,
                        puve.codigo,
                        puve.tipo,
                        puve.office_id
                        from vef.tpunto_venta puve
                        where  '||v_filtro||'
                        and ' ;

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'VF_PUVERB_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		breydi.vasquez
     #FECHA:		28-01-2021
    ***********************************/

    elsif(p_transaccion='VF_PUVERB_CONT')then

      begin

        --Sentencia de la consulta de conteo de registros
      	IF p_administrador != 1 THEN

        	IF (NOT EXISTS (select 1
                      from vef.tpermiso_sucursales
                      where id_funcionario = (
                      select id_funcionario
                      from orga.vfuncionario
                      where id_persona = (select id_persona from segu.vusuario where id_usuario= p_id_usuario)
                      ))) THEN

            v_filtro= '(1 in
                      (select id_rol
                      from segu.tusuario_rol ur
                      where ur.id_usuario = '||p_id_usuario||'
                      and ur.estado_reg = ''activo'') or
                      ( '||p_id_usuario||' in (select id_usuario
                      from vef.tsucursal_usuario
                      sucusu where
                      puve.id_punto_venta = sucusu.id_punto_venta and sucusu.tipo_usuario = ''cajero'')))';
            END IF;

        END IF;

        v_consulta:='select count(puve.id_punto_venta)
                        from vef.tpunto_venta puve
                        where '||v_filtro||'
                        and ';

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
