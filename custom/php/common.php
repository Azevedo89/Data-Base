<?php
    require_once ("custom/php/common.php");
    global $current_page;
    $current_page = get_site_url().'/'.basename(get_permalink()); // Must be on the pages

    function ConnectDatabase() //https://stackoverflow.com/questions/9026630/check-for-database-connection-otherwise-display-message
    {
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($link->connect_error)
        {
            die("Connection failed: " . $link->connect_error);
        }
        echo "<br>";
        echo "Connected successfully to database!"."\n";
        return $link;
    }

    function VerifyLogIn(){ // Function to verify if user is logged in
        if (!is_user_logged_in())
        {
            echo "<br>";
            echo "User not logged in!";
            return;
        }
        echo "<br>";
        echo "User logged in!";
    }

    function VerifyCapability($capability_name){ //Function to verify the wordpress capability
        if (!current_user_can($capability_name))
        {
            echo "<br>";
            echo "Current user cannot use ".$capability_name;
            return;
        }
        echo "<br>";
        echo "Current user can use".$capability_name;
    }

    function GoBackButton(){
        echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
    <noscript>
    <a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
    </noscript>";
    }

    function get_enum_values($connection, $table, $column )
    {
        $query = " SHOW COLUMNS FROM `$table` LIKE '$column' ";
        $result = mysqli_query($connection, $query );
        $row = mysqli_fetch_array($result , MYSQLI_NUM );
        #extract the values
        #the values are enclosed in single quotes
        #and separated by commas
        $regex = "/'(.*?)'/";
        preg_match_all( $regex , $row[1], $enum_array );
        $enum_fields = $enum_array[1];
        return( $enum_fields );
    }
?>


