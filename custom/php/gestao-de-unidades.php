<?php
require_once("custom/php/common.php");
global $current_page;
$current_page = get_site_url().'/'.basename(get_permalink());
$link = ConnectDatabase();
$query1_do_subitem_unit_type = "SELECT * FROM subitem_unit_type";
$resultado_da_query1_do_subitem_unit_type = mysqli_query($link, $query1_do_subitem_unit_type);
$conectar_base = "manage_unit_types";
$nome_ao_inserir = "";
$erro_ao_inserir = false;
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
		if(isset($_POST['estado']) == "")
		{ 
			$erro_ao_inserir = false;
			if(mysqli_num_rows($resultado_da_query1_do_subitem_unit_type) == 0)
			{
				echo 
					"<p>
					<strong> 
					Não há tipos de unidades 
					</strong>
					</p>";
			}
			else
			{
				echo 
					'<table>
						<tr class="tabela">
							<th>
								<strong> id </strong>
							</th> 
							<th>
								<strong> unidade </strong>
							</th> 
							<th>
								<strong> subitem </strong>
							</th>
						</tr>
						<style>
							.tabela {
								background-color: #ffb914; 
								color: white; 
								font-size: 30px};
						</style>';
				$query2_unit_type_do_subitem = "SELECT subitem_unit_type.id AS id_do_tipo_de_subitem, 
				subitem_unit_type.name AS nome_do_tipo_de_subitem 
				FROM subitem_unit_type 
				ORDER BY nome_do_tipo_de_subitem";
				$resultado_da_query2_unit_type_do_subitem = mysqli_query($link, $query2_unit_type_do_subitem); 
				$quantidade_de_linhas = ($resultado_da_query2_unit_type_do_subitem);
				foreach($resultado_da_query2_unit_type_do_subitem as $linhas_da_tabela)
				{
					$contadorLinhas = 1;
					$adicionar_virgulas = ",";
					echo 
						"<tr>
							<td class='linha'>$linhas_da_tabela[id_do_tipo_de_subitem]</td>
							<td class='linha'>$linhas_da_tabela[nome_do_tipo_de_subitem]</td>
							<td class='linha'>";
					$query3_nome_item_subitem = "SELECT DISTINCT item.name as item, 
					subitem.name as subitem, 
					subitem.unit_type_id as id_do_tipo_unidade 
					FROM item, subitem 
					WHERE subitem.item_id = item.id 
					AND subitem.unit_type_id = "
					.$linhas_da_tabela['id_do_tipo_de_subitem'];
					$resultado_da_query3_nome_item_subitem = mysqli_query($link, $query3_nome_item_subitem);
					$numero_de_linhas = mysqli_num_rows($resultado_da_query3_nome_item_subitem); 
					foreach($resultado_da_query3_nome_item_subitem as $resultado_da_query3)
					{
						if($contadorLinhas == $numero_de_linhas)
						{
							$adicionar_virgulas = "";
						}
						echo 
							$resultado_da_query3["subitem"]." 
							(".$resultado_da_query3["item"].")"
							.$adicionar_virgulas." ";
						$contadorLinhas++;
					}
					echo 
						"</td> 
						</tr>
						<style> 
							.linha {
								text-align: center;
								background-color: blue;
								color: white;}
						</style>";
				}			
				echo 
					"</table>";
				echo 
					"<h3>
					<strong> 
					Gestão de unidades - introdução 
					</strong>
					</h3>";
				echo 
					'<form method = "POST" 
					action = "">
					<label>
					<strong> 
					Insere o teu nome: 
					</strong>
					</label>
					<p>
					<input type = "Nome" 
					name = "nome" 
					value = "name">
					</p>
					<br>
					<p>
					<input type = "hidden" 
					name = "estado" 
					value = "inserir">
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
		}
		else if(isset($_POST['estado']) == "inserir")
		{
			$nome_ao_inserir= $_POST['nome'];
			echo 
				"<h3>
				<strong> 
				Gestão de unidades - inserção 
				</strong>
				</h3>";
			if ($nome_ao_inserir == "")
			{
				$erro_ao_inserir = true;
				echo 
					"<p> 
					Obrigatório escrever o nome 
					</p>";
			}
			else if (!preg_match("/^[A-Za-zá-úÁ-Ú' ]*$/",$nome_ao_inserir))
			{
				$erro_ao_inserir = true;
				echo 
					"<p> 
					Proíbido inserir caracteres especiais 
					</p>";
			}  
			if($erro_ao_inserir) 
			{
				GoBackButton();
			}
			else
			{
				$inserir_unidade_subitem = "INSERT INTO subitem_unit_type (name) 
				VALUES ('$nome_ao_inserir')";
				$resultado_ao_inserir_unidade_subitem = mysqli_query($link, $inserir_unidade_subitem);
				if($resultado_ao_inserir_unidade_subitem)
				{ 
					echo 
						"<p> 
						Inseriu os dados de novo tipo de unidade com sucesso 
						<br> 
						Clique em continuar para avançar 
						</p>
						<br>"; 
						echo 
							'<form method = "POST" 
							action = "">
							<p>
							<input type = "submit" 
							name = "continue" 
							value = "submit">
							</p>
							<br>
							</form>';
				} 
			} 
		} 
	} 
} 
?>  