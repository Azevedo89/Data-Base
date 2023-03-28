<?php
require_once("custom/php/common.php");
global $current_page;
$current_page = get_site_url().'/'.basename(get_permalink());
$link = ConnectDatabase();
$conectar_base = "insert_values";
if(!current_user_can($conectar_base)) 
{
	echo 
		"<p>
		<strong> 
		SINTAX Error: Não tem autorização para aceder a esta página 
		</strong>
		</p>";
}
else
{
	if(is_user_logged_in()) 
	{
		if(isset($_REQUEST['estado']) == "")
		{
			echo 
				"<h3>
				<strong> 
				Inserção de valores - criança - procurar
				</strong>
				</h3>"; 
			echo 
				"<p> 
				Introduza um dos nomes da criança a encontrar e/ou a data de nascimento dela
				</p>";
			echo 
				'<form method = "post" 
				action="'.$current_page.'" >
				<label>
				<strong> 
				Insere o teu nome: 
				</strong>
				</label>
				<p>
				<input type = "text" 
				name = "name" >
				</p>
				<br>
				<label>
				<strong> 
				Insere a tua data de nascimento: 
				</strong>
				</label>
				<p>
				<input type = "text" 
				name = "Data_Nascimento" >
				</p>
				<br>
				<p>
				<input type = "hidden" 
				name = "estado" 
				value = "escolher_crianca">
				</p>
				<br>
				<p>
				<input type = "submit" 
				name = "submit" 
				value = "submit">
				</p>
				<br>
				</form>';
		}
		else if(($_REQUEST['estado']) == "escolher_crianca")
		{
			$nome_crianca = $_POST['name'];
			$data_nascimento_crianca = $_POST['Data_Nascimento'];
			echo 
				"<h3>
				<strong> 
				Inserção de valores - criança - escolher 
				</strong>
				</h3>";
			if($nome_crianca == "" and $data_nascimento_crianca == "")
			{
				$nenhum_inserido = "SELECT DISTINCT child.id AS id_crianca, 
				child.name AS childName, 
				child.birth_date AS child_birthDate, 
				child.tutor_name AS nome_tutor_crianca, 
				child.tutor_phone AS numero_tutor_crianca, 
				child.tutor_email AS email_tutor_crianca 
				FROM child";
				$res_nenhum_inserido = mysqli_query($link, $nenhum_inserido);
				$num_rows_nenhum_inserido = mysqli_num_rows($res_nenhum_inserido);
				foreach($res_nenhum_inserido as $result)
				{
					echo 
						"<li>
						[".'<a href="insercao-de-valores?estado=escolher_item&crianca='
						.$result['id_crianca'].'">'
						.$result['childName'].'</a>'."] ("
						.$result['child_birthDate'].")
						</li>
						<br>";
				}
			}
			else if($nome_crianca != "" and $data_nascimento_crianca != "")
			{
				echo "teste";
				$inserir_ambos = "SELECT DISTINCT child.id AS id_crianca, 
				child.birth_date AS child_birthDate, 
				child.name AS childName 
				FROM child 
				WHERE child.name 
				LIKE '%".$nome_crianca."%' 
				AND child.birth_date 
				LIKE '%".$data_nascimento_crianca."%'";
				$res_inserir_ambos = mysqli_query($link, $inserir_ambos);
				$num_rows_inserir_ambos = mysqli_num_rows($res_inserir_ambos);
				foreach($res_inserir_ambos as $inserir)
				{
					echo 
						"<li>
						[".'<a href="insercao-de-valores?estado=escolher_item&crianca='
						.$inserir['id_crianca'].'">'
						.$inserir['childName'].'</a>'."] ("
						.$inserir['child_birthDate'].")
						</li>
						<br>";
				}
			}		
			else if($nome_crianca == "" and $data_nascimento_crianca != "")
			{
				$inserir_data_nascimento = "SELECT child.id AS id_crianca, 
				child.birth_date AS child_birthDate, 
				child.name AS childName 
				FROM child 
				WHERE child.birth_date 
				LIKE '%".$data_nascimento_crianca."%'";
				$res_inserir_data_nascimento = mysqli_query($link,$inserir_data_nascimento);
				$num_rows_inserir_data_nascimento = mysqli_num_rows($res_inserir_data_nascimento);
				foreach($res_inserir_data_nascimento as $data_nascimento)
				{
					echo
						"<li>
						[".'<a href="insercao-de-valores?estado=escolher_item&crianca='
						.$data_nascimento['id_crianca'].'">'
						.$data_nascimento['childName'].'</a>'."] ("
						.$data_nascimento['child_birthDate'].")
						</li>
						<br>";
				}		
			} 
			else if($nome_crianca != "" and $data_nascimento_crianca == "")
			{
				$inserir_nome = "SELECT DISTINCT child.id AS id_crianca, 
				child.birth_date AS child_birthDate, 
				child.name AS childName 
				FROM child 
				WHERE child.name 
				LIKE '%".$nome_crianca."%'";
				$res_inserir_nome = mysqli_query($link, $inserir_nome);
				$num_rows_inserir_nome = mysqli_num_rows($res_inserir_nome);
				foreach($res_inserir_nome as $nome_da_crianca)
				{
					echo 
						"<li>
						[".'<a href="insercao-de-valores?estado=escolher_item&crianca='
						.$nome_da_crianca['id_crianca'].'">'
						.$nome_da_crianca['childName'].'</a>'."] ("
						.$nome_da_crianca['child_birthDate'].")
						</li>
						<br>";
				}
			} 		
		}
		else if(($_REQUEST['estado']) == "escolher_item")
		{
			echo 
				"<h3>
				<strong> 
				Inserção de valores - escolher item 
				</strong>
				</h3>";
			$_SESSION["child_id"] = $_REQUEST['crianca'];
			$tipo_itens = "SELECT DISTINCT item_type.id AS id_tipoItem, 
			item_type.name AS nome_tipoItem, item_type.code AS code_tipoItem 
			FROM item_type";
			$res_tipo_itens = mysqli_query($link, $tipo_itens);
			$resTipoItens = mysqli_num_rows($res_tipo_itens);
			foreach($res_tipo_itens as $result1)
			{
				echo 
				$result1['nome_tipoItem'];
				$itens = "SELECT DISTINCT item.id 
				AS idItem, 
				item.name 
				AS nomeItem, 
				item.item_type_id 
				AS idTipoItem 
				FROM item ,item_type 
				WHERE item.state = 'active' 
				AND item.item_type_id = "
				.$result1['id_tipoItem'];
				$res_itens = mysqli_query($link, $itens);
				$num_rows2 = mysqli_num_rows($res_itens);
				foreach($res_itens as $result2)
				{
					echo 
						"<ul>
						<li>
						[".'<a href="insercao-de-valores?estado=introducao&item='
						.$result2['idItem'].'">'
						.$result2['nomeItem'].'</a>'."]"."</li>
						</ul>";
				} 
			} 
		}
		else if(($_REQUEST['estado']) == "introducao")
		{
			$_SESSION["item_id"] = $_REQUEST["item"];
			$item_nome = "SELECT DISTINCT item.name AS nome_item 
			FROM item 
			WHERE item.id = "
			.$_SESSION["item_id"];
			$res_item_nome = mysqli_query($link, $item_nome);
			$resul_nome_item = mysqli_num_rows($res_item_nome);
			foreach($res_item_nome as $result_item)
			{
				$_SESSION["item_name"] = $result_item["nome_item"];
			}
			$item_type_id = "SELECT DISTINCT item.item_type_id AS idTipo_item 
			FROM item 
			WHERE item.id = "
			.$_SESSION["item_id"];
			$res_item_type_id = mysqli_query($link, $item_type_id);
			$res_id_type_item = mysqli_num_rows($res_item_type_id);
			foreach($res_item_type_id as $resultItemType_id)
			{
				$_SESSION["item_type_id"] = $resultItemType_id["idTipo_item"];
			}
			echo 
				"<h3>
				<strong>".
				"Inserção de valores 1- "
				.$_SESSION['item_name']."
				</strong>
				</h3>
				<br>";
			echo 
				"<h3>
				<strong>".
				"item_type_"
				.$_SESSION['item_type_id'].
				"_item_"
				.$_SESSION['item_id']."
				</strong>
				</h3>";
			echo 
				"<form method = 'POST' 
				action = "."insercao-de-valores?estado = validar&item = "
				.$_SESSION['item_id'].">";								
				$query_subitens = "SELECT * 
				FROM subitem 
				WHERE subitem.item_id = "
				.$_SESSION['item_id']." 
				AND subitem.state = 'active' 
				ORDER BY subitem.form_field_order";
				$res_query_subitens = mysqli_query($link, $query_subitens);					
			while($each_res_query_subitens = mysqli_fetch_assoc($res_query_subitens))
			{
				switch($each_res_query_subitens['value_type'])
				{
					case "text":
						$tipo_campo = $each_res_query_subitens['form_field_type'];
						if ($tipo_campo == 'text')
						{
							echo 
								"<strong>"
								.$each_res_query_subitens['form_field_name'].
								"</strong>".
								"<input placeholder = "
								.$each_res_query_subitens['name'].
								"><br>";
						}
						else if ($tipo_campo == 'textbox')
						{
							echo 
								"<strong>"
								.$each_res_query_subitens['form_field_name'].
								"</strong>".
								"<textarea rows='5' cols='20'
								><br>";
						}
						$input_name_subitem = $each_res_query_subitens['name'];
					break;
					case "bool":
						$input_name_subitem = $each_res_query_subitens['name'];
						$proibido_espacos = str_ireplace(' ', '_', $input_name_subitem);
						echo 
							"<strong>"
							.$each_res_query_subitens['form_field_name'].
							"</strong>".
							"<input type = 'radio' 
							name = "
							.$proibido_espacos." 
							value = 1>True
							<br>";
						echo 
							"<strong>"
							.$each_res_query_subitens['form_field_name'].
							"</strong>".
							"<input type = 'radio' 
							name = "
							.$proibido_espacos." 
							value = 0>False
							<br>";
					break;
					case "int":
							$input_name_subitem = $each_res_query_subitens['name'];
							$proibido_espacos = str_ireplace(' ', '_', $input_name_subitem);
							echo 
								"<strong>"
								.$each_res_query_subitens['form_field_name'].
								"</strong>".
								"<input placeholder = "
								.$each_res_query_subitens['name'].
								"><br>";		
					break;
					case "double":
							$input_name_subitem = $each_res_query_subitens['name'];
							$proibido_espacos = str_ireplace(' ', '_', $input_name_subitem);
							echo 
								"<strong>"
								.$each_res_query_subitens['form_field_name'].
								"</strong>".
								"<input placeholder = "
								.$each_res_query_subitens['name'].
								"><br>";
					break;
					case "enum":					
						$tipo_campo = $each_res_query_subitens['form_field_type'];
						$opcoes = "SELECT * 
						FROM subitem_allowed_value 
						WHERE subitem_allowed_value.subitem_id ="
						.$each_res_query_subitens['id'];
						$res_opcoes = mysqli_query($link, $opcoes);
						echo 
							"<strong>"
							.$each_res_query_subitens['form_field_name'].
							"</strong>
							<br>";		
						$num_allowed_values = mysqli_num_rows($res_opcoes);
						$allowed_values_checkbox = [];
						$contador = 0;
						while($each_res_opcoes = mysqli_fetch_assoc($res_opcoes)){
							switch($tipo_campo){					
								case "radio":
									echo 
										"value: "
										.$each_res_opcoes['value'].
										"<br>";
									echo 
										"<input type='radio' 
										value="
										.$each_res_opcoes['value'].
										"><br>";	
								break;		
								case "checkbox":
									echo 
										"value: "
										.$each_res_opcoes['value'].
										"<br>";
									echo 
										"<input type='checkbox' 
										value="
										.$each_res_opcoes['value'].
										"><br>";
								break;
								
								case "selectbox":
									$allowed_values_checkbox[$contador] = $each_res_opcoes['value'];
									$contador++;
								break;
							}
						}
						if(!empty($allowed_values_checkbox))
						{
							echo 
								"<select name='allowed_values'>";
							for($i = 0; $i < sizeof($allowed_values_checkbox); $i++)
							{
								echo 
									"<option value="
									.$allowed_values_checkbox[$i].
									">"
									.$allowed_values_checkbox[$i].
									"</option>";
							}
							echo 
								"</select>
								<br>";
						}
					break;
				} 
			}
			echo 
				"<input type = 'hidden' 
				name = 'estado' 
				value = 'validar'>";
			echo 
				"<input type = 'submit' 
				name = 'submeter' 
				value = 'submeter'>";
		} 
		else if(($_REQUEST['estado']) == "inserir")
		{
			echo 
				"<h3>
				<strong>  
				Inserção de valores -"
				.$_SESSION['item_name'].
				"- validar
				</strong>
				</h3>";
		}
	} 
}
?> 