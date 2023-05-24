<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">
    </head>
    <body style="font-family: 'Roboto'; width:full; height:full; ">
        <div style="width: 100%; height: 100%; background-color: #181624; color:white; padding-top: 2rem; padding-bottom: 2rem;">
            <div style="margin: auto; width: 10%; font-weight: bold;">
                <x-svg-quote />              
            </div>
            <div style="margin: auto; margin-top:2%; width: 18%; height:10%; color:#DDCCAA;">
                MOVIE QUOTES
            </div>
            <div style="margin-left: 3%; width: 48%; height:10%">
                Hola {{$user->name}}!
            </div>
            <div style="margin-left: 3%; width: 80%; height:10%;">
                <p style="color: white;">
                    Thanks for joining Movie quotes! We really appreciate it. Please click the button below to verify your account:
                </p>
            </div>
            <div style="margin-left: 3%;">
                <div>
                    <a style="background-color: #E31221; border-radius: 4px; padding: 10px 30px; text-decoration:none; color:white;" 
                    href="{{config('app.front_url').'/?token='.$token.'&email='.$user->email}}">
                    Verify account</a>
                </div>
            </div>
            <div style="margin-left: 3%; width: 80%;">
                <p style="color: white;">
                    If clicking doesn't work, you can try copying and pasting it to your browser:
                </p>
            </div>
            <div style="margin-left: 3%; width: 80%; height: 20%;">
                <p style="word-wrap: break-word; color:#DDCCAA;">
                    {{config('app.front_url').'/?token='.$token.'&email='.$user->email}}
                </p>
            </div>
            <div style="margin-left:3%; width: 80%;">
                <p style="color: white;">
                    If you have any problems, please contact us: <span style="color: white;">support@moviequotes.ge</span>              
                </p>
            </div>
            <div style="margin-left:3%; width: 80%;">
                <p style="color: white;">
                    MovieQuotes Crew                
                </p>
            </div>
        </div>
    </body>
</html>