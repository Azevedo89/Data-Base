<?php
    require_once ("custom/php/common.php");
    global $current_page, $link;

    $link = ConnectDatabase();
    $capability_name = 'manage_itens';
    $current_page = get_site_url().'/index.php/'.basename(get_permalink());

    if (current_user_can($capability_name) & is_user_logged_in())
    {
        if (!isset($_REQUEST['state'])) // Verify wheter the $ request comes with somthing, gives false because its empty, but with not enters the loop
        {
            $number_rows = "SELECT * 
                            FROM ITEM;";// query to verify wheter we have items on the table
            $number_rows_result = mysqli_query($link,$number_rows);
            if (!$number_rows_result)
            {
                echo "<br>";
                echo "Error description: ".mysqli_error($link);
            }
            if (mysqli_num_rows($number_rows_result) > 0) // if there's items then we need to show the content
            {
                echo "<br>";
                ShowTable(); // Displays the table heading
                ShowContent($link); // Displays the rows of the table
                echo "</table>";// Here after, showing content, to verify that I closed the table
                ShowForm($link, $current_page);// Shows the form to insert items
            }
            else
            {
                echo"<br>";
                echo "Não há itens nesta tabela!";
            }
        }
        elseif($_REQUEST['state'] == "inserir") // after the submission of the form we come to here
        {
            echo "<h3>";
            echo "Gestão de itens - inserção";
            echo "</h3>";

            $item_name_control = false;
            $item_type_control = false;
            $item_state_control = false;

            global $item_name;
            global $item_type;
            global $item_state;

            if (!empty($_REQUEST['item_name'])) // first of all we need to verify the form fields that came, in order to verify that we dont send trash to the DB
            {
                if(!preg_match("/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/",$_REQUEST['item_name'])) // Verify wheter the item name has the respetive caracters, if haves numbers it will not be valid
                {
                    echo "<p>";
                    echo "<strong>";
                    echo "O nome do item não está dentro das normas!";
                    echo "</strong>";
                    echo "<p>";
                }
                else
                {
                    $item_name_control = true; // work variable to control the insert to the table
                }
            }
            else
            {
                echo "<p>";
                echo "<strong>";
                echo "O nome do item não pode estar em branco!";
                echo "</strong>";
                echo "</p>";
            }

            if(empty($_POST['item_type'])) // verifys if it is empty
            {
                echo "<p>";
                echo "</strong>";
                echo "O tipo de item é de preenchimento obrigatório!";
                echo "</strong>";
                echo "</p>";
            }
            else
            {
                $item_type_control = true; // work variable to control the insert to the table
            }

            if(empty($_REQUEST['item_state'])) // verifys if it is empty
            {
                echo "<p>";
                echo "</strong>";
                echo "O estado do item é de preenchimento obrigatório!";
                echo "</strong>";
                echo "</p>";
            }
            else
            {
                $item_state_control = true; // work variable to control the insert to the table
            }

            if($item_name_control & $item_state_control & $item_type_control) // if all the control variables are true insert the data to the table
            {
                $item_name = $_REQUEST['item_name'];
                $item_state = $_REQUEST['item_state'];
                $item_type = $_REQUEST['item_type'];
                Insert($item_name, $item_type, $item_state,$link,$current_page);
            }
            else
            {
                GoBackButton();
            }
        }
    }
    else
    {
        echo "<br>";
        echo "Não tem autorização para aceder a esta página!";
        VerifyLogIn();
        VerifyCapability($capability_name);
    }

    function ShowTable()
    {
        echo "<table>";
        echo "<tr>";
        echo "<th>";
        echo "<strong>";
        echo "tipo de item";
        echo "</strong>";
        echo "</th>";
        echo "<th>";
        echo "<strong>";
        echo "id";
        echo "</strong>";
        echo "</th>";
        echo "<th>";
        echo "<strong>";
        echo "nome do item";
        echo "</strong>";
        echo "</th>";
        echo "<th>";
        echo "<strong>";
        echo "estado";
        echo "</strong>";
        echo "</th>";
        echo "<th>";
        echo "<strong>";
        echo "ação";
        echo "</strong>";
        echo "</th>";
        echo "</tr>";
    }

    function ShowContent($link)
    {
        $item_type_query ="SELECT id, name  
                                FROM item_type"; // query to get the item types from the item_types table
        $item_type_result= mysqli_query($link,$item_type_query);
        if (!$item_type_result)
        {
            echo "<br>";
            echo "Error description: ".mysqli_error($link);
        }
        while($item_type = mysqli_fetch_assoc($item_type_result))
        {
            $item_query = "SELECT item.id,item.name,item.state
                                FROM item_type,item
                                WHERE item_type.id=item.item_type_id AND item.item_type_id = ".$item_type['id'].
                                " ORDER BY item.name ASC"; //query to get the respective item associated to the item_types table, and order them by name a to z
            $item_query_result = mysqli_query($link,$item_query);
            if(!$item_query_result)
            {
                echo "<br>";
                echo "Error description: ".mysqli_error($link);
            }
            $rowspan = mysqli_num_rows($item_query_result); // the number of rows that I will get from the past query, will be the number to put on the rowspawn of the td
            if($rowspan > 0) // if the rowspawn is bigger than zero it will show the normal format
            {
                echo "<tr>";
                echo "<td rowspan=" .$rowspan. " >";
                echo $item_type['name'];
                echo "</td>";
                // in order to loop through the item, there a need to declare the rowspan earlier, otherwise it would fuck up the table display
                while($item = mysqli_fetch_assoc($item_query_result))
                {
                    echo "<td>";
                    echo $item["id"];
                    echo "</td>";
                    echo "<td>";
                    echo $item["name"];
                    echo "</td>" ;
                    echo "<td>";
                    echo $item["state"];
                    echo "</td>" ;
                    echo "<td>";
                    echo "[editar] [desativar] [apagar]";
                    echo "</td>" ;
                    echo "</tr>";
                }
            }
            else // If the rowspan is lower than zero, then it must display that the item_type does not have items. Use of colspan
            {
                echo "<tr>";
                echo "<td >";
                echo $item_type['name'];
                echo "</td>";
                echo "<td colspan='4' style='text-align:center'>";
                echo "Não existem itens para este tipo de item!";
                echo "</td>";
                echo "</tr>";
            }

        }
    }

    function ShowForm($link,$current_page)
    {
        echo "<h3>";
        echo "Gestão de itens - Introdução";
        echo "</h3>";
        echo "<form method='post' action='{$current_page}'>"; //action will send the form-data to the current page
        echo "<label for='item_name'>";
        echo "Nome do item";
        echo "<span style='color:red'>";
        echo "*";
        echo "</span>";
        echo "</label>";
        echo "<br>";
        echo "<input type='text' id='item_name' name='item_name' value=''>"; // id and name are the same, in order to simplify my life
        echo "<br>";
        echo "<br>";
        echo "<label for='item_type'>";
        echo "Tipo de item";
        echo "<span style='color:red'>";
        echo "*";
        echo "</span>";
        echo "</label>";
        echo "<br>";
        $item_type_query = "SELECT id,name
                        FROM item_type";
        $item_type_result = mysqli_query($link,$item_type_query); // query to get the existents item types
        if (!$item_type_result)
        {
            echo "Error description: ".mysqli_error($link);
        }
        while($item_type = mysqli_fetch_assoc($item_type_result)) // it loops trough the query array
        {
            echo "<input type='radio' id='";
            echo $item_type["id"]; // each one has the respetive value, just like the teacher asked
            echo "' name='item_type' value='";
            echo $item_type["id"];
            echo "'>";
            echo $item_type["name"];
            echo "<br>";
        }
        echo "<br>";
        echo "<label for='item_state'>";
        echo "Estado do item";
        echo "<span style='color:red'>";
        echo "*";
        echo "</span>";
        echo "</label>";
        echo "<br>";
        echo "<input type='radio' id='item_state_active' name='item_state' value='active'>";
        echo "ativo";
        echo "<br>";
        echo "<input type='radio' id='item_state_inactive' name='item_state' value='inactive'>";
        echo "inactive";
        echo "<br>";
        echo "<p style='color:red'>";
        echo "*Obrigatório preencher";
        echo "</p>";
        echo "<br>";
        echo "<input type='hidden' name='state' value='inserir'>"; // sends the state has hidden, because its supposed to be checked on the server side
        echo "<input type='submit' value='Inserir item'>";
        echo "</form>";
    }

    function Insert($item_name, $item_type, $item_state,$link,$current_page)
    {
        $insert = "INSERT INTO item (name, item_type_id, state) VALUES ('".$item_name."' , '".$item_type."' , '".$item_state."')"; // query to insert the item to the table
        $insert_query = mysqli_query($link,$insert);
        if(!$insert_query)
        {
            echo "<br>";
            echo "Error description: ".mysqli_error($link);
        }
        else
        {
            echo "<p>";
            echo"Inseriu os dados de novo item com sucesso.";
            echo "</p>";
            echo"Clique em <a href='{$current_page}'>Continuar</a> para avançar"; // href to get back to page
        }
    }
?>