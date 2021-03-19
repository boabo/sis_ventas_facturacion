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
    v_filtro			varchar='0=0 ';

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
			v_consulta:='select
            				c.id_catalogo,
            				c.codigo,
                            c.descripcion
                          from vef.tpunto_venta p
                          inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                          inner join param.tlugar l on l.id_lugar = s.id_lugar
                          inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                          where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by  c.codigo, c.id_catalogo, c.descripcion ';
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
			v_consulta:='select count(p.id_catalogo)
						  from vef.tpunto_venta p
                          inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                          inner join param.tlugar l on l.id_lugar = s.id_lugar
                          inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                          where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by  p.id_catalogo ';
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
   	#TRANSACCION:  'VF_CCANVE_SEL'
   	#DESCRIPCION:	Consulta de datos
   	#AUTOR:		breydi.vasquez
   	#FECHA:		29-01-2021
  	***********************************/

  	elsif(p_transaccion='VF_CCANVE_SEL')then

      	begin
      		--Sentencia de la consulta
  			v_consulta:='select
              				id_catalogo,
              				codigo,
                      descripcion
  						from param.tcatalogo
                            where';

  			--Definicion de la respuesta
  			v_consulta:=v_consulta||v_parametros.filtro;
  			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
  			raise notice 'resp %',v_consulta;
  			--Devuelve la respuesta
  			return v_consulta;

  		end;

  	/*********************************
   	#TRANSACCION:  'VF_CCANVE_CONT'
   	#DESCRIPCION:	Conteo de registros
   	#AUTOR:		breydi.vasquez
   	#FECHA:		28-01-2021
  	***********************************/

  	elsif(p_transaccion='VF_CCANVE_CONT')then

  		begin
  			--Sentencia de la consulta de conteo de registros
  			v_consulta:='select count(id_catalogo)
              			  from param.tcatalogo
                      where ';

  			--Definicion de la respuesta
  			v_consulta:=v_consulta||v_parametros.filtro;
  			--Devuelve la respuesta
  			return v_consulta;

  		end;

    /*********************************
     #TRANSACCION:  'VF_OFFIDRV_SEL'
     #DESCRIPCION:	Consulta de datos Office ID por nro de codigo Iata
     #AUTOR:		breydi.vasquez
     #FECHA:		28-01-2021
    ***********************************/

    elsif(p_transaccion='VF_OFFIDRV_SEL')then

      begin

		v_consulta:= 'select p.id_punto_venta,p.office_id
                      from vef.tpunto_venta p
                      inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                      inner join param.tlugar l on l.id_lugar = s.id_lugar
                      inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                      where p.office_id is not null and p.office_id !='''' and  ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'VF_OFFIDRV_CONT'
     #DESCRIPCION:	Conteo de registros de datos Office ID por nro de codigo Iata
     #AUTOR:		breydi.vasquez
     #FECHA:		28-01-2021
    ***********************************/

    elsif(p_transaccion='VF_OFFIDRV_CONT')then

      begin

        --Sentencia de la consulta de conteo de registros

		v_consulta:= 'select count(p.office_id)
                      from vef.tpunto_venta p
                      inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                      inner join param.tlugar l on l.id_lugar = s.id_lugar
                      inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                      where p.office_id is not null and p.office_id !='''' and  ';

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


 /*     	IF p_administrador != 1 THEN
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
                        and ' ;*/

		v_consulta:= 'select p.codigo
                      from vef.tpunto_venta p
                      inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                      inner join param.tlugar l on l.id_lugar = s.id_lugar
                      inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                      where ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' group by p.codigo';
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
        /*
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
                        and ';*/

		v_consulta:= 'select count(p.codigo)
                      from vef.tpunto_venta p
                      inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                      inner join param.tlugar l on l.id_lugar = s.id_lugar
                      inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                      where ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' group by p.codigo';

        --Devuelve la respuesta
        return v_consulta;

      end;
	/*********************************
 	#TRANSACCION:  'VF_FILTIPO_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		breydi.vasquez
 	#FECHA:		29-01-2021
	***********************************/

	elsif(p_transaccion='VF_FILTIPO_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select p.tipo, c.codigo
                          from vef.tpunto_venta p
                          inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                          inner join param.tlugar l on l.id_lugar = s.id_lugar
                          inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                          where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by  p.tipo, c.codigo ';
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'resp %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FILTIPO_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-01-2021
	***********************************/

	elsif(p_transaccion='VF_FILTIPO_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(p.tipo)
                          from vef.tpunto_venta p
                          inner join vef.tsucursal s on s.id_sucursal = p.id_sucursal
                          inner join param.tlugar l on l.id_lugar = s.id_lugar
                          inner join param.tcatalogo c on c.id_catalogo = p.id_catalogo_canal
                          where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by  p.tipo, c.codigo ';
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_GETCAN_SEL'
 	#DESCRIPCION:	Consulta de canal de venta
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-01-2021
	***********************************/

	elsif(p_transaccion='VF_GETCAN_SEL')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select pxp.list(upper(codigo)::text)
              			 from param.tcatalogo
                         where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
   	#TRANSACCION:  'VF_VLUTOT_SEL'
   	#DESCRIPCION:	Consulta de lugar por pais
   	#AUTOR:		breydi.vasquez
   	#FECHA:		28-01-2021
  	***********************************/

  	elsif(p_transaccion='VF_VLUTOT_SEL')then

  		begin
  			--Sentencia de la consulta de conteo de registros

  			v_consulta:=' with t1 as (
                            select
                                lk.id_lugar,
                                lk.id_lugar_fk,
                                lk.codigo,
                                lk.nombre,
                                lk.sw_impuesto,
                                lk.sw_municipio,
                                lk.tipo,
                                lk.es_regional
                                from param.tlugar lug
                                inner join param.tlugar lk on lk.id_lugar_fk = lug.id_lugar
                                where lug.id_lugar_fk = '||v_parametros.id_lugar_fk||'

                            union

                            select
                                id_lugar,
                                id_lugar_fk,
                                codigo,
                                nombre,
                                sw_impuesto,
                                sw_municipio,
                                tipo,
                                es_regional
                                from param.tlugar
                                where  id_lugar_fk = '||v_parametros.id_lugar_fk||'
                                      )
                              select *
                              from t1
                              where  ';

  			--Definicion de la respuesta
              v_consulta:=v_consulta||v_parametros.filtro;
              raise notice 'resp %',v_consulta;
  			--Devuelve la respuesta
  			return v_consulta;

  		end;

      ----------------------stage----------

      	/*********************************
       	#TRANSACCION:  'VF_STPVCP_SEL'
       	#DESCRIPCION:	Consulta datos stage puntos de venta paises
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVCP_SEL')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					country_name,
                                   	country_code
                            from vef.tstage_punto_venta
                            where ';
                  v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by country_name, country_code)
      	                     select * from t ';

      			--Definicion de la respuesta
      			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      			--Devuelve la respuesta
      			return v_consulta;

      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVCP_CONT'
       	#DESCRIPCION:	Conteo de registros stage puntos de venta paises
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVCP_CONT')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					country_name,
                                   	country_code
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by country_name, country_code)
                            select count(country_code) from t ';

      			--Devuelve la respuesta
      			return v_consulta;

      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVC_SEL'
       	#DESCRIPCION:	Consulta datos stage puntos de venta ciudades
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVC_SEL')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					city_name,
                                   	city_code
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
      			v_consulta:=v_consulta||' group by city_name, city_code)
                            select * from t ';

          		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
      			raise notice 'resp %',v_consulta;
      			--Devuelve la respuesta
      			return v_consulta;
      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVC_CONT'
       	#DESCRIPCION:	Conteo de registros stage puntos de venta ciudades
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVC_CONT')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					city_name,
                                   	city_code
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
      			v_consulta:=v_consulta||' group by city_name, city_code)
                            select count(city_name) from t ';

      			--Devuelve la respuesta
      			return v_consulta;

      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVCH_SEL'
       	#DESCRIPCION:	Consulta datos stage puntos de venta canal
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVCH_SEL')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					sale_channel
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||'  group by sale_channel)
                            	select * from t ';

          		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      			--Devuelve la respuesta
      			return v_consulta;
      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVCH_CONT'
       	#DESCRIPCION:	Conteo de registros stage puntos de venta canal
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVCH_CONT')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					sale_channel
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by sale_channel)
                            select count(sale_channel) from t ';

      			--Devuelve la respuesta
      			return v_consulta;

      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVT_SEL'
       	#DESCRIPCION:	Consulta datos stage puntos de venta tipo
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVT_SEL')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					tipo_pos
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by tipo_pos )
                            select * from t ';

          		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      			--Devuelve la respuesta
      			return v_consulta;
      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPVT_CONT'
       	#DESCRIPCION:	Conteo de registros stage puntos de venta tipo
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPVT_CONT')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					tipo_pos
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by tipo_pos )
                            select count(tipo_pos) from t ';

      			--Devuelve la respuesta
      			return v_consulta;

      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPCI_SEL'
       	#DESCRIPCION:	Consulta datos stage puntos de venta codigo iata
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/
      	elsif(p_transaccion='VF_STPCI_SEL')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					iata_code
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
      			v_consulta:=v_consulta||' group by iata_code)
                            select * from t ';

          		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      			--Devuelve la respuesta
      			return v_consulta;
      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPCI_CONT'
       	#DESCRIPCION:	Conteo de registros stage puntos de venta codigo iata
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPCI_CONT')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					iata_code
                            from vef.tstage_punto_venta
                            where ';
      			v_consulta:=v_consulta||v_parametros.filtro;
      			v_consulta:=v_consulta||' group by iata_code)
                            select count(iata_code) from t ';

      			--Devuelve la respuesta
      			return v_consulta;

      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPOFI_SEL'
       	#DESCRIPCION:	Consulta datos stage puntos de venta office id
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/
      	elsif(p_transaccion='VF_STPOFI_SEL')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					office_id,
                            case when name_pv =''Completar el nombre de la Agencia'' then
                            ''''::varchar
                            else
                              name_pv
                          end as name_pv
                            from vef.tstage_punto_venta
                            where (office_id is not null and office_id != '''') and ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by office_id, name_pv)
                            select * from t ';

          		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      			--Devuelve la respuesta
      			return v_consulta;
      		end;

      	/*********************************
       	#TRANSACCION:  'VF_STPOFI_CONT'
       	#DESCRIPCION:	Conteo de registros stage puntos de venta codigo office id
       	#AUTOR:		breydi.vasquez
       	#FECHA:		28-01-2021
      	***********************************/

      	elsif(p_transaccion='VF_STPOFI_CONT')then

      		begin
      			--Sentencia de la consulta de conteo de registros
      			v_consulta:='
                  			with t as (
                  				select
                  					office_id,
                            case when name_pv =''Completar el nombre de la Agencia'' then
                            ''''::varchar
                            else
                              name_pv
                          end as name_pv
                            from vef.tstage_punto_venta
                            where (office_id is not null and office_id != '''') and ';
      			v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' group by office_id,name_pv)
                            select count(office_id) from t ';

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
