<!DOCTYPE html>
<html><title>Your page</title><body><pre>
<h1>Random Numbers</h1><br><br><?php
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "sendNumber:8080");
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $output = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);
        print $output;
?>
</body></html>
