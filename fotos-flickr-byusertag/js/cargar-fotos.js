function cargar(comienzo,final,usuario,idrand)
{
	items=eval("lista_"+idrand).split(";");
	for(i=comienzo;i<=final;i++)
	{
		if(typeof items[i]!== "undefined")
		{
			id=items[i].split(",")[0];
			secret=items[i].split(",")[1];
			server=items[i].split(",")[2];
			
			document.getElementById("resultados_"+idrand).innerHTML+='<div class="tarjeta_flickr"><div class="foto_flickr"><a href="https://www.flickr.com/photos/'+usuario+'/'+id+'/" target="_blank"><img src="https://farm2.staticflickr.com/'+server+'/'+id+'_'+secret+'_z_d.jpg"/></a></div><div class="nombre"></div></div>';
		}else{
		
		//document.getElementById("resultados").innerHTML+="No se encontraron más resultados";
		document.getElementById("cargarmas_"+idrand).style.display = "none"; 		
		
		}
	}
		
}
function mas(usuario,salto,id)
{
	inicio=inicio+salto;
	fin=fin+salto;	
	cargar(inicio,fin,usuario,id);
}