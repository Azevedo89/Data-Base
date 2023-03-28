<?php
$link =mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME); 
require_once("custom/php/common.php");

$verificar_capability= "manage_subitems";


if(!current_user_can($verificar_capability)) 
{
	echo 
		die	(
		"Não tem autorização para aceder a esta página"	)	;
} 



   $query_tabela_subitem =   " SELECT         
   subitem.value_type  ,
   subitem.name,
   subitem.id,
   subitem.form_field_type,
   subitem.form_field_name, 
   subitem.mandatory,
   subitem.form_field_order,
   subitem.state
   FROM
   subitem 
   ORDER BY 
   subitem.name  "  ;     
    $resultado_query_tabela_subitem =mysqli_query($link,$query_tabela_subitem)	;                                        

    $query_tabela_item	="SELECT
	item.name,
	item.id 
	FROM 
	item
	ORDER BY 
	item.name ";        
    $resultado_query_tabela_item =mysqli_query($link,$query_tabela_item);

   


        if(isset($_POST["estado_atual"])=="")
        {
			  if(mysqli_num_rows($resultado_query_tabela_subitem) == 0)//verificar se tem abributos
            {
                echo 			"	Não há crianças			"	;
            }
			else	{
			
				echo		' 
				<body>
				<table>
				<tr>
				<td class="testee">
				<strong>
				item
				</strong>
				</td >
				<td class="testee">
				<strong>
				id
				</strong>
				</td>
				<td class="testee">
				<strong>
				subitem
				</strong>
				</td>
                <td class="testee">
                <strong>				
				tipo de valor
				</strong>
				</td>
		        <td class="testee">
				<strong>
				nome do campo no formulário
				</strong>
				</td>
		        <td class="testee">
				<strong>
				tipo do campo no formulario
				</strong>
				</td>
				<td class="testee">
				<strong>
				tipo de unidade
				</strong>
				</td>
				<td class="testee">
				<strong>
				ordem do campo no formulario
				</strong>
				</td>
				<td class="testee">
				<strong>
				obrigatorio
				</strong>
				</td>
				<td class="testee">
				<strong>
				estado
				</strong>
				</td>
				<td class="testee">
				<strong>
				ação
				</strong>
				</td class="testee">
		        </tr>';
			
			
			
				
				  echo' <style>
			           tr:nth-child(even) {background-color: #f2f2f2}
							
						.testee {
                        background-color: #4CAF50;
                      color: white;
                       }	
								
					input[type=text]:focus {
 width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  border: none;
  background-position: 10px 10px;
   border-radius: 4px;
  box-sizing: border-box;
    background-repeat: no-repeat;
	
   background-color: #f1f1f1;
  transition: width 0.4s ease-in-out;
}	
		select {
  width: 100%;
  padding: 16px 20px;
  border: none;
  border-radius: 4px;
  background-color: #f1f1f1;
}

					
						</style>' ;
				
			
			while ($dados = mysqli_fetch_assoc($resultado_query_tabela_item ))	{
				
			$NomedoItem=$dados["name"]		;
			$iddoItem=$dados["id"]		;
			
			$query_tabela_subitem_com_tudo		=		"		SELECT 
			subitem.form_field_name	, 
			subitem.value_type	,
			subitem.name as nomedosubitem	,
			subitem.mandatory	,
			subitem.id	, 
			subitem.state		, 
			subitem.form_field_type	, 
			subitem.form_field_order
                 FROM 
				 item	 ,
				 subitem
				 WHERE
				 item.name='".$NomedoItem."' 
				 AND  item.id=subitem.item_id 		 ";
				
			$resultado_query_tabela_subitem_com_tudo	=	mysqli_query($link, $query_tabela_subitem_com_tudo)	;	
				
			$utilizarnorowspan 	=	mysqli_num_rows($resultado_query_tabela_subitem_com_tudo)	;	$query_para_testar	=	"
			SELECT 
			item.name	,
			item.id
			FROM 
			item
            where
			item.id='".$iddoItem."'	and
            item.id not in( select item_id from	subitem)
			ORDER BY 
			item.name		"		; 
            $resultado_testar	=	mysqli_query($link,$query_para_testar)	;			
			$contar 	=	mysqli_num_rows($resultado_testar)	;
			
            
        
			
			   
			  echo 	  ' <tr>'	  ;  
              echo  '   <td rowspan=" '	  ;
				echo 		$utilizarnorowspan+$contar	; 
				echo	' "> '	;     
				echo	$NomedoItem.'</td>'	;
			  
			
			
			if((mysqli_num_rows($resultado_query_tabela_subitem_com_tudo )))
			{
			while($dadoss	=	mysqli_fetch_assoc($resultado_query_tabela_subitem_com_tudo))	{
		       $Nome_do_sub_item	   =
			   $dadoss["nomedosubitem"]	   ;
              
			 $queryUnitType	 =	 "SELECT
			 subitem_unit_type.name
			 FROM 
			 subitem 	 ,
			 subitem_unit_type	 WHERE
			 subitem_unit_type.id=subitem.unit_type_id	 AND
			 subitem.name='".$Nome_do_sub_item."'"	 ;
				
				
				   
				$resultado_teste	= mysqli_query($link, $queryUnitType)	;
				$unidadetipo	=		mysqli_fetch_assoc($resultado_teste)		;
				 
		
				 
                $verificarmandatory	=	$dadoss['mandatory']	;     $estado		=    	$dadoss['state']		;
				
				 echo      '<td>'.$dadoss['id'].'		 </td> '	 ;
			 echo		 '<td>'		 .$dadoss['nomedosubitem'].'		 </td> '		 ;
				 echo		 '<td>'		 .$dadoss['value_type'].'		 </td> '		 ;
				 echo		 '<td>'		 .$dadoss['form_field_name'].'		 </td> '		 ;
				 echo		 '<td>'		 .$dadoss['form_field_type'].'		 </td> '		 ;
				
				
				echo	'<td>'	; 
				if( isset($unidadetipo)){
				
				
			        echo	$unidadetipo["name"]." "	;	 }
				else	{
					 echo "-"			 ;	}
				
				
			   
				
				
				echo			'</td>'		; 
				
				
				echo		'<td>'	.$dadoss['form_field_order'].		'</td> '	;
				echo		'<td>'		;
				if($verificarmandatory == 1)		{
					echo	'sim'			;	} 
                else	{
					echo		'nao'		;
						}	
                echo		'</td>'		;

                echo	'<td>'	;
				if($estado == 'active') 	{
					echo 		'ativo'	;	} 
			 else	 {
					echo 		'inativo'	;
					
				}
				echo		'</td>'	;
				echo	'<td>'	;
				echo	'[editar] [desativar] [apagar]  '	;
				echo	'</td>'	;
		echo		'</tr>'	;
				
				
				
			}
			}
			else	{
				
			echo	'<td colspan="10">'	;
			echo 	' este item não tem subitens'	;
			echo 	'</td>'	;
			
		}
			 
		

			}
					
				echo'</table>'		;
		}
			
			echo	'  <h3>Gestão de subitens - introdução</h3>'	;
			
			   $query_tabela_item	   =    "SELECT
			   item.name   ,
			   item.id
			   FROM
			   item
			   ORDER BY
			   item.name "   ;   
			  $resultado_query_tabela_item 	  =	  mysqli_query($link,$query_tabela_item)	  ;  
			  $query_tabela_subitem_unit	  =  "Select 
			  name  ,
			  id from
			  subitem_unit_type
			  GROUP BY 
			  name"  ;
			  $resultado_query_tabela_subitem_unit	  =	  mysqli_query($link,$query_tabela_subitem_unit)	  ; 
			  
		    echo 	"Introduza o Nome do subitem:"	;
		      echo   '<form action="" id = "formulario" method="post">'	  ;
                echo		'       <input placeholder = "Nome do subitem"  name="nomeDosubitem" type="text"">'		;
			
				
				
				$obter_o_tipo_de_valores_que_o_value_type_pode_receber	=	get_enum_values($link,'subitem','value_type')	;
	            
				echo 		' Tipo de Valor: '	;
				echo 	'<br>'	;
               $qualotipodevalor   =   ''   ;
               $qualotipodevalorr   =  ''	   ;
            foreach ( $obter_o_tipo_de_valores_que_o_value_type_pode_receber  as $teste => $tipo_que_queremos )
			{
					
				if($tipo_que_queremos == $qualotipodevalor)
				{
					echo			' <input type="radio" name="qualotipodevalor" value="'	;
				echo 		$tipo_que_queremos	; 
				echo		'	" >  '	; 
				echo 		$tipo_que_queremos	; 
				echo		'<br>  '	;
			}	
			else	{
						echo
					' <input type="radio" name="qualotipodevalor" value="'	;
				echo 		$tipo_que_queremos	; 
				echo		'	" >  '	; 
				echo 		$tipo_que_queremos	; 
			echo 			'<br>  '	;			
			}
				}
				
    


            echo'    <label for="ositens">Item:</label>
				 <div class="selectbox1" >  '	 ;			  
				  echo 	  "<select name='itemescolhido' >"	  ;
			echo 	' <option value=""></option>     '	;
				echo		'	</option>'	;
				
				
	while($colocarositens=mysqli_fetch_assoc($resultado_query_tabela_item)){
		
	     echo  "<option  name='itemescolhido' value=".$colocarositens["id"].">" ;
		
		echo 	' '.$colocarositens["name"].' '	;   
	    echo	' </option>' ;
	}
	
	echo "</select>";
			
				   echo		   "Tipo do campo do formulário:",'<br>'   ;
				 $obter_o_tipo_de_valores_que_o_formulario_type_pode_receber =	 get_enum_values( $link , 'subitem' , 'form_field_type' ) ;  
				    foreach ( $obter_o_tipo_de_valores_que_o_formulario_type_pode_receber  as $testee => $tipo_que_queremoss )
					{
					
				if($tipo_que_queremoss == $qualotipodevalorr)
				{
					echo	' <input type="radio" name="qualotipodevalorr" value="'	;
				echo 		$tipo_que_queremoss	; 
				echo	'	" >  '	; 
				echo 	$tipo_que_queremoss	; 
				echo 		'<br>  '		;	}	
			else	{
						echo	' <input type="radio" name="qualotipodevalorr" value="'		;
				echo 		''.$tipo_que_queremoss	; 
				echo		'	" >  '; 
				echo 		''.$tipo_que_queremoss	; 
				echo 	'<br>  '	;
					
				}
				}
				
				
				
				
				
		   echo	   '
		   <label for="ostiposdeunidade">Tipo de Unidade:</label>'   ;   	echo 	' <option value=""></option> '	;
	
	
	echo 	'<select name="subitem_unit_type">';

			While($colocarossubitem_unit=mysqli_fetch_assoc($resultado_query_tabela_subitem_unit))
			{
	    echo
		"<option  name='subitem_unit_type' value=".$colocarossubitem_unit["id"].">"	;
	     echo	 ' 	   '.$colocarossubitem_unit["name"].' ' 	 ;
		 echo  ' </option>'	 ;
	}
	echo '</select>';
		
				
				
				
				
				
				
				
				
				
				   echo	   '     <form action="" id = "formulario2" method="post">'	;
				    echo	' Ordem do campo :'	;
					echo 	' <input name="verificarordemdocampo"  placeholder="Ordem do campo"  type="text" > '	;
				   
				   
				   echo	   '   <label for="verificarobrigatorio">Obrigatório?</label> '   ;
				   echo   '<div class="tipo_radio">'   ;
				 echo 	 ' <input value=1  name="verificarseestaobrigatorio"  type="radio" />Sim  ' ;
				   echo 	   '   <input  value=0 name="verificarseestaobrigatorio"  type="radio"/>Não '   ;
				echo 	'</div>';
	             echo	 ' <input type="hidden" name="estado_atual"  value="inserir">
                 <input type="submit" name="submeterr" value="Inserir"> ' ;
	           echo   '  </form>'   ;
	

	
		}


	else if($_POST["estado_atual"] == "inserir")
	{
		
		$verificarvalor =''	;
		$verificarvalor2 =''	;
		$obrigatoriosim= '';
		echo
		'  <h3>Gestão de subitens - inserção</h3>'	;
		
		
		if(isset($_POST['qualotipodevalor'])){
			
		$verificarvalor = $_POST['qualotipodevalor']	;
				}
		if(isset($_POST['qualotipodevalorr']))
	{
			
		$verificarvalor2 = $_POST['qualotipodevalorr'];
			
		}
		
			if(isset($_POST['verificarseestaobrigatorio']))
			{
			
		$obrigatoriosim = $_POST['verificarseestaobrigatorio'];
			
		}
		
		
		
		
	

		
		$nomedosubitem = $_POST['nomeDosubitem'];
        $nomedoitem = $_POST['itemescolhido'];	
        $nomedaunidade = $_POST['subitem_unit_type'];	

		$ordemdoformulario = $_POST['verificarordemdocampo'];	
		
		
		
		$errados = 0;
		$x=1;
		
		
		 if($nomedosubitem =='') {
				echo('O campo do nome do subitem esta incompleto <br>');
				 $errados=1;
			}
			if ( !preg_match ("/^[A-Za-zá-úÁ-Ú' ]*$/",$nomedosubitem)) {
		    echo('O Nome do subitem nao pode ter caracteres especiais <br>');
		    $errados=1;
	 } 
		
		if($ordemdoformulario == '' and $ordemdoformulario <$x){
			echo('O campo da ordem do formulario esta incompleto e não pode ser 0<br>');
			$errados=1;
			
		}
		if(!is_numeric($ordemdoformulario)){
			echo('O campo da ordem do formulario não pode conter letras e não pode ser 0<br>');
			$errados=1;
		}
		if($ordemdoformulario == 0){
			echo('O campo da ordem do formulario esta incompleto e não pode ser 0<br>');
			$errados=1;
			
		}
		
		
		
		
		 if($verificarvalor == '' ){  
          
        echo'Faltou preencher o Tipo de  Valor<br>';
		 $errados=1;
		}
		
		 
		 
	     if($verificarvalor2 == '') {                                    
        echo'Faltou preencher o Tipo do campo do formulario<br>';
		 $errados=1;
    }
	
	
			 if($nomedoitem =='') {
				echo('O campo do nome do item esta incompleto <br>');
				 $errados=1;
			}
		
	 
	 
	
		 if($obrigatoriosim != 1 and $obrigatoriosim != 0  and $obrigatoriosim== NULL  ) {                                    
        echo'Faltou definir a obrigatoriedade<br>';
		 $errados=1;
    }
	 
	
		if($errados==1){
		GoBackButton();
		}
	
		
	
	if($errados==0){
	echo 'Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos ?
	<br>
	'
	;
	echo "<br><strong> Nome do subitem: </strong><br>";
	echo $nomedosubitem;
	 echo "   <br><strong>Valor do subitem:</strong><br>";
	 echo $verificarvalor ;

	echo "	  <br><strong>ID do item:</strong><br>" ;
	echo $nomedoitem ;
	echo	"  <br><strong>valor do Item:</strong><br>";
	echo $verificarvalor2;
	
	
	if($nomedaunidade != 22){
    echo " <br><strong>ID da unidade:</strong><br> " ;
	echo $nomedaunidade ;
	}
	
	
		
	echo	  "
		  <br><strong>Ordem do campo:</strong><br>" .$ordemdoformulario."
		  <br><strong>Obrigatorio?:</strong><br>"  ;
		 if ($obrigatoriosim == 1){
			  echo' Sim';
			  
		  }	 
		   if ($obrigatoriosim == 0){
			  echo' Não';
			  
		  }	
		  
		
		  

		  
		  
		  
		  
	  echo'<form action=""  method="POST" id= "formularioparaenviar">
	           
            <input  value='.$nomedosubitem.' name="nomedosubitemparaenviar" type="hidden" >
			   <input  value='.$verificarvalor.' name="valorparaenviar" type="hidden" >
			  <input   value='.$nomedoitem.' name="nomedoitemparaenviar" type="hidden" >
			  <input  value='.$verificarvalor2.' name="valor2paraenviar" type="hidden" >
			   <input  value='.$nomedaunidade.' name="nomedaunidadeparaenviar" type="hidden" >
              <input  value='.$ordemdoformulario.' name="ordemdoformularioparaenviar"  type="hidden" >
			   <input  value='.$obrigatoriosim.' name="enviarobrigatorio"  type="hidden" >
    	      <input value="enviar"  name="enviarvalidacao" type="submit"  >
			  <input   type="hidden"   name="estado_atual" value="enviar"  >
               </form>';
	  GoBackButton();


}
		
		
		
		
		

		
        

     




	}
	  else if($_POST["estado_atual"]=="enviar"){

		        
				echo( "<h3> Dados de registo - inserção </h3>");
				
				
				$seraosubitem=$_REQUEST["nomedosubitemparaenviar"];
				$seraoitem=$_REQUEST["nomedoitemparaenviar"];
			    $seraovaluetype=$_REQUEST["valorparaenviar"];
		        $seraformfieldtype=$_REQUEST["valor2paraenviar"];
	 	        $serasubitem_unit_type=$_REQUEST["nomedaunidadeparaenviar"];
	            $seraaordemformulario=$_REQUEST["ordemdoformularioparaenviar"];
                $seraomandatory=$_REQUEST["enviarobrigatorio"];
				$temporario="";
              

                $insere_subitem_query = "INSERT INTO subitem (name,item_id,value_type,form_field_name,form_field_type,unit_type_id,form_field_order,mandatory,state)
                 VALUES ('$seraosubitem','$seraoitem','$seraovaluetype','$temporario','$seraformfieldtype','$serasubitem_unit_type','$seraaordemformulario','$seraomandatory','active')";
				 
				if ($serasubitem_unit_type ==NULL){
					
					                $insere_subitem_query = "INSERT INTO subitem (name,item_id,value_type,form_field_name,form_field_type,form_field_order,mandatory,state)
                 VALUES ('$seraosubitem','$seraoitem','$seraovaluetype','$temporario','$seraformfieldtype','$seraaordemformulario','$seraomandatory','active')";
					
				}
		 
	            mysqli_query($link,$insere_subitem_query);
        
     
		
		  $o_id_do_sub_item=mysqli_insert_id($link);
		  $variavel_com_field_name="";
		  $query_com_nome_do_item='Select name from item Where id='.$seraoitem.'';
	
	      $resultado_item_nome=mysqli_query($link,$query_com_nome_do_item);
	        if($array_com_os_nomes=mysqli_fetch_assoc($resultado_item_nome)){
					 $variavel_para_os_nomes=substr($array_com_os_nomes["name"],0,3);
					 }
					 
					 
	        $seraosubitem = str_replace(' ', '_', $seraosubitem);
	        $variavel_com_field_name=$variavel_para_os_nomes."-".$o_id_do_sub_item."-".$seraosubitem;
			  $update_subitem="UPDATE subitem SET form_field_name='$variavel_com_field_name' Where id='$o_id_do_sub_item'";
			  	 

			if(mysqli_query($link,$update_subitem)){
            echo "Inseriu os dados de registo com sucesso<br>";
			 echo 'Clique em <a href="'.$current_page.'">Continuar</a> para avançar';
		
		
			
			
			
        }
		 
			  
              		  
			  
			  
	 
}



?>