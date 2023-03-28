<?php
$coneccao =mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME); 
require_once("custom/php/common.php");


$verificar_capability = "manage_records";

if(!current_user_can($verificar_capability)) 
{
	echo 
		die("Não tem autorização para aceder a esta página");
}          
$query_tabela_child = "SELECT DISTINCT  
child.birth_date,
child.name,
child.tutor_phone,
child.tutor_name,
child.tutor_email,
child.id 
FROM
 child 
ORDER BY child.name ";
$resultado_query_tabela_child = mysqli_query($coneccao, $query_tabela_child);

        if(isset($_POST["estado_atual"])=="")
        {
            if(mysqli_num_rows($resultado_query_tabela_child) == 0)
            {
                echo "Não há crianças";
            }
			
			
            else
            {
				
				echo'<body>';
			echo'	<table  class="csstabela">';
			echo'	<tr class="linhascss">';
			echo'	<td class="testee" >';
		echo'		Nome';
			echo'	</td>';
			echo'	<td class="testee"> ';
			echo'	Data de nascimento ';
			echo'	</td >';
			echo'	<td class="testee">';
			echo'	Enc. de educação';
		echo'		</td>';
          echo'      <td class="testee"> ';
		echo'		Telefone do Enc.';
		echo'		</td>';
				
		echo'        <td class="testee">';
		echo'		e-mail';
		echo'		</td>';
			
		  echo'      <td class="testee">';
		echo'		registos ';
		echo'		</td>';
				
		  echo'      </tr>';
		  
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

					
						</style>' ;
		  
		  
		  
		     while ($dados = mysqli_fetch_assoc($resultado_query_tabela_child )) {
			 $nome = $dados["name"];
             $email = $dados["tutor_email"];
             $Data_Nasc = $dados["birth_date"];
			 $Tutor_telefone = $dados["tutor_phone"];
			 $Tutor_nome = $dados["tutor_name"];
             
			 echo '
			 <tr>';
		 echo '	 <td>';
		 echo "	 $nome";
		echo "	 </td>";
		echo "	 <td>";
		echo "	 $Data_Nasc";
		echo "	 </td>";
		echo "	 <td>";
		echo "	 $Tutor_nome";
		echo "	 </td>";
        echo "     <td>";
		echo "	 $Tutor_telefone";
		echo "	 </td>";
		echo "	 <td>";
			echo " $email";
	echo "		 </td>";
			 
   
             echo
			 '
			 <td>
			 ';
			 
			 $query_tabela_item = " 
			 SELECT DISTINCT item.name AS nome_do_item,
			 item.id FROM value,
			 subitem,item 
			 WHERE   value.child_id =".$dados["id"]." 
			 and subitem.item_id = item.id 
			 and subitem.id =value.subitem_id
			 ORDER BY nome_do_item  ";
             $resultado_query_tabela_item = mysqli_query($coneccao, $query_tabela_item);
             while ($dadoss = mysqli_fetch_assoc($resultado_query_tabela_item )) {
				 echo "<p>".$dadoss["nome_do_item"].":  <br> ";
				
				 
				 
			 
			 $query_tabela_subitem = '
			 SELECT DISTINCT subitem.name AS nome_do_subitem,
			 subitem.id FROM subitem,
			 value,item 
			 WHERE  value.subitem_id = subitem.id 
			 and   value.child_id='.$dados["id"].' 
			 and subitem.item_id='.$dadoss["id"];
		     $resultado_query_tabela_subitem = mysqli_query($coneccao, $query_tabela_subitem);
			
			 while ($dadosss = mysqli_fetch_assoc($resultado_query_tabela_subitem )) {
		
			 $query_tabela_value ="
			 SELECT DISTINCT  value.id,
			 value.value,
			 value.producer,
			 value.date
                    FROM child,
					subitem, 
					item,
					value 
					WHERE  producer='user' 
					and subitem.id = value.subitem_id 
					and item.id = subitem.item_id 
					AND child.id = value.child_id
					AND child.id=".$dados['id']." 
					AND subitem.id=".$dadosss['id']." 
					AND item.id =".$dadoss['id'] ;
		      $resultado_query_tabela_value = mysqli_query($coneccao, $query_tabela_value);
			
			   
			   while ($dadossss = mysqli_fetch_assoc($resultado_query_tabela_value )) {
			
			
				   if ($dadossss['value'] != NULL && $dadossss['date'] != NULL) 
				   {
                    echo'[editar] [apagar] - ';
                    echo'<strong>'
					.$dadossss['date'].
					'</strong> ';
                    echo' ('.$dadossss['producer'].") -";


                    echo"<strong>"
					.$dadosss['nome_do_subitem']."
					</strong>  (".$dadossss['value'].'); 
					<br>';
				    //echo " ".$dadossss["typeunit"];
                }
				   
				   
				   
			
			   }	 
			 }
				 	 
 
			 }
		            echo "</td>";
                    echo "</tr>";
			 }
		     echo "</table>";
			 
			 
		    echo "<h3>Dados de registo - introdução</h3>";
		    echo "Introduza os dados pessoais básicos da criança:";
		      echo '<form action="" id = "formulario" method="post">';
                echo'<label for="nomeDaCrianca">Nome </label>
                <input placeholder = "Roberto Fernandes" id="nomeDaCrianca" name="nomeDaCrianca" type="text"">
                <label for="dataDeNascimento">Data de nascimento</label>
                <input placeholder = "Ano - Mes - Dia"   id="dataDeNascimento"   name="dataDeNascimento" type="text"  >
                <label  for="nomeDeEncarregado" >Nome Do Encarregado </label>
                <input  placeholder = "Rui Duarte"  type="text" name="nomeDoencarregadoDeeducacao">
                <label for="TelDoEncarregado">Telefone do encarregado</label>
                <input  placeholder = "961111111"  type="text" maxlength="9" name="telemovel" id="TelDoEncarregado"   >
                <label for="EmailDeEncarregado" >Endereço de e-mail</label>
                <input placeholder ="example@hotmail.com" type="text" name="emaildotutor">
                <input type="hidden" name="estado_atual"  value="validar">
                <input type="submit" name="submeterr" value="Submeter">
                </form>';
			}
		}  
		
		
	
	
	else if($_POST["estado_atual"] == "validar"){
		
		
	echo '<h3>';
   echo '<strong>';	
	
    echo "Dados de registo - validação";
	  echo '</strong>';
	echo ' </h3>';
		  
		$maildoencarregado = $_POST['emaildotutor'];
		$telemoveldoencarregado = $_POST['telemovel'];
		$Nomedacrianca = $_POST['nomeDaCrianca'];
		$datadenascimento = $_POST['dataDeNascimento'];
		$nomedoencarregado = $_POST['nomeDoencarregadoDeeducacao'];
		
	
		$errados = FALSE;
		
		
	

			
				
		    if($Nomedacrianca ==null) {
				echo('O campo do nome esta incompleto <br>');
				 $errados=TRUE;
			}
	
			
		
			if ( !preg_match ("/^[A-Za-zá-úÁ-Ú' ]*$/",$Nomedacrianca)) {
		    echo('O Nome nao pode ter caracteres especiais <br>');
		    $errados=true;
	 } 
			
			
			if($datadenascimento=='' ){
	        echo('Data Por preencher no formato: Ano- Mes- Dia  <br>');
	        $errados=true;
}

	
		
			
				 
        if($nomedoencarregado=='' ){
	    echo('Campo do nome do encarregador por preencher  <br>');
	    $errados=true;
}
	
	
	
		
		if (!preg_match("/^[a-zà-úA-ZÀ-Ú' ]*$/",$nomedoencarregado)) {
			echo('O Nome do encarregado nao pode conter caracteres especiais  <br>');
			$errados=true;
	
	}
	
	  
	  
	  
if($telemoveldoencarregado==''){
	echo('Necessario preencher o campo do Telemovel do encarregado  <br>');
	$errados=true;
}
	
	
	
		if($telemoveldoencarregado!=''){
		if ( !preg_match("/96[0-9]{7}$/",$telemoveldoencarregado) )  {
		echo'O numero precisa de 9 caracteres e começa com 96  <br>';
		$errados=true;
	}
		}
	
	
		
		if($maildoencarregado!=''){
		if(!filter_var($maildoencarregado,FILTER_VALIDATE_EMAIL)){
			
		echo("O email nao é valido.  <br>" );
		$errados=true;
	
	}
	}
	
	
	  
if($maildoencarregado=='' ){
	echo('O campo do email está por preencher  <br>');
	$errados=true;
}
	if($errados==true){
		GoBackButton();
	}
	
	
	if($errados==false){
	echo 'Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos ?
	<br>';

	echo "<br><strong> Nome Completo: </strong><br>" .$Nomedacrianca ."
	      <br><strong>Data de nascimento:</strong><br>" .$datadenascimento;
		  
		  
		  echo"
		  <br><strong>Nome Do Encarregado:</strong><br>" .$nomedoencarregado;
		  echo " <br><strong>Telefone do encarregado:</strong><br>" .$telemoveldoencarregado;
		echo  "
		        <br><strong>Endereço de e-mail:</strong><br> " .$maildoencarregado;
		  
		  
		  echo'<form action=""  method="POST" id= "formularioparaenviar">';
	           
            echo'   <input  value='.$datadenascimento.' name="dataparaenviar" type="hidden" >';
			   
			   
			  echo '  <input  value='.$Nomedacrianca.' name="nomeparaenviar" type="hidden" >';
			  
		echo '	   <input   value='.$telemoveldoencarregado.' name="telefoneparaenviar" type="hidden" >';
		
		echo ' 	   <input  value='.$nomedoencarregado.' name="nomedoencarregadoparaenviar" type="hidden" >';
		
        echo '       <input  value='.$maildoencarregado.' name="emailparaenviar"  type="hidden" >';
    echo '	       <input value="enviar"  name="enviarvalidacao" type="submit"  >';
		echo'	   <input   type="hidden"   name="estado_atual" value="enviar"  >
                </form>';
				echo'<br>';
				GoBackButton();
	  


}
	

	
	}
	
	        else if($_POST["estado_atual"]=="enviar")
        {
			
			echo( "<h3> Dados de registo - inserção </h3>");
			
			$data_para_enviar = $_POST['dataparaenviar'];
			$nome_para_enviar = $_POST['nomeparaenviar'];
			$telefone_para_enviar = $_POST['telefoneparaenviar'];
			$nome_de_encarregado_para_enviar = $_POST['nomedoencarregadoparaenviar'];
			$email_para_enviar = $_POST['emailparaenviar'];
			
		 $query_para_enviar = "
		 INSERT INTO child 
		 (name,birth_date,tutor_name,tutor_phone,tutor_email)
		 VALUES ('$nome_para_enviar','$data_para_enviar','$nome_de_encarregado_para_enviar','$telefone_para_enviar','$email_para_enviar')";

			$resultadoparaenviar = mysqli_query($coneccao,$query_para_enviar);
			
			
			 if($resultadoparaenviar){
        echo "Inseriu os dados de registo com sucesso <br>";
		echo 'Clique em <a href="'.$current_page.'">Continuar</a> para avançar';
		
				
		  
    }
	else{
		
		echo
		'Data no formato errado! Por valor preencher no formato : Ano- Mes- Dia';
		echo'<br>';
		GoBackButton();
		
	}
	   
		  
			
		}
	

?>