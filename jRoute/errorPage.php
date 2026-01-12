<?php

class ErrorPage
{
    private static $statusCodes = [
        404 => ['Not Found', "The requested URL was not found on this server."],
        405 => ['Method Not Allowed', "The method specified in the request is not allowed for the resource identified by the request URI."],
        403 => ['Forbidden', "You don't have permission to access this resource."],
        415 => ['Unsupported Media Type', "The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method."],
        422 => ['Unprocessable Entity', "The server understands the content type of the request entity, and the syntax of the request entity is correct, but it was unable to process the contained instructions."],
    ];

    public static function Display($statusCode, $errorUri, $errorMsg = "")
    {
        http_response_code($statusCode);

        $status = self::$statusCodes[$statusCode];

        echo '
<!DOCTYPE html>
<html lang="en">

<head>
    <title>' . $statusCode . ': ' . $status[0] . '</title>
    <style>
        body {
            background-color: #eeeeee;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: "Segoe UI", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }

        .error-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 90%;
        }

        h1 {
            margin-top: 0;
            color: #b35a46;
        }

        code {
            display: block;
            margin-top: 20px;
            padding: 10px;
            background-color: #eeeeee;
            border-radius: 5px;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #222222;
            }

            .error-box {
                background-color: #333333;
                color: white;
            }

            h1 {
                color: #ff8a80;
            }

            code {
                background-color: #222222;
                color: white;
            }
        }
    </style>
</head>

<body>
    <div class="error-box">
        <h1>' . $statusCode . ': ' . $status[0] . '</h1>
        <p>' . $status[1] . '</p>
        <code>' . $errorUri . '</code>
        ' . ($errorMsg != "" ? "<code>$errorMsg</code>" : "") . '
    </div>
</body>

</html>
';
    }
}