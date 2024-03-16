<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Client</title>
</head>
<body>

 <?php
    // if(isset($_POST["name"]) and !empty($_POST["name"])){
    //     print($_POST['name']);
    //     $name = $_POST["name"];
    //     $a = new MeshCtl($name);
    //     print($b->runContainer());
    // }
?> 

    <!-- <form method="post" action="/index">
    <label for="name">COntainer name:</label><br>
    <input type="text" id="name" name="name" ><br>
    <input type="submit" value="Submit">
    </form>  -->


    <div id="errorLogs"></div>

    <script>
        const socket = new WebSocket('ws://94.237.79.8:8080');

        socket.addEventListener('open', (event) => {
            console.log('Connected to the WebSocket server');
        });

        socket.addEventListener('message', (event) => {
            // Handle incoming messages (error logs in this case)
            const errorLogsDiv = document.getElementById('errorLogs');
            errorLogsDiv.innerHTML = event.data.replace(/\n/g, '<br>');
        });

        socket.addEventListener('close', (event) => {
            console.log('Disconnected from the WebSocket server');
        });
    </script>

</body>
</html>
