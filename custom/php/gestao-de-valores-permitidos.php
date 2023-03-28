<?php
    require_once ("custom/php/common.php");
    global $current_page, $link;

    $link = ConnectDatabase();
    $capability_name = 'manage_allowed_values';
    $current_page = get_site_url().'/index.php/'.basename(get_permalink()); // must tell teacher about this!

    if (current_user_can($capability_name) & is_user_logged_in())
    {
        if (!isset($_REQUEST['estado']))
        {
            $value_type = "SELECT * 
                            FROM subitem 
                            WHERE subitem.value_type = 'enum'"; // query that verifys wheter we have subitems if the value type enum
            $value_type_result = mysqli_query($link,$value_type);
            if (!$value_type_result)
            {
                echo "<br>";
                echo "Error description: ".mysqli_error($link);
            }
            $rows = mysqli_num_rows($value_type_result);
            if ($rows > 0) // if we have subitems with the value type enum then we show the content
            {
                echo "<br>";
                ShowTable(); // shows the table heading
                ShowContent($link); // shows the table content
                echo "</table>"; // Closing tag of the table
            }
            else
            {
                echo"<br>";
                echo "Não há subitems especificados cujo tipo de valor seja enum. Especificar primeiro novo(s) item(s) e depois voltar a esta opção.";
            }
        }
        elseif($_REQUEST['estado'] == "introducao") // if we click on link that is it on the subitem name
        {
            // PHP session is used to store and pass information from one page to another temporarily (until user close the website).
            // PHP session technique is widely used in shopping websites where we need to store and pass cart information e.g. username, product code, product name, product price etc from one page to another.
            // PHP session creates unique user id for each browser to recognize the user and avoid conflict between multiple browsers.
            // PHP $_SESSION is an associative array that contains all session variables. It is used to set and get session variable values.
            $_SESSION['subitem_id'] = $_REQUEST['subitem'];
            $subitem_id = $_SESSION['subitem_id'];
            InsertForm($subitem_id); // display the form
        }
        elseif($_REQUEST['estado'] == "inserir") // after we click on the insert allowed value
        {
            $insert = FormValidation(); // function that returns true or false
            $allowed_value_name = $_REQUEST['value_name'];// wrk variable
            $allowed_value_state = 'active';
            //Needed this again because $_Session was lost somewhere. Somehow was undefined
            $_SESSION['subitem_id'] = $_REQUEST['subitem'];
            $subitem_id = $_SESSION['subitem_id'];
            InsertIntoTable($insert,$link,$current_page,$subitem_id,$allowed_value_name,$allowed_value_state);
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
        echo "item";
        echo "</strong>";
        echo "</th>";
        echo "<th>";
        echo "id";
        echo "</th>";
        echo "<th>";
        echo "<strong>";
        echo "subitem";
        echo "</strong>";
        echo "</th>";
        echo "<th>";
        echo "id";
        echo "</th>";
        echo "<th>";
        echo "valores permitidos";
        echo "</th>";
        echo "<th>";
        echo "estado";
        echo "</th>";
        echo "<th>";
        echo "ação";
        echo "</th>";
    }

    function ShowContent($link)
    {
        //First of all get all items
        $item_query = "SELECT DISTINCT id,name
                       FROM item
                       ORDER BY name ASC;"; // selects the item name and id and order them by name
        $item_query_result = mysqli_query($link, $item_query);

        if (!$item_query_result)
        {
            echo "<br>";
            echo "item_query_result";
            echo "<br>";
            echo "Error description: " . mysqli_error($link);
        }

        while($item = mysqli_fetch_assoc($item_query_result))
        {
            $item_id = $item['id']; // work variables
            $item_name = $item['name'];
            $subitem_query = "SELECT id, name
                              FROM subitem  
                              WHERE subitem.item_id =".$item_id." AND subitem.value_type = 'enum'
                              ORDER BY name;"; //Select the subitem name and if that haves the same item id and the value type its enum, ordered by name
            $subitem_query_result = mysqli_query($link,$subitem_query);
            if(!$subitem_query_result)
            {
                echo "<br>";
                echo "subitem_query_result";
                echo "<br>";
                echo "Error description: " . mysqli_error($link);
            }
            while($subitem = mysqli_fetch_assoc($subitem_query_result))
            {
                $subitem_id = $subitem['id'];// work variables
                $subitem_name = $subitem['name'];
                $allowed_values = "SELECT id, value, state
                                    FROM subitem_allowed_value
                                    WHERE subitem_allowed_value.subitem_id =".$subitem_id; // get the allowed values id, value and state that have the same subitem id
                $allowed_values_result =  mysqli_query($link,$allowed_values);
                if (!$allowed_values_result)
                {
                   echo "<br>";
                   echo "allowed_values";
                   echo "<br>"; 
                   echo "Error description: " . mysqli_error($link);
                }
                 $rowspawn = mysqli_num_rows($allowed_values_result); // the rowspan will depend ont the allowed value query, because it will give the amount of allowed values of each subitem
                 if($rowspawn > 0)// if haves more than 0 allowed values
                 {
                     echo "<tr>";
                     echo "<td rowspan=".$rowspawn.">"; // rowspawn needed
                     echo $item_name;
                     echo "</td>";
                     echo "<td rowspan=".$rowspawn.">"; // rowspawn needed
                     echo $subitem_id;
                     echo "</td>";
                     echo "<td rowspan=".$rowspawn.">"; // rowspawn needed
                     //Its name is query string. After the question mark you can pass key-value pairs and use them server-side. Also used as a link to insert allowed values
                     echo "<a href = ?estado=introducao&subitem=".$subitem_id." >";
                     echo "[".$subitem_name."]";
                     echo "</a>";
                     echo "</td>";
                     while ($allowed_value = mysqli_fetch_assoc($allowed_values_result)) // loop through each allowed value
                     {
                         $allowed_value_id = $allowed_value['id'];
                         $allowed_value_value = $allowed_value['value'];
                         $allowed_value_state = $allowed_value['state'];
                         echo "<td>";
                         echo $allowed_value_id;
                         echo "</td>";
                         echo "<td>";
                         echo $allowed_value_value;
                         echo "</td>";
                         echo "<td>";
                         echo $allowed_value_state;
                         echo "</td>";
                         echo "<td>";
                         echo "[editar] <br> [desativar] <br> [apagar]";
                         echo "</td>";
                         echo "</tr>";
                     }
                 }
                 else // if the query gives less than 0 allowed values, we use colspan to say that theres no allowed values
                 {
                     echo "<tr>";
                     echo "<td>";
                     echo $item_name;
                     echo "</td>";
                     echo "<td>";
                     echo $subitem_id;
                     echo "</td>";
                     echo "<td >";
                     // Its name is query string. After the question mark you can pass key-value pairs and use them server-side.Also used as a link to insert allowed values
                     echo "<a href = ?estado=introducao&subitem=".$subitem_id." >";
                     echo "[".$subitem_name."]";
                     echo "</a>";
                     echo "</td>";
                     echo "<td colspan='4' style='text-align:center'>";
                     echo "Não há valores permitidos definidos";
                     echo "</td>";
                     echo "</tr>";
                 }
            }
        }
    }

    function InsertForm($subitem_id)
    {
        echo "<h3>";
        echo "Gestão de valores permitidos - Introdução";
        echo "</h3>";
        echo "<form method='post' action='' name = 'allowed_values_form'>";
        echo "<label for='value_name'>";
        echo "Valor Permitido ";
        echo "<span style='color:red'>";
        echo "*";
        echo "</span>";
        echo "</label>";
        echo "<br>";
        echo "<input type='text' id='value_name' name='value_name' placeholder = 'Valor Permitido'>";
        echo "<input type='hidden' name='estado' value='inserir'>";
        echo "<br>";
        echo "<p style='color:red'>";
        echo "*Obrigatório preencher";
        echo "</p>";
        echo "<input type='submit' value='Inserir valor permitido'>";
        echo "</form>";
    }

    function FormValidation()
    {
        echo "<h3>";
        echo "Gestão de valores permitidos - Inserção";
        echo "</h3>";
        if (!empty($_REQUEST['value_name'])) // verify if the field of the value its not empty
        {
                if(!preg_match("/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/",$_REQUEST['value_name'])) // verify wheter the field does not contain numbers
                {
                    echo "<p>";
                    echo "<strong>";
                    echo "O nome do valor permitido não está dentro das normas! Retire os carateres alfanuméricos";
                    echo "</strong>";
                    echo "<p>";
                    return false;
                }
                else
                {
                    return true;
                }
        }
        else
        {
            echo "<p>";
            echo "<strong>";
            echo "O nome do valor permitido não pode estar em branco!";
            echo "</strong>";
            echo "</p>";
            return false;
        }
    }

    function InsertIntoTable($insert,$link,$current_page,$subitem_id,$allowed_value_name,$allowed_value_state)
    {
        if (!$insert) // if the insert comes not valid enters in here
        {
            echo "<p>";
            echo "<strong>";
            echo "O nome do valor permitido não está dentro das normas!";
            echo "</strong>";
            echo "</p>";
            GoBackButton();
        }
        else // otherwise we enter the allowed value on the table
        {
            $insert_query = "INSERT INTO subitem_allowed_value (subitem_id, value, state) VALUES ('" . $subitem_id . "', '" . $allowed_value_name ."' , '" . $allowed_value_state ."');";
            $insert_query_result = mysqli_query($link,$insert_query);
            if(!$insert_query_result)
            {
                echo "<br>";
                echo "insert_value";
                echo "<br>";
                echo "Error description: ".mysqli_error($link);
                echo "<br>";
                GoBackButton();
            }
            else
            {
                echo "<p>";
                echo"Inseriu os dados de novo valor permitido com sucesso.";
                echo "</p>";
                echo"Clique em <a href='{$current_page}'>Continuar</a> para avançar";
            }
        }
    }
?>