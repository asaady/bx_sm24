<?
uploadFTP("93.185.192.56", "Elyon", "El770770", $_SERVER['DOCUMENT_ROOT'].'/dnc/goods_6_1.txt', "test.txt");

function uploadFTP($server, $username, $password, $local_file, $remote_file){
    // connect to server
    $connection = ftp_connect($server);

    // login
    if (@ftp_login($connection, $username, $password)){
        // successfully connected
    }else{
        return false;
    }

    ftp_put($connection, $remote_file, $local_file, FTP_BINARY);
    ftp_close($connection);
    return true;
}
?>